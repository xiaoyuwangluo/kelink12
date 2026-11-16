<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 小说模块 - 添加章节 (管理员或小说作者可操作)
 * 注：WapNovel 无 userid 列，"小说作者"以 author 字段匹配当前会员用户名/昵称判定
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class NovelChapterAddPage extends WapPage {
    public function run() {
        // 鉴权：后台管理员(kl_adminid)直接放行，否则走会员登录校验（未登录跳转）
        if (!$this->userSession->isAdminLogin()) {
            $this->checkLogin();
        }
        $db = $this->db;
        $novelid = (int)($_GET['novelid'] ?? 0);
        if ($novelid <= 0) $this->showError('参数错误');

        $novel = $db->getOne("SELECT * FROM WapNovel WHERE id=?", [$novelid]);
        if (!$novel) $this->showError('小说不存在');
        $this->checkAuthorAuth($novel);

        $this->showTop('添加章节');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->doAdd($novel);
        } else {
            $this->showForm($novel);
        }
        $this->showDown();
    }

    /** 鉴权：仅管理员或小说作者(author 匹配当前会员用户名/昵称)可操作 */
    private function checkAuthorAuth($novel) {
        if ($this->userSession->isAdminLogin()) return;
        $author = trim((string)($novel['author'] ?? ''));
        $name = $this->userSession->getUserName();
        $nick = (string)($_SESSION['kl_nickname'] ?? '');
        if ($author !== '' && ($author === $name || ($nick !== '' && $author === $nick))) return;
        $this->showError('无权限：仅管理员或小说作者可操作该小说',
            'view.php?classid=' . (int)$novel['classid'] . '&id=' . (int)$novel['id']);
    }

    private function showForm($novel) {
        echo '<div class="subtitle">添加章节：' . $this->ToHtm($novel['title']) . '</div>';
        echo '<form method="post" action="chapter_add.php?classid=' . (int)$novel['classid'] . '&novelid=' . (int)$novel['id'] . '">';
        echo '章节标题:<input type="text" name="title" required/><br/>';
        echo '排序值:<input type="number" name="orderindex" value="0"/><br/>';
        echo '内容:<textarea name="content" rows="15"></textarea><br/>';
        echo '<input type="submit" value="提交章节"/></form>';
        echo '<div><a href="view.php?classid=' . (int)$novel['classid'] . '&id=' . (int)$novel['id'] . '">返回目录</a></div>';
    }

    private function doAdd($novel) {
        $db = $this->db;
        $title = trim($_POST['title'] ?? '');
        $content = (string)($_POST['content'] ?? '');
        $orderindex = (int)($_POST['orderindex'] ?? 0);
        if ($title === '') { echo '<div class="error">章节标题不能为空</div>'; $this->showForm($novel); return; }
        if (!$this->userSession->checkSubmitLimit()) { echo '<div class="error">操作过于频繁</div>'; return; }

        $cid = $db->insert(
            "INSERT INTO WapNovelChapter (novelid,title,content,orderindex,addtime) VALUES (?,?,?,?,NOW())",
            [$novel['id'], $title, $content, $orderindex]
        );
        echo '<div class="success">章节添加成功 <a href="view.php?classid=' . (int)$novel['classid'] . '&id=' . (int)$novel['id'] . '&cid=' . $cid . '">查看本章</a> '
            . '<a href="view.php?classid=' . (int)$novel['classid'] . '&id=' . (int)$novel['id'] . '">返回目录</a></div>';
    }
}

$page = new NovelChapterAddPage();
$page->run();
