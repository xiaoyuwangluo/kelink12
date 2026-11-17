<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 商城模块 - 商品直购入口
 * 校验商品存在/在售/库存后，统一跳转结算页(checkout.php)完成下单与收货信息填写
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class ShopOrderPage extends WapPage {
    public function run() {
        $this->checkLogin();
        $db = $this->db;
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) $this->showError('参数错误', 'list.php?classid=' . $this->classid);

        $product = $db->getOne("SELECT * FROM WapShop WHERE id=?", [$id]);
        if (!$product) $this->showError('商品不存在', 'list.php?classid=' . $this->classid);
        if ((int)$product['isshow'] !== 1) {
            $this->showError('商品已下架', 'list.php?classid=' . $this->classid);
        }

        $stock = (int)$product['stock'];
        if ($stock <= 0) {
            $this->showError('商品已售罄', 'view.php?classid=' . $this->classid . '&id=' . $id);
        }

        // 数量校验：默认 1，超出库存则收敛到库存上限并提示
        $num = (int)($_GET['num'] ?? 1);
        if ($num < 1) $num = 1;
        $notice = '';
        if ($num > $stock) {
            $num = $stock;
            $notice = '购买数量已调整为库存上限 ' . $stock . ' 件';
        }

        $this->strTitle = '确认订单';
        $this->showTop($this->strTitle);
        echo '<div class="subtitle">确认订单</div>';
        echo '<div class="content">';
        echo '商品:' . $this->ToHtm($product['title']) . '<br/>';
        echo '单价:￥' . number_format((float)$product['price'], 2) . '<br/>';
        echo '库存:' . $stock . ' 件<br/>';
        if ($notice !== '') echo '<div class="error">' . $notice . '</div>';
        echo '<form method="get" action="checkout.php">';
        echo '<input type="hidden" name="classid" value="' . $this->classid . '"/>';
        echo '<input type="hidden" name="shopid" value="' . $id . '"/>';
        echo '数量:<input type="number" name="num" value="' . $num . '" min="1" max="' . $stock . '"/><br/>';
        echo '<input type="submit" value="去结算"/></form>';
        echo '</div>';
        echo '<div><a href="view.php?classid=' . $this->classid . '&id=' . $id . '">返回商品</a> ';
        echo '<a href="list.php?classid=' . $this->classid . '">商城</a></div>';
        $this->showDown();
    }
}
$page = new ShopOrderPage();
$page->run();
