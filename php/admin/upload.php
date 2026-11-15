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

    public function run() {
        $this->checkAdminLogin();
        $msg = '';
        $back = 'upload.php?siteid=' . $this->siteid;

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
        $this->showTop($this->strTitle);
        echo '<div class="subtitle">文件管理</div><div><a href="index.php">返回后台首页</a></div>';
        if ($msg !== '') echo '<div class="' . (strpos($msg, '成功') !== false || strpos($msg, '已删除') !== false ? 'success' : 'error') . '">' . $this->ToHtm($msg) . '</div>';

        // 上传表单
        echo '<div class="content"><form method="post" action="' . $back . '" enctype="multipart/form-data">'
            . '<input type="hidden" name="action" value="upload"/>'
            . '选择文件: <input type="file" name="userfile"/>'
            . '<input type="submit" value="上传"/>'
            . '<div class="time">限制: 单文件最大 ' . Upload::formatSize(KL_UpMaxSize * 1024) . '，允许扩展: ' . $this->ToHtm(KL_UpAllowExt) . '</div>'
            . '</form></div>';

        // 文件列表
        $files = $this->scanFiles();
        $totalSize = 0;
        echo '<div class="content"><ul class="list">';
        foreach ($files as $f) {
            $totalSize += $f['size'];
            $url = '../' . Upload::url($f['relpath']);
            echo '<li><a href="' . $url . '" target="_blank">' . $this->ToHtm($f['relpath']) . '</a>'
                . ' [' . Upload::formatSize($f['size']) . '] ' . date('Y-m-d H:i', $f['mtime']) . '<br/>'
                . '<form method="post" action="' . $back . '" style="display:inline" onsubmit="return confirm(\'确认删除该文件?\')">'
                . '<input type="hidden" name="action" value="delete"/>'
                . '<input type="hidden" name="file" value="' . $this->ToHtm($f['relpath']) . '"/>'
                . '<input type="submit" value="删除"/></form></li>';
        }
        if (empty($files)) echo '<li>uploadfiles 目录暂无文件</li>';
        echo '</ul><div class="time">共 ' . count($files) . ' 个文件，合计 ' . Upload::formatSize($totalSize) . '</div></div>';

        $this->showDown();
    }
}

$page = new AdminUploadPage();
$page->run();
