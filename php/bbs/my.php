<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 论坛模块 - 我的帖子 / 我的回复 (作者自助管理)
 * 支持作者查看、编辑、隐藏(软删除)、恢复自己的帖子与隐藏自己的回复
 */
require_once __DIR__ . '/../includes/bootstrap.php';
require_once KL_ROOT . '/includes/Community.php';

class BbsMyPage extends WapPage {
    private $uid = 0;
    private $type = 'posts';
    private $myClassid = 0;

    public function run() {
        $db = $this->db;
        $this->checkLogin();
        $this->uid = (int)$this->userSession->getUserId();
        $this->type = (($_GET['type'] ?? 'posts') === 'replies') ? 'replies' : 'posts';
        $this->myClassid = (int)($_GET['classid'] ?? 0);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleAction();
        }

        $page = max(1, (int)($_GET['page'] ?? 1));
        $postCount = (int)$db->getScalar("SELECT COUNT(*) FROM WapBbs WHERE siteid=? AND userid=?",
            [$this->siteid, $this->uid]);
        $replyCount = (int)$db->getScalar("SELECT COUNT(*) FROM WapBbsReply WHERE siteid=? AND userid=?",
            [$this->siteid, $this->uid]);

        $this->strTitle = '我的帖子';
        $this->showTop($this->strTitle);
        echo '<div class="subtitle">我的帖子与回复</div>';

        $base = 'my.php?siteid=' . $this->siteid . ($this->myClassid > 0 ? '&classid=' . $this->myClassid : '');
        echo '<div class="menu">';
        echo $this->type === 'posts'
            ? '<strong>我的帖子(' . $postCount . ')</strong>'
            : '<a href="' . $base . '&type=posts">我的帖子(' . $postCount . ')</a>';
        echo ' | ';
        echo $this->type === 'replies'
            ? '<strong>我的回复(' . $replyCount . ')</strong>'
            : '<a href="' . $base . '&type=replies">我的回复(' . $replyCount . ')</a>';
        echo ' | <a href="list.php?siteid=' . $this->siteid . '&classid=' . ($this->myClassid > 0 ? $this->myClassid : 1) . '">返回论坛</a>';
        echo '</div>';

        $msg = trim((string)($_GET['msg'] ?? ''));
        $err = trim((string)($_GET['err'] ?? ''));
        if ($msg !== '') echo '<div class="success">' . $this->ToHtm($msg) . '</div>';
        if ($err !== '') echo '<div class="error">' . $this->ToHtm($err) . '</div>';

        if ($this->type === 'posts') {
            $this->renderPosts($page, $base);
        } else {
            $this->renderReplies($page, $base);
        }

        $this->showDown();
    }

    /** 帖子列表 */
    private function renderPosts($page, $base) {
        $db = $this->db;
        $result = $db->getPage('WapBbs', 'id,classid,title,isshow,replynum,hits,addtime',
            'siteid=? AND userid=?', 'id DESC', $page, 20, [$this->siteid, $this->uid]);

        if (empty($result['list'])) {
            echo '<div class="content">你还没有发布过帖子</div>';
        } else {
            // 批量取待审状态，避免 N+1
            $ids = [];
            foreach ($result['list'] as $p) $ids[] = (int)$p['id'];
            $pending = [];
            if ($ids) {
                $in = implode(',', $ids);
                foreach ($db->getAll("SELECT contentid FROM WapAudit WHERE siteid=? AND module='bbs' AND status=0 AND contentid IN ($in)",
                    [$this->siteid]) as $a) {
                    $pending[(int)$a['contentid']] = 1;
                }
            }

            echo '<div class="content"><ul class="list">';
            foreach ($result['list'] as $p) {
                $pid = (int)$p['id'];
                $cid = (int)$p['classid'];
                $isPending = isset($pending[$pid]);
                $hidden = ((int)$p['isshow'] === 0);
                $status = $isPending ? '待审核' : ($hidden ? '已隐藏' : '已发布');

                echo '<li><span class="info">[' . $status . ']</span> ';
                echo '<a href="view.php?siteid=' . $this->siteid . '&classid=' . $cid . '&id=' . $pid . '">'
                    . $this->ToHtm($p['title']) . '</a>';
                echo ' <span class="info">回' . (int)$p['replynum'] . '/阅' . (int)$p['hits'] . '</span>';
                echo ' <span class="time">' . date('m-d H:i', strtotime($p['addtime'])) . '</span>';
                echo '<div class="actionbar">';
                echo '<a href="mod.php?siteid=' . $this->siteid . '&act=edit&id=' . $pid . '">编辑</a> ';
                if ($isPending) {
                    echo '<span class="info">审核中不可操作</span>';
                } else {
                    echo $this->actionForm($hidden ? 'restore' : 'hide', $pid, $hidden ? '恢复' : '隐藏');
                }
                echo '</div></li>';
            }
            echo '</ul></div>';
        }
        $this->renderPager($result, $page, $base, 'posts');
    }

    /** 回复列表 */
    private function renderReplies($page, $base) {
        $db = $this->db;
        $result = $db->getPage('WapBbsReply', 'id,bbsid,content,isshow,addtime',
            'siteid=? AND userid=?', 'id DESC', $page, 20, [$this->siteid, $this->uid]);

        if (empty($result['list'])) {
            echo '<div class="content">你还没有发表过回复</div>';
        } else {
            // 批量取所属帖子标题
            $ids = [];
            foreach ($result['list'] as $r) $ids[] = (int)$r['bbsid'];
            $titles = [];
            if ($ids) {
                $in = implode(',', array_unique($ids));
                foreach ($db->getAll("SELECT id,title,classid FROM WapBbs WHERE id IN ($in)") as $t) {
                    $titles[(int)$t['id']] = $t;
                }
            }

            echo '<div class="content"><ul class="list">';
            foreach ($result['list'] as $r) {
                $rid = (int)$r['id'];
                $bid = (int)$r['bbsid'];
                $thread = $titles[$bid] ?? null;
                $excerpt = mb_substr(trim(strip_tags((string)$r['content'])), 0, 40);
                if ($excerpt === '') $excerpt = '(无内容)';

                echo '<li>';
                if ($thread) {
                    echo '<a href="view.php?siteid=' . $this->siteid . '&classid=' . (int)$thread['classid'] . '&id=' . $bid . '">'
                        . $this->ToHtm($thread['title']) . '</a> ';
                } else {
                    echo '<span class="info">[帖子已删除]</span> ';
                }
                echo (int)$r['isshow'] === 1 ? '<span class="info">[已显示]</span>' : '<span class="info">[已隐藏]</span>';
                echo '<div class="content">' . $this->ToHtm($excerpt) . '</div>';
                echo '<span class="time">' . date('m-d H:i', strtotime($r['addtime'])) . '</span>';
                if ((int)$r['isshow'] === 1) {
                    echo ' ' . $this->actionForm('hidereply', $rid, '隐藏');
                }
                echo '</li>';
            }
            echo '</ul></div>';
        }
        $this->renderPager($result, $page, $base, 'replies');
    }

    /** 操作按钮（POST，避免误触与链接抓取） */
    private function actionForm($act, $id, $label) {
        return '<form method="post" action="my.php?siteid=' . $this->siteid . '&type=' . $this->type
            . ($this->myClassid > 0 ? '&classid=' . $this->myClassid : '') . '" style="display:inline">'
            . '<input type="hidden" name="act" value="' . $this->ToHtm($act) . '"/>'
            . '<input type="hidden" name="id" value="' . (int)$id . '"/>'
            . '<input type="submit" value="' . $this->ToHtm($label) . '"/></form>';
    }

    /** 分页 */
    private function renderPager($result, $page, $base, $type) {
        if ($result['pageCount'] <= 1) return;
        echo '<div class="pager">';
        if ($page > 1) echo '<a href="' . $base . '&type=' . $type . '&page=' . ($page - 1) . '">上一页</a> ';
        echo '第' . $page . '/' . $result['pageCount'] . '页 ';
        if ($page < $result['pageCount']) echo '<a href="' . $base . '&type=' . $type . '&page=' . ($page + 1) . '">下一页</a>';
        echo '</div>';
    }

    /** 处理隐藏/恢复/隐藏回复 */
    private function handleAction() {
        $db = $this->db;
        $act = trim((string)($_POST['act'] ?? ''));
        $id = (int)($_POST['id'] ?? 0);
        $back = 'my.php?siteid=' . $this->siteid . '&type=' . $this->type
            . ($this->myClassid > 0 ? '&classid=' . $this->myClassid : '');

        if ($id <= 0 || !in_array($act, ['hide', 'restore', 'hidereply'], true)) {
            $this->redirect($back, '', '参数错误');
        }

        if ($act === 'hidereply') {
            $reply = $db->getOne("SELECT id FROM WapBbsReply WHERE id=? AND siteid=? AND userid=?",
                [$id, $this->siteid, $this->uid]);
            if (!$reply) $this->redirect($back, '', '回复不存在或无权操作');
            $db->execute("UPDATE WapBbsReply SET isshow=0 WHERE id=? AND userid=?", [$id, $this->uid]);
            kl_cache_clear_site($this->siteid);
            $this->redirect($back, '回复已隐藏');
        }

        $post = $db->getOne("SELECT id,isshow FROM WapBbs WHERE id=? AND siteid=? AND userid=?",
            [$id, $this->siteid, $this->uid]);
        if (!$post) $this->redirect($back, '', '帖子不存在或无权操作');

        if ($act === 'hide') {
            $db->execute("UPDATE WapBbs SET isshow=0 WHERE id=? AND userid=?", [$id, $this->uid]);
            kl_cache_clear_site($this->siteid);
            $this->redirect($back, '帖子已隐藏');
        }

        // restore: 审核中的内容不允许自行恢复
        $pending = $db->getOne("SELECT id FROM WapAudit WHERE siteid=? AND module='bbs' AND contentid=? AND status=0",
            [$this->siteid, $id]);
        if ($pending) $this->redirect($back, '', '该帖子正在审核中，暂不能恢复');
        $db->execute("UPDATE WapBbs SET isshow=1 WHERE id=? AND userid=?", [$id, $this->uid]);
        kl_cache_clear_site($this->siteid);
        $this->redirect($back, '帖子已恢复');
    }

    /** 跳转并携带提示 */
    private function redirect($back, $msg = '', $err = '') {
        $url = $back;
        if ($msg !== '') $url .= '&msg=' . urlencode($msg);
        if ($err !== '') $url .= '&err=' . urlencode($err);
        header('Location: ' . $url);
        exit;
    }
}

$page = new BbsMyPage();
$page->run();
