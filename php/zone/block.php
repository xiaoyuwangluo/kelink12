<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 会员空间 - 拉黑/取消拉黑 与黑名单列表
 * ?uid= 目标用户（切换式）；不带 uid 显示我的黑名单
 */
require_once __DIR__ . '/../includes/bootstrap.php';
require_once KL_ROOT . '/includes/Community.php';

class ZoneBlockPage extends WapPage {
    public function run() {
        $db = $this->db;
        $this->checkLogin();
        $me = (int)$this->userSession->getUserId();
        $sid = (int)$this->siteid;

        $uid = (int)($_GET['uid'] ?? 0);
        if ($uid <= 0) {
            $this->showList($me, $sid);
            return;
        }
        if ($uid === $me) {
            $this->showError('不能拉黑自己', 'block.php?siteid=' . $sid);
        }
        $user = $db->getOne("SELECT id,nickname,username FROM WapUser WHERE id=?", [$uid]);
        if (!$user) $this->showError('用户不存在', 'block.php?siteid=' . $sid);

        $exists = $db->getOne("SELECT id FROM WapBlock WHERE siteid=? AND userid=? AND blockuid=?", [$sid, $me, $uid]);
        if ($exists) {
            $db->execute("DELETE FROM WapBlock WHERE id=?", [$exists['id']]);
            $msg = '已取消拉黑';
        } else {
            $db->insert("INSERT INTO WapBlock (siteid,userid,blockuid,addtime) VALUES (?,?,?,?)",
                [$sid, $me, $uid, date('Y-m-d H:i:s')]);
            // 拉黑同时取消我对其的关注，避免继续收到动态
            $db->execute("DELETE FROM WapFollow WHERE siteid=? AND userid=? AND followuid=?", [$sid, $me, $uid]);
            $msg = '已拉黑，双方将无法互相发私信、关注或 @ 提醒';
        }
        kl_cache_clear_site($sid);
        $this->showSuccess($msg, 'block.php?siteid=' . $sid);
    }

    /** 我的黑名单 */
    private function showList($me, $sid) {
        $db = $this->db;
        $rows = $db->getAll(
            "SELECT b.id, b.blockuid, b.addtime, u.nickname, u.username
             FROM WapBlock b LEFT JOIN WapUser u ON u.id=b.blockuid
             WHERE b.siteid=? AND b.userid=? ORDER BY b.id DESC",
            [$sid, $me]
        );

        $this->strTitle = '我的黑名单';
        $this->showTop($this->strTitle);
        echo '<div class="subtitle">我的黑名单</div>';
        echo '<div class="menu"><a href="index.php?siteid=' . $sid . '&uid=' . $me . '">我的空间</a></div>';

        if (!$rows) {
            echo '<div class="content">黑名单为空</div>';
            $this->showDown();
            return;
        }

        echo '<div class="content"><ul class="list">';
        foreach ($rows as $r) {
            $name = trim((string)$r['nickname']);
            if ($name === '') $name = (string)$r['username'];
            if ($name === '') $name = '用户' . (int)$r['blockuid'];
            echo '<li>' . $this->ToHtm($name);
            echo ' <span class="time">' . $this->ToHtm($r['addtime']) . '</span> ';
            echo '<a href="block.php?siteid=' . $sid . '&uid=' . (int)$r['blockuid'] . '">取消拉黑</a>';
            echo '</li>';
        }
        echo '</ul></div>';
        $this->showDown();
    }
}

$page = new ZoneBlockPage();
$page->run();
