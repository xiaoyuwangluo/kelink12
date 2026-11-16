<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * guessbook模块 - 后台删除留言 (仿 article/admin_WAPdel.php，GET确认/POST删除)
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class GuessbookDelPage extends WapPage {
    public function run() {
        $this->checkAdminLogin();
        $db = $this->db;
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) $this->showError('参数错误');

        $item = $db->getOne("SELECT title FROM WapGuestbook WHERE id=?", [$id]);
        if (!$item) $this->showError('记录不存在');

        $this->showTop('删除留言');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $db->execute("DELETE FROM WapGuestbook WHERE id=?", [$id]);
            $db->execute("DELETE FROM WapReply WHERE module='guessbook' AND contentid=?", [$id]);
            echo '<div class="success">删除成功</div>';
            echo '<a href="list.php?classid=' . $this->classid . '">返回列表</a>';
        } else {
            echo '<div class="subtitle">删除留言</div>';
            echo '<div class="content">确定要删除「' . $this->ToHtm($item['title']) . '」吗？该操作不可恢复。</div>';
            echo '<form method="post" action="admin_WAPdel.php?classid=' . $this->classid . '&id=' . $id . '">';
            echo '<input type="submit" value="确认删除"/></form>';
            echo '<a href="list.php?classid=' . $this->classid . '">取消返回列表</a>';
        }
        $this->showDown();
    }
}

$page = new GuessbookDelPage();
$page->run();
