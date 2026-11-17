<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * guessbook模块 - 列表
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class GuessbookListPage extends WapPage {
    public function run() {
        $db = $this->db;
        $page = max(1, (int)($_GET['page'] ?? 1));
        $classname = $this->classVo->classname ?? '留言板';
        $this->strTitle = $classname;
        $this->showTop($this->strTitle);
        echo '<div class="ui-breadcrumb"><a href="' . $this->http_start . 'wapindex.php">首页</a> / ' . $this->ToHtm($classname) . '</div>';
        echo '<div class="subtitle">' . $this->ToHtm($classname) . '</div>';

        $result = $db->getPage('WapGuestbook', 'id,title,addtime,hits',"classid=? AND isshow=1", 'id DESC', $page, 20,[$this->classid]);
        $isAdmin = $this->userSession->isAdminLogin();
        if (empty($result['list'])) {
            echo '<div class="ui-card ui-mt-2"><div class="ui-empty"><div class="icon">💬</div>暂无留言</div></div>';
        } else {
            echo '<ul class="list ui-list ui-mt-2">';
            foreach ($result['list'] as $row) {
                echo '<li><a href="view.php?classid=' . $this->classid . '&id=' . $row['id'] . '">';
                echo $this->ToHtm($row['title']) . '</a>';
                echo '<span class="ui-flex ui-gap-2 ui-center"><span class="ui-tag">' . (int)$row['hits'] . '</span>'
                   . '<span class="ui-tag ui-tag-muted">' . date('m-d', strtotime($row['addtime'])) . '</span></span>';
                // 管理员登录(session kl_adminid)时追加后台管理操作
                if ($isAdmin) {
                    $qs = '?classid=' . $this->classid . '&id=' . $row['id'];
                    echo '<span class="ui-flex ui-gap-2 ui-mt-2"><a class="ui-btn ui-btn-sm" href="admin_WAPmodify.php' . $qs . '">修改</a>'
                       . '<a class="ui-btn ui-btn-sm" href="admin_WAPdel.php' . $qs . '">删除</a>'
                       . '<a class="ui-btn ui-btn-sm" href="admin_WAPchange.php' . $qs . '">显隐</a></span>';
                }
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
}
$page = new GuessbookListPage();
$page->run();
