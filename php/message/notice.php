<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 站内消息 - 通知中心
 * 支持 only=unread；type=回复/提及/点赞/关注/好友/审核 筛选；act=read&id=X 标记已读并跳转来源；act=readall 全部已读
 */
require_once __DIR__ . '/../includes/bootstrap.php';
require_once KL_ROOT . '/includes/Community.php';

class MessageNoticePage extends WapPage {
    /** 支持的筛选类型 */
    private $types = ['', 'reply', 'mention', 'like', 'follow', 'friend', 'audit'];

    public function run() {
        $db = $this->db;
        $this->checkLogin();
        $uid = (int)$this->userSession->getUserId();

        $act = trim((string)($_GET['act'] ?? ''));

        // 单条标记已读并跳转来源
        if ($act === 'read') {
            $nid = (int)($_GET['id'] ?? 0);
            $notice = $nid > 0
                ? $db->getOne("SELECT * FROM WapNotice WHERE id=? AND uid=?", [$nid, $uid])
                : null;
            if ($notice) {
                $db->execute("UPDATE WapNotice SET isread=1 WHERE id=? AND uid=?", [$nid, $uid]);
                header('Location: ' . $this->noticeUrl($notice));
                exit;
            }
            header('Location: notice.php');
            exit;
        }

        // 全部标记已读
        if ($act === 'readall') {
            $db->execute("UPDATE WapNotice SET isread=1 WHERE uid=?", [$uid]);
            header('Location: notice.php');
            exit;
        }

        $onlyUnread = (($_GET['only'] ?? '') === 'unread');
        $type = trim((string)($_GET['type'] ?? ''));
        if (!in_array($type, $this->types, true)) $type = '';
        $page = max(1, (int)($_GET['page'] ?? 1));

        $where = 'uid=?';
        $params = [$uid];
        if ($onlyUnread) {
            $where .= ' AND isread=0';
        }
        if ($type !== '') {
            $where .= ' AND ntype=?';
            $params[] = $type;
        }
        $result = $db->getPage('WapNotice', '*', $where, 'id DESC', $page, 20, $params);

        $unread = (int)$db->getScalar("SELECT COUNT(*) FROM WapNotice WHERE uid=? AND isread=0",[$uid]);

        $suffix = ($onlyUnread ? '&only=unread' : '') . ($type !== '' ? '&type=' . urlencode($type) : '');

        $this->strTitle = '通知中心';
        $this->showTop($this->strTitle);
        echo '<div class="subtitle">通知中心</div>';
        echo '<div class="menu">';
        echo '<a href="notice.php">全部通知</a> | ';
        echo '<a href="notice.php?only=unread">仅未读(' . $unread . ')</a> | ';
        echo '<a href="notice.php?act=readall">全部标为已读</a> | ';
        echo '<a href="index.php">返回消息</a>';
        echo '</div>';

        // 类型筛选
        echo '<div class="menu">筛选: ';
        $labels = ['' => '全部', 'reply' => '回复', 'mention' => '@提及', 'like' => '点赞',
            'follow' => '关注', 'friend' => '好友', 'audit' => '审核'];
        $links = [];
        foreach ($labels as $k => $label) {
            $url = 'notice.php'
                . ($onlyUnread ? '&only=unread' : '')
                . ($k !== '' ? '&type=' . urlencode($k) : '');
            $links[] = $k === $type
                ? '<strong>' . $label . '</strong>'
                : '<a href="' . $url . '">' . $label . '</a>';
        }
        echo implode(' | ', $links);
        echo '</div>';

        if (empty($result['list'])) {
            echo '<div class="content">暂无通知</div>';
        } else {
            // 批量取来源用户，避免 N+1
            $fromUids = [];
            foreach ($result['list'] as $n) {
                $fid = (int)$n['fromuid'];
                if ($fid > 0) $fromUids[$fid] = 1;
            }
            $nameMap = [];
            if ($fromUids) {
                $in = implode(',', array_keys($fromUids));
                foreach ($db->getAll("SELECT id,username,nickname FROM WapUser WHERE id IN ($in)") as $u) {
                    $nameMap[(int)$u['id']] = $u['nickname'] !== '' ? $u['nickname'] : $u['username'];
                }
            }

            echo '<div class="content"><ul class="list">';
            foreach ($result['list'] as $n) {
                $from = (int)$n['fromuid'] > 0
                    ? ($nameMap[(int)$n['fromuid']] ?? '已注销用户')
                    : '系统';
                $line = '[' . kl_notice_label_ext($n['ntype']) . '] ' . $from . ' ' . $n['content'];
                $time = '<span class="time">' . $this->ToHtm($n['addtime']) . '</span>';
                $target = $this->noticeUrl($n);
                if (!$n['isread']) {
                    $href = 'notice.php?act=read&id=' . (int)$n['id'];
                    echo '<li><strong><a href="' . $href . '">' . $this->ToHtm($line) . '</a></strong> ' . $time . '</li>';
                } elseif ($target !== 'notice.php') {
                    echo '<li><a href="' . $this->ToHtm($target) . '">' . $this->ToHtm($line) . '</a> ' . $time . '</li>';
                } else {
                    echo '<li>' . $this->ToHtm($line) . ' ' . $time . '</li>';
                }
            }
            echo '</ul></div>';
        }

        if ($result['pageCount'] > 1) {
            echo '<div class="pager">';
            if ($page > 1) echo '<a href="?' . $suffix . '&page=' . ($page - 1) . '">上一页</a> ';
            echo '第' . $page . '/' . $result['pageCount'] . '页 ';
            if ($page < $result['pageCount']) echo '<a href="?' . $suffix . '&page=' . ($page + 1) . '">下一页</a>';
            echo '</div>';
        }

        echo '<div><a href="index.php">返回消息中心</a></div>';
        $this->showDown();
    }

    /**
     * 通知跳转地址：帖子/文章详情、关注与好友对象空间、其余回私信会话
     */
    private function noticeUrl($n) {
        $ntype = (string)($n['ntype'] ?? '');
        $module = (string)($n['module'] ?? '');
        $cid = (int)($n['contentid'] ?? 0);
        $fid = (int)($n['fromuid'] ?? 0);

        if ($cid > 0 && $module === 'bbs') {
            return '../bbs/view.php?id=' . $cid;
        }
        if ($cid > 0 && $module === 'article') {
            return '../article/view.php?id=' . $cid;
        }
        if (($ntype === 'follow' || $ntype === 'friend') && $fid > 0) {
            return '../zone/index.php?uid=' . $fid;
        }
        if ($fid > 0) {
            return 'chat.php?uid=' . $fid;
        }
        return 'notice.php';
    }
}

$page = new MessageNoticePage();
$page->run();
