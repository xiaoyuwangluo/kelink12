<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 论坛模块 - 点赞/取消赞 (切换式)
 * 参数 id(帖子ID) + replyid(0=主帖) + act(like/unlike，不传则切换)
 */
require_once __DIR__ . '/../includes/bootstrap.php';
require_once KL_ROOT . '/includes/Community.php';

class BbsLikePage extends WapPage {
    public function run() {
        $db = $this->db;
        $this->checkLogin();
        $uid = (int)$this->userSession->getUserId();

        $id = (int)($_GET['id'] ?? 0);
        $replyid = (int)($_GET['replyid'] ?? 0);
        if ($id <= 0) $this->showError('参数错误');

        $thread = $db->getOne("SELECT id,siteid,userid FROM WapBbs WHERE id=? AND siteid=?",
            [$id, $this->siteid]);
        if (!$thread) $this->showError('帖子不存在', 'list.php?siteid=' . $this->siteid . '&classid=' . $this->classid);

        $reply = null;
        if ($replyid > 0) {
            $reply = $db->getOne("SELECT id,userid FROM WapBbsReply WHERE id=? AND bbsid=?",
                [$replyid, $id]);
            if (!$reply) $this->showError('回复不存在');
        }

        $backUrl = 'view.php?siteid=' . $this->siteid . '&id=' . $id;
        $act = strtolower(trim((string)($_GET['act'] ?? '')));
        $exists = kl_like_exists($db, $this->siteid, 'bbs', $id, $replyid, $uid);

        $doAdd = false;
        $doDelete = false;
        if ($act === 'like') {
            $doAdd = !$exists;
        } elseif ($act === 'unlike') {
            $doDelete = $exists;
        } else {
            if ($exists) {
                $doDelete = true;
            } else {
                $doAdd = true;
            }
        }

        if ($doAdd) {
            $db->insert(
                "INSERT INTO WapLike (siteid,module,contentid,replyid,userid,addtime) VALUES (?,?,?,?,?,?)",
                [$this->siteid, 'bbs', $id, $replyid, $uid, date('Y-m-d H:i:s')]
            );
            $authorId = $replyid > 0 ? (int)$reply['userid'] : (int)$thread['userid'];
            $word = $replyid > 0 ? '赞了你的回复' : '赞了你的帖子';
            kl_notice_add($db, $this->siteid, $authorId, $uid, 'like', 'bbs', $id, $word);
            $msg = '点赞成功';
        } elseif ($doDelete) {
            $db->execute(
                "DELETE FROM WapLike WHERE siteid=? AND module=? AND contentid=? AND replyid=? AND userid=?",
                [$this->siteid, 'bbs', $id, $replyid, $uid]
            );
            $msg = '已取消赞';
        } else {
            $msg = '已赞过';
        }

        kl_cache_clear_site($this->siteid);
        $this->showSuccess($msg, $backUrl);
    }
}

$page = new BbsLikePage();
$page->run();
