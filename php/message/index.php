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
        $q = trim((string)($_GET['q'] ?? ''));
        $statusRaw = (string)($_GET['status'] ?? '');
        $status = in_array($statusRaw, ['unread', 'read'], true) ? $statusRaw : '';
        $statusInvalid = $statusRaw !== '' && $status === '';

        // 未读数
        $unread = (int)$db->getScalar("SELECT COUNT(*) FROM WapMessage WHERE toid=? AND isread=0", [$uid]);
        $noticeUnread = (int)$db->getScalar("SELECT COUNT(*) FROM WapNotice WHERE uid=? AND isread=0",[$uid]);

        $this->strTitle = $box === 'in' ? '收件箱' : '发件箱';
        $this->showTop($this->strTitle);
        echo '<div class="subtitle">' . $this->strTitle . '</div>';
        echo '<div class="menu">';
        echo '<a href="index.php?box=in">收件箱' . ($unread > 0 ? '(' . $unread . '未读)' : '') . '</a> | ';
        echo '<a href="index.php?box=out">发件箱</a> | ';
        echo '<a href="notice.php">通知中心' . ($noticeUnread > 0 ? '(未读' . $noticeUnread . ')' : '') . '</a> | ';
        echo '<a href="sessions.php">会话</a> | ';
        echo '<a href="send.php">发送短信</a>';
        echo '</div>';

        // 本箱总数与未读数
        $boxField = $box === 'in' ? 'toid' : 'fromid';
        $boxTotal = (int)$db->getScalar("SELECT COUNT(*) FROM WapMessage WHERE $boxField=?", [$uid]);
        $boxUnread = (int)$db->getScalar("SELECT COUNT(*) FROM WapMessage WHERE $boxField=? AND isread=0", [$uid]);
        $boxRead = $boxTotal - $boxUnread;

        // 筛选条件
        $where = "$boxField=?";
        $params = [$uid];
        if ($q !== '') {
            $where .= ' AND (title LIKE ? OR fromname LIKE ? OR toname LIKE ?)';
            $like = '%' . $q . '%';
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
        }
        if ($status !== '') {
            $where .= ' AND isread=' . ($status === 'unread' ? 0 : 1);
        }

        $result = $db->getPage('WapMessage', 'id,fromname,toname,title,isread,addtime',
            $where, 'id DESC', $page, 20, $params);

        if ($statusInvalid) {
            echo '<div class="content">未知的筛选状态，已按“全部”显示。</div>';
        }

        // 搜索与状态页签（页签保留关键词）
        echo '<div class="content">';
        echo '<form method="get" action="index.php">';
        echo '<input type="hidden" name="box" value="' . $box . '"/>';
        if ($status !== '') echo '<input type="hidden" name="status" value="' . $status . '"/>';
        echo '关键词:<input type="text" name="q" value="' . $this->ToHtm($q) . '"/> ';
        echo '<input type="submit" value="搜索"/>';
        echo '</form>';
        $tabKeep = 'box=' . $box . ($q !== '' ? '&q=' . urlencode($q) : '');
        $unreadName = $box === 'in' ? '未读' : '对方未读';
        $readName = $box === 'in' ? '已读' : '对方已读';
        echo '<a href="index.php?' . $tabKeep . '">全部(' . $boxTotal . ')</a> | ';
        echo '<a href="index.php?' . $tabKeep . '&status=unread">' . $unreadName . '(' . $boxUnread . ')</a> | ';
        echo '<a href="index.php?' . $tabKeep . '&status=read">' . $readName . '(' . $boxRead . ')</a>';
        if ($q !== '' || $status !== '') {
            echo ' | <a href="index.php?box=' . $box . '">清除筛选</a>';
        }
        echo '</div>';

        echo '<div class="content">共 ' . $boxTotal . ' 条' . ($box === 'in' ? '收件' : '发件')
            . '，' . ($box === 'in' ? '未读 ' : '对方未读 ') . $boxUnread . ' 条，当前条件 ' . (int)$result['total'] . ' 条，本页 '
            . count($result['list']) . ' 条</div>';

        if (empty($result['list'])) {
            if ($q !== '' || $status !== '') {
                echo '<div class="content">没有符合条件的消息</div>';
            } else {
                echo '<div class="content">' . ($box === 'in' ? '收件箱为空' : '发件箱为空') . '</div>';
            }
        } else {
            echo '<div class="content"><ul class="list">';
            foreach ($result['list'] as $m) {
                $who = $box === 'in' ? '来自:' . $m['fromname'] : '发给:' . $m['toname'];
                if ($box === 'in') {
                    $new = !$m['isread'] ? '<strong>[未读]</strong>' : '';
                } else {
                    $new = '<span class="info">[' . ($m['isread'] ? '对方已读' : '对方未读') . ']</span>';
                }
                echo '<li>' . $new . '<a href="view.php?id=' . (int)$m['id'] . '">';
                echo $this->ToHtm($m['title'] !== '' ? $m['title'] : '(无标题)') . '</a>';
                echo ' <span class="info">' . $this->ToHtm($who) . '</span>';
                echo ' <span class="time">' . $this->ToHtm($m['addtime']) . '</span></li>';
            }
            echo '</ul></div>';
        }

        if ($result['pageCount'] > 1) {
            $keep = 'box=' . $box . ($q !== '' ? '&q=' . urlencode($q) : '')
                . ($status !== '' ? '&status=' . $status : '');
            echo '<div class="pager">';
            if ($page > 1) echo '<a href="?' . $keep . '&page=' . ($page - 1) . '">上一页</a> ';
            echo '第' . $page . '/' . $result['pageCount'] . '页 ';
            if ($page < $result['pageCount']) echo '<a href="?' . $keep . '&page=' . ($page + 1) . '">下一页</a>';
            echo '</div>';
        }

        echo '<div><a href="../myfile.php">返回用户中心</a></div>';
        $this->showDown();
    }
}

$page = new MessageIndexPage();
$page->run();
