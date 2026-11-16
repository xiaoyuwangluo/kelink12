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

        $result = $db->getPage('WapNovel', 'id,title,author,addtime,hits,size',"classid=? AND isshow=1", 'id DESC', $page, $pageSize,[$this->classid]);

        echo '<div class="content"><ul class="novellist">';
        foreach ($result['list'] as $n) {
            echo '<li><a href="view.php?classid=' . $this->classid . '&id=' . $n['id'] . '">';
            echo $this->ToHtm($n['title']) . '</a> - ' . $this->ToHtm($n['author'] ?? '佚名');
            echo ' <span class="hits">(' . (int)$n['hits'] . ')</span>';
            echo $this->adminLinks((int)$n['id']);
            echo '</li>';
        }
        echo '</ul></div>';

        if ($result['pageCount'] > 1) {
            echo '<div class="pager">';
            if ($page > 1) echo '<a href="?classid=' . $this->classid . '&page=' . ($page-1) . '">上一页</a> ';
            echo '第' . $page . '/' . $result['pageCount'] . '页 ';
            if ($page < $result['pageCount']) echo '<a href="?classid=' . $this->classid . '&page=' . ($page+1) . '">下一页</a>';
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

$page = new NovelListPage();
$page->run();
