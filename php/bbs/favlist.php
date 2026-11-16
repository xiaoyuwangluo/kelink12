<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 论坛模块 - 我的收藏列表 (对应原 bbs/favlist.aspx)
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class BbsFavListPage extends WapPage {
    public function run() {
        $db = $this->db;
        $this->checkLogin();
        $uid = (int)$this->userSession->getUserId();
        $page = max(1, (int)($_GET['page'] ?? 1));

        $this->strTitle = '我的收藏';
        $this->showTop($this->strTitle);
        echo '<div class="subtitle">我的收藏</div>';

        $result = $db->getPage('WapFav', 'id,module,contentid,title,addtime',
            'userid=?', 'id DESC', $page, 20, [$uid]);

        if (empty($result['list'])) {
            echo '<div class="content">暂无收藏内容</div>';
        } else {
            echo '<div class="content"><ul class="list">';
            foreach ($result['list'] as $row) {
                $module = $row['module'] === 'bbs' ? 'view.php' : '../' . $row['module'] . '/view.php';
                echo '<li><a href="' . $module . '?id=' . (int)$row['contentid'] . '">';
                echo '[' . $this->ToHtm($row['module']) . ']' . $this->ToHtm($row['title']) . '</a>';
                echo ' <a href="fav.php?module=' . $this->ToHtm($row['module']) . '&id=' . (int)$row['contentid'] . '">[取消]</a>';
                echo ' <span class="time">' . $this->ToHtm($row['addtime']) . '</span></li>';
            }
            echo '</ul></div>';
        }

        if ($result['pageCount'] > 1) {
            echo '<div class="pager">';
            if ($page > 1) echo '<a href="?page=' . ($page - 1) . '">上一页</a> ';
            echo '第' . $page . '/' . $result['pageCount'] . '页 ';
            if ($page < $result['pageCount']) echo '<a href="?page=' . ($page + 1) . '">下一页</a>';
            echo '</div>';
        }

        echo '<div><a href="../myfile.php">返回用户中心</a></div>';
        $this->showDown();
    }
}

$page = new BbsFavListPage();
$page->run();
