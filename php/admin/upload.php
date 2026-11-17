<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 管理后台 - 文件管理器 (uploadfiles 目录可视化上传/列表/删除)
 */
require_once __DIR__ . '/../includes/bootstrap.php';
require_once KL_ROOT . '/includes/Upload.php';

class AdminUploadPage extends WapPage {

    /** 递归收集 uploadfiles 下全部文件 (返回相对路径) */
    private function scanFiles() {
        $list = [];
        if (!is_dir(KL_UPLOAD_DIR)) return $list;
        $it = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(KL_UPLOAD_DIR, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );
        foreach ($it as $f) {
            if ($f->isFile()) {
                $rel = str_replace('\\', '/', substr($f->getPathname(), strlen(KL_UPLOAD_DIR) + 1));
                $list[] = ['relpath' => $rel, 'size' => $f->getSize(), 'mtime' => $f->getMTime()];
            }
        }
        usort($list, function ($a, $b) { return strcmp($b['relpath'], $a['relpath']); });
        return $list;
    }

    /** 按扩展名归类 */
    private function fileType($rel) {
        $ext = strtolower(pathinfo($rel, PATHINFO_EXTENSION));
        $map = [
            'image' => ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg', 'ico'],
            'doc' => ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'pdf', 'txt', 'md', 'csv'],
            'archive' => ['zip', 'rar', '7z', 'gz', 'tar', 'bz2'],
        ];
        foreach ($map as $type => $exts) {
            if (in_array($ext, $exts, true)) return $type;
        }
        return 'other';
    }

    /** 删除二次确认页 */
    private function showDeleteConfirm($rel, $back) {
        $abs = Upload::absPath($rel);
        if ($abs === null || !is_file($abs)) $this->showError('文件不存在或路径非法', $back);
        $size = @filesize($abs);
        $sizeTxt = $size !== false ? Upload::formatSize($size) : '未知';

        $this->strTitle = '删除文件确认';
        $adminTitle = '删除文件确认';
        $activeNav = 'upload';
        require __DIR__ . '/inc/header.php';
        echo '<div class="subtitle">删除文件确认</div>';
        echo '<div><a href="' . $back . '">返回文件列表</a></div>';
        echo '<div class="content">文件：' . $this->ToHtm($rel) . '<br/>大小：' . $sizeTxt . '<br/>'
            . '删除后引用该文件的页面将无法显示，请谨慎操作。';
        echo '<div class="error">确认删除该文件？</div>';
        echo '<form method="post" action="upload.php">'
            . '<input type="hidden" name="action" value="delete"/>'
            . '<input type="hidden" name="file" value="' . $this->ToHtm($rel) . '"/>'
            . '<input type="submit" value="确认删除"/></form></div>';
        require __DIR__ . '/inc/footer.php';
        exit;
    }

    public function run() {
        $this->checkAdminLogin();
        $msg = '';
        $back = 'upload.php';

        $q = trim($_GET['q'] ?? '');
        $type = $_GET['type'] ?? 'all';
        if (!in_array($type, ['all', 'image', 'doc', 'archive', 'other'], true)) $type = 'all';
        $sort = $_GET['sort'] ?? 'new';
        if (!in_array($sort, ['new', 'size', 'name'], true)) $sort = 'new';

        // 删除二次确认页
        if (($_GET['act'] ?? '') === 'del') {
            $this->showDeleteConfirm((string)($_GET['file'] ?? ''), $back);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            if ($action === 'upload') {
                if (!$this->userSession->checkSubmitLimit()) {
                    $msg = '提交过于频繁，请稍后再试';
                } elseif (!isset($_FILES['userfile'])) {
                    $msg = '未选择文件';
                } else {
                    list($ok, $msg, $info) = Upload::save($_FILES['userfile']);
                    if ($ok) $msg = '上传成功: ' . $info['relpath'];
                }
            } elseif ($action === 'delete') {
                $rel = $_POST['file'] ?? '';
                $abs = Upload::absPath($rel); // 防路径穿越，仅限 uploadfiles 内
                if ($abs === null || !is_file($abs)) {
                    $msg = '非法文件路径';
                } elseif (@unlink($abs)) {
                    $msg = '已删除: ' . $rel;
                } else {
                    $msg = '删除失败';
                }
            } else {
                $msg = '非法操作';
            }
        }

        $this->strTitle = '文件管理';
        $adminTitle = '文件管理';
        $activeNav = 'upload';
        require __DIR__ . '/inc/header.php';
        echo '<div class="subtitle">文件管理</div><div><a href="index.php">返回后台首页</a></div>';
        if ($msg !== '') echo '<div class="' . (strpos($msg, '成功') !== false || strpos($msg, '已删除') !== false ? 'success' : 'error') . '">' . $this->ToHtm($msg) . '</div>';

        // 上传表单
        echo '<div class="content"><form method="post" action="' . $back . '" enctype="multipart/form-data">'
            . '<input type="hidden" name="action" value="upload"/>'
            . '选择文件: <input type="file" name="userfile"/>'
            . '<input type="submit" value="上传"/>'
            . '<div class="time">限制: 单文件最大 ' . Upload::formatSize(KL_UpMaxSize * 1024) . '，允许扩展: ' . $this->ToHtm(KL_UpAllowExt) . '</div>'
            . '</form></div>';

        // 文件列表：分类计数 + 搜索 + 排序
        $files = $this->scanFiles();
        $counts = ['all' => count($files), 'image' => 0, 'doc' => 0, 'archive' => 0, 'other' => 0];
        foreach ($files as $f) {
            $counts[$this->fileType($f['relpath'])]++;
        }
        $filtered = [];
        foreach ($files as $f) {
            if ($type !== 'all' && $this->fileType($f['relpath']) !== $type) continue;
            if ($q !== '' && stripos($f['relpath'], $q) === false) continue;
            $filtered[] = $f;
        }
        if ($sort === 'size') {
            usort($filtered, function ($a, $b) { return $b['size'] <=> $a['size']; });
        } elseif ($sort === 'name') {
            usort($filtered, function ($a, $b) { return strcasecmp($a['relpath'], $b['relpath']); });
        } else {
            usort($filtered, function ($a, $b) { return $b['mtime'] <=> $a['mtime']; });
        }
        $backUrl = 'upload.php?type=' . urlencode($type) . '&sort=' . urlencode($sort)
            . ($q !== '' ? '&q=' . urlencode($q) : '');

        // 筛选与排序
        echo '<div class="content">筛选：';
        $typeLabels = ['all' => '全部', 'image' => '图片', 'doc' => '文档', 'archive' => '压缩包', 'other' => '其他'];
        foreach ($typeLabels as $k => $label) {
            $txt = $label . '(' . $counts[$k] . ')';
            echo ($type === $k)
                ? '<strong>[' . $txt . ']</strong> '
                : '<a href="upload.php?type=' . $k . '&sort=' . urlencode($sort) . ($q !== '' ? '&q=' . urlencode($q) : '') . '">' . $txt . '</a> ';
        }
        echo '<br/>排序：';
        $sortLabels = ['new' => '最新', 'size' => '大小', 'name' => '名称'];
        foreach ($sortLabels as $k => $label) {
            echo ($sort === $k)
                ? '<strong>[' . $label . ']</strong> '
                : '<a href="upload.php?type=' . urlencode($type) . '&sort=' . $k . ($q !== '' ? '&q=' . urlencode($q) : '') . '">' . $label . '</a> ';
        }
        echo '</div>';

        // 搜索
        echo '<div class="content"><form method="get" action="upload.php">'
            . '搜索：<input type="text" name="q" value="' . $this->ToHtm($q) . '" placeholder="文件名"/>'
            . '<input type="hidden" name="type" value="' . $this->ToHtm($type) . '"/>'
            . '<input type="hidden" name="sort" value="' . $this->ToHtm($sort) . '"/>'
            . '<input type="submit" value="查询"/></form>';
        if ($q !== '') echo ' <a href="upload.php?type=' . urlencode($type) . '&sort=' . urlencode($sort) . '">清除搜索</a>';
        echo '</div>';

        $totalSize = 0;
        echo '<div class="content"><ul class="list">';
        foreach ($filtered as $f) {
            $totalSize += $f['size'];
            $url = '../' . Upload::url($f['relpath']);
            echo '<li><a href="' . $url . '" target="_blank">' . $this->ToHtm($f['relpath']) . '</a>'
                . ' [' . Upload::formatSize($f['size']) . '] ' . date('Y-m-d H:i', $f['mtime']) . '<br/>'
                . '<a href="' . $backUrl . '&act=del&file=' . urlencode($f['relpath']) . '">删除</a></li>';
        }
        if (empty($filtered)) echo '<li>没有符合条件的文件</li>';
        echo '</ul><div class="time">当前显示 ' . count($filtered) . ' / ' . count($files) . ' 个文件，合计 ' . Upload::formatSize($totalSize) . '</div></div>';

        require __DIR__ . '/inc/footer.php';
    }
}

$page = new AdminUploadPage();
$page->run();
