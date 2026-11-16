<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 商城模块 - 下单 (对应原 shop/orderlist 系列)
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class ShopOrderPage extends WapPage {
    public function run() {
        $this->checkLogin();
        $db = $this->db;
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) $this->showError('参数错误');

        $product = $db->getOne("SELECT * FROM WapShop WHERE id=?", [$id]);
        if (!$product) $this->showError('商品不存在');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $num = max(1, (int)($_POST['num'] ?? 1));
            $total = $product['price'] * $num;
            $uid = $this->userSession->getUserId();
            $orderId = $db->insert("INSERT INTO WapShopOrder (userid,shopid,num,totalprice,status,addtime) VALUES (?,?,?,?,0,NOW())",[$uid,$id,$num,$total]);
            $this->showTop('下单成功');
            echo '<div class="success">下单成功！订单号: ' . $orderId . ' 金额:￥' . number_format($total, 2) . '</div>';
            echo '<div><a href="view.php?classid=' . $this->classid . '&id=' . $id . '">返回商品</a></div>';
            $this->showDown();
            return;
        }

        $this->strTitle = '确认订单';
        $this->showTop($this->strTitle);
        echo '<div class="subtitle">确认订单</div>';
        echo '<div class="content">';
        echo '商品:' . $this->ToHtm($product['title']) . '<br/>';
        echo '单价:￥' . number_format($product['price'], 2) . '<br/>';
        echo '<form method="post" action="order.php?classid=' . $this->classid . '&id=' . $id . '">';
        echo '数量:<input type="number" name="num" value="1" min="1"/><br/>';
        echo '<input type="submit" value="提交订单"/></form>';
        echo '</div>';
        $this->showDown();
    }
}
$page = new ShopOrderPage();
$page->run();
