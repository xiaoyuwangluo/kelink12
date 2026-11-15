<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 游戏模块 - 提交游戏成绩 (需登录)
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class GamesScorePage extends WapPage {
    public function run() {
        $this->checkLogin();
        $db = $this->db;
        $gameid = (int)($_POST['gameid'] ?? ($_GET['gameid'] ?? 0));
        if ($gameid <= 0) $this->showError('参数错误');

        $game = $db->getOne("SELECT id,name FROM WapGames WHERE id=? AND siteid=?", [$gameid, $this->siteid]);
        if (!$game) $this->showError('游戏不存在');

        // 处理提交 (在输出头部前完成，成功则跳转)
        $err = '';
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            $score = (int)($_POST['score'] ?? -1);
            if ($score < 0 || $score > 1000000) {
                $err = '成绩必须为0-1000000之间的整数';
            } elseif (!$this->userSession->checkSubmitLimit()) {
                $err = '操作过于频繁，请稍后再试';
            } else {
                $uid = (int)$this->userSession->getUserId();
                $nick = $this->userVo['nickname'] ?? '';
                if ($nick === '' || $nick === null) $nick = $this->userVo['username'] ?? '会员';
                $db->insert(
                    "INSERT INTO WapGamesScore (siteid,gameid,userid,nickname,score,addtime) VALUES (?,?,?,?,?,NOW())",
                    [$this->siteid, $gameid, $uid, $nick, $score]
                );
                header('Location: rank.php?siteid=' . $this->siteid . '&classid=' . $this->classid . '&gameid=' . $gameid);
                exit;
            }
        }

        $this->strTitle = '提交成绩 - ' . $game['name'];
        $this->showTop($this->strTitle);
        echo '<div class="subtitle">提交成绩：' . $this->ToHtm($game['name']) . '</div>';
        if ($err !== '') echo '<div class="error">' . $this->ToHtm($err) . '</div>';
        echo '<div class="content"><form method="post" action="score.php?siteid=' . $this->siteid . '&classid=' . $this->classid . '">';
        echo '<input type="hidden" name="gameid" value="' . $gameid . '"/>';
        echo '成绩:<input type="number" name="score" min="0" max="1000000" required/><br/>';
        echo '<input type="submit" value="提交"/>';
        echo '</form></div>';
        echo '<div class="actionbar"><a href="rank.php?siteid=' . $this->siteid . '&classid=' . $this->classid . '&gameid=' . $gameid . '">查看排行</a></div>';
        $this->showDown();
    }
}

$page = new GamesScorePage();
$page->run();
