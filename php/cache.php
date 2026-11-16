<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 清空缓存 (对应原 Cache.aspx)
 * 需管理员会话 kl_adminid，删除 KL_CACHE_DIR 下所有 .cache 文件
 */
require_once __DIR__ . '/includes/bootstrap.php';

class CachePage extends WapPage {
    public function run() {
        // 校验管理员会话 kl_adminid，未登录跳转后台登录页
        if (!$this->userSession->isAdminLogin()) {
            header('Location: admin/login.php');
            exit;
        }

        // 删除缓存目录下所有 .cache 文件
        $count = 0;
        $files = glob(KL_CACHE_DIR . '/*.cache');
        if (is_array($files)) {
            foreach ($files as $file) {
                if (is_file($file) && @unlink($file)) $count++;
            }
        }

        $this->showTop('清空缓存');
        echo '<div class="success">缓存清理完成，共删除 ' . (int)$count . ' 个缓存文件。</div>';
        echo '<div class="content"><a href="wapindex.php">返回前台首页</a> | ';
        echo '<a href="admin/index.php">返回后台</a></div>';
        $this->showDown();
    }
}

$page = new CachePage();
$page->run();
