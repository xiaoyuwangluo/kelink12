<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 好友模块 - 我的好友列表 (对应原 friend/index.aspx)
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class FriendIndexPage extends WapPage {
    public function run() {
        $db = $this->db;
        $this->checkLogin();
        $uid = (int)$this->userSession->getUserId();
        $page = max(1, (int)($_GET['page'] ?? 1));

        $this->strTitle = '我的好友';
        $this->showTop($this->strTitle);
        echo '<div class="subtitle">我的好友</div>';

        $result = $db->getPage('WapFriend', 'id,friendid,addtime',
            'userid=? AND status=1', 'id DESC', $page, 20, [$uid]);

        if (empty($result['list'])) {
            echo '<div class="content">您还没有好友</div>';
        } else {
            echo '<div class="content"><ul class="list">';
            foreach ($result['list'] as $f) {
                $fu = $db->getOne("SELECT id,username,nickname FROM WapUser WHERE id=?", [$f['friendid']]);
                $name = $fu ? ($fu['nickname'] !== '' ? $fu['nickname'] . '(' . $fu['username'] . ')' : $fu['username']) : '已注销用户';
                echo '<li>' . $this->ToHtm($name);
                if ($fu) {
                    echo ' <a href="../zone/index.php?uid=' . (int)$fu['id'] . '">[空间]</a>';
                    echo ' <a href="../message/send.php?to=' . urlencode($fu['username']) . '">[发短信]</a>';
                    echo ' <a href="del.php?fid=' . (int)$f['friendid'] . '">[删除]</a>';
                }
                echo ' <span class="time">' . $this->ToHtm($f['addtime']) . '</span></li>';
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

        // 添加好友
        echo '<div class="content"><form method="get" action="add.php">';
        echo '添加好友(用户名):<input type="text" name="username"/> <input type="submit" value="添加"/>';
        echo '</form></div>';

        echo '<div><a href="../myfile.php">返回用户中心</a></div>';
        $this->showDown();
    }
}

$page = new FriendIndexPage();
$page->run();
