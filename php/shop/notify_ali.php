<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 商城模块 - 支付宝异步回调 (notify_url)
 * 流程: 验签(ksort+拼接+openssl_verify 支付宝公钥) → 查订单 → 置status=1 → 扣库存 → 写流水
 * 幂等: 已支付订单直接应答 success；应答格式遵循支付宝规范 (success/failure 纯文本)
 * 版权所有: 小宇网络
 */
require_once __DIR__ . '/../includes/bootstrap.php';
require_once KL_ROOT . '/includes/PayGateway.php';

header('Content-Type: text/plain; charset=utf-8');

$pay = new AliPay();
if (!$pay->verifyNotify($_POST)) {
    // 通道未启用或验签失败，均应答 failure 等待渠道重试
    echo 'failure';
    exit;
}

// 仅最终交易状态结算
$tradeStatus = $_POST['trade_status'] ?? '';
if (!in_array($tradeStatus, ['TRADE_SUCCESS', 'TRADE_FINISHED'], true)) {
    echo 'success'; // 中间状态确认接收，等待后续通知
    exit;
}

$orderid = (int)preg_replace('/\D/', '', (string)($_POST['out_trade_no'] ?? ''));
$paidYuan = (float)($_POST['total_fee'] ?? 0);
$db = Database::getInstance();
$result = klPaySettleOrder($db, $orderid, $paidYuan, '支付宝');
echo $result === 'ok' ? 'success' : 'failure';
