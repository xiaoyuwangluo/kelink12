<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 论坛模块 - 帖子查看 (对应原 bbs/view.aspx)
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class BbsViewPage extends WapPage {
    public function run() {
        $db = $this->db;
        $id = (int)($_GET['id'] ?? 0);
        $page = max(1, (int)($_GET['page'] ?? 1));
        $pageSize = 20;

        if ($id <= 0) $this->showError('参数错误');

        $thread = $db->getOne("SELECT * FROM WapBbs WHERE id=? AND siteid=?", [$id, $this->siteid]);
        if (!$thread) $this->showError('帖子不存在');

        // 更新点击
        $db->execute("UPDATE WapBbs SET hits=hits+1 WHERE id=?", [$id]);

        $this->strTitle = $thread['title'];
        $this->showTop($this->strTitle);

        // 楼主
        echo '<div class="subtitle">' . $this->ToHtm($thread['title']) . '</div>';
        echo '<div class="post">';
        echo '<div class="poster">' . $this->ToHtm($thread['nickname'] ?? '楼主') . ' ' . date('Y-m-d H:i', strtotime($thread['addtime'])) . '</div>';
        echo '<div class="content">' . $this->wapTool->ToWML($thread['content'], $this->wmlVo) . '</div>';
        echo '</div>';

        // 回复列表
        $replys = $db->getAll(
            "SELECT * FROM WapBbsReply WHERE bbsid=? AND isshow=1 ORDER BY id ASC LIMIT " . (($page-1)*$pageSize) . ",$pageSize",
            [$id]
        );
        $total = $db->getScalar("SELECT COUNT(*) FROM WapBbsReply WHERE bbsid=? AND isshow=1", [$id]);
        $pageCount = ceil($total / $pageSize);

        echo '<div class="replylist">';
        $floor = ($page - 1) * $pageSize + 1;
        foreach ($replys as $r) {
            echo '<div class="reply">';
            echo '<div class="poster">#' . $floor . ' ' . $this->ToHtm($r['nickname'] ?? '游客') . ' ' . date('m-d H:i', strtotime($r['addtime'])) . '</div>';
            echo '<div class="content">' . $this->wapTool->ToWML($r['content'], $this->wmlVo) . '</div>';
            echo '</div>';
            $floor++;
        }
        echo '</div>';

        // 分页
        if ($pageCount > 1) {
            echo '<div class="pager">';
            if ($page > 1) echo '<a href="?siteid=' . $this->siteid . '&classid=' . $this->classid . '&id=' . $id . '&page=' . ($page-1) . '">上一页</a> ';
            echo '第' . $page . '/' . $pageCount . '页 ';
            if ($page < $pageCount) echo '<a href="?siteid=' . $this->siteid . '&classid=' . $this->classid . '&id=' . $id . '&page=' . ($page+1) . '">下一页</a>';
            echo '</div>';
        }

        // 回复/操作
        echo '<div class="actionbar">';
        if (!$thread['islock']) {
            echo '<a href="book_re.php?siteid=' . $this->siteid . '&classid=' . $this->classid . '&id=' . $id . '">回复</a> ';
        }
        echo '<a href="fav.php?siteid=' . $this->siteid . '&classid=' . $this->classid . '&module=bbs&id=' . $id . '">收藏</a> ';
        // 作者或管理员显示管理入口
        $isOwner = $this->userSession->isLogin() && (int)$thread['userid'] === (int)$this->userSession->getUserId();
        $isAdmin = !empty($_SESSION['kl_adminid']);
        if ($isOwner || $isAdmin) {
            echo '<a href="mod.php?siteid=' . $this->siteid . '&act=edit&id=' . $id . '">编辑</a> ';
            echo '<a href="mod.php?siteid=' . $this->siteid . '&act=top&id=' . $id . '">置顶</a> ';
            echo '<a href="mod.php?siteid=' . $this->siteid . '&act=good&id=' . $id . '">加精</a> ';
            echo '<a href="mod.php?siteid=' . $this->siteid . '&act=lock&id=' . $id . '">锁帖</a> ';
            echo '<a href="mod.php?siteid=' . $this->siteid . '&act=del&id=' . $id . '">删除</a> ';
        }
        echo '<a href="list.php?siteid=' . $this->siteid . '&classid=' . $this->classid . '">返回列表</a>';
        echo '</div>';

        // 帖子关联投票 (读 WapVote / WapVoteOption)
        $vote = $db->getOne("SELECT * FROM WapVote WHERE bbsid=? AND siteid=?", [$id, $this->siteid]);
        if ($vote) {
            $options = $db->getAll("SELECT * FROM WapVoteOption WHERE voteid=? ORDER BY id", [$vote['id']]);
            $vtotal = max(1, (int)$vote['total']);
            echo '<div class="vote">';
            echo '<div class="subtitle">' . $this->ToHtm($vote['question']) . '</div>';
            $canVote = $this->userSession->isLogin();
            echo '<form method="post" action="vote.php?siteid=' . $this->siteid . '&voteid=' . (int)$vote['id'] . '">';
            foreach ($options as $o) {
                $pct = round((int)$o['votes'] * 100 / $vtotal);
                echo '<label>';
                if ($canVote) echo '<input type="radio" name="optionid" value="' . (int)$o['id'] . '"/> ';
                echo $this->ToHtm($o['optionname']) . ' (' . (int)$o['votes'] . '票 ' . $pct . '%)';
                echo '</label><br/>';
            }
            echo '总票数:' . (int)$vote['total'];
            if ($canVote) {
                echo ' <input type="submit" value="投票"/>';
            } else {
                echo ' <a href="../waplogin.php?siteid=' . $this->siteid . '">登录后投票</a>';
            }
            echo '</form></div>';
        }

        $this->showDown();
    }
}

$page = new BbsViewPage();
$page->run();
