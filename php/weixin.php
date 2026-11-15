<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 微信服务器回调 (对应原 WeiXin/WeiXin.aspx)
 * GET 带 signature 参数时回显 echostr 完成接入校验；
 * POST 时解析 XML(或表单)消息，写入 WapWeixinMsg 并回复文本消息。
 */
require_once __DIR__ . '/includes/bootstrap.php';

$db = Database::getInstance();
$siteid = isset($_GET['siteid']) ? (int)$_GET['siteid'] : 1;

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

    $raw = file_get_contents('php://input');
    if ($raw !== false && strpos(ltrim($raw), '<') === 0) {
        // 解析微信 XML 消息体
        $xml = @simplexml_load_string($raw, 'SimpleXMLElement', LIBXML_NOCDATA);
        if ($xml !== false) {
            $fromuser = (string)($xml->FromUserName ?? '');
            $touser   = (string)($xml->ToUserName ?? '');
            $content  = (string)($xml->Content ?? '');
        }
    }
    if ($fromuser === '' && $content === '') {
        // 兼容表单格式提交
        $fromuser = trim($_POST['FromUserName'] ?? $_POST['fromuser'] ?? '');
        $touser   = trim($_POST['ToUserName'] ?? $_POST['touser'] ?? '');
        $content  = trim($_POST['Content'] ?? $_POST['content'] ?? '');
    }

    // 回复文本消息
    $reply = '小宇网络WAP建站系统：已收到您的消息' . ($content !== '' ? '「' . $content . '」' : '') . '，回复"建站"可申请开通WAP站点。';

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
