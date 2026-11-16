<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 商城模块 - 商品详情 (对应原 shop/view.php)
 * 展示库存/点击、售罄拦截、下架提示、同栏目相关推荐
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class ShopViewPage extends WapPage {
    public function run() {
        $db = $this->db;
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) $this->showError('参数错误');

        $product = $db->getOne("SELECT * FROM WapShop WHERE id=?", [$id]);
        if (!$product) $this->showError('商品不存在');

        $classid = (int)$product['classid'];
        $class = $db->getOne("SELECT classname FROM WapClass WHERE id=?", [$classid]);
        $classname = $class['classname'] ?? '未分类';
        $isOnShow = ((int)$product['isshow'] === 1);

        // 下架商品：提示并阻止购买，不增加点击数
        if (!$isOnShow) {
            $this->strTitle = $product['title'];
            $this->showTop($this->strTitle);
            echo '<div class="subtitle">' . $this->ToHtm($product['title']) . '</div>';
            echo '<div class="content"><div class="error">该商品已下架，暂不可购买。</div>'
                . '价格:￥' . number_format((float)$product['price'], 2) . '</div>';
            echo '<div><a href="list.php?classid=' . $this->classid . '">返回商品列表</a></div>';
            $this->showDown();
            return;
        }

        $db->execute("UPDATE WapShop SET hits=hits+1 WHERE id=?", [$id]);
        $hits = (int)$product['hits'] + 1;
        $stock = (int)$product['stock'];

        $this->strTitle = $product['title'];
        $this->showTop($this->strTitle);

        echo '<div class="nav"><a href="list.php?classid=' . $this->classid . '">' . $this->ToHtm($classname) . '</a> &gt; 商品详情</div>';
        echo '<div class="subtitle">' . $this->ToHtm($product['title']) . '</div>';
        if (!empty($product['picurl'])) {
            echo '<div class="pic"><img src="' . $this->ToHtm($product['picurl']) . '"/></div>';
        }
        echo '<div class="price">价格:￥' . number_format((float)$product['price'], 2) . '</div>';
        echo '<div class="content">库存:' . $stock
            . ($stock <= 0 ? ' <span style="color:red">已售罄</span>' : ($stock < 5 ? ' <span style="color:red">仅剩少量</span>' : ''))
            . '　点击:' . $hits . '</div>';
        echo '<div class="content">' . $this->wapTool->ToWML($product['content'], $this->wmlVo) . '</div>';

        // 购买区：售罄只提示不下单
        echo '<div class="buybar">';
        if ($stock <= 0) {
            echo '<div class="error">该商品已售罄，无法加入购物车或立即购买。</div>';
        } else {
            $max = $stock < 99 ? $stock : 99;
            echo '<form method="post" action="cart.php?classid=' . $this->classid . '">';
            echo '<input type="hidden" name="act" value="add"/><input type="hidden" name="shopid" value="' . $id . '"/>';
            echo '数量:<input type="number" name="num" value="1" min="1" max="' . $max . '"/>';
            echo '<input type="submit" value="加入购物车"/>';
            echo '</form>';
            echo '<form method="get" action="checkout.php">';
            echo '<input type="hidden" name="classid" value="' . $this->classid . '"/>';
            echo '<input type="hidden" name="shopid" value="' . $id . '"/>';
            echo '<input type="hidden" name="num" value="1"/>';
            echo '<input type="submit" value="立即购买"/>';
            echo '</form>';
        }
        echo '</div>';
        echo '<div class="shopnav"><a href="cart.php?classid=' . $this->classid . '">购物车</a> ';
        echo '<a href="myorder.php?classid=' . $this->classid . '">我的订单</a></div>';

        // 同栏目相关推荐
        $related = $db->getAll("SELECT id,title,price,stock FROM WapShop WHERE classid=? AND id<>? AND isshow=1 ORDER BY id DESC LIMIT 5", [$classid, $id]);
        if ($related) {
            echo '<div class="subtitle">相关商品</div><div class="content"><ul class="list">';
            foreach ($related as $r) {
                echo '<li><a href="view.php?classid=' . $this->classid . '&id=' . (int)$r['id'] . '">' . $this->ToHtm($r['title']) . '</a>'
                    . ' ￥' . number_format((float)$r['price'], 2)
                    . ((int)$r['stock'] <= 0 ? ' <span style="color:red">售罄</span>' : '') . '</li>';
            }
            echo '</ul></div>';
        }

        echo '<div><a href="list.php?classid=' . $this->classid . '">返回列表</a></div>';
        $this->showDown();
    }
}

$page = new ShopViewPage();
$page->run();
