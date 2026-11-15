<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 问答模块 - 问题详情与回答列表 (浏览即hits+1)
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class ZhdaoViewPage extends WapPage {
    public function run() {
        $db = $this->db;
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) $this->showError('参数错误');

        $question = $db->getOne("SELECT * FROM WapZhiDao WHERE id=? AND siteid=?", [$id, $this->siteid]);
        if (!$question) $this->showError('问题不存在');

        // 更新点击
        $db->execute("UPDATE WapZhiDao SET hits=hits+1 WHERE id=?", [$id]);

        $this->strTitle = $question['title'];
        $this->showTop($this->strTitle);

        echo '<div class="subtitle">' . $this->ToHtm($question['title']) . '</div>';
        echo '<div class="post">';
        echo '<div class="poster">' . $this->ToHtm($question['nickname'] !== '' && $question['nickname'] !== null ? $question['nickname'] : '游客')
            . ' ' . (!empty($question['addtime']) ? date('Y-m-d H:i', strtotime($question['addtime'])) : '')
            . ' ' . ((int)$question['status'] === 1 ? '[已解决]' : '[待解决]') . '</div>';
        echo '<div class="content">' . $this->wapTool->ToWML($question['content'], $this->wmlVo) . '</div>';
        echo '</div>';

        // 回答列表 (采纳最佳答案在前)
        $replies = $db->getAll(
            "SELECT * FROM WapZhiDaoReply WHERE zhidoid=? AND siteid=? ORDER BY isbest DESC, id ASC",
            [$id, $this->siteid]
        );
        echo '<div class="subtitle">回答 (' . count($replies) . ')</div>';
        echo '<div class="replylist">';
        $floor = 1;
        foreach ($replies as $r) {
            echo '<div class="reply">';
            echo '<div class="poster">#' . $floor . ' '
                . $this->ToHtm($r['nickname'] !== '' && $r['nickname'] !== null ? $r['nickname'] : '游客')
                . ((int)$r['isbest'] === 1 ? ' [最佳]' : '')
                . ' ' . (!empty($r['addtime']) ? date('m-d H:i', strtotime($r['addtime'])) : '') . '</div>';
            echo '<div class="content">' . $this->wapTool->ToWML($r['content'], $this->wmlVo) . '</div>';
            echo '</div>';
            $floor++;
        }
        echo '</div>';

        echo '<div class="actionbar">';
        echo '<a href="answer.php?siteid=' . $this->siteid . '&classid=' . $this->classid . '&id=' . $id . '">回答问题</a> ';
        echo '<a href="index.php?siteid=' . $this->siteid . '&classid=' . $this->classid . '">返回列表</a>';
        echo '</div>';
        $this->showDown();
    }
}

$page = new ZhdaoViewPage();
$page->run();
