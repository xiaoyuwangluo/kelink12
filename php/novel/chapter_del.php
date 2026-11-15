<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 小说模块 - 删除章节 (管理员或小说作者可操作，仿 admin_WAPdel 风格：GET确认 POST执行)
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class NovelChapterDelPage extends WapPage {
    public function run() {
        // 鉴权：后台管理员(kl_adminid)直接放行，否则走会员登录校验（未登录跳转）
        if (!$this->userSession->isAdminLogin()) {
            $this->checkLogin();
        }
        $db = $this->db;
        $cid = (int)($_GET['cid'] ?? 0);
        if ($cid <= 0) $this->showError('参数错误');

        $chapter = $db->getOne("SELECT id,novelid,title FROM WapNovelChapter WHERE id=?", [$cid]);
        if (!$chapter) $this->showError('章节不存在');
        $novel = $db->getOne("SELECT id,classid,title,author FROM WapNovel WHERE id=?", [$chapter['novelid']]);
        if (!$novel) $this->showError('小说不存在');
        $this->checkAuthorAuth($novel);

        $this->showTop('删除章节');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $db->execute("DELETE FROM WapNovelChapter WHERE id=?", [$cid]);
            echo '<div class="success">删除成功 <a href="view.php?siteid=' . $this->siteid . '&classid=' . (int)$novel['classid'] . '&id=' . (int)$novel['id'] . '">返回目录</a></div>';
        } else {
            echo '<div class="subtitle">确认删除章节？</div>';
            echo '<div class="content">小说：' . $this->ToHtm($novel['title']) . '<br/>章节：' . $this->ToHtm($chapter['title']) . '</div>';
            echo '<form method="post" action="chapter_del.php?siteid=' . $this->siteid . '&classid=' . (int)$novel['classid'] . '&cid=' . $cid . '">';
            echo '<input type="submit" value="确认删除"/></form>';
            echo '<a href="view.php?siteid=' . $this->siteid . '&classid=' . (int)$novel['classid'] . '&id=' . (int)$novel['id'] . '">取消返回</a>';
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
            'view.php?siteid=' . $this->siteid . '&classid=' . (int)$novel['classid'] . '&id=' . (int)$novel['id']);
    }
}

$page = new NovelChapterDelPage();
$page->run();
