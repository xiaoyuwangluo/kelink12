<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 论坛模块 - 回复帖子 (对应原 bbs/Book_Re.aspx)
 */
require_once __DIR__ . '/../includes/bootstrap.php';
require_once KL_ROOT . '/includes/Community.php';

class BbsReplyPage extends WapPage {
    public function run() {
        $this->showTop('回复帖子');
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) $this->showError('参数错误');

        $db = $this->db;
        $thread = $db->getOne("SELECT id,title,islock,userid FROM WapBbs WHERE id=?", [$id]);
        if (!$thread) $this->showError('帖子不存在');
        if ($thread['islock']) $this->showError('帖子已锁定，不能回复');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->doReply($id);
        }

        echo '<div class="subtitle">回复:' . $this->ToHtm($thread['title']) . '</div>';
        echo '<div class="content"><form method="post" action="book_re.php?classid=' . $this->classid . '&id=' . $id . '">';
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

        echo '<div><a href="view.php?classid=' . $this->classid . '&id=' . $id . '">返回帖子</a></div>';
        $this->showDown();
    }

    private function doReply($id) {
        $db = $this->db;
        // 取楼主 uid 用于回复通知
        $thread = $db->getOne("SELECT id,userid FROM WapBbs WHERE id=?", [$id]);
        if (!$thread) $thread = ['id' => $id, 'userid' => 0];
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

        // 敏感词检测：命中则回复进入待审核（isshow=0）并写审核记录
        $isSensitive = kl_has_sensitive($content, $db);
        $isshow = $isSensitive ? 0 : 1;

        $replyid = $db->insert("INSERT INTO WapBbsReply (bbsid,classid,userid,nickname,content,addtime,isshow) VALUES (?,?,?,?,?,NOW(),?)",[$id,$this->classid,$uid,$nickname,$content,$isshow]);
        if ($isSensitive) {
            kl_audit_add($db, $uid, 'bbs_re', $replyid, '', $content);
        }
        $db->execute("UPDATE WapBbs SET replynum=replynum+1 WHERE id=?", [$id]);

        // 通知楼主 (楼主=回复者则自动跳过)
        kl_notice_add($db, (int)$thread['userid'], $uid, 'reply', 'bbs', $id, '回复了你的帖子');

        // 解析 @昵称，最多 5 个，命中且非本人则写 mention 通知
        if (preg_match_all('/@([\p{Han}A-Za-z0-9_]{1,20})/u', $content, $m)) {
            $names = array_slice(array_unique($m[1]), 0, 5);
            foreach ($names as $name) {
                $target = $db->getOne("SELECT id FROM WapUser WHERE nickname=? OR username=? LIMIT 1",
                    [$name, $name]);
                if ($target && (int)$target['id'] !== (int)$uid
                    && !kl_is_blocked($db, (int)$target['id'], $uid)) {
                    kl_notice_add($db, (int)$target['id'], $uid, 'mention', 'bbs', $id, '在回复中提到了你');
                }
            }
        }

        kl_cache_clear_site();
        echo '<div class="success">回复成功' . ($isSensitive ? '，内容含敏感词，待管理员审核' : '') . '</div>';
    }
}

$page = new BbsReplyPage();
$page->run();
