<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 新站模块 - 后台切换显示/隐藏
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class XinzhangChangePage extends WapPage {
    public function run() {
        $this->checkAdminLogin();
        $db = $this->db;
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) $this->showError('参数错误');

        $row = $db->getOne("SELECT isshow FROM WapXinzhang WHERE id=?", [$id]);
        if (!$row) $this->showError('记录不存在');

        $new = (int)$row['isshow'] === 1 ? 0 : 1;
        $db->execute("UPDATE WapXinzhang SET isshow=? WHERE id=?", [$new, $id]);

        $this->showTop('显示状态');
        echo '<div class="success">已设为' . ($new === 1 ? '显示' : '隐藏') . '</div>';
        echo '<a href="list.php?siteid=' . $this->siteid . '&classid=' . $this->classid . '">返回列表</a>';
        $this->showDown();
    }
}

$page = new XinzhangChangePage();
$page->run();
