<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * GD 图形验证码类 (配合 config.php 中 KL_RegUser_CheckCode 开关使用)
 * 随机4位字符，生成 PNG 图片输出并将答案存入 session；verify() 一次性校验
 */
if (!defined('KL_ROOT')) exit;

class Captcha {
    const SESSION_KEY = 'kl_captcha_code';
    /** 去除易混淆字符 (0/O/1/I/L) */
    private static $chars = 'ABCDEFGHJKMNPQRSTUVWXYZ23456789';

    /** 生成随机验证码字符串 */
    public static function generate($len = 4) {
        $code = '';
        $max = strlen(self::$chars) - 1;
        for ($i = 0; $i < $len; $i++) {
            $code .= self::$chars[mt_rand(0, $max)];
        }
        return $code;
    }

    /**
     * 生成并直接输出 PNG 验证码图片，答案存入 session
     * @return bool GD 可用且输出成功返回 true
     */
    public static function render($width = 120, $height = 40) {
        if (!function_exists('imagecreatetruecolor')) return false;
        if (session_status() === PHP_SESSION_NONE) {
            session_set_cookie_params(KL_SessionTimeOut * 60);
            session_start();
        }
        $code = self::generate();
        $_SESSION[self::SESSION_KEY] = $code;

        $img = imagecreatetruecolor($width, $height);
        $bg = imagecolorallocate($img, mt_rand(235, 250), mt_rand(235, 250), mt_rand(235, 250));
        imagefilledrectangle($img, 0, 0, $width, $height, $bg);

        // 干扰线
        for ($i = 0; $i < 4; $i++) {
            $c = imagecolorallocate($img, mt_rand(150, 220), mt_rand(150, 220), mt_rand(150, 220));
            imageline($img, mt_rand(0, $width), mt_rand(0, $height), mt_rand(0, $width), mt_rand(0, $height), $c);
        }
        // 干扰点
        for ($i = 0; $i < 80; $i++) {
            $c = imagecolorallocate($img, mt_rand(120, 230), mt_rand(120, 230), mt_rand(120, 230));
            imagesetpixel($img, mt_rand(0, $width - 1), mt_rand(0, $height - 1), $c);
        }
        // 字符 (gd5 内置字体: 约9x16像素，逐字随机颜色与纵向抖动)
        $slot = (int)($width / (strlen($code) + 1));
        foreach (str_split($code) as $i => $ch) {
            $c = imagecolorallocate($img, mt_rand(0, 110), mt_rand(0, 110), mt_rand(0, 110));
            $x = (int)($slot * ($i + 0.5));
            $y = mt_rand(4, $height - 20);
            imagestring($img, 5, $x, $y, $ch, $c);
        }

        if (!headers_sent()) {
            header('Content-Type: image/png');
            header('Cache-Control: no-cache, no-store, must-revalidate');
            header('Pragma: no-cache');
        }
        imagepng($img);
        return true;
    }

    /**
     * 校验用户输入 (不区分大小写，一次性使用：校验后即销毁 session 值)
     * @return bool
     */
    public static function verify($input) {
        if (session_status() === PHP_SESSION_NONE) {
            session_set_cookie_params(KL_SessionTimeOut * 60);
            session_start();
        }
        $input = trim((string)$input);
        $code = (string)($_SESSION[self::SESSION_KEY] ?? '');
        unset($_SESSION[self::SESSION_KEY]);
        if ($code === '' || $input === '') return false;
        return strcasecmp($input, $code) === 0;
    }
}
