<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 论坛模块 - 热门话题（标签云）
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class BbsTagsPage extends WapPage {
    public function run() {
        $db = $this->db;

        $rows = $db->getAll("SELECT t.tagname, COUNT(*) AS c FROM WapBbsTag t
 JOIN WapBbs b ON b.id=t.bbsid
 WHERE b.isshow=1
 GROUP BY t.tagname ORDER BY c DESC, t.tagname ASC LIMIT 50",[]);

        $this->strTitle = '热门话题';
        $this->showTop($this->strTitle);
        echo '<div class="subtitle">热门话题</div>';
        echo '<div class="content"><a href="list.php?classid=1">返回论坛</a></div>';

        if (!$rows) {
            echo '<div class="content">暂无话题，发帖时填写标签即可创建话题</div>';
            $this->showDown();
            return;
        }

        echo '<div class="content"><ul class="tagcloud">';
        foreach ($rows as $r) {
            $name = (string)$r['tagname'];
            echo '<li><a href="tag.php?name=' . rawurlencode($name) . '">#'
                . $this->ToHtm($name) . '#</a> <span class="info">' . (int)$r['c'] . '</span></li>';
        }
        echo '</ul></div>';
        $this->showDown();
    }
}

$page = new BbsTagsPage();
$page->run();
