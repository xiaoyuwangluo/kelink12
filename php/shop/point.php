<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 积分商城 - 积分商品列表 (搜索 / 积分区间 / 排序)
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class ShopPointPage extends WapPage {
    public function run() {
        $db = $this->db;
        $page = max(1, (int)($_GET['page'] ?? 1));

        // 排序白名单
        $sort = (string)($_GET['sort'] ?? 'default');
        $sortMap = [
            'default' => ['默认排序', 'sortnum DESC, id DESC'],
            'points_asc' => ['积分从低到高', 'points ASC, id DESC'],
            'points_desc' => ['积分从高到低', 'points DESC, id DESC'],
        ];
        if (!isset($sortMap[$sort])) $sort = 'default';

        // 关键词与积分区间
        $q = trim((string)($_GET['q'] ?? ''));
        $minRaw = trim((string)($_GET['minpoints'] ?? ''));
        $maxRaw = trim((string)($_GET['maxpoints'] ?? ''));
        $min = ($minRaw !== '' && ctype_digit($minRaw)) ? (int)$minRaw : null;
        $max = ($maxRaw !== '' && ctype_digit($maxRaw)) ? (int)$maxRaw : null;
        $notice = '';
        if ($minRaw !== '' && $min === null) {
            $notice = '最低积分需为数字，已忽略该条件';
        } elseif ($maxRaw !== '' && $max === null) {
            $notice = '最高积分需为数字，已忽略该条件';
        } elseif ($min !== null && $max !== null && $min > $max) {
            $notice = '最低积分大于最高积分，已自动交换';
            list($min, $max) = [$max, $min];
        }

        $where = 'isshow=1 AND stock>0';
        $params = [];
        if ($q !== '') {
            $where .= ' AND name LIKE ?';
            $params[] = '%' . $q . '%';
        }
        if ($min !== null) {
            $where .= ' AND points>=?';
            $params[] = $min;
        }
        if ($max !== null) {
            $where .= ' AND points<=?';
            $params[] = $max;
        }

        $result = $db->getPage('WapPointGoods', 'id,name,intro,points,stock,image,addtime',
            $where, $sortMap[$sort][1], $page, 20, $params);
        $filtered = $db->getOne("SELECT COUNT(*) AS c FROM WapPointGoods WHERE " . $where, $params);
        $allCount = (int)$db->getScalar("SELECT COUNT(*) FROM WapPointGoods WHERE isshow=1 AND stock>0");
        $hasFilter = ($q !== '' || $min !== null || $max !== null || $sort !== 'default');
        $clearUrl = 'point.php';

        $this->strTitle = '积分商城';
        $this->showTop($this->strTitle);
        echo '<div class="ui-breadcrumb"><a href="' . $this->http_start . 'wapindex.php">首页</a> / 积分商城</div>';
        echo '<div class="subtitle">积分商城</div>';

        $isLogin = $this->userSession->isLogin();
        $user = $isLogin ? $this->userSession->getUserVo() : null;
        $myPoint = $isLogin ? (int)($user['point'] ?? 0) : 0;
        if ($isLogin) {
            echo '<div class="ui-card"><div class="ui-flex ui-between ui-center"><span class="ui-tag ui-tag-muted">我的积分</span>'
                . '<span class="price">' . $myPoint . '</span></div>'
                . '<div class="ui-flex ui-gap-2 ui-mt-2"><a class="ui-btn ui-btn-sm" href="point_orders.php">我的兑换记录</a></div></div>';
        }

        // 排序 tabs
        echo '<div class="ui-tabs ui-mt-2">';
        foreach ($sortMap as $k => $v) {
            $href = $this->filterUrl($q, $minRaw, $maxRaw, $k);
            echo ($sort === $k)
                ? '<a class="is-active" href="' . $href . '">' . $v[0] . '</a>'
                : '<a href="' . $href . '">' . $v[0] . '</a>';
        }
        echo '</div>';

        // 搜索 / 积分区间
        echo '<div class="ui-card ui-mt-2"><form method="get" action="point.php">';
        echo '<input type="hidden" name="sort" value="' . $this->ToHtm($sort) . '"/>';
        echo '<div class="ui-field"><label for="q">商品名</label><input type="text" id="q" name="q" value="' . $this->ToHtm($q) . '"/></div>';
        echo '<div class="ui-flex ui-gap-2"><div class="ui-field" style="flex:1"><label for="minpoints">最低积分</label><input type="text" id="minpoints" name="minpoints" value="' . $this->ToHtm($minRaw) . '"/></div>';
        echo '<div class="ui-field" style="flex:1"><label for="maxpoints">最高积分</label><input type="text" id="maxpoints" name="maxpoints" value="' . $this->ToHtm($maxRaw) . '"/></div></div>';
        echo '<input class="ui-btn ui-btn-primary ui-btn-block" type="submit" value="筛选"/></form>';
        if ($hasFilter) {
            echo '<a class="ui-btn ui-btn-ghost ui-btn-sm ui-mt-2" href="' . $clearUrl . '">清除筛选</a>';
        }
        if ($notice !== '') echo '<div class="ui-alert ui-alert-warning">' . $this->ToHtm($notice) . '</div>';
        echo '</div>';

        echo '<div class="ui-flex ui-between ui-center ui-mt-2"><span class="ui-tag">共 ' . $allCount . ' 件可兑换商品</span>'
            . '<span class="ui-tag ui-tag-muted">当前条件 ' . (int)$filtered['c'] . ' 件</span></div>';

        if (!$result['list']) {
            echo '<div class="ui-card ui-mt-2"><div class="ui-empty"><div class="icon">🎁</div>'
                . ($hasFilter ? '没有符合条件的商品' : '暂无可兑换商品') . '</div></div>';
        } else {
            echo '<ul class="shoplist list ui-list ui-mt-2">';
            foreach ($result['list'] as $g) {
                echo '<li>';
                if (!empty($g['image'])) echo '<img src="' . $this->ToHtm($g['image']) . '" alt="" style="max-width:60px;margin-right:8px;vertical-align:middle"/>';
                echo '<strong>' . $this->ToHtm($g['name']) . '</strong>';
                echo '<span class="ui-flex ui-gap-2 ui-center"><span class="ui-tag ui-tag-warning">' . (int)$g['points'] . ' 积分</span>'
                    . '<span class="ui-tag ui-tag-muted">库存 ' . (int)$g['stock'] . '</span></span>';
                if (!empty($g['intro'])) echo '<div class="meta ui-mt-2">' . $this->ToHtm($g['intro']) . '</div>';
                echo '<div class="ui-flex ui-gap-2 ui-center ui-mt-2">';
                if (!$isLogin) {
                    echo '<a class="ui-btn ui-btn-sm" href="../waplogin.php">登录后兑换</a>';
                } elseif ($myPoint < (int)$g['points']) {
                    echo '<span class="ui-alert ui-alert-warning" style="margin:0">积分不足（还差 ' . ((int)$g['points'] - $myPoint) . ' 分）</span>'
                        . '<a class="ui-btn ui-btn-sm" href="../signin/index.php">去赚积分</a>';
                } else {
                    echo '<a class="ui-btn ui-btn-sm ui-btn-primary" href="point_buy.php?id=' . (int)$g['id'] . '">兑换</a>';
                }
                echo '</div>';
                echo '</li>';
            }
            echo '</ul>';
        }

        if ($result['pageCount'] > 1) {
            echo '<div class="ui-pager">';
            if ($page > 1) echo '<a href="' . $this->pageUrl($q, $minRaw, $maxRaw, $sort, $page - 1) . '">上一页</a>';
            echo '<span class="current">' . $page . ' / ' . (int)$result['pageCount'] . '</span>';
            if ($page < $result['pageCount']) echo '<a href="' . $this->pageUrl($q, $minRaw, $maxRaw, $sort, $page + 1) . '">下一页</a>';
            echo '</div>';
        }

        echo '<div class="ui-flex ui-gap-2 ui-mt-2"><a class="ui-btn ui-btn-ghost" href="../wapindex.php">返回首页</a>';
        if ($isLogin) echo '<a class="ui-btn" href="point_orders.php">我的兑换记录</a>';
        echo '</div>';
        $this->showDown();
    }

    /** 筛选+排序链接（不含页码） */
    private function filterUrl($q, $minRaw, $maxRaw, $sort) {
        $qs = [];
        if ($q !== '') $qs[] = 'q=' . urlencode($q);
        if ($minRaw !== '') $qs[] = 'minpoints=' . urlencode($minRaw);
        if ($maxRaw !== '') $qs[] = 'maxpoints=' . urlencode($maxRaw);
        if ($sort !== 'default') $qs[] = 'sort=' . $sort;
        return 'point.php' . ($qs ? '?' . implode('&', $qs) : '');
    }

    /** 分页链接（保留筛选与排序） */
    private function pageUrl($q, $minRaw, $maxRaw, $sort, $p) {
        $url = $this->filterUrl($q, $minRaw, $maxRaw, $sort);
        return $url . (strpos($url, '?') === false ? '?' : '&') . 'page=' . $p;
    }
}

$page = new ShopPointPage();
$page->run();
