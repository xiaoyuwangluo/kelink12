<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 图片模块 - 后台删除图片 (对应原 picture/admin_WAPdel.aspx)
 * GET 显示确认页，POST 提交才执行删除
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class PictureDelPage extends WapPage {
    public function run() {
        // 鉴权：后台管理员(kl_adminid)直接放行，否则走会员登录校验（未登录跳转）
        if (!$this->userSession->isAdminLogin()) {
            $this->checkLogin();
        }
        $db = $this->db;
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) $this->showError('参数错误');

        $pic = $db->getOne("SELECT id,title FROM WapPicture WHERE id=?", [$id]);
        if (!$pic) $this->showError('图片不存在');

        $this->showTop('删除图片');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $db->execute("DELETE FROM WapPicture WHERE id=?", [$id]);
            echo '<div class="success">删除成功 <a href="list.php?classid=' . $this->classid . '">返回列表</a></div>';
        } else {
            echo '<div class="subtitle">确认删除图片？</div>';
            echo '<div class="content">图片：' . $this->ToHtm($pic['title']) . '</div>';
            echo '<form method="post" action="admin_WAPdel.php?classid=' . $this->classid . '&id=' . $id . '">';
            echo '<input type="submit" value="确认删除"/></form>';
            echo '<a href="list.php?classid=' . $this->classid . '">取消返回</a>';
        }
        $this->showDown();
    }
}

$page = new PictureDelPage();
$page->run();
