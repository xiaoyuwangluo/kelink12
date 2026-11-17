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
        $q = trim((string)($_GET['q'] ?? ''));

        $this->strTitle = '我的好友';
        $this->showTop($this->strTitle);
        echo '<div class="subtitle">我的好友</div>';

        $total = (int)$db->getScalar("SELECT COUNT(*) FROM WapFriend WHERE userid=? AND status=1", [$uid]);

        // 好友列表联表会员资料，支持用户名/昵称搜索
        $where = 'f.userid=? AND f.status=1';
        $params = [$uid];
        if ($q !== '') {
            $where .= ' AND (u.username LIKE ? OR u.nickname LIKE ?)';
            $like = '%' . $q . '%';
            $params[] = $like;
            $params[] = $like;
        }
        $matched = (int)$db->getScalar("SELECT COUNT(*) FROM WapFriend f LEFT JOIN WapUser u ON u.id=f.friendid WHERE $where", $params);
        $pageCount = (int)ceil($matched / 20);
        if ($pageCount > 0 && $page > $pageCount) $page = $pageCount;
        $offset = ($page - 1) * 20;

        // 搜索
        echo '<div class="content">';
        echo '<form method="get" action="index.php">';
        echo '搜索好友:<input type="text" name="q" value="' . $this->ToHtm($q) . '"/> ';
        echo '<input type="submit" value="搜索"/>';
        if ($q !== '') echo ' <a href="index.php">清除搜索</a>';
        echo '</form>';
        echo '共 ' . $total . ' 位好友';
        if ($q !== '') echo '，当前条件 ' . $matched . ' 位';
        echo '，本页 ' . ($matched > 0 ? min(20, $matched - $offset) : 0) . ' 位';
        echo '</div>';

        if ($matched <= 0) {
            if ($q !== '') {
                echo '<div class="content">没有找到匹配的好友</div>';
            } else {
                echo '<div class="content">您还没有好友</div>';
            }
        } else {
            $list = $db->getAll("SELECT f.id,f.friendid,f.addtime,u.username,u.nickname FROM WapFriend f LEFT JOIN WapUser u ON u.id=f.friendid WHERE $where ORDER BY f.id DESC LIMIT 20 OFFSET $offset", $params);
            echo '<div class="content"><ul class="list">';
            foreach ($list as $f) {
                $hasUser = isset($f['username']) && $f['username'] !== null && $f['username'] !== '';
                $nick = (string)($f['nickname'] ?? '');
                $name = $hasUser
                    ? ($nick !== '' ? $nick . '(' . $f['username'] . ')' : $f['username'])
                    : '已注销用户';
                echo '<li>' . $this->ToHtm($name);
                if ($hasUser) {
                    echo ' <a href="../zone/index.php?uid=' . (int)$f['friendid'] . '">[空间]</a>';
                    echo ' <a href="../message/send.php?to=' . urlencode($f['username']) . '">[发短信]</a>';
                    echo ' <a href="del.php?fid=' . (int)$f['friendid'] . '">[删除]</a>';
                }
                echo ' <span class="time">' . $this->ToHtm($f['addtime']) . '</span></li>';
            }
            echo '</ul></div>';
        }

        if ($pageCount > 1) {
            $keep = $q !== '' ? 'q=' . urlencode($q) . '&' : '';
            echo '<div class="pager">';
            if ($page > 1) echo '<a href="?' . $keep . 'page=' . ($page - 1) . '">上一页</a> ';
            echo '第' . $page . '/' . $pageCount . '页 ';
            if ($page < $pageCount) echo '<a href="?' . $keep . 'page=' . ($page + 1) . '">下一页</a>';
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
