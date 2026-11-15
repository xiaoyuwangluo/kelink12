<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 论坛模块 - 后台修改帖子 (对应原 bbs/admin_WAPmodify.aspx)
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class BbsModifyPage extends WapPage {
    public function run() {
        // 鉴权：后台管理员(kl_adminid)直接放行，否则走会员登录校验（未登录跳转）
        if (!$this->userSession->isAdminLogin()) {
            $this->checkLogin();
        }
        $db = $this->db;
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) $this->showError('参数错误');

        $post = $db->getOne("SELECT * FROM WapBbs WHERE id=?", [$id]);
        if (!$post) $this->showError('帖子不存在');

        $this->showTop('修改帖子');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->doModify($id);
        } else {
            $this->showForm($post);
        }
        $this->showDown();
    }

    private function showForm($a) {
        echo '<div class="subtitle">修改帖子</div>';
        echo '<form method="post" action="admin_WAPmodify.php?siteid=' . $this->siteid . '&classid=' . $this->classid . '&id=' . $a['id'] . '">';
        echo '标题:<input type="text" name="title" value="' . $this->ToHtm($a['title']) . '"/><br/>';
        echo '内容:<textarea name="content" rows="10">' . $this->ToHtm($a['content'] ?? '') . '</textarea><br/>';
        echo '置顶:<input type="checkbox" name="istop" value="1" ' . ($a['istop'] ? 'checked' : '') . '/>';
        echo ' 精华:<input type="checkbox" name="isgood" value="1" ' . ($a['isgood'] ? 'checked' : '') . '/>';
        echo ' 锁定:<input type="checkbox" name="islock" value="1" ' . ($a['islock'] ? 'checked' : '') . '/><br/>';
        echo '<input type="submit" value="保存修改"/></form>';
    }

    private function doModify($id) {
        $db = $this->db;
        $title = trim($_POST['title'] ?? '');
        $content = $_POST['content'] ?? '';
        $istop = isset($_POST['istop']) ? 1 : 0;
        $isgood = isset($_POST['isgood']) ? 1 : 0;
        $islock = isset($_POST['islock']) ? 1 : 0;

        if ($title === '') {
            echo '<div class="error">标题不能为空</div>';
            return;
        }
        $db->execute(
            "UPDATE WapBbs SET title=?,content=?,istop=?,isgood=?,islock=? WHERE id=?",
            [$title, $content, $istop, $isgood, $islock, $id]
        );
        echo '<div class="success">修改成功 <a href="view.php?siteid=' . $this->siteid . '&classid=' . $this->classid . '&id=' . $id . '">查看</a></div>';
    }
}

$page = new BbsModifyPage();
$page->run();
