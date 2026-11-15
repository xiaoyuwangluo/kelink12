<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 站内消息 - 通知中心
 * 支持 only=unread；act=read&id=X 标记已读；act=readall 全部已读
 */
require_once __DIR__ . '/../includes/bootstrap.php';
require_once KL_ROOT . '/includes/Community.php';

class MessageNoticePage extends WapPage {
    public function run() {
        $db = $this->db;
        $this->checkLogin();
        $uid = (int)$this->userSession->getUserId();

        $act = trim((string)($_GET['act'] ?? ''));

        // 单条标记已读
        if ($act === 'read') {
            $nid = (int)($_GET['id'] ?? 0);
            $notice = $nid > 0
                ? $db->getOne("SELECT * FROM WapNotice WHERE id=? AND uid=?", [$nid, $uid])
                : null;
            if ($notice) {
                $db->execute("UPDATE WapNotice SET isread=1 WHERE id=? AND uid=?", [$nid, $uid]);
                if ($notice['module'] === 'bbs' && (int)$notice['contentid'] > 0) {
                    header('Location: ../bbs/view.php?siteid=' . $this->siteid . '&id=' . (int)$notice['contentid']);
                    exit;
                }
            }
            header('Location: notice.php?siteid=' . $this->siteid);
            exit;
        }

        // 全部标记已读
        if ($act === 'readall') {
            $db->execute("UPDATE WapNotice SET isread=1 WHERE uid=?", [$uid]);
            header('Location: notice.php?siteid=' . $this->siteid);
            exit;
        }

        $onlyUnread = (($_GET['only'] ?? '') === 'unread');
        $page = max(1, (int)($_GET['page'] ?? 1));
        $unread = (int)$db->getScalar("SELECT COUNT(*) FROM WapNotice WHERE siteid=? AND uid=? AND isread=0",
            [$this->siteid, $uid]);

        $where = 'siteid=? AND uid=?';
        $params = [$this->siteid, $uid];
        if ($onlyUnread) {
            $where .= ' AND isread=0';
        }
        $result = $db->getPage('WapNotice', '*', $where, 'id DESC', $page, 20, $params);

        $this->strTitle = '通知中心';
        $this->showTop($this->strTitle);
        echo '<div class="subtitle">通知中心</div>';
        echo '<div class="menu">';
        echo '<a href="notice.php?siteid=' . $this->siteid . '">全部通知</a> | ';
        echo '<a href="notice.php?siteid=' . $this->siteid . '&only=unread">仅未读(' . $unread . ')</a> | ';
        echo '<a href="notice.php?siteid=' . $this->siteid . '&act=readall">全部标为已读</a> | ';
        echo '<a href="index.php?siteid=' . $this->siteid . '">返回消息</a>';
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
                $line = '[' . kl_notice_label($n['ntype']) . '] ' . $from . ' ' . $n['content'];
                $time = '<span class="time">' . $this->ToHtm($n['addtime']) . '</span>';
                if (!$n['isread']) {
                    echo '<li><strong><a href="notice.php?siteid=' . $this->siteid . '&act=read&id=' . (int)$n['id'] . '">'
                        . $this->ToHtm($line) . '</a></strong> ' . $time . '</li>';
                } else {
                    echo '<li>' . $this->ToHtm($line) . ' ' . $time . '</li>';
                }
            }
            echo '</ul></div>';
        }

        if ($result['pageCount'] > 1) {
            echo '<div class="pager">';
            $suffix = $onlyUnread ? '&only=unread' : '';
            if ($page > 1) echo '<a href="?siteid=' . $this->siteid . $suffix . '&page=' . ($page - 1) . '">上一页</a> ';
            echo '第' . $page . '/' . $result['pageCount'] . '页 ';
            if ($page < $result['pageCount']) echo '<a href="?siteid=' . $this->siteid . $suffix . '&page=' . ($page + 1) . '">下一页</a>';
            echo '</div>';
        }

        echo '<div><a href="index.php?siteid=' . $this->siteid . '">返回消息中心</a></div>';
        $this->showDown();
    }
}

$page = new MessageNoticePage();
$page->run();
