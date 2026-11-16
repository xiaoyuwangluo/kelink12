<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 图片模块 - 后台切换图片显隐 (isshow)
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class PictureChangePage extends WapPage {
    public function run() {
        // 鉴权：后台管理员(kl_adminid)直接放行，否则走会员登录校验（未登录跳转）
        if (!$this->userSession->isAdminLogin()) {
            $this->checkLogin();
        }
        $db = $this->db;
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) $this->showError('参数错误');

        $pic = $db->getOne("SELECT id,title,isshow FROM WapPicture WHERE id=?", [$id]);
        if (!$pic) $this->showError('图片不存在');

        $new = $pic['isshow'] ? 0 : 1;
        $db->execute("UPDATE WapPicture SET isshow=? WHERE id=?", [$new, $id]);

        $this->showTop('切换显隐');
        echo '<div class="success">已设为' . ($new ? '显示' : '隐藏') . '：' . $this->ToHtm($pic['title'])
            . ' <a href="list.php?classid=' . $this->classid . '">返回列表</a></div>';
        $this->showDown();
    }
}

$page = new PictureChangePage();
$page->run();
