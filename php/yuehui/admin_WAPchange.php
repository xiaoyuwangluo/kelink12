<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * yuehui模块 - 后台切换显示/隐藏 (对应原 yuehui/admin_WAPchange.aspx)
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class YuehuiChangePage extends WapPage {
    public function run() {
        $this->guardAuth();
        $db = $this->db;
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) $this->showError('参数错误');

        $row = $db->getOne("SELECT title,isshow FROM WapYuehui WHERE id=?", [$id]);
        if (!$row) $this->showError('记录不存在');

        $new = $row['isshow'] ? 0 : 1;
        $db->execute("UPDATE WapYuehui SET isshow=? WHERE id=?", [$new, $id]);

        $this->showTop('显隐切换');
        echo '<div class="success">「' . $this->ToHtm($row['title']) . '」已切换为' . ($new ? '显示' : '隐藏') . '</div>';
        echo '<a href="list.php?siteid=' . $this->siteid . '&classid=' . $this->classid . '">返回列表</a>';
        $this->showDown();
    }

    /** 鉴权：后台管理员(kl_adminid)或已登录会员可操作，未登录跳转登录页 */
    private function guardAuth() {
        if ($this->userSession->isAdminLogin()) return;
        $this->checkLogin();
    }
}

$page = new YuehuiChangePage();
$page->run();
