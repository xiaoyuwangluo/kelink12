<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * picture模块 - 列表
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class PictureListPage extends WapPage {
    public function run() {
        $db = $this->db;
        $page = max(1, (int)($_GET['page'] ?? 1));
        $this->strTitle = $this->classVo->classname ?? 'picture';
        $this->showTop($this->strTitle);
        echo '<div class="subtitle">' . $this->ToHtm($this->classVo->classname ?? 'picture') . '</div>';
        $result = $db->getPage('WapPicture', 'id,title,addtime,hits',
            "siteid=? AND classid=? AND isshow=1", 'id DESC', $page, 20,
            [$this->siteid, $this->classid]);
        echo '<div class="content"><ul class="list">';
        foreach ($result['list'] as $row) {
            echo '<li><a href="view.php?siteid=' . $this->siteid . '&classid=' . $this->classid . '&id=' . $row['id'] . '">';
            echo $this->ToHtm($row['title']) . '</a> <span class="hits">(' . (int)$row['hits'] . ')</span>';
            echo $this->adminLinks((int)$row['id']);
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
$page = new PictureListPage();
$page->run();
