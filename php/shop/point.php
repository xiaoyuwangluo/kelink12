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
        echo '<div class="subtitle">积分商城</div>';

        $isLogin = $this->userSession->isLogin();
        $user = $isLogin ? $this->userSession->getUserVo() : null;
        $myPoint = $isLogin ? (int)($user['point'] ?? 0) : 0;
        if ($isLogin) {
            echo '<div class="content">我的积分：' . $myPoint . ' <a href="point_orders.php">我的兑换记录</a></div>';
        }

        // 搜索 / 区间 / 排序
        echo '<div class="content"><form method="get" action="point.php">';
        echo '商品名:<input type="text" name="q" value="' . $this->ToHtm($q) . '" size="10"/> ';
        echo '积分:<input type="text" name="minpoints" value="' . $this->ToHtm($minRaw) . '" size="4"/>-';
        echo '<input type="text" name="maxpoints" value="' . $this->ToHtm($maxRaw) . '" size="4"/> ';
        echo '<select name="sort">';
        foreach ($sortMap as $k => $v) {
            echo '<option value="' . $k . '"' . ($sort === $k ? ' selected="selected"' : '') . '>' . $v[0] . '</option>';
        }
        echo '</select> <input type="submit" value="筛选"/></form>';
        if ($notice !== '') echo '<div class="error">' . $notice . '</div>';
        echo '<div class="time">共 ' . $allCount . ' 件可兑换商品，当前条件 ' . (int)$filtered['c'] . ' 件，本页 '
            . count($result['list']) . ' 件'
            . ($hasFilter ? '　<a href="' . $clearUrl . '">清除筛选</a>' : '') . '</div>';

        if (!$result['list']) {
            echo $hasFilter ? '没有符合条件的商品。' : '暂无可兑换商品';
        } else {
            echo '<ul class="shoplist">';
            foreach ($result['list'] as $g) {
                echo '<li>';
                if (!empty($g['image'])) echo '<img src="' . $this->ToHtm($g['image']) . '" alt=""/>';
                echo '<strong>' . $this->ToHtm($g['name']) . '</strong><br/>';
                if (!empty($g['intro'])) echo $this->ToHtm($g['intro']) . '<br/>';
                echo '所需积分：' . (int)$g['points'] . ' 剩余库存：' . (int)$g['stock'] . '<br/>';
                if (!$isLogin) {
                    echo '<a href="../waplogin.php">登录后兑换</a>';
                } elseif ($myPoint < (int)$g['points']) {
                    echo '<span class="time">积分不足（还差 ' . ((int)$g['points'] - $myPoint) . ' 分）</span> '
                        . '<a href="../signin/index.php">去赚积分</a>';
                } else {
                    echo '<a href="point_buy.php?id=' . (int)$g['id'] . '">兑换</a>';
                }
                echo '</li>';
            }
            echo '</ul>';
        }
        echo '</div>';

        if ($result['pageCount'] > 1) {
            $pageUrl = function ($p) use ($q, $sort, $minRaw, $maxRaw) {
                $qs = [];
                if ($q !== '') $qs[] = 'q=' . urlencode($q);
                if ($minRaw !== '') $qs[] = 'minpoints=' . urlencode($minRaw);
                if ($maxRaw !== '') $qs[] = 'maxpoints=' . urlencode($maxRaw);
                if ($sort !== 'default') $qs[] = 'sort=' . $sort;
                $qs[] = 'page=' . $p;
                return 'point.php?' . implode('&', $qs);
            };
            echo '<div class="pager">';
            if ($page > 1) echo '<a href="' . $pageUrl($page - 1) . '">上一页</a> ';
            echo '第' . $page . '/' . $result['pageCount'] . '页 ';
            if ($page < $result['pageCount']) echo '<a href="' . $pageUrl($page + 1) . '">下一页</a>';
            echo '</div>';
        }

        echo '<div class="nav"><a href="../wapindex.php">返回首页</a>';
        if ($isLogin) echo ' <a href="point_orders.php">我的兑换记录</a>';
        echo '</div>';
        $this->showDown();
    }
}

$page = new ShopPointPage();
$page->run();
