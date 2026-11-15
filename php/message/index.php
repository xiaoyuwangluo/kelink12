<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 站内消息 - 收件箱/发件箱 (对应原 message/index.aspx)
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class MessageIndexPage extends WapPage {
    public function run() {
        $db = $this->db;
        $this->checkLogin();
        $uid = (int)$this->userSession->getUserId();
        $box = (($_GET['box'] ?? 'in') === 'out') ? 'out' : 'in';
        $page = max(1, (int)($_GET['page'] ?? 1));

        // 未读数
        $unread = (int)$db->getScalar("SELECT COUNT(*) FROM WapMessage WHERE toid=? AND isread=0", [$uid]);
        $noticeUnread = (int)$db->getScalar("SELECT COUNT(*) FROM WapNotice WHERE siteid=? AND uid=? AND isread=0",
            [$this->siteid, $uid]);

        $this->strTitle = $box === 'in' ? '收件箱' : '发件箱';
        $this->showTop($this->strTitle);
        echo '<div class="subtitle">' . $this->strTitle . '</div>';
        echo '<div class="menu">';
        echo '<a href="index.php?siteid=' . $this->siteid . '&box=in">收件箱' . ($unread > 0 ? '(' . $unread . '未读)' : '') . '</a> | ';
        echo '<a href="index.php?siteid=' . $this->siteid . '&box=out">发件箱</a> | ';
        echo '<a href="notice.php?siteid=' . $this->siteid . '">通知中心' . ($noticeUnread > 0 ? '(未读' . $noticeUnread . ')' : '') . '</a> | ';
        echo '<a href="sessions.php?siteid=' . $this->siteid . '">会话</a> | ';
        echo '<a href="send.php?siteid=' . $this->siteid . '">发送短信</a>';
        echo '</div>';

        $where = $box === 'in' ? 'toid=?' : 'fromid=?';
        $result = $db->getPage('WapMessage', 'id,fromname,toname,title,isread,addtime',
            $where, 'id DESC', $page, 20, [$uid]);

        if (empty($result['list'])) {
            echo '<div class="content">' . ($box === 'in' ? '收件箱为空' : '发件箱为空') . '</div>';
        } else {
            echo '<div class="content"><ul class="list">';
            foreach ($result['list'] as $m) {
                $who = $box === 'in' ? '来自:' . $m['fromname'] : '发给:' . $m['toname'];
                $new = ($box === 'in' && !$m['isread']) ? '<strong>[未读]</strong>' : '';
                echo '<li>' . $new . '<a href="view.php?siteid=' . $this->siteid . '&id=' . (int)$m['id'] . '">';
                echo $this->ToHtm($m['title'] !== '' ? $m['title'] : '(无标题)') . '</a>';
                echo ' <span class="info">' . $this->ToHtm($who) . '</span>';
                echo ' <span class="time">' . $this->ToHtm($m['addtime']) . '</span></li>';
            }
            echo '</ul></div>';
        }

        if ($result['pageCount'] > 1) {
            echo '<div class="pager">';
            if ($page > 1) echo '<a href="?siteid=' . $this->siteid . '&box=' . $box . '&page=' . ($page - 1) . '">上一页</a> ';
            echo '第' . $page . '/' . $result['pageCount'] . '页 ';
            if ($page < $result['pageCount']) echo '<a href="?siteid=' . $this->siteid . '&box=' . $box . '&page=' . ($page + 1) . '">下一页</a>';
            echo '</div>';
        }

        echo '<div><a href="../myfile.php?siteid=' . $this->siteid . '">返回用户中心</a></div>';
        $this->showDown();
    }
}

$page = new MessageIndexPage();
$page->run();
