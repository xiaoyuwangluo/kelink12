<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 站内消息 - 删除 (对应原 message/del.aspx)
 * 仅可删除自己收/发的消息
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class MessageDelPage extends WapPage {
    public function run() {
        $db = $this->db;
        $this->checkLogin();
        $uid = (int)$this->userSession->getUserId();
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) $this->showError('参数错误');

        $msg = $db->getOne("SELECT * FROM WapMessage WHERE id=?", [$id]);
        if (!$msg || ((int)$msg['toid'] !== $uid && (int)$msg['fromid'] !== $uid)) {
            $this->showError('消息不存在或无权删除');
        }

        $db->execute("DELETE FROM WapMessage WHERE id=?", [$id]);
        $this->showSuccess('已删除', 'index.php');
    }
}

$page = new MessageDelPage();
$page->run();
