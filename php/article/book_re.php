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

        echo '<div class="ui-breadcrumb"><a href="' . $this->http_start . 'wapindex.php">首页</a> / <a href="view.php?classid=' . $this->classid . '&id=' . $id . '">' . $this->ToHtm($article['title']) . '</a> / 评论</div>';
        echo '<div class="subtitle">评论:' . $this->ToHtm($article['title']) . '</div>';

        // 发表评论
        echo '<div class="ui-card ui-mt-2">';
        echo '<form method="post" action="book_re.php?classid=' . $this->classid . '&id=' . $id . '">';
        echo '<div class="ui-field">';
        if ($this->userSession->isLogin()) {
            echo '<label>昵称</label><div class="ui-tag">' . $this->ToHtm($this->userSession->getUserName()) . '</div>';
        } else {
            echo '<label for="nickname">昵称</label><input type="text" id="nickname" name="nickname" required maxlength="20" placeholder="请输入昵称"/>';
        }
        echo '</div>';
        echo '<div class="ui-field"><label for="content">内容</label><textarea id="content" name="content" rows="3" required maxlength="1000" placeholder="友善评论，理性发言"></textarea><div class="hint">最多 1000 字，提交后立即显示</div></div>';
        echo '<input class="ui-btn ui-btn-primary ui-btn-block" type="submit" name="submit" value="发表评论"/>';
        echo '</form></div>';

        if (isset($_POST['submit'])) {
            $this->addReply($id);
        }

        // 评论列表
        $result = $db->getPage('WapReply', '*', "module='article' AND contentid=? AND isshow=1", 'id DESC', $page, $pageSize, [$id]);
        echo '<div class="ui-card ui-mt-3">';
        echo '<div class="ui-card-head"><span class="ui-card-title">全部评论</span><span class="ui-card-extra">共 ' . (int)$result['total'] . ' 条</span></div>';
        if (empty($result['list'])) {
            echo '<div class="ui-empty"><div class="icon">💬</div>还没有评论，来抢沙发</div>';
        } else {
            echo '<ul class="ui-list">';
            foreach ($result['list'] as $re) {
                echo '<li>';
                echo '<div class="ui-flex ui-between ui-center"><strong>' . $this->ToHtm($re['nickname'] ?? '游客') . '</strong>';
                echo '<span class="meta">' . date('m-d H:i', strtotime($re['addtime'])) . '</span></div>';
                echo '<div class="ui-mt-1">' . $this->wapTool->ToWML($re['content'], $this->wmlVo) . '</div>';
                echo '</li>';
            }
            echo '</ul>';
        }
        echo '</div>';

        // 分页
        if ($result['pageCount'] > 1) {
            echo '<div class="ui-pager">';
            if ($page > 1) echo '<a href="?classid=' . $this->classid . '&id=' . $id . '&page=' . ($page - 1) . '">上一页</a>';
            echo '<span class="current">' . $page . ' / ' . $result['pageCount'] . '</span>';
            if ($page < $result['pageCount']) echo '<a href="?classid=' . $this->classid . '&id=' . $id . '&page=' . ($page + 1) . '">下一页</a>';
            echo '</div>';
        }

        echo '<div class="ui-mt-2"><a class="ui-btn ui-btn-ghost" href="view.php?classid=' . $this->classid . '&id=' . $id . '">返回文章</a></div>';
        $this->showDown();
    }

    private function addReply($id) {
        $db = $this->db;
        // 检查游客是否可评论
        $isLogin = $this->userSession->isLogin();
        if (!$isLogin && KL_ARTRE_Anonymous_Open == 0) {
            echo '<div class="ui-alert ui-alert-danger">请先登录后再评论</div>';
            return;
        }
        $nickname = $isLogin ? $this->userSession->getUserName() : trim($_POST['nickname'] ?? '游客');
        $content = trim($_POST['content'] ?? '');
        if ($content === '') {
            echo '<div class="ui-alert ui-alert-danger">评论内容不能为空</div>';
            return;
        }
        if (WapTool::checkSqlInject($content)) {
            echo '<div class="ui-alert ui-alert-danger">内容包含非法字符</div>';
            return;
        }
        if (!$this->userSession->checkSubmitLimit()) {
            echo '<div class="ui-alert ui-alert-warning">操作过于频繁，请稍后再试</div>';
            return;
        }
        $uid = $this->userSession->getUserId();
        $db->insert("INSERT INTO WapReply (module,contentid,classid,userid,nickname,content,addtime,isshow) VALUES ('article',?,?,?,?,?,NOW(),1)",[$id,$this->classid,$uid,$nickname,$content]);
        echo '<div class="ui-alert ui-alert-success">评论发表成功</div>';
    }
}

$page = new ArticleReplyPage();
$page->run();
