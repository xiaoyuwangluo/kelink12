<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 退出登录 (对应原 WapLogout.aspx)
 */
require_once __DIR__ . '/includes/bootstrap.php';

$page = new WapPage();
$page->userSession->logout();
$siteid = $page->siteid;
header('Location: wapindex.php?siteid=' . $siteid);
exit;
