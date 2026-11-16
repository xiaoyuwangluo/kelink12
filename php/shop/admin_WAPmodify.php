<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 商城模块 - 后台修改商品 (对应原 shop/admin_WAPmodify.aspx)
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class ShopModifyPage extends WapPage {
    public function run() {
        // 鉴权：后台管理员(kl_adminid)直接放行，否则走会员登录校验（未登录跳转）
        if (!$this->userSession->isAdminLogin()) {
            $this->checkLogin();
        }
        $db = $this->db;
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) $this->showError('参数错误');

        $goods = $db->getOne("SELECT * FROM WapShop WHERE id=?", [$id]);
        if (!$goods) $this->showError('商品不存在');

        $this->showTop('修改商品');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->doModify($id);
        } else {
            $this->showForm($goods);
        }
        $this->showDown();
    }

    private function showForm($a) {
        echo '<div class="subtitle">修改商品</div>';
        echo '<form method="post" action="admin_WAPmodify.php?classid=' . $this->classid . '&id=' . $a['id'] . '">';
        echo '标题:<input type="text" name="title" value="' . $this->ToHtm($a['title']) . '"/><br/>';
        echo '价格:<input type="text" name="price" value="' . $this->ToHtm($a['price']) . '"/><br/>';
        echo '库存:<input type="text" name="stock" value="' . $this->ToHtm($a['stock']) . '"/><br/>';
        echo '图片:<input type="text" name="picurl" value="' . $this->ToHtm($a['picurl'] ?? '') . '"/><br/>';
        echo '介绍:<textarea name="content" rows="10">' . $this->ToHtm($a['content'] ?? '') . '</textarea><br/>';
        echo '<input type="submit" value="保存修改"/></form>';
    }

    private function doModify($id) {
        $db = $this->db;
        $title = trim($_POST['title'] ?? '');
        $content = $_POST['content'] ?? '';
        $picurl = trim($_POST['picurl'] ?? '');
        $price = floatval($_POST['price'] ?? 0);
        $stock = (int)($_POST['stock'] ?? 0);

        if ($title === '') {
            echo '<div class="error">标题不能为空</div>';
            return;
        }
        $db->execute(
            "UPDATE WapShop SET title=?,content=?,picurl=?,price=?,stock=? WHERE id=?",
            [$title, $content, $picurl, $price, $stock, $id]
        );
        echo '<div class="success">修改成功 <a href="view.php?classid=' . $this->classid . '&id=' . $id . '">查看</a></div>';
    }
}

$page = new ShopModifyPage();
$page->run();
