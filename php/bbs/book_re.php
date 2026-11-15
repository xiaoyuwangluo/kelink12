<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 论坛模块 - 回复帖子 (对应原 bbs/Book_Re.aspx)
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class BbsReplyPage extends WapPage {
    public function run() {
        $this->showTop('回复帖子');
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) $this->showError('参数错误');

        $db = $this->db;
        $thread = $db->getOne("SELECT id,title,islock FROM WapBbs WHERE id=?", [$id]);
        if (!$thread) $this->showError('帖子不存在');
        if ($thread['islock']) $this->showError('帖子已锁定，不能回复');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->doReply($id);
        }

        echo '<div class="subtitle">回复:' . $this->ToHtm($thread['title']) . '</div>';
        echo '<div class="content"><form method="post" action="book_re.php?siteid=' . $this->siteid . '&classid=' . $this->classid . '&id=' . $id . '">';
        if ($this->userSession->isLogin()) {
            echo '昵称:' . $this->ToHtm($this->userSession->getUserName()) . '<br/>';
        } else {
            if (KL_BBSRE_Anonymous_Open == 0) {
                echo '<div class="error">请先登录</div>';
                $this->showDown();
                return;
            }
            echo '昵称:<input type="text" name="nickname"/><br/>';
        }
        echo '内容:<textarea name="content" rows="5" required></textarea><br/>';
        echo '<input type="submit" value="发表回复"/></form></div>';

        echo '<div><a href="view.php?siteid=' . $this->siteid . '&classid=' . $this->classid . '&id=' . $id . '">返回帖子</a></div>';
        $this->showDown();
    }

    private function doReply($id) {
        $db = $this->db;
        $isLogin = $this->userSession->isLogin();
        if (!$isLogin && KL_BBSRE_Anonymous_Open == 0) {
            echo '<div class="error">游客不能回复，请登录</div>';
            return;
        }
        $nickname = $isLogin ? $this->userSession->getUserName() : trim($_POST['nickname'] ?? '游客');
        $content = trim($_POST['content'] ?? '');
        if ($content === '') { echo '<div class="error">内容不能为空</div>'; return; }
        if (WapTool::checkSqlInject($content)) { echo '<div class="error">含非法字符</div>'; return; }
        if (!$this->userSession->checkSubmitLimit()) { echo '<div class="error">操作过于频繁</div>'; return; }

        // 每日回帖限制
        $uid = $this->userSession->getUserId();
        $todayCount = $db->getScalar(
            "SELECT COUNT(*) FROM WapBbsReply WHERE userid=? AND DATE(addtime)=CURDATE()",
            [$uid]
        );
        if (KL_CheckBBSreCount > 0 && $todayCount >= KL_CheckBBSreCount) {
            echo '<div class="error">今日回帖已达上限</div>';
            return;
        }

        $db->insert(
            "INSERT INTO WapBbsReply (bbsid,siteid,classid,userid,nickname,content,addtime,isshow) VALUES (?,?,?,?,?,?,NOW(),1)",
            [$id, $this->siteid, $this->classid, $uid, $nickname, $content]
        );
        $db->execute("UPDATE WapBbs SET replynum=replynum+1 WHERE id=?", [$id]);
        echo '<div class="success">回复成功</div>';
    }
}

$page = new BbsReplyPage();
$page->run();
