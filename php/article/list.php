<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 文章模块 - 列表页 (对应原 article/list.aspx)
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class ArticleListPage extends WapPage {
    public function run() {
        $db = $this->db;
        $classid = $this->classid;
        $page = max(1, (int)($_GET['page'] ?? 1));
        $pageSize = 20;

        // 栏目配置
        $classVo = $this->classVo;
        $module = $classVo->module ?? 'article';
        $table = 'WapArticle';

        $this->strTitle = $classVo->classname ?? '文章列表';
        $this->showTop($this->strTitle);

        echo '<div class="subtitle">' . $this->ToHtm($classVo->classname ?? '文章列表') . '</div>';
        echo '<div class="content">';

        // 栏目置顶/公告
        $tops = $db->getAll("SELECT * FROM $table WHERE classid=? AND istop=1 AND isshow=1 ORDER BY id DESC LIMIT 10",[$classid]);
        foreach ($tops as $row) {
            echo '<div class="topitem">';
            echo '<a href="view.php?classid=' . $classid . '&id=' . $row['id'] . '">';
            echo '[顶]' . $this->ToHtm($row['title']);
            echo '</a></div>';
        }

        // 正常列表
        $where = "classid=? AND istop=0 AND isshow=1";
        $result = $db->getPage($table, 'id,title,addtime,hits', $where, 'id DESC', $page, $pageSize, [$classid]);

        echo '<ul class="list">';
        foreach ($result['list'] as $row) {
            echo '<li>';
            echo '<a href="view.php?classid=' . $classid . '&id=' . $row['id'] . '">';
            echo $this->ToHtm($row['title']);
            echo '</a>';
            echo ' <span class="time">' . date('m-d', strtotime($row['addtime'])) . '</span>';
            echo ' <span class="hits">(' . (int)$row['hits'] . ')</span>';
            echo '</li>';
        }
        echo '</ul>';

        // 分页
        $this->showPager($result['pageCount'], $page);

        echo '</div>';
        $this->showDown();
    }

    private function showPager($pageCount, $curPage) {
        if ($pageCount <= 1) return;
        echo '<div class="pager">';
        if ($curPage > 1) {
            echo '<a href="?classid=' . $this->classid . '&page=' . ($curPage - 1) . '">上一页</a> ';
        }
        echo '第' . $curPage . '/' . $pageCount . '页 ';
        if ($curPage < $pageCount) {
            echo '<a href="?classid=' . $this->classid . '&page=' . ($curPage + 1) . '">下一页</a>';
        }
        echo '</div>';
    }
}

$page = new ArticleListPage();
$page->run();
