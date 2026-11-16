<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 文章模块 - 评论/回复页 (对应原 article/Book_Re.aspx)
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class ArticleReplyPage extends WapPage {
    public function run() {
        $db = $this->db;
        $id = (int)($_GET['id'] ?? 0);
        $page = max(1, (int)($_GET['page'] ?? 1));
        $pageSize = 20;

        if ($id <= 0) $this->showError('参数错误');

        $article = $db->getOne("SELECT id,title FROM WapArticle WHERE id=?", [$id]);
        if (!$article) $this->showError('文章不存在');

        $this->strTitle = '评论:' . $article['title'];
        $this->showTop($this->strTitle);

        echo '<div class="subtitle">评论:' . $this->ToHtm($article['title']) . '</div>';

        // 发表评论
        echo '<div class="re_form">';
        echo '<form method="post" action="book_re.php?classid=' . $this->classid . '&id=' . $id . '">';
        if ($this->userSession->isLogin()) {
            echo '昵称:' . $this->ToHtm($this->userSession->getUserName()) . '<br/>';
        } else {
            echo '昵称:<input type="text" name="nickname"/><br/>';
        }
        echo '内容:<textarea name="content" rows="3" required></textarea><br/>';
        echo '<input type="submit" name="submit" value="发表评论"/>';
        echo '</form></div>';

        if (isset($_POST['submit'])) {
            $this->addReply($id);
        }

        // 评论列表
        $result = $db->getPage('WapReply', '*', "module='article' AND contentid=? AND isshow=1", 'id DESC', $page, $pageSize, [$id]);
        echo '<div class="content"><ul class="relist">';
        foreach ($result['list'] as $re) {
            echo '<li>';
            echo '<b>' . $this->ToHtm($re['nickname'] ?? '游客') . '</b> ';
            echo '<span class="time">' . date('m-d H:i', strtotime($re['addtime'])) . '</span><br/>';
            echo $this->wapTool->ToWML($re['content'], $this->wmlVo);
            echo '</li>';
        }
        echo '</ul></div>';

        // 分页
        if ($result['pageCount'] > 1) {
            echo '<div class="pager">';
            if ($page > 1) echo '<a href="?classid=' . $this->classid . '&id=' . $id . '&page=' . ($page - 1) . '">上一页</a> ';
            echo '第' . $page . '/' . $result['pageCount'] . '页 ';
            if ($page < $result['pageCount']) echo '<a href="?classid=' . $this->classid . '&id=' . $id . '&page=' . ($page + 1) . '">下一页</a>';
            echo '</div>';
        }

        echo '<div><a href="view.php?classid=' . $this->classid . '&id=' . $id . '">返回文章</a></div>';
        $this->showDown();
    }

    private function addReply($id) {
        $db = $this->db;
        // 检查游客是否可评论
        $isLogin = $this->userSession->isLogin();
        if (!$isLogin && KL_ARTRE_Anonymous_Open == 0) {
            echo '<div class="error">请先登录后再评论</div>';
            return;
        }
        $nickname = $isLogin ? $this->userSession->getUserName() : trim($_POST['nickname'] ?? '游客');
        $content = trim($_POST['content'] ?? '');
        if ($content === '') {
            echo '<div class="error">评论内容不能为空</div>';
            return;
        }
        if (WapTool::checkSqlInject($content)) {
            echo '<div class="error">内容包含非法字符</div>';
            return;
        }
        if (!$this->userSession->checkSubmitLimit()) {
            echo '<div class="error">操作过于频繁</div>';
            return;
        }
        $uid = $this->userSession->getUserId();
        $db->insert("INSERT INTO WapReply (module,contentid,classid,userid,nickname,content,addtime,isshow) VALUES ('article',?,?,?,?,?,NOW(),1)",[$id,$this->classid,$uid,$nickname,$content]);
        echo '<div class="success">评论发表成功</div>';
    }
}

$page = new ArticleReplyPage();
$page->run();
