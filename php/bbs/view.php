<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 论坛模块 - 帖子查看 (对应原 bbs/view.aspx)
 */
require_once __DIR__ . '/../includes/bootstrap.php';
require_once KL_ROOT . '/includes/Community.php';
require_once KL_ROOT . '/includes/Level.php';

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
        $threadUid = (int)$thread['userid'];
        $authorName = trim((string)($thread['nickname'] ?? ''));
        $authorPoint = 0;
        if ($threadUid > 0) {
            $author = $db->getOne("SELECT username,nickname,point FROM WapUser WHERE id=?", [$threadUid]);
            if ($author) {
                $authorPoint = (int)$author['point'];
                if ($authorName === '') {
                    $authorName = $author['nickname'] !== '' ? $author['nickname'] : $author['username'];
                }
            }
        }
        if ($authorName === '') $authorName = '楼主';

        echo '<div class="subtitle">' . $this->ToHtm($thread['title']) . '</div>';
        echo '<div class="post">';
        echo '<div class="poster">';
        if ($threadUid > 0) {
            echo '<a href="../zone/index.php?siteid=' . $this->siteid . '&uid=' . $threadUid . '">'
                . $this->ToHtm($authorName) . '</a>';
        } else {
            echo $this->ToHtm($authorName);
        }
        echo ' ' . kl_level_tag_html($authorPoint);
        echo ' ' . date('Y-m-d H:i', strtotime($thread['addtime'])) . '</div>';
        echo '<div class="content">' . $this->wapTool->ToWML($thread['content'], $this->wmlVo) . '</div>';
        echo '</div>';

        // 帖子标签
        $threadTags = $db->getAll("SELECT tagname FROM WapBbsTag WHERE siteid=? AND bbsid=? ORDER BY id", [$this->siteid, $id]);
        if ($threadTags) {
            echo '<div class="tags">标签：';
            foreach ($threadTags as $tt) {
                $tname = (string)$tt['tagname'];
                echo '<a href="tag.php?siteid=' . $this->siteid . '&name=' . rawurlencode($tname) . '">#'
                    . $this->ToHtm($tname) . '#</a> ';
            }
            echo '</div>';
        }

        // 主帖点赞
        $likeCount = kl_like_count($db, $this->siteid, 'bbs', $id, 0);
        $isLogin = $this->userSession->isLogin();
        $meLiked = $isLogin ? kl_like_exists($db, $this->siteid, 'bbs', $id, 0, (int)$this->userSession->getUserId()) : false;
        echo '<div class="postlike">赞(' . $likeCount . ') ';
        if ($isLogin) {
            echo '<a href="like.php?siteid=' . $this->siteid . '&id=' . $id . '&replyid=0&act='
                . ($meLiked ? 'unlike' : 'like') . '">' . ($meLiked ? '取消赞' : '赞') . '</a>';
        } else {
            echo '<a href="../waplogin.php?siteid=' . $this->siteid . '">登录后点赞</a>';
        }
        echo '</div>';

        // 帖子图片附件 (WapBbsFile)
        $bbsFiles = $db->getAll("SELECT * FROM WapBbsFile WHERE bbsid=? AND siteid=? ORDER BY id", [$id, $this->siteid]);
        if ($bbsFiles) {
            echo '<div class="content"><div class="time">图片附件:</div>';
            foreach ($bbsFiles as $bf) {
                $url = '../uploadfiles/' . implode('/', array_map('rawurlencode', explode('/', $bf['filepath'])));
                $ext = strtolower(pathinfo($bf['filepath'], PATHINFO_EXTENSION));
                if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'bmp'], true)) {
                    echo '<a href="' . $url . '" target="_blank"><img src="' . $url . '" alt="' . $this->ToHtm($bf['filename']) . '" style="max-width:100%"/></a> ';
                } else {
                    echo '<a href="' . $url . '" target="_blank">' . $this->ToHtm($bf['filename']) . '</a> ';
                }
            }
            echo '</div>';
        }

        // 回复列表
        $replys = $db->getAll(
            "SELECT * FROM WapBbsReply WHERE bbsid=? AND isshow=1 ORDER BY id ASC LIMIT " . (($page-1)*$pageSize) . ",$pageSize",
            [$id]
        );
        $total = $db->getScalar("SELECT COUNT(*) FROM WapBbsReply WHERE bbsid=? AND isshow=1", [$id]);
        $pageCount = ceil($total / $pageSize);

        // 批量取回复作者信息 (避免 N+1)
        $replyUids = [];
        foreach ($replys as $r) {
            $ru = (int)$r['userid'];
            if ($ru > 0) $replyUids[$ru] = 1;
        }
        $replyUserMap = [];
        if ($replyUids) {
            $in = implode(',', array_keys($replyUids));
            foreach ($db->getAll("SELECT id,username,nickname,point FROM WapUser WHERE id IN ($in)") as $u) {
                $replyUserMap[(int)$u['id']] = $u;
            }
        }

        // 批量取各回复点赞数与本用户点赞状态
        $replyLikeCounts = [];
        foreach ($db->getAll("SELECT replyid, COUNT(*) AS c FROM WapLike WHERE siteid=? AND module='bbs' AND contentid=? GROUP BY replyid",
            [$this->siteid, $id]) as $lc) {
            $replyLikeCounts[(int)$lc['replyid']] = (int)$lc['c'];
        }
        $myReplyLikes = [];
        if ($isLogin) {
            foreach ($db->getAll("SELECT replyid FROM WapLike WHERE siteid=? AND module='bbs' AND contentid=? AND userid=? AND replyid>0",
                [$this->siteid, $id, (int)$this->userSession->getUserId()]) as $ml) {
                $myReplyLikes[(int)$ml['replyid']] = 1;
            }
        }

        echo '<div class="replylist">';
        $floor = ($page - 1) * $pageSize + 1;
        foreach ($replys as $r) {
            $rUid = (int)$r['userid'];
            $rUser = $replyUserMap[$rUid] ?? null;
            $rName = trim((string)($r['nickname'] ?? ''));
            if ($rName === '' && $rUser) {
                $rName = $rUser['nickname'] !== '' ? $rUser['nickname'] : $rUser['username'];
            }
            if ($rName === '') $rName = '游客';
            $rPoint = $rUser ? (int)$rUser['point'] : 0;

            echo '<div class="reply">';
            echo '<div class="poster">#' . $floor . ' ';
            if ($rUid > 0) {
                echo '<a href="../zone/index.php?siteid=' . $this->siteid . '&uid=' . $rUid . '">'
                    . $this->ToHtm($rName) . '</a>';
            } else {
                echo $this->ToHtm($rName);
            }
            echo ' ' . kl_level_tag_html($rPoint) . ' ' . date('m-d H:i', strtotime($r['addtime'])) . '</div>';
            echo '<div class="content">' . $this->wapTool->ToWML($r['content'], $this->wmlVo) . '</div>';
            $rid = (int)$r['id'];
            $rcount = $replyLikeCounts[$rid] ?? 0;
            echo '<div class="replylike">赞(' . $rcount . ') ';
            if ($isLogin) {
                $rLiked = isset($myReplyLikes[$rid]);
                echo '<a href="like.php?siteid=' . $this->siteid . '&id=' . $id . '&replyid=' . $rid . '&act='
                    . ($rLiked ? 'unlike' : 'like') . '">' . ($rLiked ? '取消赞' : '赞') . '</a>';
            } else {
                echo '<a href="../waplogin.php?siteid=' . $this->siteid . '">登录后点赞</a>';
            }
            echo '</div>';
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
        if ($this->userSession->isLogin()) {
            echo '<a href="report.php?siteid=' . $this->siteid . '&classid=' . $this->classid . '&id=' . $id . '">举报</a> ';
        }
        // 作者或管理员显示管理入口
        $isOwner = $this->userSession->isLogin() && (int)$thread['userid'] === (int)$this->userSession->getUserId();
        $isAdmin = !empty($_SESSION['kl_adminid']);
        if ($isOwner || $isAdmin) {
            echo '<a href="mod.php?siteid=' . $this->siteid . '&act=edit&id=' . $id . '">编辑</a> ';
            if ($isAdmin) {
                echo '<a href="mod.php?siteid=' . $this->siteid . '&act=top&id=' . $id . '">置顶</a> ';
                echo '<a href="mod.php?siteid=' . $this->siteid . '&act=good&id=' . $id . '">加精</a> ';
                echo '<a href="mod.php?siteid=' . $this->siteid . '&act=lock&id=' . $id . '">锁帖</a> ';
            }
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
