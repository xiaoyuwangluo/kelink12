<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 小说模块 - 后台切换小说显隐 (isshow)
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class NovelChangePage extends WapPage {
    public function run() {
        // 鉴权：后台管理员(kl_adminid)直接放行，否则走会员登录校验（未登录跳转）
        if (!$this->userSession->isAdminLogin()) {
            $this->checkLogin();
        }
        $db = $this->db;
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) $this->showError('参数错误');

        $novel = $db->getOne("SELECT id,title,isshow FROM WapNovel WHERE id=?", [$id]);
        if (!$novel) $this->showError('小说不存在');

        $new = $novel['isshow'] ? 0 : 1;
        $db->execute("UPDATE WapNovel SET isshow=? WHERE id=?", [$new, $id]);

        $this->showTop('切换显隐');
        echo '<div class="success">已设为' . ($new ? '显示' : '隐藏') . '：' . $this->ToHtm($novel['title'])
            . ' <a href="list.php?classid=' . $this->classid . '">返回列表</a></div>';
        $this->showDown();
    }
}

$page = new NovelChangePage();
$page->run();
