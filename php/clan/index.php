<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 家族模块 - 家族列表
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class ClanIndexPage extends WapPage {
    public function run() {
        $db = $this->db;
        $this->strTitle = $this->classVo->classname ?? '家族';
        $this->showTop($this->strTitle);

        echo '<div class="subtitle">' . $this->ToHtm($this->strTitle) . '</div>';
        echo '<div class="postbar"><a href="create.php?classid=' . $this->classid . '">创建家族</a></div>';

        $rows = $db->getAll("SELECT id,name,intro,members,point,addtime FROM WapClan ORDER BY id DESC LIMIT 100",[]);
        echo '<div class="content"><ul class="list">';
        if (!$rows) {
            echo '<li>暂无家族，快来创建第一个吧</li>';
        }
        foreach ($rows as $r) {
            echo '<li><a href="view.php?classid=' . $this->classid . '&id=' . (int)$r['id'] . '">'
                . $this->ToHtm($r['name']) . '</a>'
                . ' <span class="info">成员' . (int)$r['members'] . '/积分' . (int)$r['point'] . '</span></li>';
        }
        echo '</ul></div>';

        echo '<div class="actionbar"><a href="../wapindex.php">返回首页</a></div>';
        $this->showDown();
    }
}

$page = new ClanIndexPage();
$page->run();
