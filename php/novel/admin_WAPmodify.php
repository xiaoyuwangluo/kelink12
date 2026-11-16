<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 小说模块 - 后台修改小说 (对应原 novel/admin_WAPmodify.aspx)
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class NovelModifyPage extends WapPage {
    public function run() {
        // 鉴权：后台管理员(kl_adminid)直接放行，否则走会员登录校验（未登录跳转）
        if (!$this->userSession->isAdminLogin()) {
            $this->checkLogin();
        }
        $db = $this->db;
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) $this->showError('参数错误');

        $novel = $db->getOne("SELECT * FROM WapNovel WHERE id=?", [$id]);
        if (!$novel) $this->showError('小说不存在');

        $this->showTop('修改小说');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->doModify($id);
        } else {
            $this->showForm($novel);
        }
        $this->showDown();
    }

    private function showForm($a) {
        echo '<div class="subtitle">修改小说</div>';
        echo '<form method="post" action="admin_WAPmodify.php?classid=' . $this->classid . '&id=' . $a['id'] . '">';
        echo '书名:<input type="text" name="title" value="' . $this->ToHtm($a['title']) . '"/><br/>';
        echo '作者:<input type="text" name="author" value="' . $this->ToHtm($a['author'] ?? '') . '"/><br/>';
        echo '大小:<input type="text" name="size" value="' . $this->ToHtm($a['size'] ?? '') . '"/><br/>';
        echo '封面:<input type="text" name="picurl" value="' . $this->ToHtm($a['picurl'] ?? '') . '"/><br/>';
        echo '简介:<textarea name="intro" rows="10">' . $this->ToHtm($a['intro'] ?? '') . '</textarea><br/>';
        echo '<input type="submit" value="保存修改"/></form>';
    }

    private function doModify($id) {
        $db = $this->db;
        $title = trim($_POST['title'] ?? '');
        $author = trim($_POST['author'] ?? '');
        $size = trim($_POST['size'] ?? '');
        $picurl = trim($_POST['picurl'] ?? '');
        $intro = $_POST['intro'] ?? '';

        if ($title === '') {
            echo '<div class="error">书名不能为空</div>';
            return;
        }
        $db->execute(
            "UPDATE WapNovel SET title=?,author=?,size=?,picurl=?,intro=? WHERE id=?",
            [$title, $author, $size, $picurl, $intro, $id]
        );
        echo '<div class="success">修改成功 <a href="view.php?classid=' . $this->classid . '&id=' . $id . '">查看</a></div>';
    }
}

$page = new NovelModifyPage();
$page->run();
