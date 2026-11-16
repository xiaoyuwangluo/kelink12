<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 论坛模块 - 帖子投票 (对应原 bbs/vote.aspx)
 * 提交 optionid，写 WapVoteLog 并更新选项票数/总票数，一人一票
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class BbsVotePage extends WapPage {
    public function run() {
        $db = $this->db;
        $voteid = (int)($_REQUEST['voteid'] ?? 0);
        if ($voteid <= 0) $this->showError('参数错误');

        $vote = $db->getOne("SELECT * FROM WapVote WHERE id=?", [$voteid]);
        if (!$vote) $this->showError('投票不存在');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->doVote($vote);
        }

        // 展示投票/结果
        $this->strTitle = '投票:' . $vote['question'];
        $this->showTop($this->strTitle);
        $options = $db->getAll("SELECT * FROM WapVoteOption WHERE voteid=? ORDER BY id", [$voteid]);
        $total = max(1, (int)$vote['total']);

        $myChoice = 0;
        if ($this->userSession->isLogin()) {
            $log = $db->getOne("SELECT optionid FROM WapVoteLog WHERE voteid=? AND userid=?",
                [$voteid, $this->userSession->getUserId()]);
            if ($log) $myChoice = (int)$log['optionid'];
        }

        echo '<div class="subtitle">' . $this->ToHtm($vote['question']) . '</div>';
        echo '<div class="content">';
        if ($myChoice > 0) {
            echo '<div class="success">您已参与投票</div>';
        } elseif (!$this->userSession->isLogin()) {
            echo '<div class="error">请先登录后参与投票</div>';
        }
        echo '<form method="post" action="vote.php?voteid=' . $voteid . '">';
        foreach ($options as $o) {
            $pct = round((int)$o['votes'] * 100 / $total);
            $checked = ($myChoice === (int)$o['id']) ? ' checked="checked"' : '';
            echo '<label>';
            if ($myChoice === 0 && $this->userSession->isLogin()) {
                echo '<input type="radio" name="optionid" value="' . (int)$o['id'] . '"' . $checked . '/> ';
            }
            echo $this->ToHtm($o['optionname']) . ' (' . (int)$o['votes'] . '票 ' . $pct . '%)';
            if ($myChoice === (int)$o['id']) echo ' [我的选择]';
            echo '</label><br/>';
        }
        if ($myChoice === 0 && $this->userSession->isLogin()) {
            echo '<input type="submit" value="提交投票"/>';
        }
        echo '</form>';
        echo '总票数:' . (int)$vote['total'] . '</div>';

        if ($vote['bbsid'] > 0) {
            echo '<div><a href="view.php?id=' . (int)$vote['bbsid'] . '">返回帖子</a></div>';
        }
        $this->showDown();
    }

    /** 提交投票，一人一票 */
    private function doVote($vote) {
        $db = $this->db;
        $this->checkLogin();
        $uid = (int)$this->userSession->getUserId();
        $optionid = (int)($_POST['optionid'] ?? 0);
        if ($optionid <= 0) $this->showError('请选择投票选项');

        $option = $db->getOne("SELECT * FROM WapVoteOption WHERE id=? AND voteid=?", [$optionid, $vote['id']]);
        if (!$option) $this->showError('选项不存在');

        $log = $db->getOne("SELECT id FROM WapVoteLog WHERE voteid=? AND userid=?", [$vote['id'], $uid]);
        if ($log) $this->showError('您已投过票，每人限投一次');

        $db->insert("INSERT INTO WapVoteLog (voteid,userid,optionid,addtime) VALUES (?,?,?,NOW())",
            [$vote['id'], $uid, $optionid]);
        $db->execute("UPDATE WapVoteOption SET votes=votes+1 WHERE id=?", [$optionid]);
        $db->execute("UPDATE WapVote SET total=total+1 WHERE id=?", [$vote['id']]);

        $this->showSuccess('投票成功', 'vote.php?voteid=' . (int)$vote['id']);
    }
}

$page = new BbsVotePage();
$page->run();
