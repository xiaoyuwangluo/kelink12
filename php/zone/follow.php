<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 会员空间 - 关注/取消关注 (切换式)
 * ?uid= 被关注者
 */
require_once __DIR__ . '/../includes/bootstrap.php';
require_once KL_ROOT . '/includes/Community.php';

class ZoneFollowPage extends WapPage {
    public function run() {
        $db = $this->db;
        $this->checkLogin();
        $me = (int)$this->userSession->getUserId();

        $uid = (int)($_GET['uid'] ?? 0);
        if ($uid <= 0) $this->showError('参数错误');
        if ($uid === $me) $this->showError('不能关注自己', 'index.php?siteid=' . $this->siteid . '&uid=' . $me);

        $user = $db->getOne("SELECT id FROM WapUser WHERE id=?", [$uid]);
        if (!$user) $this->showError('用户不存在', 'index.php?siteid=' . $this->siteid . '&uid=' . $me);

        $backUrl = 'index.php?siteid=' . $this->siteid . '&uid=' . $uid;
        $exists = $db->getOne("SELECT id FROM WapFollow WHERE siteid=? AND userid=? AND followuid=?",
            [$this->siteid, $me, $uid]);

        if ($exists) {
            $db->execute("DELETE FROM WapFollow WHERE id=?", [$exists['id']]);
            $msg = '已取消关注';
        } else {
            $db->insert("INSERT INTO WapFollow (siteid,userid,followuid,addtime) VALUES (?,?,?,?)",
                [$this->siteid, $me, $uid, date('Y-m-d H:i:s')]);
            kl_notice_add($db, $this->siteid, $uid, $me, 'follow', 'zone', $me, '关注了你');
            $msg = '已关注';
        }

        kl_cache_clear_site($this->siteid);
        $this->showSuccess($msg, $backUrl);
    }
}

$page = new ZoneFollowPage();
$page->run();
