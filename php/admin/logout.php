<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 管理后台 - 退出登录 (对应原 admin/logout.aspx)
 */
require_once __DIR__ . '/../includes/bootstrap.php';

$page = new WapPage();
$page->checkAdminLogin();

// 清除会话并返回登录页
$sess = new UserSession();
$sess->logout();
header('Location: login.php');
exit;
