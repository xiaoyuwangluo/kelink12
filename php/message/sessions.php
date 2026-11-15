<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 站内消息 - 会话列表（按往来用户聚合）
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class MessageSessionsPage extends WapPage {
    public function run() {
        $db = $this->db;
        $this->checkLogin();
        $uid = (int)$this->userSession->getUserId();
        $sid = (int)$this->siteid;

        // 最近 200 条往来消息，按对方用户聚合出会话
        $rows = $db->getAll(
            "SELECT * FROM WapMessage WHERE siteid=? AND (fromid=? OR toid=?) ORDER BY id DESC LIMIT 200",
            [$sid, $uid, $uid]
        );
        $sessions = [];
        foreach ($rows as $m) {
            $peer = ((int)$m['fromid'] === $uid) ? (int)$m['toid'] : (int)$m['fromid'];
            if ($peer <= 0) continue;
            if (!isset($sessions[$peer])) {
                $sessions[$peer] = [
                    'uid' => $peer,
                    'name' => ((int)$m['fromid'] === $uid) ? $m['toname'] : $m['fromname'],
                    'last' => (string)$m['content'],
                    'title' => (string)$m['title'],
                    'time' => (string)$m['addtime'],
                    'new' => 0,
                ];
            }
        }

        // 未读按对方分组统计
        if ($sessions) {
            foreach ($db->getAll(
                "SELECT fromid, COUNT(*) AS c FROM WapMessage WHERE siteid=? AND toid=? AND isread=0 GROUP BY fromid",
                [$sid, $uid]
            ) as $u) {
                $from = (int)$u['fromid'];
                if (isset($sessions[$from])) $sessions[$from]['new'] = (int)$u['c'];
            }
        }

        $this->strTitle = '会话';
        $this->showTop($this->strTitle);
        echo '<div class="subtitle">会话</div>';
        echo '<div class="menu">';
        echo '<a href="index.php?siteid=' . $sid . '">收件箱/发件箱</a> | ';
        echo '<a href="notice.php?siteid=' . $sid . '">通知中心</a>';
        echo '</div>';

        if (!$sessions) {
            echo '<div class="content">暂无会话</div>';
            $this->showDown();
            return;
        }

        echo '<div class="content"><ul class="list">';
        foreach ($sessions as $s) {
            $preview = mb_substr(trim(strip_tags($s['last'])), 0, 30);
            if ($preview === '') $preview = $s['title'] !== '' ? $s['title'] : '(无内容)';
            echo '<li>';
            if ($s['new'] > 0) echo '<strong>[' . $s['new'] . '未读]</strong> ';
            echo '<a href="chat.php?siteid=' . $sid . '&uid=' . (int)$s['uid'] . '">' . $this->ToHtm($s['name']) . '</a>';
            echo ' <span class="info">' . $this->ToHtm($preview) . '</span>';
            echo ' <span class="time">' . $this->ToHtm($s['time']) . '</span>';
            echo '</li>';
        }
        echo '</ul></div>';
        $this->showDown();
    }
}

$page = new MessageSessionsPage();
$page->run();
