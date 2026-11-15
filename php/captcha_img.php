<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 验证码图片输出页 (根目录)：GET 直接输出 PNG，答案存入 session 供 Captcha::verify() 校验
 * 不加载 bootstrap，避免 HTML 头干扰图片输出
 */
require_once __DIR__ . '/config.php';
require_once KL_ROOT . '/includes/Captcha.php';

if (!Captcha::render()) {
    http_response_code(503);
    exit('GD 扩展不可用，无法生成验证码');
}
