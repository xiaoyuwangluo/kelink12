<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 聊天室模块 - 发送发言 (需登录，写库后跳回聊天室)
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class ChatSendPage extends WapPage {
    public function run() {
        $this->checkLogin();

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            header('Location: index.php?classid=' . $this->classid);
            exit;
        }

        $back = 'index.php?classid=' . $this->classid;
        $content = trim($_POST['content'] ?? '');
        if ($content === '') {
            $this->showError('发言内容不能为空', $back);
        }
        if (mb_strlen($content) > 200) {
            $this->showError('发言内容不能超过200字', $back);
        }
        if (WapTool::checkSqlInject($content)) {
            $this->showError('包含非法字符', $back);
        }
        if (!$this->userSession->checkSubmitLimit()) {
            $this->showError('操作过于频繁，请稍后再试', $back);
        }

        $uid = (int)$this->userSession->getUserId();
        $nick = $this->userVo['nickname'] ?? '';
        if ($nick === '' || $nick === null) $nick = $this->userVo['username'] ?? '会员';

        $this->db->insert("INSERT INTO WapChatSpeak (userid,nickname,content,addtime) VALUES (?,?,?,NOW())",[$uid,$nick,$content]);
        header('Location: ' . $back);
        exit;
    }
}

$page = new ChatSendPage();
$page->run();
