<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 好友模块 - 删除好友 (对应原 friend/del.aspx)
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class FriendDelPage extends WapPage {
    public function run() {
        $db = $this->db;
        $this->checkLogin();
        $uid = (int)$this->userSession->getUserId();
        $fid = (int)($_GET['fid'] ?? 0);
        if ($fid <= 0) $this->showError('参数错误', 'index.php');

        $row = $db->getOne("SELECT id FROM WapFriend WHERE userid=? AND friendid=?", [$uid, $fid]);
        if (!$row) $this->showError('您没有该好友', 'index.php');

        $db->execute("DELETE FROM WapFriend WHERE id=?", [$row['id']]);
        $this->showSuccess('已删除好友', 'index.php');
    }
}

$page = new FriendDelPage();
$page->run();
