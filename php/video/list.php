<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 视频模块 - 列表 (对应原 video/list.aspx)
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class VideoListPage extends WapPage {
    public function run() {
        $db = $this->db;
        $page = max(1, (int)($_GET['page'] ?? 1));
        $pageSize = 20;
        $this->strTitle = $this->classVo->classname ?? '视频';
        $this->showTop($this->strTitle);
        echo '<div class="subtitle">' . $this->ToHtm($this->classVo->classname ?? '视频') . '</div>';

        $result = $db->getPage('WapVideo', 'id,title,picurl,addtime,hits',
            "siteid=? AND classid=? AND isshow=1", 'id DESC', $page, $pageSize,
            [$this->siteid, $this->classid]);
        echo '<div class="content"><ul class="videolist">';
        foreach ($result['list'] as $v) {
            echo '<li>';
            if (!empty($v['picurl'])) echo '<img src="' . $this->ToHtm($v['picurl']) . '" alt=""/>';
            echo '<a href="view.php?siteid=' . $this->siteid . '&classid=' . $this->classid . '&id=' . $v['id'] . '">' . $this->ToHtm($v['title']) . '</a>';
            echo ' <span class="hits">(' . (int)$v['hits'] . ')</span>';
            echo $this->adminLinks((int)$v['id']);
            echo '</li>';
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

    /** 后台管理员(kl_adminid)可见的修改/删除/显隐操作链接 */
    private function adminLinks($id) {
        if (!$this->userSession->isAdminLogin()) return '';
        $base = 'siteid=' . $this->siteid . '&classid=' . $this->classid . '&id=' . $id;
        return ' <a href="admin_WAPmodify.php?' . $base . '">修改</a>'
             . ' <a href="admin_WAPdel.php?' . $base . '">删除</a>'
             . ' <a href="admin_WAPchange.php?' . $base . '">显隐</a>';
    }
}
$page = new VideoListPage();
$page->run();
