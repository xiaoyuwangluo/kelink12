<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 论坛模块 - 后台修改帖子 (对应原 bbs/admin_WAPmodify.aspx)
 */
require_once __DIR__ . '/../includes/bootstrap.php';
require_once KL_ROOT . '/includes/Community.php';

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

        // 非管理员仅允许修改自己的帖子，避免越权编辑
        $isAdmin = $this->userSession->isAdminLogin();
        if (!$isAdmin && (int)$post['userid'] !== (int)$this->userSession->getUserId()) {
            $this->showError('无权限修改此帖子', '../myfile.php');
        }

        $this->showTop('修改帖子');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->doModify($id, $post, $isAdmin);
        } else {
            $this->showForm($post, $isAdmin);
        }
        $this->showDown();
    }

    private function showForm($a, $isAdmin = false) {
        echo '<div class="subtitle">修改帖子</div>';
        echo '<form method="post" action="admin_WAPmodify.php?classid=' . $this->classid . '&id=' . $a['id'] . '">';
        echo '标题:<input type="text" name="title" value="' . $this->ToHtm($a['title']) . '"/><br/>';
        echo '内容:<textarea name="content" rows="10">' . $this->ToHtm($a['content'] ?? '') . '</textarea><br/>';
        if ($isAdmin) {
            echo '置顶:<input type="checkbox" name="istop" value="1" ' . ($a['istop'] ? 'checked' : '') . '/>';
            echo ' 精华:<input type="checkbox" name="isgood" value="1" ' . ($a['isgood'] ? 'checked' : '') . '/>';
            echo ' 锁定:<input type="checkbox" name="islock" value="1" ' . ($a['islock'] ? 'checked' : '') . '/><br/>';
        }
        echo '<input type="submit" value="保存修改"/></form>';
    }

    private function doModify($id, $post, $isAdmin) {
        $db = $this->db;
        $title = trim($_POST['title'] ?? '');
        $content = $_POST['content'] ?? '';
        // 非管理员不可变更置顶/精华/锁定等管理属性
        $istop = $isAdmin ? (isset($_POST['istop']) ? 1 : 0) : (int)$post['istop'];
        $isgood = $isAdmin ? (isset($_POST['isgood']) ? 1 : 0) : (int)$post['isgood'];
        $islock = $isAdmin ? (isset($_POST['islock']) ? 1 : 0) : (int)$post['islock'];

        if ($title === '') {
            echo '<div class="error">标题不能为空</div>';
            return;
        }
        $db->execute(
            "UPDATE WapBbs SET title=?,content=?,istop=?,isgood=?,islock=? WHERE id=?",
            [$title, $content, $istop, $isgood, $islock, $id]
        );
        kl_cache_clear_site();
        echo '<div class="success">修改成功 <a href="view.php?classid=' . $this->classid . '&id=' . $id . '">查看</a></div>';
    }
}

$page = new BbsModifyPage();
$page->run();
