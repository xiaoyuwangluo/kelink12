<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 站内消息 - 一对一聊天会话
 */
require_once __DIR__ . '/../includes/bootstrap.php';
require_once KL_ROOT . '/includes/Community.php';

class MessageChatPage extends WapPage {
    public function run() {
        $db = $this->db;
        $this->checkLogin();
        $uid = (int)$this->userSession->getUserId();
        $peer = (int)($_GET['uid'] ?? 0);
        $back = 'sessions.php';

        if ($peer <= 0 || $peer === $uid) $this->showError('参数错误', $back);
        $peerUser = $db->getOne("SELECT id,username,nickname FROM WapUser WHERE id=?", [$peer]);
        if (!$peerUser) $this->showError('用户不存在', $back);
        if (kl_is_blocked($db, $uid, $peer)) $this->showError('你与对方存在拉黑关系，无法会话', $back);

        $peerName = trim((string)$peerUser['nickname']);
        if ($peerName === '') $peerName = (string)$peerUser['username'];

        $err = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $content = trim((string)($_POST['content'] ?? ''));
            if ($content === '') {
                $err = '内容不能为空';
            } elseif (mb_strlen($content) > 1000) {
                $err = '内容不能超过 1000 字';
            } elseif (WapTool::checkSqlInject($content)) {
                $err = '含非法字符';
            } elseif (!$this->userSession->checkSubmitLimit()) {
                $err = '操作过于频繁，请稍后再试';
            } else {
                $todayCount = (int)$db->getScalar("SELECT COUNT(*) FROM WapMessage WHERE fromid=? AND DATE(addtime)=CURDATE()", [$uid]);
                if (KL_SendMSGCount > 0 && $todayCount >= KL_SendMSGCount) {
                    $err = '今日发信已达上限';
                }
            }

            if ($err === '') {
                $me = $db->getOne("SELECT username FROM WapUser WHERE id=?", [$uid]);
                $db->insert("INSERT INTO WapMessage (fromid,fromname,toid,toname,title,content,isread,addtime) VALUES (?,?,?,?,?,?,0,NOW())",[$uid,(string)($me['username'] ?? ''),$peer,(string)$peerUser['username'],'',$content]);
                header('Location: chat.php?uid=' . $peer);
                exit;
            }
        }

        // 标记对方发来的消息为已读
        $db->execute("UPDATE WapMessage SET isread=1 WHERE fromid=? AND toid=? AND isread=0",[$peer,$uid]);

        $rows = $db->getAll("SELECT * FROM WapMessage WHERE ((fromid=? AND toid=?) OR (fromid=? AND toid=?)) ORDER BY id DESC LIMIT 50",[$uid,$peer,$peer,$uid]);
        $rows = array_reverse($rows);

        $this->strTitle = '与 ' . $peerName . ' 的会话';
        $this->showTop($this->strTitle);
        echo '<div class="subtitle">与 ' . $this->ToHtm($peerName) . ' 的会话</div>';
        echo '<div class="menu"><a href="' . $back . '">会话列表</a> | <a href="index.php">收件箱</a></div>';
        if ($err !== '') echo '<div class="error">' . $this->ToHtm($err) . '</div>';

        if (!$rows) {
            echo '<div class="content">暂无消息，发送第一条吧</div>';
        } else {
            echo '<div class="content">';
            foreach ($rows as $m) {
                $mine = ((int)$m['fromid'] === $uid);
                $who = $mine ? '我' : $peerName;
                echo '<div class="reply">';
                echo '<div class="poster">' . $this->ToHtm($who) . ' <span class="time">'
                    . date('m-d H:i', strtotime((string)$m['addtime'])) . '</span></div>';
                echo '<div class="content">' . $this->wapTool->ToWML($m['content'], $this->wmlVo) . '</div>';
                echo '</div>';
            }
            echo '</div>';
        }

        echo '<div class="content"><form method="post" action="chat.php?uid=' . $peer . '">';
        echo '<textarea name="content" rows="4" cols="30" maxlength="1000" required></textarea><br/>';
        echo '<input type="submit" value="发送"/></form></div>';
        echo '<div><a href="../zone/index.php?uid=' . $peer . '">查看对方空间</a></div>';
        $this->showDown();
    }
}

$page = new MessageChatPage();
$page->run();
