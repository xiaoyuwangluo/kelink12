<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 支付网关抽象层 (对应开发路线图"真实支付网关")
 * - AliPay : 支付宝页面跳转支付 (RSA2 排序签名) + 异步回调验签 (openssl_verify 支付宝公钥)
 * - WxPay  : 微信支付 APIv3 Native 下单 (RSA 私钥签名头 WECHATPAY2-SHA256-RSA2048) + 回调 AES-256-GCM 解密
 * 全部基于 openssl_* / 原生 curl 实现，无任何 composer 依赖。
 * 凭据为空或开关关闭 (KL_AliPay_Open / KL_WxPay_Open = 0) 时，对应方法返回 false，
 * 提示文案通过 getLastError() 获取。
 * 版权所有: 小宇网络
 */
if (!defined('KL_ROOT')) exit;

/**
 * 网关公共基类: PEM 组装与 RSA 签名/验签工具
 */
abstract class PayGatewayBase {
    protected $lastError = '';      // 最近一次失败提示文案
    protected $overridden = false;  // 构造注入了凭据 (测试用途) 时忽略 Open 开关

    /** 最近一次失败的提示文案 */
    public function getLastError() {
        return $this->lastError;
    }

    /** 裸 base64 密钥文本自动组装为 PEM (已是 PEM 则原样返回) */
    protected function toPem($body, $type) {
        $body = trim((string)$body);
        if ($body === '') return '';
        if (strpos($body, '-----BEGIN') === 0) return $body;
        return "-----BEGIN {$type}-----\n" . chunk_split($body, 64, "\n") . "-----END {$type}-----\n";
    }

    /** RSA-SHA256 (RSA2) 私钥签名，返回 base64 签名串；失败返回 false */
    protected function rsaSign($data, $privateKeyPem) {
        $pkey = openssl_pkey_get_private($privateKeyPem);
        if (!$pkey) { $this->lastError = '私钥无效，无法加载签名密钥'; return false; }
        $sig = '';
        $ok = @openssl_sign($data, $sig, $pkey, OPENSSL_ALGO_SHA256);
        return $ok ? base64_encode($sig) : false;
    }

    /** RSA-SHA256 公钥验签，返回 bool */
    protected function rsaVerify($data, $signatureB64, $publicKeyPem) {
        $pub = openssl_pkey_get_public($publicKeyPem);
        if (!$pub) { $this->lastError = '公钥无效，无法加载验签密钥'; return false; }
        return @openssl_verify($data, base64_decode((string)$signatureB64), $pub, OPENSSL_ALGO_SHA256) === 1;
    }
}

/**
 * 支付宝网关 (经典 gateway.do 直连/ wap 支付，RSA2 签名)
 */
class AliPay extends PayGatewayBase {
    private $gateway = 'https://openapi.alipay.com/gateway.do';
    private $partner = '';
    private $sellerId = '';
    private $privateKey = '';
    private $publicKey = '';

    /**
     * @param array $config 可注入覆盖凭据 (测试用): partner/seller_id/private_key/public_key
     */
    public function __construct($config = []) {
        $this->overridden = !empty($config);
        $this->partner    = $config['partner']     ?? KL_AliPay_Partner;
        $this->sellerId   = $config['seller_id']   ?? KL_AliPay_Seller;
        $this->privateKey = $config['private_key'] ?? KL_AliPay_PrivateKey;
        $this->publicKey  = $config['public_key']  ?? KL_AliPay_PublicKey;
    }

    /** 通道是否可用: KL_AliPay_Open=1 且应用私钥/支付宝公钥齐全 (构造注入视为可用) */
    public function ready() {
        if (!$this->overridden && KL_AliPay_Open != 1) return false;
        return $this->privateKey !== '' && $this->publicKey !== '';
    }

    /**
     * 页面跳转支付: 组装业务参数 → ASCII 排序拼接 → RSA2 私钥签名 → 生成带 sign 的完整跳转 URL
     * @param string $outTradeNo 商户订单号
     * @param float  $totalFee   金额(元)
     * @return string|false 成功返回跳转 URL，失败返回 false (提示见 getLastError)
     */
    public function pagePay($outTradeNo, $totalFee, $subject, $notifyUrl, $returnUrl) {
        if (!$this->ready()) { $this->lastError = '支付宝通道未启用或凭据不全'; return false; }
        $params = [
            'service'        => 'alipay.wap.create.direct.pay.by.gateway',
            '_input_charset' => 'utf-8',
            'sign_type'      => 'RSA2',
            'partner'        => $this->partner,
            'seller_id'      => $this->sellerId,
            'out_trade_no'   => (string)$outTradeNo,
            'subject'        => (string)$subject,
            'payment_type'   => '1',
            'total_fee'      => number_format((float)$totalFee, 2, '.', ''),
            'notify_url'     => (string)$notifyUrl,
            'return_url'     => (string)$returnUrl,
        ];
        // 空值参数不参与签名与提交
        $params = array_filter($params, function ($v) { return $v !== '' && $v !== null; });
        $sign = $this->rsaSign($this->buildSignString($params), $this->toPem($this->privateKey, 'RSA PRIVATE KEY'));
        if ($sign === false) return false;
        $params['sign'] = $sign;
        return $this->gateway . '?' . http_build_query($params);
    }

    /**
     * 异步回调验签: 剔除 sign/sign_type → ksort → k=v& 拼接 → openssl_verify (支付宝公钥)
     * @param array $post 回调 POST 参数
     * @return bool 验签通过返回 true
     */
    public function verifyNotify($post) {
        if (!$this->ready()) { $this->lastError = '支付宝通道未启用或凭据不全'; return false; }
        if (!is_array($post) || empty($post['sign'])) { $this->lastError = '回调缺少签名'; return false; }
        $sign = (string)$post['sign'];
        $data = $this->buildSignString($post);
        $result = $this->rsaVerify($data, $sign, $this->toPem($this->publicKey, 'PUBLIC KEY'));
        if (!$result && $this->lastError === '') $this->lastError = '回调验签失败';
        return $result;
    }

    /** 待签名串: 剔除 sign/sign_type，按键 ASCII 升序，k=v 以 & 连接 (原始值，不 url 编码) */
    private function buildSignString($params) {
        unset($params['sign'], $params['sign_type']);
        ksort($params);
        $parts = [];
        foreach ($params as $k => $v) {
            if ($v === '' || $v === null) continue;
            $parts[] = $k . '=' . $v;
        }
        return implode('&', $parts);
    }
}

/**
 * 微信支付网关 (APIv3 Native 下单 + 回调 AEAD 解密)
 */
class WxPay extends PayGatewayBase {
    private $apiBase = 'https://api.mch.weixin.qq.com';
    private $nativePath = '/v3/pay/transactions/native';
    private $appid = '';
    private $mchid = '';
    private $apiv3Key = '';
    private $certPath = '';

    /**
     * @param array $config 可注入覆盖凭据 (测试用): appid/mchid/apiv3_key/cert_path
     */
    public function __construct($config = []) {
        $this->overridden = !empty($config);
        $this->appid    = $config['appid']     ?? KL_WxPay_Appid;
        $this->mchid    = $config['mchid']     ?? KL_WxPay_Mchid;
        $this->apiv3Key = $config['apiv3_key'] ?? KL_WxPay_Key;
        $this->certPath = $config['cert_path'] ?? KL_WxPay_CertPath;
    }

    /** 通道是否可用: KL_WxPay_Open=1 且 Appid/Mchid/APIv3密钥/证书目录(含 apiclient_key.pem) 齐全 (构造注入视为可用) */
    public function ready() {
        if (!$this->overridden && KL_WxPay_Open != 1) return false;
        if ($this->appid === '' || $this->mchid === '' || $this->apiv3Key === '' || $this->certPath === '') return false;
        return is_file($this->keyFile());
    }

    private function keyFile() {
        return rtrim($this->certPath, '/') . '/apiclient_key.pem';
    }

    /**
     * 构造 APIv3 签名串与 Authorization 头 (可单独调用便于测试)
     * 签名串: METHOD\nURL\nTIMESTAMP\nNONCE\nBODY\n ，RSA-SHA256 私钥签名后 base64
     * @return string|false 形如 WECHATPAY2-SHA256-RSA2048 mchid="...",nonce_str="...",...
     */
    public function buildAuthorization($method, $urlPath, $body) {
        $pem = @file_get_contents($this->keyFile());
        if ($pem === false) { $this->lastError = '商户私钥文件不存在: apiclient_key.pem'; return false; }
        $nonce = strtoupper(bin2hex(random_bytes(16)));
        $ts = (string)time();
        $message = strtoupper($method) . "\n" . $urlPath . "\n" . $ts . "\n" . $nonce . "\n" . $body . "\n";
        $sign = $this->rsaSign($message, $pem);
        if ($sign === false) return false;
        return sprintf('WECHATPAY2-SHA256-RSA2048 mchid="%s",nonce_str="%s",timestamp="%s",serial_no="%s",signature="%s"',
            $this->mchid, $nonce, $ts, $this->getCertSerial(), $sign);
    }

    /** 读取商户证书序列号 (apiclient_cert.pem，缺省返回空串) */
    private function getCertSerial() {
        $certFile = rtrim($this->certPath, '/') . '/apiclient_cert.pem';
        if (is_file($certFile)) {
            $info = @openssl_x509_parse((string)@file_get_contents($certFile));
            if (is_array($info) && isset($info['serialNumber'])) return strtoupper($info['serialNumber']);
        }
        return '';
    }

    /**
     * Native 下单: 请求 unifiedorder 接口，返回 code_url (供前端生成二维码)
     * @param int $totalFen 金额(分)
     * @return string|false 成功返回 code_url，失败返回 false (提示见 getLastError)
     */
    public function nativePay($outTradeNo, $totalFen, $description, $notifyUrl) {
        if (!$this->ready()) { $this->lastError = '微信支付通道未启用或凭据不全'; return false; }
        $body = json_encode([
            'appid'        => $this->appid,
            'mchid'        => $this->mchid,
            'description'  => (string)$description,
            'out_trade_no' => (string)$outTradeNo,
            'notify_url'   => (string)$notifyUrl,
            'amount'       => ['total' => (int)$totalFen, 'currency' => 'CNY'],
        ], JSON_UNESCAPED_UNICODE);
        $auth = $this->buildAuthorization('POST', $this->nativePath, $body);
        if ($auth === false) return false;

        $ch = curl_init($this->apiBase . $this->nativePath);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $body,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_HTTPHEADER     => [
                'Accept: application/json',
                'Content-Type: application/json; charset=utf-8',
                'User-Agent: XiaoyuWap-PayGateway/1.0',
                'Authorization: ' . $auth,
            ],
        ]);
        $resp = curl_exec($ch);
        $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr = curl_error($ch);
        curl_close($ch);

        if ($resp === false) { $this->lastError = '微信支付请求失败: ' . $curlErr; return false; }
        $data = json_decode((string)$resp, true);
        if ($httpCode != 200 || !is_array($data) || empty($data['code_url'])) {
            $this->lastError = '微信支付下单失败: ' . (is_array($data) && isset($data['message']) ? $data['message'] : 'HTTP ' . $httpCode);
            return false;
        }
        return $data['code_url'];
    }

    /**
     * 回调处理: 解析 JSON → resource 字段 AES-256-GCM 解密 (APIv3 密钥)
     * @param string $rawBody php://input 原始报文
     * @return array|false 解密后的业务数据数组；失败返回 false (提示见 getLastError)
     */
    public function verifyNotify($rawBody) {
        if (!$this->ready()) { $this->lastError = '微信支付通道未启用或凭据不全'; return false; }
        if (strlen($this->apiv3Key) != 32) { $this->lastError = 'APIv3 密钥必须为 32 字节'; return false; }
        $json = json_decode((string)$rawBody, true);
        $res = is_array($json) ? ($json['resource'] ?? null) : null;
        if (!is_array($res) || empty($res['ciphertext']) || empty($res['nonce'])) {
            $this->lastError = '回调报文格式错误: 缺少 resource';
            return false;
        }
        $cipher = base64_decode((string)$res['ciphertext'], true);
        if ($cipher === false || strlen($cipher) <= 16) { $this->lastError = '回调密文无效'; return false; }
        // 密文尾部 16 字节为 GCM 认证标签
        $tag = substr($cipher, -16);
        $data = substr($cipher, 0, -16);
        $plain = @openssl_decrypt($data, 'aes-256-gcm', $this->apiv3Key,
            OPENSSL_RAW_DATA, (string)$res['nonce'], $tag, (string)($res['associated_data'] ?? ''));
        if ($plain === false) { $this->lastError = '回调解密失败 (密钥不匹配或数据被篡改)'; return false; }
        $decoded = json_decode($plain, true);
        if (!is_array($decoded)) { $this->lastError = '回调明文解析失败'; return false; }
        return $decoded;
    }
}

/**
 * 回调订单结算 (notify_ali.php / notify_wx.php 共用)
 * 流程: 查订单 → 金额核对 → 置 status=1 (仅 0→1，幂等) → 扣库存 → 写 WapAccountLog 流水
 * @param Database $db       数据库实例
 * @param int      $orderid  订单号 (out_trade_no)
 * @param float    $paidYuan 回调实付金额(元)
 * @param string   $channel  渠道名 (用于流水备注)
 * @return string 'ok' 已结算或幂等成功 | 'fail' 拒绝 | 'notfound' 订单不存在
 */
function klPaySettleOrder($db, $orderid, $paidYuan, $channel) {
    $order = $db->getOne("SELECT * FROM WapShopOrder WHERE id=?", [$orderid]);
    if (!$order) return 'notfound';
    $st = (int)$order['status'];
    if ($st != 0) {
        // 幂等: 已支付/已完成直接确认成功；已取消等状态拒绝
        return ($st === 1 || $st === 2) ? 'ok' : 'fail';
    }
    // 金额核对 (容差 1 分)
    if (abs((float)$paidYuan - (float)$order['totalprice']) > 0.011) return 'fail';
    try {
        $db->beginTransaction();
        // 仅状态 0 可置 1，并发重复回调在此被拒
        $n = $db->execute("UPDATE WapShopOrder SET status=1 WHERE id=? AND status=0", [$orderid]);
        if ($n != 1) {
            $db->rollBack();
            return 'ok'; // 并发下已被其他回调结算，按幂等成功应答
        }
        // 扣库存 (不足则回滚，等待渠道重试)
        $n = $db->execute("UPDATE WapShop SET stock=stock-? WHERE id=? AND stock>=?",
            [$order['num'], $order['shopid'], $order['num']]);
        if ($n != 1) {
            $db->rollBack();
            return 'fail';
        }
        // 写账户流水 (第三方支付不动余额，balance 记当前余额)
        $balance = (int)$db->getScalar("SELECT money FROM WapUser WHERE id=?", [$order['userid']]);
        $db->insert("INSERT INTO WapAccountLog (userid,type,num,balance,remark,addtime) VALUES (?,?,?,?,?,NOW())",
            [$order['userid'], 'out', (int)round($order['totalprice']), $balance,
             $channel . '支付订单 #' . $orderid]);
        $db->commit();
        return 'ok';
    } catch (Exception $e) {
        if ($db->getPdo()->inTransaction()) $db->rollBack();
        return 'fail';
    }
}
