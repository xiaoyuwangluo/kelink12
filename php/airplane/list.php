<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * airplane模块 - 列表
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class AirplaneListPage extends WapPage {
    public function run() {
        $db = $this->db;
        $page = max(1, (int)($_GET['page'] ?? 1));
        $this->strTitle = $this->classVo->classname ?? 'airplane';
        $this->showTop($this->strTitle);
        echo '<div class="subtitle">' . $this->ToHtm($this->classVo->classname ?? 'airplane') . '</div>';
        $result = $db->getPage('WapAirplane', 'id,title,addtime,hits',"classid=? AND isshow=1", 'id DESC', $page, 20,[$this->classid]);
        $isAdmin = $this->userSession->isAdminLogin();
        echo '<div class="content"><ul class="list">';
        foreach ($result['list'] as $row) {
            echo '<li><a href="view.php?classid=' . $this->classid . '&id=' . $row['id'] . '">';
            echo $this->ToHtm($row['title']) . '</a> <span class="hits">(' . (int)$row['hits'] . ')</span>';
            if ($isAdmin) {
                echo ' <a href="admin_WAPmodify.php?classid=' . $this->classid . '&id=' . $row['id'] . '">修改</a>';
                echo ' <a href="admin_WAPdel.php?classid=' . $this->classid . '&id=' . $row['id'] . '">删除</a>';
                echo ' <a href="admin_WAPchange.php?classid=' . $this->classid . '&id=' . $row['id'] . '">显隐</a>';
            }
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
}
$page = new AirplaneListPage();
$page->run();
