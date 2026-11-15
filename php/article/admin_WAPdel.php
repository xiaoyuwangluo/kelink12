<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 文章模块 - 后台删除文章 (对应原 article/admin_WAPdel.aspx)
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class ArticleDelPage extends WapPage {
    public function run() {
        $this->checkLogin();
        $db = $this->db;
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) $this->showError('参数错误');

        // 权限检查: 本人或管理员
        $article = $db->getOne("SELECT userid FROM WapArticle WHERE id=?", [$id]);
        if (!$article) $this->showError('文章不存在');

        $uid = $this->userSession->getUserId();
        $isAdmin = false; // 简化，实际查管理员表
        if ($article['userid'] != $uid && !$isAdmin) {
            $this->showError('无权限删除');
        }

        $db->execute("DELETE FROM WapArticle WHERE id=?", [$id]);
        $db->execute("DELETE FROM WapReply WHERE module='article' AND contentid=?", [$id]);
        echo '<div class="success">删除成功</div>';
        echo '<a href="list.php?siteid=' . $this->siteid . '&classid=' . $this->classid . '">返回列表</a>';
    }
}

$page = new ArticleDelPage();
$page->run();
