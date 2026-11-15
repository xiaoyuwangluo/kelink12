<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * guessbook模块 - 列表
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class GuessbookListPage extends WapPage {
    public function run() {
        $db = $this->db;
        $page = max(1, (int)($_GET['page'] ?? 1));
        $this->strTitle = $this->classVo->classname ?? 'guessbook';
        $this->showTop($this->strTitle);
        echo '<div class="subtitle">' . $this->ToHtm($this->classVo->classname ?? 'guessbook') . '</div>';
        $result = $db->getPage('WapGuestbook', 'id,title,addtime,hits',
            "siteid=? AND classid=? AND isshow=1", 'id DESC', $page, 20,
            [$this->siteid, $this->classid]);
        echo '<div class="content"><ul class="list">';
        foreach ($result['list'] as $row) {
            echo '<li><a href="view.php?siteid=' . $this->siteid . '&classid=' . $this->classid . '&id=' . $row['id'] . '">';
            echo $this->ToHtm($row['title']) . '</a> <span class="hits">(' . (int)$row['hits'] . ')</span></li>';
        }
        echo '</ul></div>';
        if ($result['pageCount'] > 1) {
            echo '<div class="pager">';
            if ($page > 1) echo '<a href="?siteid=' . $this->siteid . '&classid=' . $this->classid . '&page=' . ($page-1) . '">上一页</a> ';
            echo '第' . $page . '/' . $result['pageCount'] . '页 ';
            if ($page < $result['pageCount']) echo '<a href="?siteid=' . $this->siteid . '&classid=' . $this->classid . '&page=' . ($page+1) . '">下一页</a>';
            echo '</div>';
        }
        $this->showDown();
    }
}
$page = new GuessbookListPage();
$page->run();
