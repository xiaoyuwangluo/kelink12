<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 日志模块 - 后台删除内容 (仿 article/admin_WAPdel.php，GET 确认 / POST 删除)
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class RizhiDelPage extends WapPage {
    public function run() {
        $this->checkAdminLogin();
        $db = $this->db;
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) $this->showError('参数错误');

        $row = $db->getOne("SELECT title FROM WapRizhi WHERE id=?", [$id]);
        if (!$row) $this->showError('记录不存在');

        $this->showTop('删除确认');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $db->execute("DELETE FROM WapRizhi WHERE id=?", [$id]);
            $db->execute("DELETE FROM WapReply WHERE module='rizhi' AND contentid=?", [$id]);
            echo '<div class="success">删除成功</div>';
            echo '<a href="list.php?classid=' . $this->classid . '">返回列表</a>';
        } else {
            echo '<div class="subtitle">确认删除「' . $this->ToHtm($row['title']) . '」吗？</div>';
            echo '<form method="post" action="admin_WAPdel.php?classid=' . $this->classid . '&id=' . $id . '">';
            echo '<input type="submit" value="确认删除"/></form>';
            echo '<a href="list.php?classid=' . $this->classid . '">取消返回</a>';
        }
        $this->showDown();
    }
}

$page = new RizhiDelPage();
$page->run();
