<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 聊天室模块 - 聊天室首页 (显示最近50条发言，10秒自动刷新)
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class ChatIndexPage extends WapPage {
    public function run() {
        $db = $this->db;
        $this->strTitle = $this->classVo->classname ?? '聊天室';
        $this->showTop($this->strTitle);
        // 10秒自动刷新
        echo '<meta http-equiv="refresh" content="10" />' . "\n";

        echo '<div class="subtitle">' . $this->ToHtm($this->strTitle) . '</div>';

        // 最近50条发言，按时间升序展示
        $rows = $db->getAll("SELECT * FROM (SELECT id,userid,nickname,content,addtime FROM WapChatSpeak ORDER BY id DESC LIMIT 50) t ORDER BY t.id ASC",[]);
        echo '<div class="content"><ul class="list">';
        if (!$rows) {
            echo '<li>聊天室还没有发言</li>';
        }
        foreach ($rows as $r) {
            $time = !empty($r['addtime']) ? date('m-d H:i', strtotime($r['addtime'])) : '';
            echo '<li><b>' . $this->ToHtm($r['nickname'] !== '' && $r['nickname'] !== null ? $r['nickname'] : '游客') . '</b>：'
                . $this->ToHtm($r['content'])
                . ' <span class="time">' . $time . '</span></li>';
        }
        echo '</ul></div>';

        // 发言输入框
        if ($this->userSession->isLogin()) {
            $nick = $_SESSION['kl_nickname'] ?? '';
            if ($nick === '' || $nick === null) $nick = $_SESSION['kl_username'] ?? '会员';
            echo '<div class="content">';
            echo '<form method="post" action="send.php?classid=' . $this->classid . '">';
            echo '当前身份：' . $this->ToHtm($nick) . '<br/>';
            echo '<input type="text" name="content" maxlength="200" required/>';
            echo '<input type="submit" value="发送"/>';
            echo '</form></div>';
        } else {
            echo '<div class="content"><a href="../waplogin.php">登录后即可发言</a></div>';
        }

        echo '<div class="actionbar"><a href="../wapindex.php">返回首页</a></div>';
        $this->showDown();
    }
}

$page = new ChatIndexPage();
$page->run();
