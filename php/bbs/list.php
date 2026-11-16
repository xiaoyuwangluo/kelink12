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
        echo '<div class="postbar"><a href="admin_WAPadd.php?classid=' . $classid . '">发表新帖</a> '
            . '<a href="my.php?classid=' . $classid . '">我的帖子</a> '
            . '<a href="tags.php">热门话题</a></div>';

        // 置顶帖
        $tops = $db->getAll("SELECT * FROM WapBbs WHERE classid=? AND istop=1 AND isshow=1 ORDER BY id DESC LIMIT 10",[$classid]);
        echo '<div class="toplist">';
        foreach ($tops as $t) {
            echo '<div><a href="view.php?classid=' . $classid . '&id=' . $t['id'] . '">[顶]' . $this->ToHtm($t['title']) . '</a>';
            echo $this->adminLinks((int)$t['id']);
            echo '</div>';
        }
        echo '</div>';

        // 帖子列表 (含精华、锁定标记)
        $result = $db->getPage('WapBbs', 'id,title,addtime,hits,replynum,isgood,islock,istop',"classid=? AND istop=0 AND isshow=1", 'id DESC', $page, $pageSize,[$classid]);

        echo '<div class="content"><ul class="bbslist">';
        foreach ($result['list'] as $t) {
            $flag = '';
            if ($t['isgood']) $flag .= '[精]';
            if ($t['islock']) $flag .= '[锁]';
            echo '<li>' . $flag;
            echo '<a href="view.php?classid=' . $classid . '&id=' . $t['id'] . '">';
            echo $this->ToHtm($t['title']) . '</a>';
            echo ' <span class="info">回' . (int)$t['replynum'] . '/阅' . (int)$t['hits'] . '</span>';
            echo ' <span class="time">' . date('m-d', strtotime($t['addtime'])) . '</span>';
            echo $this->adminLinks((int)$t['id']);
            echo '</li>';
        }
        echo '</ul></div>';

        // 分页
        if ($result['pageCount'] > 1) {
            echo '<div class="pager">';
            if ($page > 1) echo '<a href="?classid=' . $classid . '&page=' . ($page-1) . '">上一页</a> ';
            echo '第' . $page . '/' . $result['pageCount'] . '页 ';
            if ($page < $result['pageCount']) echo '<a href="?classid=' . $classid . '&page=' . ($page+1) . '">下一页</a>';
            echo '</div>';
        }

        $this->showDown();
    }

    /** 后台管理员(kl_adminid)可见的修改/删除/显隐操作链接 */
    private function adminLinks($id) {
        if (!$this->userSession->isAdminLogin()) return '';
        $base = '?classid=' . $this->classid . '&id=' . $id;
        return ' <a href="admin_WAPmodify.php' . $base . '">修改</a>'
             . ' <a href="admin_WAPdel.php' . $base . '">删除</a>'
             . ' <a href="admin_WAPchange.php' . $base . '">显隐</a>';
    }
}

$page = new BbsListPage();
$page->run();
