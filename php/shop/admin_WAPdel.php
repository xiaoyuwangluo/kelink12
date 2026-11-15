<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 商城模块 - 后台删除商品 (对应原 shop/admin_WAPdel.aspx)
 * GET 显示确认页，POST 提交才执行删除
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class ShopDelPage extends WapPage {
    public function run() {
        // 鉴权：后台管理员(kl_adminid)直接放行，否则走会员登录校验（未登录跳转）
        if (!$this->userSession->isAdminLogin()) {
            $this->checkLogin();
        }
        $db = $this->db;
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) $this->showError('参数错误');

        $goods = $db->getOne("SELECT id,title FROM WapShop WHERE id=?", [$id]);
        if (!$goods) $this->showError('商品不存在');

        $this->showTop('删除商品');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $db->execute("DELETE FROM WapShop WHERE id=?", [$id]);
            echo '<div class="success">删除成功 <a href="list.php?siteid=' . $this->siteid . '&classid=' . $this->classid . '">返回列表</a></div>';
        } else {
            echo '<div class="subtitle">确认删除商品？</div>';
            echo '<div class="content">商品：' . $this->ToHtm($goods['title']) . '</div>';
            echo '<form method="post" action="admin_WAPdel.php?siteid=' . $this->siteid . '&classid=' . $this->classid . '&id=' . $id . '">';
            echo '<input type="submit" value="确认删除"/></form>';
            echo '<a href="list.php?siteid=' . $this->siteid . '&classid=' . $this->classid . '">取消返回</a>';
        }
        $this->showDown();
    }
}

$page = new ShopDelPage();
$page->run();
