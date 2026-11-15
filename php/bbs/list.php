<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 论坛模块 - 帖子列表 (对应原 bbs/list.aspx)
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class BbsListPage extends WapPage {
    public function run() {
        $db = $this->db;
        $classid = $this->classid;
        $page = max(1, (int)($_GET['page'] ?? 1));
        $pageSize = 20;

        $this->strTitle = $this->classVo->classname ?? '论坛';
        $this->showTop($this->strTitle);

        echo '<div class="subtitle">' . $this->ToHtm($this->classVo->classname ?? '论坛') . '</div>';

        // 发帖入口
        echo '<div class="postbar"><a href="admin_WAPadd.php?siteid=' . $this->siteid . '&classid=' . $classid . '">发表新帖</a></div>';

        // 置顶帖
        $tops = $db->getAll(
            "SELECT * FROM WapBbs WHERE siteid=? AND classid=? AND istop=1 AND isshow=1 ORDER BY id DESC LIMIT 10",
            [$this->siteid, $classid]
        );
        echo '<div class="toplist">';
        foreach ($tops as $t) {
            echo '<div><a href="view.php?siteid=' . $this->siteid . '&classid=' . $classid . '&id=' . $t['id'] . '">[顶]' . $this->ToHtm($t['title']) . '</a></div>';
        }
        echo '</div>';

        // 帖子列表 (含精华、锁定标记)
        $result = $db->getPage('WapBbs', 'id,title,addtime,hits,replynum,isgood,islock,istop',
            "siteid=? AND classid=? AND istop=0 AND isshow=1", 'id DESC', $page, $pageSize,
            [$this->siteid, $classid]);

        echo '<div class="content"><ul class="bbslist">';
        foreach ($result['list'] as $t) {
            $flag = '';
            if ($t['isgood']) $flag .= '[精]';
            if ($t['islock']) $flag .= '[锁]';
            echo '<li>' . $flag;
            echo '<a href="view.php?siteid=' . $this->siteid . '&classid=' . $classid . '&id=' . $t['id'] . '">';
            echo $this->ToHtm($t['title']) . '</a>';
            echo ' <span class="info">回' . (int)$t['replynum'] . '/阅' . (int)$t['hits'] . '</span>';
            echo ' <span class="time">' . date('m-d', strtotime($t['addtime'])) . '</span>';
            echo '</li>';
        }
        echo '</ul></div>';

        // 分页
        if ($result['pageCount'] > 1) {
            echo '<div class="pager">';
            if ($page > 1) echo '<a href="?siteid=' . $this->siteid . '&classid=' . $classid . '&page=' . ($page-1) . '">上一页</a> ';
            echo '第' . $page . '/' . $result['pageCount'] . '页 ';
            if ($page < $result['pageCount']) echo '<a href="?siteid=' . $this->siteid . '&classid=' . $classid . '&page=' . ($page+1) . '">下一页</a>';
            echo '</div>';
        }

        $this->showDown();
    }
}

$page = new BbsListPage();
$page->run();
