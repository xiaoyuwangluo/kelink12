<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 小说模块 - 章节列表/阅读 (对应原 Novel/view.aspx + Book_View.aspx)
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class NovelViewPage extends WapPage {
    public function run() {
        $db = $this->db;
        $id = (int)($_GET['id'] ?? 0);
        $cid = (int)($_GET['cid'] ?? 0); // 章节ID

        if ($id <= 0) $this->showError('参数错误');
        $novel = $db->getOne("SELECT * FROM WapNovel WHERE id=? AND siteid=?", [$id, $this->siteid]);
        if (!$novel) $this->showError('小说不存在');

        $db->execute("UPDATE WapNovel SET hits=hits+1 WHERE id=?", [$id]);

        if ($cid > 0) {
            // 阅读章节
            $chapter = $db->getOne("SELECT * FROM WapNovelChapter WHERE id=? AND novelid=?", [$cid, $id]);
            if (!$chapter) $this->showError('章节不存在');
            $this->strTitle = $chapter['title'];
            $this->showTop($this->strTitle);
            echo '<div class="subtitle">' . $this->ToHtm($chapter['title']) . '</div>';
            echo '<div class="content">' . nl2br($this->ToHtm($chapter['content'])) . '</div>';
            // 上一章/下一章
            $prev = $db->getOne("SELECT id,title FROM WapNovelChapter WHERE novelid=? AND id<? ORDER BY id DESC LIMIT 1", [$id, $cid]);
            $next = $db->getOne("SELECT id,title FROM WapNovelChapter WHERE novelid=? AND id>? ORDER BY id ASC LIMIT 1", [$id, $cid]);
            echo '<div class="prevnext">';
            if ($prev) echo '<a href="view.php?siteid=' . $this->siteid . '&classid=' . $this->classid . '&id=' . $id . '&cid=' . $prev['id'] . '">上一章</a> ';
            echo '<a href="view.php?siteid=' . $this->siteid . '&classid=' . $this->classid . '&id=' . $id . '">目录</a> ';
            if ($next) echo '<a href="view.php?siteid=' . $this->siteid . '&classid=' . $this->classid . '&id=' . $id . '&cid=' . $next['id'] . '">下一章</a>';
            echo '</div>';
        } else {
            // 章节列表
            $this->strTitle = $novel['title'];
            $this->showTop($this->strTitle);
            echo '<div class="subtitle">' . $this->ToHtm($novel['title']) . '</div>';
            echo '<div class="info">作者:' . $this->ToHtm($novel['author'] ?? '佚名') . ' 点击:' . (int)$novel['hits'] . '</div>';
            echo '<div class="content">' . $this->wapTool->ToWML($novel['intro'], $this->wmlVo) . '</div>';
            // 章节列表 (按排序值+ID排序)
            $chapters = $db->getAll("SELECT id,title FROM WapNovelChapter WHERE novelid=? ORDER BY orderindex ASC, id ASC", [$id]);
            // 管理员或小说作者(author 匹配当前会员用户名/昵称)可管理章节
            $canManage = $this->userSession->isAdminLogin();
            if (!$canManage && $this->userSession->isLogin()) {
                $author = trim((string)($novel['author'] ?? ''));
                $name = $this->userSession->getUserName();
                $nick = (string)($_SESSION['kl_nickname'] ?? '');
                $canManage = $author !== '' && ($author === $name || ($nick !== '' && $author === $nick));
            }
            echo '<div class="chapterlist"><ul>';
            foreach ($chapters as $c) {
                echo '<li><a href="view.php?siteid=' . $this->siteid . '&classid=' . $this->classid . '&id=' . $id . '&cid=' . $c['id'] . '">' . $this->ToHtm($c['title']) . '</a>';
                if ($canManage) {
                    echo ' <a href="chapter_mod.php?siteid=' . $this->siteid . '&classid=' . $this->classid . '&cid=' . (int)$c['id'] . '">修改</a>'
                       . ' <a href="chapter_del.php?siteid=' . $this->siteid . '&classid=' . $this->classid . '&cid=' . (int)$c['id'] . '">删除</a>';
                }
                echo '</li>';
            }
            echo '</ul></div>';
            if ($canManage) {
                echo '<div class="actionbar">'
                    . '<a href="chapter_add.php?siteid=' . $this->siteid . '&classid=' . $this->classid . '&novelid=' . $id . '">添加章节</a> '
                    . '<a href="import.php?siteid=' . $this->siteid . '&classid=' . $this->classid . '&novelid=' . $id . '">导入TXT</a></div>';
            }
        }
        echo '<div><a href="list.php?siteid=' . $this->siteid . '&classid=' . $this->classid . '">返回书单</a></div>';
        $this->showDown();
    }
}

$page = new NovelViewPage();
$page->run();
