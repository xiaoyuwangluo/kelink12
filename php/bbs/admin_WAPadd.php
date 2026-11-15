<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 论坛模块 - 发表帖子 (对应原 bbs/admin_WAPadd.aspx)
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class BbsAddPage extends WapPage {
    public function run() {
        $this->checkLogin();
        $this->showTop('发表帖子');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->doAdd();
        } else {
            $this->showForm();
        }
        $this->showDown();
    }

    private function showForm() {
        echo '<div class="subtitle">发表帖子</div>';
        echo '<div class="content"><form method="post" action="admin_WAPadd.php?siteid=' . $this->siteid . '&classid=' . $this->classid . '">';
        echo '标题:<input type="text" name="title" required/><br/>';
        echo '内容:<textarea name="content" rows="8" required></textarea><br/>';
        echo '<input type="submit" value="发表"/></form></div>';
    }

    private function doAdd() {
        $db = $this->db;
        $title = trim($_POST['title'] ?? '');
        $content = $_POST['content'] ?? '';
        if ($title === '' || $content === '') { echo '<div class="error">标题内容不能为空</div>'; $this->showForm(); return; }
        if (WapTool::checkSqlInject($title) || WapTool::checkSqlInject($content)) { echo '<div class="error">含非法字符</div>'; return; }
        if (!$this->userSession->checkSubmitLimit()) { echo '<div class="error">操作过于频繁</div>'; return; }

        $uid = $this->userSession->getUserId();
        $nick = $this->userSession->getUserName();

        // 每日发帖限制
        $todayCount = $db->getScalar("SELECT COUNT(*) FROM WapBbs WHERE userid=? AND DATE(addtime)=CURDATE()", [$uid]);
        if (KL_CheckBBSCount > 0 && $todayCount >= KL_CheckBBSCount) {
            echo '<div class="error">今日发帖已达上限</div>';
            return;
        }

        $id = $db->insert(
            "INSERT INTO WapBbs (siteid,classid,userid,nickname,title,content,istop,isgood,islock,isshow,addtime,hits,replynum) VALUES (?,?,?,?,?,?,0,0,0,1,NOW(),0,0)",
            [$this->siteid, $this->classid, $uid, $nick, $title, $content]
        );
        echo '<div class="success">发表成功 <a href="view.php?siteid=' . $this->siteid . '&classid=' . $this->classid . '&id=' . $id . '">查看</a></div>';
    }
}

$page = new BbsAddPage();
$page->run();
