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
        echo '<div class="ui-breadcrumb"><a href="' . $this->http_start . 'wapindex.php">首页</a> / ' . $this->ToHtm($this->classVo->classname ?? 'picture') . '</div>';
        echo '<div class="subtitle">' . $this->ToHtm($this->classVo->classname ?? 'picture') . '</div>';
        $result = $db->getPage('WapPicture', 'id,title,addtime,hits',"classid=? AND isshow=1", 'id DESC', $page, 20,[$this->classid]);
        if (empty($result['list'])) {
            echo '<div class="ui-card ui-mt-2"><div class="ui-empty"><div class="icon">🖼️</div>该栏目暂无图片</div></div>';
        } else {
            echo '<ul class="list ui-list ui-mt-2">';
            foreach ($result['list'] as $row) {
                echo '<li><a href="view.php?classid=' . $this->classid . '&id=' . $row['id'] . '">';
                echo $this->ToHtm($row['title']) . '</a> <span class="ui-tag">' . (int)$row['hits'] . '</span>';
                echo $this->adminLinks((int)$row['id']);
                echo '</li>';
            }
            echo '</ul>';
        }
        if ($result['pageCount'] > 1) {
            echo '<div class="ui-pager">';
            if ($page > 1) echo '<a href="?classid=' . $this->classid . '&page=' . ($page-1) . '">上一页</a>';
            echo '<span class="current">' . $page . ' / ' . (int)$result['pageCount'] . '</span>';
            if ($page < $result['pageCount']) echo '<a href="?classid=' . $this->classid . '&page=' . ($page+1) . '">下一页</a>';
            echo '</div>';
        }
        $this->showDown();
    }

    /** 后台管理员(kl_adminid)可见的修改/删除/显隐操作链接 */
    private function adminLinks($id) {
        if (!$this->userSession->isAdminLogin()) return '';
        $base = '?classid=' . $this->classid . '&id=' . $id;
        return ' <span class="ui-flex ui-gap-2"><a class="ui-btn ui-btn-sm" href="admin_WAPmodify.php' . $base . '">修改</a>'
             . '<a class="ui-btn ui-btn-sm" href="admin_WAPdel.php' . $base . '">删除</a>'
             . '<a class="ui-btn ui-btn-sm" href="admin_WAPchange.php' . $base . '">显隐</a></span>';
    }
}
$page = new PictureListPage();
$page->run();
