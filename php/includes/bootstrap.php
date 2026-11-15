<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 引导文件 - 所有页面入口处 require 此文件
 */
require_once __DIR__ . '/../config.php';
require_once KL_ROOT . '/includes/Database.php';
require_once KL_ROOT . '/includes/WapTool.php';
require_once KL_ROOT . '/includes/UserSession.php';
require_once KL_ROOT . '/includes/WapPage.php';

// 设置编码
header('Content-Type: text/html; charset=utf-8');
