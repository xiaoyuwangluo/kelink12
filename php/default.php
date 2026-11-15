<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 站点入口 (对应原 Default.aspx)
 * 302 跳转到默认站点首页
 */
require_once __DIR__ . '/includes/bootstrap.php';

header('Location: wapindex.php?siteid=1', true, 302);
exit;
