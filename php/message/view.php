<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 站内消息 - 查看并标记已读 (对应原 message/view.aspx)
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class MessageViewPage extends WapPage {
    public function run() {
        $db = $this->db;
        $this->checkLogin();
        $uid = (int)$this->userSession->getUserId();
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) $this->showError('参数错误');

        $msg = $db->getOne("SELECT * FROM WapMessage WHERE id=?", [$id]);
        if (!$msg || ((int)$msg['toid'] !== $uid && (int)$msg['fromid'] !== $uid)) {
            $this->showError('消息不存在或无权查看');
        }

        // 收件人查看时标记已读
        if ((int)$msg['toid'] === $uid && !$msg['isread']) {
            $db->execute("UPDATE WapMessage SET isread=1 WHERE id=?", [$id]);
        }

        $this->strTitle = '查看消息';
        $this->showTop($this->strTitle);
        echo '<div class="subtitle">' . $this->ToHtm($msg['title'] !== '' ? $msg['title'] : '(无标题)') . '</div>';
        echo '<div class="content">';
        echo '发件人:' . $this->ToHtm($msg['fromname']) . '<br/>';
        echo '收件人:' . $this->ToHtm($msg['toname']) . '<br/>';
        echo '时间:' . $this->ToHtm($msg['addtime']) . '<br/>';
        echo '内容:<br/>' . $this->wapTool->ToWML($msg['content'], $this->wmlVo);
        echo '</div>';

        echo '<div class="actionbar">';
        if ((int)$msg['fromid'] !== $uid) {
            echo '<a href="send.php?siteid=' . $this->siteid . '&to=' . urlencode($msg['fromname']) . '">回复</a> ';
        }
        echo '<a href="del.php?siteid=' . $this->siteid . '&id=' . $id . '">删除</a> ';
        echo '<a href="index.php?siteid=' . $this->siteid . '">返回收件箱</a>';
        echo '</div>';
        $this->showDown();
    }
}

$page = new MessageViewPage();
$page->run();
