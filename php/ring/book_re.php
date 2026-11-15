<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * ring模块 - 评论
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class RingReplyPage extends WapPage {
    public function run() {
        $db = $this->db;
        $id = (int)($_GET['id'] ?? 0);
        $page = max(1, (int)($_GET['page'] ?? 1));
        if ($id <= 0) $this->showError('参数错误');
        $item = $db->getOne("SELECT id,title FROM WapRing WHERE id=?", [$id]);
        if (!$item) $this->showError('内容不存在');
        $this->strTitle = '评论:' . $item['title'];
        $this->showTop($this->strTitle);
        echo '<div class="subtitle">评论:' . $this->ToHtm($item['title']) . '</div>';
        echo '<div class="re_form"><form method="post" action="book_re.php?siteid=' . $this->siteid . '&classid=' . $this->classid . '&id=' . $id . '">';
        if ($this->userSession->isLogin()) echo '昵称:' . $this->ToHtm($this->userSession->getUserName()) . '<br/>';
        else echo '昵称:<input type="text" name="nickname"/><br/>';
        echo '内容:<textarea name="content" rows="3" required></textarea><br/>';
        echo '<input type="submit" name="submit" value="发表评论"/></form></div>';
        if (isset($_POST['submit'])) $this->addReply($id);
        $result = $db->getPage('WapReply', '*', "module='ring' AND contentid=? AND isshow=1", 'id DESC', $page, 20, [$id]);
        echo '<div class="content"><ul class="relist">';
        foreach ($result['list'] as $re) {
            echo '<li><b>' . $this->ToHtm($re['nickname'] ?? '游客') . '</b> ' . date('m-d H:i', strtotime($re['addtime'])) . '<br/>';
            echo $this->wapTool->ToWML($re['content'], $this->wmlVo) . '</li>';
        }
        echo '</ul></div>';
        if ($result['pageCount'] > 1) {
            echo '<div class="pager">';
            if ($page > 1) echo '<a href="?siteid=' . $this->siteid . '&classid=' . $this->classid . '&id=' . $id . '&page=' . ($page-1) . '">上一页</a> ';
            echo '第' . $page . '/' . $result['pageCount'] . '页 ';
            if ($page < $result['pageCount']) echo '<a href="?siteid=' . $this->siteid . '&classid=' . $this->classid . '&id=' . $id . '&page=' . ($page+1) . '">下一页</a>';
            echo '</div>';
        }
        echo '<div><a href="view.php?siteid=' . $this->siteid . '&classid=' . $this->classid . '&id=' . $id . '">返回</a></div>';
        $this->showDown();
    }
    private function addReply($id) {
        $db = $this->db;
        $isLogin = $this->userSession->isLogin();
        $nickname = $isLogin ? $this->userSession->getUserName() : trim($_POST['nickname'] ?? '游客');
        $content = trim($_POST['content'] ?? '');
        if ($content === '') { echo '<div class="error">内容不能为空</div>'; return; }
        if (WapTool::checkSqlInject($content)) { echo '<div class="error">含非法字符</div>'; return; }
        if (!$this->userSession->checkSubmitLimit()) { echo '<div class="error">操作过于频繁</div>'; return; }
        $uid = $this->userSession->getUserId();
        $db->insert("INSERT INTO WapReply (module,contentid,siteid,classid,userid,nickname,content,addtime,isshow) VALUES ('ring',?,?,?,?,?,?,NOW(),1)",
            [$id, $this->siteid, $this->classid, $uid, $nickname, $content]);
        echo '<div class="success">评论成功</div>';
    }
}
$page = new RingReplyPage();
$page->run();
