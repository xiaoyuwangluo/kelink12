<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 下载处理 (含防盗链) (对应原下载逻辑)
 * 支持防盗链、限速、文件类型过滤
 */
require_once __DIR__ . '/includes/bootstrap.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) die('参数错误');

$db = Database::getInstance();

// 查找下载记录 (从各模块表)
$file = null;
$tables = ['WapDownload', 'WapVideo', 'WapRing', 'WapPicture', 'WapArticle'];
foreach ($tables as $t) {
    $file = $db->getOne("SELECT id,title,downurl FROM $t WHERE id=?", [$id]);
    if ($file) break;
}
if (!$file || empty($file['downurl'])) die('文件不存在');

$downurl = $file['downurl'];

// 防盗链检查
if (KL_DownCheck == 1) {
    if (KL_DownCheckReferrer == 1) {
        $referer = $_SERVER['HTTP_REFERER'] ?? '';
        $host = $_SERVER['HTTP_HOST'] ?? '';
        if ($referer !== '' && strpos($referer, $host) === false) {
            die('非法下载请求');
        }
    }
}

// 文件类型过滤
$ext = strtolower(pathinfo(parse_url($downurl, PHP_URL_PATH), PATHINFO_EXTENSION));
if (in_array($ext, explode('|', KL_NotDownAndUpload))) {
    die('该文件类型不允许下载');
}

// 判断是本地文件还是远程URL
if (preg_match('/^https?:\/\//i', $downurl)) {
    // 远程URL - 直接跳转 (防盗链模式下隐藏真实地址)
    if (KL_DownCheck == 1) {
        // 重定向到真实地址
        header('Location: ' . $downurl);
        exit;
    } else {
        header('Location: ' . $downurl);
        exit;
    }
}

// 本地文件（限制在 uploadfiles 目录内，防路径穿越）
$localPath = KL_ROOT . $downurl;
$real = realpath($localPath);
if ($real === false || strpos($real, realpath(KL_UPLOAD_DIR)) !== 0) die('文件不存在');
$localPath = $real;

// 输出下载
$filename = basename($localPath);
// MIME 类型
$mineTypes = [
    '3gp' => 'video/3gpp', 'mp4' => 'video/mp4', 'mp3' => 'audio/mpeg',
    'jpg' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif',
    'zip' => 'application/zip', 'rar' => 'application/x-rar-compressed',
    'txt' => 'text/plain', 'pdf' => 'application/pdf',
];
$mime = $mineTypes[$ext] ?? 'application/octet-stream';

header('Content-Type: ' . $mime);
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Content-Length: ' . filesize($localPath));

// 限速下载
if (KL_DownThread == 0 && KL_DownSpeed > 0) {
    $fp = fopen($localPath, 'rb');
    while (!feof($fp)) {
        echo fread($fp, KL_DownSpeed);
        flush();
        sleep(1);
    }
    fclose($fp);
} else {
    readfile($localPath);
}
exit;
