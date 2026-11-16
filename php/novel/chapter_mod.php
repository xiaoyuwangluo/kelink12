<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 小说模块 - 修改章节 (管理员或小说作者可操作，仿 admin_WAPmodify 风格)
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class NovelChapterModifyPage extends WapPage {
    public function run() {
        // 鉴权：后台管理员(kl_adminid)直接放行，否则走会员登录校验（未登录跳转）
        if (!$this->userSession->isAdminLogin()) {
            $this->checkLogin();
        }
        $db = $this->db;
        $cid = (int)($_GET['cid'] ?? 0);
        if ($cid <= 0) $this->showError('参数错误');

        $chapter = $db->getOne("SELECT * FROM WapNovelChapter WHERE id=?", [$cid]);
        if (!$chapter) $this->showError('章节不存在');
        $novel = $db->getOne("SELECT * FROM WapNovel WHERE id=?", [$chapter['novelid']]);
        if (!$novel) $this->showError('小说不存在');
        $this->checkAuthorAuth($novel);

        $this->showTop('修改章节');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->doModify($chapter);
        } else {
            $this->showForm($novel, $chapter);
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

    private function showForm($novel, $chapter) {
        echo '<div class="subtitle">修改章节：' . $this->ToHtm($novel['title']) . '</div>';
        echo '<form method="post" action="chapter_mod.php?classid=' . (int)$novel['classid'] . '&cid=' . (int)$chapter['id'] . '">';
        echo '章节标题:<input type="text" name="title" value="' . $this->ToHtm($chapter['title']) . '"/><br/>';
        echo '排序值:<input type="number" name="orderindex" value="' . (int)($chapter['orderindex'] ?? 0) . '"/><br/>';
        echo '内容:<textarea name="content" rows="15">' . $this->ToHtm($chapter['content'] ?? '') . '</textarea><br/>';
        echo '<input type="submit" value="保存修改"/></form>';
        echo '<div><a href="view.php?classid=' . (int)$novel['classid'] . '&id=' . (int)$novel['id'] . '">返回目录</a></div>';
    }

    private function doModify($chapter) {
        $db = $this->db;
        $title = trim($_POST['title'] ?? '');
        $content = (string)($_POST['content'] ?? '');
        $orderindex = (int)($_POST['orderindex'] ?? 0);
        if ($title === '') { echo '<div class="error">章节标题不能为空</div>'; return; }

        $db->execute("UPDATE WapNovelChapter SET title=?,content=?,orderindex=? WHERE id=?",
            [$title, $content, $orderindex, $chapter['id']]);
        echo '<div class="success">修改成功 <a href="view.php?classid=' . (int)$this->classid . '&id=' . (int)$chapter['novelid'] . '&cid=' . (int)$chapter['id'] . '">查看本章</a></div>';
    }
}

$page = new NovelChapterModifyPage();
$page->run();
