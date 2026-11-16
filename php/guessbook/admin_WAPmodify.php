<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * guessbook模块 - 后台修改留言 (仿 article/admin_WAPmodify.php)
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class GuessbookModifyPage extends WapPage {
    public function run() {
        $this->checkAdminLogin();
        $db = $this->db;
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) $this->showError('参数错误');

        $item = $db->getOne("SELECT * FROM WapGuestbook WHERE id=?", [$id]);
        if (!$item) $this->showError('记录不存在');

        $this->showTop('修改留言');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->doModify($id);
        } else {
            $this->showForm($item);
        }
        $this->showDown();
    }

    private function showForm($a) {
        echo '<div class="subtitle">修改留言</div>';
        echo '<form method="post" action="admin_WAPmodify.php?classid=' . $this->classid . '&id=' . $a['id'] . '">';
        echo '留言主题:<input type="text" name="title" value="' . $this->ToHtm($a['title']) . '"/><br/>';
        echo '留言内容:<textarea name="content" rows="10">' . $this->ToHtm($a['content'] ?? '') . '</textarea><br/>';
        echo '置顶:<input type="checkbox" name="istop" value="1" ' . ((int)($a['istop'] ?? 0) ? 'checked' : '') . '/><br/>';
        echo '<input type="submit" value="保存修改"/></form>';
    }

    private function doModify($id) {
        $db = $this->db;
        $title = trim($_POST['title'] ?? '');
        $content = $_POST['content'] ?? '';
        $istop = isset($_POST['istop']) ? 1 : 0;

        if ($title === '') {
            echo '<div class="error">留言主题不能为空</div>';
            return;
        }
        $db->execute(
            "UPDATE WapGuestbook SET title=?,content=?,istop=? WHERE id=?",
            [$title, $content, $istop, $id]
        );
        echo '<div class="success">修改成功 <a href="view.php?classid=' . $this->classid . '&id=' . $id . '">查看</a></div>';
    }
}

$page = new GuessbookModifyPage();
$page->run();
