<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 问答模块 - 回答问题 (需登录，回答后问题状态置为已答)
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class ZhdaoAnswerPage extends WapPage {
    public function run() {
        $this->checkLogin();
        $db = $this->db;
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) $this->showError('参数错误');

        $question = $db->getOne("SELECT * FROM WapZhiDao WHERE id=?",[$id]);
        if (!$question) $this->showError('问题不存在');

        $this->showTop('回答问题');

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            $this->doAnswer($id, $question['title']);
        } else {
            $this->showForm($question['title']);
        }
        $this->showDown();
    }

    private function showForm($title) {
        echo '<div class="subtitle">回答：' . $this->ToHtm($title) . '</div>';
        echo '<div class="content"><form method="post" action="answer.php?classid=' . $this->classid . '&id=' . (int)($_GET['id'] ?? 0) . '">';
        echo '回答内容:<textarea name="content" rows="6" required></textarea><br/>';
        echo '<input type="submit" value="提交回答"/>';
        echo '</form></div>';
        echo '<div class="actionbar"><a href="view.php?classid=' . $this->classid . '&id=' . (int)($_GET['id'] ?? 0) . '">返回问题</a></div>';
    }

    private function doAnswer($id, $title) {
        $db = $this->db;
        $content = trim($_POST['content'] ?? '');
        if ($content === '' || mb_strlen($content) > 1000) {
            echo '<div class="error">回答内容不能为空且不超过1000字</div>';
            $this->showForm($title);
            return;
        }
        if (WapTool::checkSqlInject($content)) {
            echo '<div class="error">包含非法字符</div>';
            return;
        }
        if (!$this->userSession->checkSubmitLimit()) {
            echo '<div class="error">操作过于频繁，请稍后再试</div>';
            return;
        }

        $uid = (int)$this->userSession->getUserId();
        $nick = $this->userVo['nickname'] ?? '';
        if ($nick === '' || $nick === null) $nick = $this->userVo['username'] ?? '会员';

        $db->insert("INSERT INTO WapZhiDaoReply (zhidoid,userid,nickname,content,isbest,addtime) VALUES (?,?,?,?,0,NOW())",[$id,$uid,$nick,$content]);
        // 回答后问题状态置为已答
        $db->execute("UPDATE WapZhiDao SET status=1 WHERE id=?", [$id]);

        header('Location: view.php?classid=' . $this->classid . '&id=' . $id);
        exit;
    }
}

$page = new ZhdaoAnswerPage();
$page->run();
