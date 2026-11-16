<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 家族模块 - 加入家族 (需登录)
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class ClanJoinPage extends WapPage {
    public function run() {
        $this->checkLogin();
        $db = $this->db;
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) $this->showError('参数错误');

        $clan = $db->getOne("SELECT * FROM WapClan WHERE id=?",[$id]);
        if (!$clan) $this->showError('家族不存在');

        $uid = (int)$this->userSession->getUserId();
        if ($db->getScalar("SELECT COUNT(*) FROM WapClanMember WHERE clanid=? AND userid=?", [$id, $uid])) {
            $this->showError('你已经是该家族成员', 'view.php?classid=' . $this->classid . '&id=' . $id);
        }

        $nick = $this->userVo['nickname'] ?? '';
        if ($nick === '' || $nick === null) $nick = $this->userVo['username'] ?? '会员';

        $db->insert(
            "INSERT INTO WapClanMember (clanid,userid,nickname,role,addtime) VALUES (?,?,?,2,NOW())",
            [$id, $uid, $nick]
        );
        $db->execute("UPDATE WapClan SET members=members+1 WHERE id=?", [$id]);

        header('Location: view.php?classid=' . $this->classid . '&id=' . $id);
        exit;
    }
}

$page = new ClanJoinPage();
$page->run();
