<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * gongqiu模块 - 后台修改供求 (仿 article/admin_WAPmodify.php)
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class GongqiuModifyPage extends WapPage {
    public function run() {
        $this->checkAdminLogin();
        $db = $this->db;
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) $this->showError('参数错误');

        $item = $db->getOne("SELECT * FROM WapGongqiu WHERE id=?", [$id]);
        if (!$item) $this->showError('记录不存在');

        $this->showTop('修改供求');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->doModify($id);
        } else {
            $this->showForm($item);
        }
        $this->showDown();
    }

    private function showForm($a) {
        echo '<div class="subtitle">修改供求</div>';
        echo '<form method="post" action="admin_WAPmodify.php?classid=' . $this->classid . '&id=' . $a['id'] . '">';
        echo '标题:<input type="text" name="title" value="' . $this->ToHtm($a['title']) . '"/><br/>';
        echo '内容详情:<textarea name="content" rows="10">' . $this->ToHtm($a['content'] ?? '') . '</textarea><br/>';
        echo '相关网址:<input type="text" name="downurl" value="' . $this->ToHtm($a['downurl'] ?? '') . '"/><br/>';
        echo '置顶:<input type="checkbox" name="istop" value="1" ' . ((int)($a['istop'] ?? 0) ? 'checked' : '') . '/><br/>';
        echo '<input type="submit" value="保存修改"/></form>';
    }

    private function doModify($id) {
        $db = $this->db;
        $title = trim($_POST['title'] ?? '');
        $content = $_POST['content'] ?? '';
        $downurl = trim($_POST['downurl'] ?? '');
        $istop = isset($_POST['istop']) ? 1 : 0;

        if ($title === '') {
            echo '<div class="error">标题不能为空</div>';
            return;
        }
        $db->execute(
            "UPDATE WapGongqiu SET title=?,content=?,downurl=?,istop=? WHERE id=?",
            [$title, $content, $downurl, $istop, $id]
        );
        echo '<div class="success">修改成功 <a href="view.php?classid=' . $this->classid . '&id=' . $id . '">查看</a></div>';
    }
}

$page = new GongqiuModifyPage();
$page->run();
