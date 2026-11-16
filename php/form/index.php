<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 表单模块 - 表单列表
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class FormIndexPage extends WapPage {
    public function run() {
        $db = $this->db;
        $this->strTitle = $this->classVo->classname ?? '表单';
        $this->showTop($this->strTitle);

        echo '<div class="subtitle">' . $this->ToHtm($this->strTitle) . '</div>';

        $rows = $db->getAll("SELECT id,name,addtime FROM WapForm ORDER BY id DESC LIMIT 100",[]);
        echo '<div class="content"><ul class="list">';
        if (!$rows) echo '<li>暂无表单</li>';
        $isAdmin = $this->userSession->isAdminLogin();
        foreach ($rows as $r) {
            $fid = (int)$r['id'];
            echo '<li><a href="view.php?classid=' . $this->classid . '&id=' . $fid . '">'
                . $this->ToHtm($r['name']) . '</a>';
            if ($isAdmin) {
                echo ' <a href="data.php?classid=' . $this->classid . '&id=' . $fid . '">[查看提交]</a>';
            }
            echo '</li>';
        }
        echo '</ul></div>';

        echo '<div class="actionbar"><a href="../wapindex.php">返回首页</a></div>';
        $this->showDown();
    }
}

$page = new FormIndexPage();
$page->run();
