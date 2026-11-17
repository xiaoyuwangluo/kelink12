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
            echo '<div class="ui-breadcrumb"><a href="' . $this->http_start . 'wapindex.php">首页</a> / <a href="list.php?classid=' . $this->classid . '">' . $this->ToHtm($classname) . '</a> / 商品详情</div>';
            echo '<div class="subtitle">' . $this->ToHtm($product['title']) . '</div>';
            echo '<div class="ui-card ui-mt-2"><div class="ui-alert ui-alert-danger">该商品已下架，暂不可购买。</div>'
                . '<div class="price">价格:￥' . number_format((float)$product['price'], 2) . '</div></div>';
            echo '<div class="ui-mt-2"><a class="ui-btn ui-btn-ghost" href="list.php?classid=' . $this->classid . '">返回商品列表</a></div>';
            $this->showDown();
            return;
        }

        $db->execute("UPDATE WapShop SET hits=hits+1 WHERE id=?", [$id]);
        $hits = (int)$product['hits'] + 1;
        $stock = (int)$product['stock'];

        $this->strTitle = $product['title'];
        $this->showTop($this->strTitle);

        echo '<div class="ui-breadcrumb"><a href="' . $this->http_start . 'wapindex.php">首页</a> / <a href="list.php?classid=' . $this->classid . '">' . $this->ToHtm($classname) . '</a> / 商品详情</div>';
        echo '<div class="subtitle">' . $this->ToHtm($product['title']) . '</div>';

        // 商品信息卡
        echo '<div class="ui-card">';
        if (!empty($product['picurl'])) {
            echo '<div class="pic"><img src="' . $this->ToHtm($product['picurl']) . '" alt="' . $this->ToHtm($product['title']) . '"/></div>';
        }
        echo '<div class="price">￥' . number_format((float)$product['price'], 2) . '</div>';
        echo '<div class="ui-flex ui-flex-wrap ui-gap-2 ui-mt-2">';
        if ($stock <= 0) {
            echo '<span class="ui-tag ui-tag-danger">已售罄</span>';
        } elseif ($stock < 5) {
            echo '<span class="ui-tag ui-tag-warning">仅剩 ' . $stock . ' 件</span>';
        } else {
            echo '<span class="ui-tag ui-tag-success">库存 ' . $stock . ' 件</span>';
        }
        echo '<span class="ui-tag ui-tag-muted">点击 ' . $hits . '</span>';
        echo '<span class="ui-tag ui-tag-muted">' . $this->ToHtm($classname) . '</span>';
        echo '</div>';
        echo '</div>';

        // 商品描述
        echo '<div class="ui-card"><div class="ui-card-head"><span class="ui-card-title">商品详情</span></div>';
        echo '<div class="content">' . $this->wapTool->ToWML($product['content'], $this->wmlVo) . '</div></div>';

        // 购买区：售罄只提示不下单
        echo '<div class="ui-card buybar">';
        if ($stock <= 0) {
            echo '<div class="ui-alert ui-alert-danger">该商品已售罄，无法加入购物车或立即购买。</div>';
        } else {
            $max = $stock < 99 ? $stock : 99;
            echo '<form method="post" action="cart.php?classid=' . $this->classid . '">';
            echo '<input type="hidden" name="act" value="add"/><input type="hidden" name="shopid" value="' . $id . '"/>';
            echo '<div class="ui-field"><label for="num">数量</label><input type="number" id="num" name="num" value="1" min="1" max="' . $max . '"/><div class="hint">单次最多 ' . $max . ' 件</div></div>';
            echo '<input class="ui-btn ui-btn-primary ui-btn-block" type="submit" value="加入购物车"/>';
            echo '</form>';
            echo '<form method="get" action="checkout.php" class="ui-mt-2">';
            echo '<input type="hidden" name="classid" value="' . $this->classid . '"/>';
            echo '<input type="hidden" name="shopid" value="' . $id . '"/>';
            echo '<input type="hidden" name="num" value="1"/>';
            echo '<input class="ui-btn ui-btn-block" type="submit" value="立即购买"/>';
            echo '</form>';
        }
        echo '</div>';

        echo '<div class="shopnav ui-mt-2"><a class="ui-btn ui-btn-sm" href="cart.php?classid=' . $this->classid . '">购物车</a> ';
        echo '<a class="ui-btn ui-btn-sm" href="myorder.php?classid=' . $this->classid . '">我的订单</a></div>';

        // 同栏目相关推荐
        $related = $db->getAll("SELECT id,title,price,stock FROM WapShop WHERE classid=? AND id<>? AND isshow=1 ORDER BY id DESC LIMIT 5", [$classid, $id]);
        if ($related) {
            echo '<div class="ui-card ui-mt-3"><div class="ui-card-head"><span class="ui-card-title">相关商品</span><span class="ui-card-extra">同栏目推荐</span></div><ul class="ui-list">';
            foreach ($related as $r) {
                echo '<li><div class="ui-flex ui-between ui-center"><a href="view.php?classid=' . $this->classid . '&id=' . (int)$r['id'] . '">' . $this->ToHtm($r['title']) . '</a>'
                    . '<span class="ui-tag' . ((int)$r['stock'] <= 0 ? ' ui-tag-danger' : '') . '">￥' . number_format((float)$r['price'], 2) . '</span></div>'
                    . ((int)$r['stock'] <= 0 ? '<div class="meta">已售罄</div>' : '') . '</li>';
            }
            echo '</ul></div>';
        }

        echo '<div class="ui-mt-2"><a class="ui-btn ui-btn-ghost" href="list.php?classid=' . $this->classid . '">返回列表</a></div>';
        $this->showDown();
    }
}

$page = new ShopViewPage();
$page->run();
