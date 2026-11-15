<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 论坛模块 - 举报帖子 (登录会员可举报，同一用户对同一帖未处理举报仅允许一次)
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class BbsReportPage extends WapPage {
    /** 允许的举报原因 */
    private $reasons = ['广告', '色情', '政治', '其他'];

    public function run() {
        $this->checkLogin(); // 需会员登录
        $db = $this->db;
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) $this->showError('参数错误');

        $thread = $db->getOne("SELECT id,title FROM WapBbs WHERE id=? AND siteid=?", [$id, $this->siteid]);
        if (!$thread) $this->showError('帖子不存在');

        $this->showTop('举报帖子');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->doReport($thread);
        } else {
            $this->showForm($thread);
        }
        $this->showDown();
    }

    private function showForm($thread) {
        echo '<div class="subtitle">举报帖子</div>';
        echo '<div class="content">被举报帖子：' . $this->ToHtm($thread['title']) . '</div>';
        echo '<form method="post" action="report.php?siteid=' . $this->siteid . '&classid=' . $this->classid . '&id=' . (int)$thread['id'] . '">';
        echo '举报原因:<br/>';
        foreach ($this->reasons as $i => $r) {
            echo '<label><input type="radio" name="reason" value="' . $this->ToHtm($r) . '"' . ($i === 0 ? ' required' : '') . '/>' . $this->ToHtm($r) . '</label> ';
        }
        echo '<br/>补充说明:<textarea name="note" rows="4"></textarea><br/>';
        echo '<input type="submit" value="提交举报"/></form>';
        echo '<div><a href="view.php?siteid=' . $this->siteid . '&classid=' . $this->classid . '&id=' . (int)$thread['id'] . '">返回帖子</a></div>';
    }

    private function doReport($thread) {
        $db = $this->db;
        $uid = (int)$this->userSession->getUserId();
        $reason = trim($_POST['reason'] ?? '');
        $note = mb_substr(trim((string)($_POST['note'] ?? '')), 0, 500);

        if (!in_array($reason, $this->reasons, true)) { echo '<div class="error">请选择举报原因</div>'; $this->showForm($thread); return; }
        if ($reason === '其他' && $note === '') { echo '<div class="error">选择"其他"时请填写具体说明</div>'; $this->showForm($thread); return; }
        if (!$this->userSession->checkSubmitLimit()) { echo '<div class="error">操作过于频繁</div>'; return; }

        // 同一用户对同一帖未处理(status=0)的举报只允许一次
        $pending = $db->getOne("SELECT id FROM WapReport WHERE bbsid=? AND userid=? AND status=0", [$thread['id'], $uid]);
        if ($pending) {
            echo '<div class="error">您已举报过该帖子，正在处理中，请勿重复举报</div>';
            echo '<div><a href="view.php?siteid=' . $this->siteid . '&classid=' . $this->classid . '&id=' . (int)$thread['id'] . '">返回帖子</a></div>';
            return;
        }

        $db->insert("INSERT INTO WapReport (siteid,bbsid,userid,reason,note,status,addtime) VALUES (?,?,?,?,?,0,NOW())",
            [$this->siteid, $thread['id'], $uid, $reason, $note]);
        echo '<div class="success">举报已提交，感谢监督 <a href="view.php?siteid=' . $this->siteid . '&classid=' . $this->classid . '&id=' . (int)$thread['id'] . '">返回帖子</a></div>';
    }
}

$page = new BbsReportPage();
$page->run();
