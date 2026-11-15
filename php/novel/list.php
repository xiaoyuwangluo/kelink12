<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 小说模块 - 书单列表 (对应原 Novel/list.aspx)
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class NovelListPage extends WapPage {
    public function run() {
        $db = $this->db;
        $page = max(1, (int)($_GET['page'] ?? 1));
        $pageSize = 20;

        $this->strTitle = $this->classVo->classname ?? '小说';
        $this->showTop($this->strTitle);
        echo '<div class="subtitle">' . $this->ToHtm($this->classVo->classname ?? '小说') . '</div>';

        $result = $db->getPage('WapNovel', 'id,title,author,addtime,hits,size',
            "siteid=? AND classid=? AND isshow=1", 'id DESC', $page, $pageSize,
            [$this->siteid, $this->classid]);

        echo '<div class="content"><ul class="novellist">';
        foreach ($result['list'] as $n) {
            echo '<li><a href="view.php?siteid=' . $this->siteid . '&classid=' . $this->classid . '&id=' . $n['id'] . '">';
            echo $this->ToHtm($n['title']) . '</a> - ' . $this->ToHtm($n['author'] ?? '佚名');
            echo ' <span class="hits">(' . (int)$n['hits'] . ')</span></li>';
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

$page = new NovelListPage();
$page->run();
