<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 文章模块 - 后台修改文章 (对应原 article/admin_WAPmodify.aspx)
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class ArticleModifyPage extends WapPage {
    public function run() {
        $this->checkLogin();
        $db = $this->db;
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) $this->showError('参数错误');

        $article = $db->getOne("SELECT * FROM WapArticle WHERE id=?", [$id]);
        if (!$article) $this->showError('文章不存在');

        $this->showTop('修改文章');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->doModify($id);
        } else {
            $this->showForm($article);
        }
        $this->showDown();
    }

    private function showForm($a) {
        echo '<div class="subtitle">修改文章</div>';
        echo '<form method="post" action="admin_WAPmodify.php?siteid=' . $this->siteid . '&classid=' . $this->classid . '&id=' . $a['id'] . '" enctype="multipart/form-data">';
        echo '标题:<input type="text" name="title" value="' . $this->ToHtm($a['title']) . '"/><br/>';
        echo '作者:<input type="text" name="author" value="' . $this->ToHtm($a['author'] ?? '') . '"/><br/>';
        echo '内容:<textarea name="content" rows="10">' . $this->ToHtm($a['content']) . '</textarea><br/>';
        echo '置顶:<input type="checkbox" name="istop" value="1" ' . ($a['istop'] ? 'checked' : '') . '/><br/>';
        echo '<input type="submit" value="保存修改"/></form>';
    }

    private function doModify($id) {
        $db = $this->db;
        $title = trim($_POST['title'] ?? '');
        $author = trim($_POST['author'] ?? '');
        $content = $_POST['content'] ?? '';
        $istop = isset($_POST['istop']) ? 1 : 0;

        if ($title === '') {
            echo '<div class="error">标题不能为空</div>';
            return;
        }
        $db->execute(
            "UPDATE WapArticle SET title=?,author=?,content=?,istop=? WHERE id=?",
            [$title, $author, $content, $istop, $id]
        );
        echo '<div class="success">修改成功 <a href="view.php?siteid=' . $this->siteid . '&classid=' . $this->classid . '&id=' . $id . '">查看</a></div>';
    }
}

$page = new ArticleModifyPage();
$page->run();
