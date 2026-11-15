<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 问答模块 - 我要提问 (需登录)
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class ZhdaoAskPage extends WapPage {
    public function run() {
        $this->checkLogin();
        $this->showTop('我要提问');

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            $this->doAsk();
        } else {
            $this->showForm();
        }
        $this->showDown();
    }

    private function showForm() {
        echo '<div class="subtitle">我要提问</div>';
        echo '<div class="content"><form method="post" action="ask.php?siteid=' . $this->siteid . '&classid=' . $this->classid . '">';
        echo '问题标题:<input type="text" name="title" maxlength="100" required/><br/>';
        echo '问题描述:<textarea name="content" rows="6" required></textarea><br/>';
        echo '<input type="submit" value="提交提问"/>';
        echo '</form></div>';
        echo '<div class="actionbar"><a href="index.php?siteid=' . $this->siteid . '&classid=' . $this->classid . '">返回列表</a></div>';
    }

    private function doAsk() {
        $db = $this->db;
        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');
        if ($title === '' || mb_strlen($title) > 100) {
            echo '<div class="error">标题不能为空且不超过100字</div>';
            $this->showForm();
            return;
        }
        if ($content === '') {
            echo '<div class="error">问题描述不能为空</div>';
            $this->showForm();
            return;
        }
        if (WapTool::checkSqlInject($title) || WapTool::checkSqlInject($content)) {
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

        $id = $db->insert(
            "INSERT INTO WapZhiDao (siteid,classid,userid,nickname,title,content,status,hits,addtime) VALUES (?,?,?,?,?,?,0,0,NOW())",
            [$this->siteid, $this->classid, $uid, $nick, $title, $content]
        );
        header('Location: view.php?siteid=' . $this->siteid . '&classid=' . $this->classid . '&id=' . $id);
        exit;
    }
}

$page = new ZhdaoAskPage();
$page->run();
