<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 商城模块 - 商品列表 (对应原 shop/list.aspx)
 * 支持: 关键词搜索 / 价格区间 / 排序(最新 价格 人气) / 售罄标记 / 分页保留筛选
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class ShopListPage extends WapPage {
    public function run() {
        $db = $this->db;
        $page = max(1, (int)($_GET['page'] ?? 1));
        $pageSize = 20;

        $q = trim($_GET['q'] ?? '');
        $sort = (string)($_GET['sort'] ?? 'new');
        if (!in_array($sort, ['new', 'price_asc', 'price_desc', 'hot'], true)) $sort = 'new';
        $minprice = trim($_GET['minprice'] ?? '');
        $maxprice = trim($_GET['maxprice'] ?? '');
        $err = '';
        $isPrice = function ($v) {
            return preg_match('/^\d+(\.\d{1,2})?$/', $v) === 1;
        };
        if ($minprice !== '' && !$isPrice($minprice)) { $err = '最低价必须为非负数字'; $minprice = ''; }
        if ($maxprice !== '' && !$isPrice($maxprice)) { $err = '最高价必须为非负数字'; $maxprice = ''; }
        if ($err === '' && $minprice !== '' && $maxprice !== '' && (float)$minprice > (float)$maxprice) {
            $err = '最低价不能大于最高价'; $maxprice = '';
        }

        $where = 'classid=? AND isshow=1';
        $params = [$this->classid];
        if ($q !== '') {
            $where .= ' AND (id=? OR title LIKE ?)';
            $params[] = (int)$q;
            $params[] = '%' . $q . '%';
        }
        if ($minprice !== '') { $where .= ' AND price>=?'; $params[] = (float)$minprice; }
        if ($maxprice !== '') { $where .= ' AND price<=?'; $params[] = (float)$maxprice; }
        $sortSql = [
            'new' => 'id DESC',
            'price_asc' => 'price ASC, id DESC',
            'price_desc' => 'price DESC, id DESC',
            'hot' => 'hits DESC, id DESC',
        ];

        $this->strTitle = $this->classVo->classname ?? '商城';
        $this->showTop($this->strTitle);
        echo '<div class="ui-breadcrumb"><a href="' . $this->http_start . 'wapindex.php">首页</a> / ' . $this->ToHtm($this->classVo->classname ?? '商城') . '</div>';
        echo '<div class="subtitle">' . $this->ToHtm($this->classVo->classname ?? '商城') . '</div>';

        echo '<div class="ui-tabs">';
        $sorts = ['new' => '最新', 'price_asc' => '价格↑', 'price_desc' => '价格↓', 'hot' => '人气'];
        foreach ($sorts as $k => $label) {
            echo ($sort === $k)
                ? '<a class="is-active" href="' . $this->backUrl($q, $k, $minprice, $maxprice) . '">' . $label . '</a>'
                : '<a href="' . $this->backUrl($q, $k, $minprice, $maxprice) . '">' . $label . '</a>';
        }
        echo '</div>';

        echo '<div class="ui-card ui-mt-2"><form method="get" action="list.php">'
            . '<input type="hidden" name="classid" value="' . $this->classid . '"/>'
            . '<input type="hidden" name="sort" value="' . $this->ToHtm($sort) . '"/>'
            . '<div class="ui-field"><label for="q">搜索</label><input type="text" id="q" name="q" value="' . $this->ToHtm($q) . '" placeholder="商品 ID 或标题"/></div>'
            . '<div class="ui-flex ui-gap-2"><div class="ui-field" style="flex:1"><label for="minprice">最低价</label><input type="text" id="minprice" name="minprice" value="' . $this->ToHtm($minprice) . '"/></div>'
            . '<div class="ui-field" style="flex:1"><label for="maxprice">最高价</label><input type="text" id="maxprice" name="maxprice" value="' . $this->ToHtm($maxprice) . '"/></div></div>'
            . '<input class="ui-btn ui-btn-primary ui-btn-block" type="submit" value="筛选"/></form>';
        if ($q !== '' || $minprice !== '' || $maxprice !== '') {
            echo '<a class="ui-btn ui-btn-ghost ui-btn-sm ui-mt-2" href="list.php?classid=' . $this->classid . '&sort=' . $this->ToHtm($sort) . '">清除筛选</a>';
        }
        if ($err !== '') echo '<div class="ui-alert ui-alert-danger">' . $this->ToHtm($err) . '</div>';
        echo '</div>';

        $total = (int)$db->getScalar("SELECT COUNT(*) FROM WapShop WHERE " . $where, $params);
        echo '<div class="ui-flex ui-between ui-center ui-mt-2"><span class="ui-tag">共 ' . $total . ' 件商品</span>';
        if ($total === 0) echo '<a class="ui-btn ui-btn-sm" href="list.php?classid=' . $this->classid . '">查看全部</a>';
        echo '</div>';

        $result = $db->getPage('WapShop', 'id,title,picurl,price,stock,addtime,hits', $where,
            $sortSql[$sort], $page, $pageSize, $params);

        if ($total === 0) {
            echo '<div class="ui-card ui-mt-2"><div class="ui-empty"><div class="icon">🛒</div>没有找到符合条件的商品</div></div>';
        } else {
            echo '<ul class="list ui-list ui-mt-2">';
            foreach ($result['list'] as $p) {
                $id = (int)$p['id'];
                $stock = (int)$p['stock'];
                echo '<li>';
                echo '<a href="view.php?classid=' . $this->classid . '&id=' . $id . '">';
                if (!empty($p['picurl'])) echo '<img src="' . $this->ToHtm($p['picurl']) . '" alt="" style="max-width:60px;margin-right:8px;vertical-align:middle"/>';
                echo $this->ToHtm($p['title']) . '</a>';
                echo '<span class="ui-flex ui-gap-2 ui-center"><span class="price">￥' . number_format((float)$p['price'], 2) . '</span>';
                if ($stock <= 0) {
                    echo '<span class="ui-tag ui-tag-danger">售罄</span>';
                } else {
                    echo '<form method="post" action="cart.php?classid=' . $this->classid . '" style="display:inline">';
                    echo '<input type="hidden" name="act" value="add"/><input type="hidden" name="shopid" value="' . $id . '"/>';
                    echo '<input type="hidden" name="num" value="1"/>';
                    echo '<input class="ui-btn ui-btn-sm ui-btn-primary" type="submit" value="加入购物车"/></form>';
                    echo '<a class="ui-btn ui-btn-sm" href="checkout.php?classid=' . $this->classid . '&shopid=' . $id . '&num=1">立即购买</a>';
                }
                echo $this->adminLinks($id) . '</span>';
                echo '</li>';
            }
            echo '</ul>';
        }

        if ($result['pageCount'] > 1) {
            echo '<div class="ui-pager">';
            if ($page > 1) echo '<a href="' . $this->backUrl($q, $sort, $minprice, $maxprice) . '&page=' . ($page - 1) . '">上一页</a>';
            echo '<span class="current">' . $page . ' / ' . (int)$result['pageCount'] . '</span>';
            if ($page < $result['pageCount']) echo '<a href="' . $this->backUrl($q, $sort, $minprice, $maxprice) . '&page=' . ($page + 1) . '">下一页</a>';
            echo '</div>';
        }
        $this->showDown();
    }

    /** 保留筛选条件的链接 */
    private function backUrl($q, $sort, $minprice, $maxprice) {
        $url = 'list.php?classid=' . $this->classid . '&sort=' . urlencode($sort);
        if ($q !== '') $url .= '&q=' . urlencode($q);
        if ($minprice !== '') $url .= '&minprice=' . urlencode($minprice);
        if ($maxprice !== '') $url .= '&maxprice=' . urlencode($maxprice);
        return $url;
    }

    /** 后台管理员(kl_adminid)可见的修改/删除/显隐操作链接 */
    private function adminLinks($id) {
        if (!$this->userSession->isAdminLogin()) return '';
        $base = '?classid=' . $this->classid . '&id=' . $id;
        return ' <a href="admin_WAPmodify.php' . $base . '">修改</a>'
             . ' <a href="admin_WAPdel.php' . $base . '">删除</a>'
             . ' <a href="admin_WAPchange.php' . $base . '">显隐</a>';
    }
}

$page = new ShopListPage();
$page->run();
