<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 论坛模块 - 标签话题聚合
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class BbsTagPage extends WapPage {
    public function run() {
        $db = $this->db;
        $name = trim((string)($_GET['name'] ?? ''));
        if ($name === '' || !preg_match('/^[\x{4e00}-\x{9fa5}A-Za-z0-9_]{1,12}$/u', $name)) {
            $this->showError('标签不合法', 'tags.php?siteid=' . $this->siteid);
        }
        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = 20;
        $offset = ($page - 1) * $limit;
        $sid = (int)$this->siteid;

        $total = (int)$db->getScalar(
            "SELECT COUNT(*) FROM WapBbsTag t JOIN WapBbs b ON b.id=t.bbsid AND b.siteid=t.siteid
             WHERE t.siteid=? AND t.tagname=? AND b.isshow=1",
            [$sid, $name]
        );
        $rows = $db->getAll(
            "SELECT b.* FROM WapBbsTag t JOIN WapBbs b ON b.id=t.bbsid AND b.siteid=t.siteid
             WHERE t.siteid=? AND t.tagname=? AND b.isshow=1
             ORDER BY b.istop DESC, b.id DESC LIMIT $limit OFFSET $offset",
            [$sid, $name]
        );

        $this->strTitle = '话题：' . $name;
        $this->showTop($this->strTitle);
        echo '<div class="subtitle">话题：#' . $this->ToHtm($name) . '#</div>';
        echo '<div class="content">共 ' . $total . ' 个帖子 <a href="tags.php?siteid=' . $sid . '">热门话题</a></div>';

        if (!$rows) {
            echo '<div class="content">该话题下暂无帖子</div>';
            $this->showDown();
            return;
        }

        echo '<div class="content"><ul class="bbslist">';
        foreach ($rows as $t) {
            echo '<li>';
            if ($t['isgood']) echo '[精]';
            if ($t['islock']) echo '[锁]';
            echo '<a href="view.php?siteid=' . $sid . '&classid=' . (int)$t['classid'] . '&id=' . (int)$t['id'] . '">'
                . $this->ToHtm($t['title']) . '</a>';
            echo ' <span class="info">回' . (int)$t['replynum'] . '/阅' . (int)$t['hits'] . '</span>';
            echo ' <span class="time">' . date('m-d', strtotime($t['addtime'])) . '</span>';
            echo '</li>';
        }
        echo '</ul></div>';

        $pages = (int)ceil($total / $limit);
        if ($pages > 1) {
            echo '<div class="pager">';
            if ($page > 1) echo '<a href="?siteid=' . $sid . '&name=' . rawurlencode($name) . '&page=' . ($page - 1) . '">上一页</a> ';
            echo '第' . $page . '/' . $pages . '页 ';
            if ($page < $pages) echo '<a href="?siteid=' . $sid . '&name=' . rawurlencode($name) . '&page=' . ($page + 1) . '">下一页</a>';
            echo '</div>';
        }
        $this->showDown();
    }
}

$page = new BbsTagPage();
$page->run();
