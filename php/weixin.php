<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 微信服务器回调 (对应原 WeiXin/WeiXin.aspx)
 * GET 带 signature 参数时回显 echostr 完成接入校验；
 * POST 时解析 XML(或表单)消息，写入 WapWeixinMsg 并回复文本消息。
 * 正式对接：WapWeixin.isopen=1 且 appid/secret 配置齐全时启用
 *   - access_token 缓存获取 (kl_cache_get/kl_cache_put)
 *   - 自定义菜单创建接口函数
 *   - 关注(subscribe)事件自动注册 WapUser(wx_openid) 并回复欢迎语
 * 凭据未配置(isopen=0 或 appid/secret 为空)时维持本地模拟应答。
 */
require_once __DIR__ . '/includes/bootstrap.php';

$db = Database::getInstance();
$siteid = isset($_GET['siteid']) ? (int)$_GET['siteid'] : 1;

// ===== 读取微信配置 (WapWeixin: siteid,appid,secret,token,isopen) =====
$wxvo = null;
try {
    $wxvo = $db->getOne("SELECT * FROM WapWeixin WHERE siteid=?", [$siteid]);
} catch (Exception $e) {
    $wxvo = null;
}
$wx_enabled = $wxvo
    && (int)($wxvo['isopen'] ?? 0) === 1
    && trim((string)($wxvo['appid'] ?? '')) !== ''
    && trim((string)($wxvo['secret'] ?? '')) !== '';

/**
 * 获取微信全局 access_token (带文件缓存，缓存开关 KL_OpenCache)
 * @param string $appid  公众号AppID
 * @param string $secret 公众号AppSecret
 * @param bool   $refresh 强制刷新缓存
 * @return string 获取失败返回空字符串
 */
function kl_weixin_access_token($appid, $secret, $refresh = false) {
    $appid = trim((string)$appid);
    $secret = trim((string)$secret);
    if ($appid === '' || $secret === '') return '';
    $cache_key = 'kl_weixin_token_' . $appid;
    if (!$refresh) {
        $cached = kl_cache_get($cache_key);
        if (is_string($cached) && $cached !== '') return $cached;
    }
    $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential'
         . '&appid=' . urlencode($appid) . '&secret=' . urlencode($secret);
    $ctx = stream_context_create(['http' => ['timeout' => 5]]);
    $body = @file_get_contents($url, false, $ctx);
    if ($body === false) return '';
    $json = json_decode($body, true);
    if (!is_array($json) || empty($json['access_token'])) return '';
    $token = (string)$json['access_token'];
    // access_token 官方有效期 7200 秒，实际缓存时长由 KL_OpenCacheTime(小时) 控制
    kl_cache_put($cache_key, $token);
    return $token;
}

/**
 * 创建微信自定义菜单
 * @param string $access_token 通过 kl_weixin_access_token 获取
 * @param array  $buttons 微信官方 button 结构数组
 * @return array ['errcode'=>int, 'errmsg'=>string]
 */
function kl_weixin_create_menu($access_token, $buttons) {
    if ($access_token === '' || empty($buttons) || !is_array($buttons)) {
        return ['errcode' => -1, 'errmsg' => '参数不完整'];
    }
    $url = 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token=' . urlencode($access_token);
    $payload = json_encode(['button' => array_values($buttons)], JSON_UNESCAPED_UNICODE);
    $ctx = stream_context_create(['http' => [
        'method'  => 'POST',
        'header'  => "Content-Type: application/json; charset=utf-8\r\n",
        'content' => $payload,
        'timeout' => 5,
    ]]);
    $body = @file_get_contents($url, false, $ctx);
    if ($body === false) return ['errcode' => -1, 'errmsg' => '请求失败'];
    $json = json_decode($body, true);
    return is_array($json) ? $json : ['errcode' => -1, 'errmsg' => '响应格式错误'];
}

/** 关注事件自动注册微信用户 (按 wx_openid 匹配，不存在则注册) */
function kl_weixin_subscribe_register($db, $openid) {
    if ($openid === '') return;
    try {
        $exist = $db->getOne("SELECT id FROM WapUser WHERE wx_openid=?", [$openid]);
        if ($exist) return;
        $username = 'wx_' . substr(md5($openid), 0, 12);
        $i = 0;
        while ((int)$db->getScalar("SELECT COUNT(*) FROM WapUser WHERE username=?", [$username]) > 0) {
            $i++;
            $username = 'wx_' . substr(md5($openid . $i), 0, 12);
        }
        $db->insert(
            "INSERT INTO WapUser (username, password, nickname, email, regtime, regip, islock, money, wx_openid) VALUES (?,?,?,?,NOW(),?,0,0,?)",
            [$username, md5(uniqid('wx', true)), '微信用户', '', UserSession::getClientIp(), $openid]
        );
    } catch (Exception $e) {
        // 注册失败不影响消息应答
    }
}

// ===== 接入校验：GET signature + echostr → 原样回显 =====
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['signature']) && isset($_GET['echostr'])) {
    header('Content-Type: text/plain; charset=utf-8');
    echo $_GET['echostr'];
    exit;
}

// ===== 消息接收：POST =====
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fromuser = '';
    $touser = '';
    $content = '';
    $msgtype = '';
    $event = '';

    $raw = file_get_contents('php://input');
    if ($raw !== false && strpos(ltrim($raw), '<') === 0) {
        // 解析微信 XML 消息体
        $xml = @simplexml_load_string($raw, 'SimpleXMLElement', LIBXML_NOCDATA);
        if ($xml !== false) {
            $fromuser = (string)($xml->FromUserName ?? '');
            $touser   = (string)($xml->ToUserName ?? '');
            $content  = (string)($xml->Content ?? '');
            $msgtype  = strtolower((string)($xml->MsgType ?? ''));
            $event    = strtolower((string)($xml->Event ?? ''));
        }
    }
    if ($fromuser === '' && $content === '' && $msgtype === '') {
        // 兼容表单格式提交
        $fromuser = trim($_POST['FromUserName'] ?? $_POST['fromuser'] ?? '');
        $touser   = trim($_POST['ToUserName'] ?? $_POST['touser'] ?? '');
        $content  = trim($_POST['Content'] ?? $_POST['content'] ?? '');
        $msgtype  = strtolower(trim($_POST['MsgType'] ?? $_POST['msgtype'] ?? ''));
        $event    = strtolower(trim($_POST['Event'] ?? $_POST['event'] ?? ''));
    }

    // 正式对接：关注事件 → 自动注册 wx_openid 用户并回复欢迎语
    if ($wx_enabled && $msgtype === 'event' && $event === 'subscribe') {
        kl_weixin_subscribe_register($db, $fromuser);
        $reply = '欢迎使用小宇网络WAP建站系统！回复"建站"可申请开通WAP站点。';
    } else {
        // 本地模拟回复 (凭据未配置或非关注事件时行为不变)
        $reply = '小宇网络WAP建站系统：已收到您的消息' . ($content !== '' ? '「' . $content . '」' : '') . '，回复"建站"可申请开通WAP站点。';
    }

    // 写入消息表
    try {
        $db->insert(
            "INSERT INTO WapWeixinMsg (siteid, fromuser, content, reply, addtime) VALUES (?,?,?,?,NOW())",
            [$siteid, $fromuser, $content, $reply]
        );
    } catch (Exception $e) {
        // 入库失败不影响回复
    }

    // 输出微信文本消息回复 XML
    header('Content-Type: text/xml; charset=utf-8');
    echo '<xml>' . "\n"
        . '<ToUserName><![CDATA[' . $fromuser . ']]></ToUserName>' . "\n"
        . '<FromUserName><![CDATA[' . $touser . ']]></FromUserName>' . "\n"
        . '<CreateTime>' . time() . '</CreateTime>' . "\n"
        . '<MsgType><![CDATA[text]]></MsgType>' . "\n"
        . '<Content><![CDATA[' . $reply . ']]></Content>' . "\n"
        . '<FuncFlag>0</FuncFlag>' . "\n"
        . '</xml>';
    exit;
}

// ===== 其他访问：接口说明 =====
header('Content-Type: text/plain; charset=utf-8');
echo 'WeiXin Callback - 小宇网络WAP建站系统微信回调接口';
