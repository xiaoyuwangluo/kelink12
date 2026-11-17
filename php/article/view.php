<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 文章模块 - 内容页 (对应原 article/view.aspx)
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class ArticleViewPage extends WapPage {
    public function run() {
        $db = $this->db;
        $id = (int)($_GET['id'] ?? 0);

        if ($id <= 0) {
            $this->showError('文章不存在', 'list.php?classid=' . $this->classid);
        }

        $article = $db->getOne("SELECT * FROM WapArticle WHERE id=?",[$id]);
        if (!$article) {
            $this->showError('文章不存在或已被删除', 'list.php?classid=' . $this->classid);
        }

        // 更新点击量
        $db->execute("UPDATE WapArticle SET hits=hits+1 WHERE id=?", [$id]);

        $this->strTitle = $article['title'];
        $this->showTop($this->strTitle);

        echo '<div class="ui-breadcrumb"><a href="' . $this->http_start . 'wapindex.php">首页</a> / <a href="list.php?classid=' . $this->classid . '">' . $this->ToHtm($this->classVo->classname ?? '文章') . '</a> / 正文</div>';
        echo '<div class="subtitle">' . $this->ToHtm($article['title']) . '</div>';
        echo '<div class="content ui-card">';
        echo '<div class="ui-flex ui-mb-2" style="gap:8px;flex-wrap:wrap">';
        echo '<span class="ui-tag">作者：' . $this->ToHtm($article['author'] ?? '佚名') . '</span>';
        echo '<span class="ui-tag ui-tag-muted">' . date('Y-m-d H:i', strtotime($article['addtime'])) . '</span>';
        echo '<span class="ui-tag ui-tag-muted">点击 ' . (int)$article['hits'] . '</span>';
        echo '</div>';
        echo $this->wapTool->ToWML($article['content'], $this->wmlVo);
        echo '</div>';

        // 下载附件
        if (!empty($article['downurl'])) {
            echo '<div class="download"><a href="' . $this->http_start . 'down.php?id=' . $id . '">下载附件</a></div>';
        }

        // 上一条/下一条
        $this->showPrevNext($id);

        // 回复入口
        echo '<div class="replybar ui-mt-3">';
        echo '<a class="ui-btn ui-btn-primary" href="book_re.php?classid=' . $this->classid . '&id=' . $id . '">查看/发表评论</a>';
        echo '<a class="ui-btn ui-btn-ghost" href="list.php?classid=' . $this->classid . '">返回列表</a>';
        echo '</div>';

        $this->showDown();
    }

    private function showPrevNext($id) {
        if (KL_ShowPreNextTitle_article == 1) return;
        $db = $this->db;
        $prev = $db->getOne("SELECT id,title FROM WapArticle WHERE id<? AND classid=? ORDER BY id DESC LIMIT 1", [$id, $this->classid]);
        $next = $db->getOne("SELECT id,title FROM WapArticle WHERE id>? AND classid=? ORDER BY id ASC LIMIT 1", [$id, $this->classid]);
        echo '<div class="prevnext">';
        if ($prev) {
            echo '上一条:<a href="view.php?classid=' . $this->classid . '&id=' . $prev['id'] . '">' . $this->ToHtm($prev['title']) . '</a><br/>';
        }
        if ($next) {
            echo '下一条:<a href="view.php?classid=' . $this->classid . '&id=' . $next['id'] . '">' . $this->ToHtm($next['title']) . '</a>';
        }
        echo '</div>';
    }
}

$page = new ArticleViewPage();
$page->run();
