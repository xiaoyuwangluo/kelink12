<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 通用文件上传类 (对应原上传组件，配合 config.php 中 KL_UpMaxSize / KL_UpAllowExt / KL_NotDownAndUpload)
 * 安全策略：扩展名白名单 + 黑名单双重校验、大小限制、文件名随机化（防覆盖/防路径穿越）、按日期分层目录
 */
if (!defined('KL_ROOT')) exit;

class Upload {

    /**
     * 保存一个 $_FILES 条目到 uploadfiles/YYYYMM/ 下
     * @param array $file $_FILES['xxx'] 数组
     * @return array [bool 是否成功, string 提示信息, array 文件信息[filename原始名, relpath相对uploadfiles路径, abspath绝对路径, size字节]]
     */
    public static function save($file) {
        if (!is_array($file) || !isset($file['error'])) {
            return [false, '无效的上传数据', []];
        }
        // PHP 上传错误码
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errMap = [
                UPLOAD_ERR_INI_SIZE   => '文件超过服务器单文件限制',
                UPLOAD_ERR_FORM_SIZE  => '文件超过表单限制',
                UPLOAD_ERR_PARTIAL    => '文件仅部分上传',
                UPLOAD_ERR_NO_FILE    => '未选择文件',
                UPLOAD_ERR_NO_TMP_DIR => '服务器缺少临时目录',
                UPLOAD_ERR_CANT_WRITE => '文件写入失败',
            ];
            return [false, $errMap[$file['error']] ?? '上传失败', []];
        }
        if (!is_uploaded_file($file['tmp_name'])) {
            return [false, '非法上传来源', []];
        }

        // 大小限制 (KL_UpMaxSize 单位 KB)
        $size = (int)$file['size'];
        if ($size <= 0) return [false, '上传文件为空', []];
        if ($size > KL_UpMaxSize * 1024) {
            return [false, '文件超过大小限制(' . self::formatSize(KL_UpMaxSize * 1024) . ')', []];
        }

        // 扩展名提取：只取原始文件名 basename 的扩展名，防路径穿越
        $original = basename(str_replace('\\', '/', $file['name']));
        $ext = strtolower(pathinfo($original, PATHINFO_EXTENSION));
        if ($ext === '' || !preg_match('/^[a-z0-9]{1,10}$/', $ext)) {
            return [false, '文件扩展名非法', []];
        }

        // 黑名单拒绝 (KL_NotDownAndUpload)
        $blacklist = array_filter(explode('|', strtolower(KL_NotDownAndUpload)));
        if (in_array($ext, $blacklist, true)) {
            return [false, '不允许上传 .' . $ext . ' 类型文件', []];
        }
        // 白名单校验 (KL_UpAllowExt)
        $allowlist = array_filter(explode('|', strtolower(KL_UpAllowExt)));
        if (!in_array($ext, $allowlist, true)) {
            return [false, '扩展名不在允许列表内(允许: ' . KL_UpAllowExt . ')', []];
        }

        // 随机化文件名：日期时间 + 随机串，防覆盖防穿越
        $ym = date('Ym');
        $newName = date('YmdHis') . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
        $dir = KL_UPLOAD_DIR . '/' . $ym;
        if (!is_dir($dir) && !@mkdir($dir, 0755, true) && !is_dir($dir)) {
            return [false, '创建上传目录失败', []];
        }
        $absPath = $dir . '/' . $newName;
        if (!@move_uploaded_file($file['tmp_name'], $absPath)) {
            return [false, '保存文件失败', []];
        }
        @chmod($absPath, 0644);

        return [true, '上传成功', [
            'filename' => $original,
            'relpath'  => $ym . '/' . $newName,
            'abspath'  => $absPath,
            'size'     => $size,
        ]];
    }

    /**
     * 相对路径(相对 uploadfiles)转绝对路径，带防穿越校验；非法返回 null
     */
    public static function absPath($relPath) {
        $relPath = str_replace('\\', '/', (string)$relPath);
        if ($relPath === '' || strpos($relPath, '..') !== false || strpos($relPath, "\0") !== false) return null;
        if ($relPath[0] === '/' || preg_match('/^[A-Za-z]:/', $relPath)) return null;
        $abs = KL_UPLOAD_DIR . '/' . $relPath;
        $realDir = realpath(KL_UPLOAD_DIR);
        $real = realpath($abs);
        if ($realDir === false || $real === false) return null;
        if (strpos($real, $realDir . DIRECTORY_SEPARATOR) !== 0) return null;
        return $real;
    }

    /**
     * 文件访问 URL（相对站点根）
     */
    public static function url($relPath) {
        return 'uploadfiles/' . $relPath;
    }

    /** 格式化文件大小 */
    public static function formatSize($bytes) {
        $bytes = (float)$bytes;
        if ($bytes < 1024) return $bytes . ' B';
        $units = ['KB', 'MB', 'GB', 'TB'];
        $i = -1;
        do { $bytes /= 1024; $i++; } while ($bytes >= 1024 && $i < count($units) - 1);
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
