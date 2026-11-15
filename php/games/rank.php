<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 游戏模块 - 积分排行榜 (Top20)
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class GamesRankPage extends WapPage {
    public function run() {
        $db = $this->db;
        $gameid = (int)($_GET['gameid'] ?? 0);
        if ($gameid <= 0) $this->showError('参数错误');

        $game = $db->getOne("SELECT id,name FROM WapGames WHERE id=? AND siteid=?", [$gameid, $this->siteid]);
        if (!$game) $this->showError('游戏不存在');

        $this->strTitle = $game['name'] . ' 排行榜';
        $this->showTop($this->strTitle);
        echo '<div class="subtitle">' . $this->ToHtm($this->strTitle) . '</div>';

        $rows = $db->getAll(
            "SELECT nickname,score,addtime FROM WapGamesScore WHERE gameid=? AND siteid=? ORDER BY score DESC, id ASC LIMIT 20",
            [$gameid, $this->siteid]
        );
        echo '<div class="content"><ul class="list">';
        if (!$rows) echo '<li>暂无成绩，快来挑战吧</li>';
        $rank = 1;
        foreach ($rows as $r) {
            echo '<li>第' . $rank . '名 ' . $this->ToHtm($r['nickname'] !== '' && $r['nickname'] !== null ? $r['nickname'] : '游客')
                . ' <span class="info">' . (int)$r['score'] . '分</span></li>';
            $rank++;
        }
        echo '</ul></div>';

        echo '<div class="actionbar">';
        echo '<a href="score.php?siteid=' . $this->siteid . '&classid=' . $this->classid . '&gameid=' . $gameid . '">提交成绩</a> ';
        echo '<a href="index.php?siteid=' . $this->siteid . '&classid=' . $this->classid . '">返回游戏列表</a>';
        echo '</div>';
        $this->showDown();
    }
}

$page = new GamesRankPage();
$page->run();
