<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 商城模块 - 商品列表 (对应原 shop/list.aspx)
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class ShopListPage extends WapPage {
    public function run() {
        $db = $this->db;
        $page = max(1, (int)($_GET['page'] ?? 1));
        $pageSize = 20;

        $this->strTitle = $this->classVo->classname ?? '商城';
        $this->showTop($this->strTitle);
        echo '<div class="subtitle">' . $this->ToHtm($this->classVo->classname ?? '商城') . '</div>';

        $result = $db->getPage('WapShop', 'id,title,picurl,price,addtime,hits',
            "siteid=? AND classid=? AND isshow=1", 'id DESC', $page, $pageSize,
            [$this->siteid, $this->classid]);

        echo '<div class="content"><ul class="shoplist">';
        foreach ($result['list'] as $p) {
            echo '<li>';
            if (!empty($p['picurl'])) echo '<img src="' . $this->ToHtm($p['picurl']) . '" alt=""/>';
            echo '<a href="view.php?siteid=' . $this->siteid . '&classid=' . $this->classid . '&id=' . $p['id'] . '">';
            echo $this->ToHtm($p['title']) . '</a>';
            echo ' <span class="price">￥' . number_format($p['price'], 2) . '</span>';
            // 加入购物车 / 立即购买 快捷链接
            echo '<form method="post" action="cart.php?siteid=' . $this->siteid . '&classid=' . $this->classid . '" style="display:inline">';
            echo '<input type="hidden" name="act" value="add"/><input type="hidden" name="shopid" value="' . (int)$p['id'] . '"/>';
            echo '<input type="hidden" name="num" value="1"/>';
            echo '<input type="submit" value="加入购物车"/></form> ';
            echo '<a href="checkout.php?siteid=' . $this->siteid . '&classid=' . $this->classid . '&shopid=' . (int)$p['id'] . '&num=1">立即购买</a>';
            echo '</li>';
        }
        echo '</ul></div>';

        if ($result['pageCount'] > 1) {
            echo '<div class="pager">';
            if ($page > 1) echo '<a href="?siteid=' . $this->siteid . '&classid=' . $this->classid . '&page=' . ($page-1) . '">上一页</a> ';
            echo '第' . $page . '/' . $result['pageCount'] . '页 ';
            if ($page < $result['pageCount']) echo '<a href="?siteid=' . $this->siteid . '&classid=' . $this->classid . '&page=' . ($page+1) . '">下一页</a>';
            echo '</div>';
        }
        $this->showDown();
    }
}

$page = new ShopListPage();
$page->run();
