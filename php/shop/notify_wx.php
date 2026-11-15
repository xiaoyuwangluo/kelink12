<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 商城模块 - 微信支付异步回调 (APIv3 notify_url)
 * 流程: AES-256-GCM 解密 resource (APIv3密钥) → 查订单 → 置status=1 → 扣库存 → 写流水
 * 幂等: 已支付订单直接应答 SUCCESS；应答格式遵循微信规范 (JSON code=SUCCESS/FAIL)
 * 版权所有: 小宇网络
 */
require_once __DIR__ . '/../includes/bootstrap.php';
require_once KL_ROOT . '/includes/PayGateway.php';

header('Content-Type: application/json; charset=utf-8');

function wxNotifyReply($ok, $msg) {
    if (!$ok) http_response_code(500);
    echo json_encode(['code' => $ok ? 'SUCCESS' : 'FAIL', 'message' => $msg], JSON_UNESCAPED_UNICODE);
    exit;
}

$raw = file_get_contents('php://input');
$pay = new WxPay();
$data = $pay->verifyNotify($raw);
if ($data === false) {
    wxNotifyReply(false, $pay->getLastError() ?: '回调解密失败');
}

// 仅支付成功事件结算
if (($data['trade_state'] ?? '') !== 'SUCCESS') {
    wxNotifyReply(true, '成功'); // 非成功状态确认接收
}

$orderid = (int)preg_replace('/\D/', '', (string)($data['out_trade_no'] ?? ''));
$paidYuan = (int)($data['amount']['total'] ?? 0) / 100; // 微信金额为分
$db = Database::getInstance();
$result = klPaySettleOrder($db, $orderid, $paidYuan, '微信');
wxNotifyReply($result === 'ok', $result === 'ok' ? '成功' : '订单结算失败');
