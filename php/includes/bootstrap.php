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

/**
 * 文件缓存 (对应原 KL_OpenCache 缓存系统)
 * kl_cache_get($key) / kl_cache_put($key, $value)
 */
function kl_cache_get($key) {
    if (KL_OpenCache != 1) return null;
    $file = KL_CACHE_DIR . '/' . md5($key) . '.cache';
    if (!is_file($file)) return null;
    if (filemtime($file) + KL_OpenCacheTime * 3600 < time()) { @unlink($file); return null; }
    $data = @file_get_contents($file);
    $val = @unserialize($data);
    return $val === false && $data !== serialize(false) ? null : $val;
}

function kl_cache_put($key, $value) {
    if (KL_OpenCache != 1) return false;
    $file = KL_CACHE_DIR . '/' . md5($key) . '.cache';
    return @file_put_contents($file, serialize($value)) !== false;
}
