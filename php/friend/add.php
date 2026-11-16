<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 好友模块 - 添加好友 (对应原 friend/add.aspx)
 * 申请即好友(status=1)，双向建立好友关系
 */
require_once __DIR__ . '/../includes/bootstrap.php';
require_once KL_ROOT . '/includes/Community.php';

class FriendAddPage extends WapPage {
    public function run() {
        $db = $this->db;
        $this->checkLogin();
        $uid = (int)$this->userSession->getUserId();

        $fid = (int)($_GET['fid'] ?? 0);
        $fname = trim($_GET['username'] ?? '');

        if ($fid <= 0 && $fname === '') $this->showError('请指定要添加的好友', 'index.php');
        if (WapTool::checkSqlInject($fname)) $this->showError('包含非法字符');

        $target = $fid > 0
            ? $db->getOne("SELECT id,username FROM WapUser WHERE id=?", [$fid])
            : $db->getOne("SELECT id,username FROM WapUser WHERE username=?", [$fname]);
        if (!$target) $this->showError('用户不存在', 'index.php');
        $tid = (int)$target['id'];
        if ($tid === $uid) $this->showError('不能添加自己为好友');

        // 已是好友检查
        $exists = $db->getOne("SELECT id FROM WapFriend WHERE userid=? AND friendid=?", [$uid, $tid]);
        if ($exists) $this->showError('您与 ' . $target['username'] . ' 已经是好友', 'index.php');

        // 防刷：提交间隔 + 每日加好友上限
        if (!$this->userSession->checkSubmitLimit()) $this->showError('操作过于频繁，请稍后再试');
        $todayCount = (int)$db->getScalar("SELECT COUNT(*) FROM WapFriend WHERE userid=? AND DATE(addtime)=CURDATE()", [$uid]);
        if (KL_ADDFriendCount > 0 && $todayCount >= KL_ADDFriendCount) $this->showError('今日加好友已达上限');

        // 申请即好友，双向 status=1
        $db->insert("INSERT INTO WapFriend (userid,friendid,status,addtime) VALUES (?,?,1,NOW())",[$uid,$tid]);
        $reverse = $db->getOne("SELECT id FROM WapFriend WHERE userid=? AND friendid=?", [$tid, $uid]);
        if (!$reverse) {
            $db->insert("INSERT INTO WapFriend (userid,friendid,status,addtime) VALUES (?,?,1,NOW())",[$tid,$uid]);
        }

        kl_notice_add($db, $tid, $uid, 'friend', 'friend', $uid, '加你为好友');
        kl_cache_clear_site();

        $this->showSuccess('添加 ' . $target['username'] . ' 为好友成功', 'index.php');
    }
}

$page = new FriendAddPage();
$page->run();
