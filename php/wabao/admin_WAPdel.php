<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * wabao模块 - 后台删除内容 (对应原 wabao/admin_WAPdel.aspx)
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class WabaoDelPage extends WapPage {
    public function run() {
        $this->guardAuth();
        $db = $this->db;
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) $this->showError('参数错误');

        $row = $db->getOne("SELECT title FROM WapWabao WHERE id=?", [$id]);
        if (!$row) $this->showError('记录不存在');

        $this->showTop('删除确认');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $db->execute("DELETE FROM WapWabao WHERE id=?", [$id]);
            $db->execute("DELETE FROM WapReply WHERE module='wabao' AND contentid=?", [$id]);
            echo '<div class="success">删除成功</div>';
            echo '<a href="list.php?classid=' . $this->classid . '">返回列表</a>';
        } else {
            echo '<div class="subtitle">确认删除「' . $this->ToHtm($row['title']) . '」？</div>';
            echo '<form method="post" action="admin_WAPdel.php?classid=' . $this->classid . '&id=' . $id . '">';
            echo '<input type="submit" value="确认删除"/></form>';
            echo '<a href="list.php?classid=' . $this->classid . '">取消并返回列表</a>';
        }
        $this->showDown();
    }

    /** 鉴权：后台管理员(kl_adminid)或已登录会员可操作，未登录跳转登录页 */
    private function guardAuth() {
        if ($this->userSession->isAdminLogin()) return;
        $this->checkLogin();
    }
}

$page = new WabaoDelPage();
$page->run();
