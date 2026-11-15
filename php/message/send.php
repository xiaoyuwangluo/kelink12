<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 站内消息 - 发送 (对应原 message/send.aspx)
 * 需登录，防刷新提交与每日发送数限制(KL_SendMSGCount)
 */
require_once __DIR__ . '/../includes/bootstrap.php';
require_once KL_ROOT . '/includes/Community.php';

class MessageSendPage extends WapPage {
    /** 会话回跳目标用户（back=chat 时） */
    private $backChat = 0;

    public function run() {
        $db = $this->db;
        $this->checkLogin();
        $this->userVo = $this->userSession->getUserVo();
        if (($_GET['back'] ?? '') === 'chat') $this->backChat = (int)($_GET['backuid'] ?? 0);

        $this->strTitle = '发送站内短信';
        $this->showTop($this->strTitle);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->doSend();
        } else {
            $this->showForm();
        }
        $this->showDown();
    }

    private function showForm() {
        $to = $_GET['to'] ?? '';
        $action = 'send.php?siteid=' . $this->siteid
            . ($this->backChat > 0 ? '&back=chat&backuid=' . $this->backChat : '');
        echo '<div class="subtitle">发送站内短信</div>';
        echo '<div class="content"><form method="post" action="' . $action . '">';
        echo '收件人:<input type="text" name="to" value="' . $this->ToHtm($to) . '" required/><br/>';
        echo '标题:<input type="text" name="title"/><br/>';
        echo '内容:<textarea name="content" rows="6" required></textarea><br/>';
        echo '<input type="submit" value="发送"/></form></div>';
        echo '<div><a href="index.php?siteid=' . $this->siteid . '">返回收件箱</a></div>';
    }

    private function doSend() {
        $db = $this->db;
        $uid = (int)$this->userSession->getUserId();
        $uname = $this->userSession->getUserName();

        $to = trim($_POST['to'] ?? '');
        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');
        if ($to === '' || $content === '') { $this->showForm(); echo '<div class="error">收件人和内容不能为空</div>'; return; }
        if (WapTool::checkSqlInject($to) || WapTool::checkSqlInject($content)) {
            $this->showForm(); echo '<div class="error">含非法字符</div>'; return;
        }
        // 防刷：提交间隔 + 每日发送上限
        if (!$this->userSession->checkSubmitLimit()) { $this->showForm(); echo '<div class="error">操作过于频繁，请稍后再试</div>'; return; }
        $todayCount = (int)$db->getScalar("SELECT COUNT(*) FROM WapMessage WHERE fromid=? AND DATE(addtime)=CURDATE()", [$uid]);
        if (KL_SendMSGCount > 0 && $todayCount >= KL_SendMSGCount) {
            $this->showForm(); echo '<div class="error">今日发信已达上限</div>'; return;
        }

        $touser = $db->getOne("SELECT id,username FROM WapUser WHERE username=?", [$to]);
        if (!$touser) { $this->showForm(); echo '<div class="error">收件人不存在</div>'; return; }
        if ((int)$touser['id'] === $uid) { $this->showForm(); echo '<div class="error">不能发给自己</div>'; return; }
        if (kl_is_blocked($db, $this->siteid, $uid, (int)$touser['id'])) {
            $this->showForm(); echo '<div class="error">你与对方存在拉黑关系，无法发送</div>'; return;
        }

        $db->insert(
            "INSERT INTO WapMessage (siteid,fromid,fromname,toid,toname,title,content,isread,addtime) VALUES (?,?,?,?,?,?,?,0,NOW())",
            [$this->siteid, $uid, $uname, (int)$touser['id'], $touser['username'], $title, $content]
        );
        $this->showSuccess('发送成功',
            $this->backChat > 0
                ? 'chat.php?siteid=' . $this->siteid . '&uid=' . $this->backChat
                : 'index.php?siteid=' . $this->siteid . '&box=out');
    }
}

$page = new MessageSendPage();
$page->run();
