<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 商城模块 - 商品详情 (对应原 shop/view.php)
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class ShopViewPage extends WapPage {
    public function run() {
        $db = $this->db;
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) $this->showError('参数错误');

        $product = $db->getOne("SELECT * FROM WapShop WHERE id=? AND siteid=?", [$id, $this->siteid]);
        if (!$product) $this->showError('商品不存在');

        $db->execute("UPDATE WapShop SET hits=hits+1 WHERE id=?", [$id]);

        $this->strTitle = $product['title'];
        $this->showTop($this->strTitle);

        echo '<div class="subtitle">' . $this->ToHtm($product['title']) . '</div>';
        if (!empty($product['picurl'])) {
            echo '<div class="pic"><img src="' . $this->ToHtm($product['picurl']) . '"/></div>';
        }
        echo '<div class="price">价格:￥' . number_format($product['price'], 2) . '</div>';
        echo '<div class="content">' . $this->wapTool->ToWML($product['content'], $this->wmlVo) . '</div>';

        // 购买按钮：加入购物车 / 立即购买
        echo '<div class="buybar">';
        echo '<form method="post" action="cart.php?siteid=' . $this->siteid . '&classid=' . $this->classid . '">';
        echo '<input type="hidden" name="act" value="add"/><input type="hidden" name="shopid" value="' . $id . '"/>';
        echo '数量:<input type="number" name="num" value="1" min="1"/>';
        echo '<input type="submit" value="加入购物车"/>';
        echo '</form>';
        echo '<form method="get" action="checkout.php">';
        echo '<input type="hidden" name="siteid" value="' . $this->siteid . '"/>';
        echo '<input type="hidden" name="classid" value="' . $this->classid . '"/>';
        echo '<input type="hidden" name="shopid" value="' . $id . '"/>';
        echo '<input type="hidden" name="num" value="1"/>';
        echo '<input type="submit" value="立即购买"/>';
        echo '</form>';
        echo '</div>';
        echo '<div class="shopnav"><a href="cart.php?siteid=' . $this->siteid . '&classid=' . $this->classid . '">购物车</a> ';
        echo '<a href="myorder.php?siteid=' . $this->siteid . '&classid=' . $this->classid . '">我的订单</a></div>';

        echo '<div><a href="list.php?siteid=' . $this->siteid . '&classid=' . $this->classid . '">返回列表</a></div>';
        $this->showDown();
    }
}

$page = new ShopViewPage();
$page->run();
