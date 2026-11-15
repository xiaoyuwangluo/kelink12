<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 游戏模块 - 游戏列表
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class GamesIndexPage extends WapPage {
    public function run() {
        $db = $this->db;
        $this->strTitle = $this->classVo->classname ?? '游戏';
        $this->showTop($this->strTitle);

        echo '<div class="subtitle">' . $this->ToHtm($this->strTitle) . '</div>';

        $rows = $db->getAll(
            "SELECT id,name,picurl,url,intro,clicks FROM WapGames WHERE siteid=? ORDER BY id DESC LIMIT 100",
            [$this->siteid]
        );
        echo '<div class="content"><ul class="list">';
        if (!$rows) echo '<li>暂无游戏</li>';
        foreach ($rows as $r) {
            $gid = (int)$r['id'];
            echo '<li>';
            if (!empty($r['picurl'])) {
                echo '<img src="' . $this->ToHtm($r['picurl']) . '" alt="" width="40" />';
            }
            if (!empty($r['url'])) {
                echo '<a href="' . $this->ToHtm($r['url']) . '" target="_blank">' . $this->ToHtm($r['name']) . '</a>';
            } else {
                echo $this->ToHtm($r['name']);
            }
            echo ' <span class="info">点击' . (int)$r['clicks'] . '</span>';
            echo ' <a href="rank.php?siteid=' . $this->siteid . '&classid=' . $this->classid . '&gameid=' . $gid . '">[排行]</a>';
            if (!empty($r['intro'])) {
                echo '<div class="info">' . $this->ToHtm($r['intro']) . '</div>';
            }
            echo '</li>';
        }
        echo '</ul></div>';

        echo '<div class="actionbar"><a href="../wapindex.php?siteid=' . $this->siteid . '">返回首页</a></div>';
        $this->showDown();
    }
}

$page = new GamesIndexPage();
$page->run();
