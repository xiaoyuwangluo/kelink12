<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * gongqiu模块 - 后台切换显隐 (isshow)
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class GongqiuChangePage extends WapPage {
    public function run() {
        $this->checkAdminLogin();
        $db = $this->db;
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) $this->showError('参数错误');

        $item = $db->getOne("SELECT title,isshow FROM WapGongqiu WHERE id=?", [$id]);
        if (!$item) $this->showError('记录不存在');

        $new = (int)$item['isshow'] === 1 ? 0 : 1;
        $db->execute("UPDATE WapGongqiu SET isshow=? WHERE id=?", [$new, $id]);

        $this->showTop('切换显隐');
        echo '<div class="success">「' . $this->ToHtm($item['title']) . '」已设为' . ($new === 1 ? '显示' : '隐藏') . '</div>';
        echo '<a href="list.php?siteid=' . $this->siteid . '&classid=' . $this->classid . '">返回列表</a>';
        $this->showDown();
    }
}

$page = new GongqiuChangePage();
$page->run();
