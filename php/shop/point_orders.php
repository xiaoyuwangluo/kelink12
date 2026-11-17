<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 积分商城 - 我的兑换记录 (状态筛选 / 汇总 / 分页)
 * 兑换状态: 0待发放 1已发放 2已取消
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class ShopPointOrdersPage extends WapPage {
    const STATUS_TEXT = [0 => '待发放', 1 => '已发放', 2 => '已取消'];

    public function run() {
        $this->checkLogin();
        $db = $this->db;
        $uid = $this->userSession->getUserId();
        $user = $this->userSession->getUserVo();

        // 状态筛选
        $status = (string)($_GET['status'] ?? 'all');
        if (!in_array($status, ['all', '0', '1', '2'], true)) $status = 'all';
        $where = 'userid=?';
        $params = [$uid];
        if ($status !== 'all') {
            $where .= ' AND status=?';
            $params[] = (int)$status;
        }

        $page = max(1, (int)($_GET['page'] ?? 1));
        $result = $db->getPage('WapPointOrder', 'id,goodsname,points,num,status,note,addtime',
            $where, 'id DESC', $page, 20, $params);

        // 各状态数量与当前条件累计消耗积分
        $counts = [];
        foreach ($db->getAll("SELECT status, COUNT(*) AS c FROM WapPointOrder WHERE userid=? GROUP BY status", [$uid]) as $r) {
            $counts[(int)$r['status']] = (int)$r['c'];
        }
        $total = array_sum($counts);
        $sum = $db->getOne("SELECT COUNT(*) AS c, COALESCE(SUM(points*num),0) AS sp FROM WapPointOrder WHERE " . $where, $params);

        $this->strTitle = '我的兑换记录';
        $this->showTop($this->strTitle);
        echo '<div class="ui-breadcrumb"><a href="' . $this->http_start . 'wapindex.php">首页</a> / <a href="point.php">积分商城</a> / 兑换记录</div>';
        echo '<div class="subtitle">我的兑换记录</div>';

        echo '<div class="ui-card"><div class="ui-flex ui-between ui-center"><span class="ui-tag ui-tag-muted">我的积分</span>'
            . '<span class="price">' . (int)($user['point'] ?? 0) . '</span></div></div>';

        // 状态筛选 tabs
        $tabs = ['all' => '全部'] + self::STATUS_TEXT;
        echo '<div class="ui-tabs ui-mt-2">';
        foreach ($tabs as $st => $label) {
            $cnt = ($st === 'all') ? $total : ($counts[(int)$st] ?? 0);
            $txt = $label . '(' . $cnt . ')';
            $href = 'point_orders.php' . ((string)$st === 'all' ? '' : '?status=' . $st);
            echo ((string)$status === (string)$st)
                ? '<a class="is-active" href="' . $href . '">' . $txt . '</a>'
                : '<a href="' . $href . '">' . $txt . '</a>';
        }
        echo '</div>';

        echo '<div class="ui-flex ui-flex-wrap ui-gap-2 ui-center ui-mt-2">'
            . '<span class="ui-tag">共 ' . $total . ' 条记录</span>'
            . '<span class="ui-tag ui-tag-muted">当前条件 ' . (int)$sum['c'] . ' 条</span>'
            . '<span class="ui-tag ui-tag-muted">累计消耗 ' . (int)$sum['sp'] . ' 积分</span></div>';

        if (!$result['list']) {
            echo '<div class="ui-card ui-mt-2"><div class="ui-empty"><div class="icon">📦</div>'
                . ($status === 'all' ? '暂无兑换记录' : '该状态下暂无兑换记录') . '</div></div>';
        } else {
            echo '<ul class="loglist list ui-list ui-mt-2">';
            foreach ($result['list'] as $o) {
                $st = (int)$o['status'];
                $tagClass = $st === 1 ? 'ui-tag-success' : ($st === 2 ? 'ui-tag-muted' : 'ui-tag-warning');
                echo '<li>#' . (int)$o['id'] . ' ' . $this->ToHtm($o['goodsname']);
                echo '<span class="ui-flex ui-gap-2 ui-center">'
                    . '<span class="ui-tag">积分 ' . (int)$o['points'] . ' × ' . (int)$o['num'] . '</span>'
                    . '<span class="ui-tag ' . $tagClass . '">' . (self::STATUS_TEXT[$st] ?? '未知') . '</span>'
                    . '<span class="ui-tag ui-tag-muted">' . ($o['addtime'] ? date('m-d H:i', strtotime($o['addtime'])) : '') . '</span>'
                    . '</span>';
                if (!empty($o['note'])) echo '<div class="meta">备注：' . $this->ToHtm($o['note']) . '</div>';
                echo '</li>';
            }
            echo '</ul>';
        }

        if ($result['pageCount'] > 1) {
            $pageUrl = function ($p) use ($status) {
                return 'point_orders.php?' . ($status === 'all' ? '' : 'status=' . $status . '&') . 'page=' . $p;
            };
            echo '<div class="ui-pager">';
            if ($page > 1) echo '<a href="' . $pageUrl($page - 1) . '">上一页</a>';
            echo '<span class="current">' . $page . ' / ' . (int)$result['pageCount'] . '</span>';
            if ($page < $result['pageCount']) echo '<a href="' . $pageUrl($page + 1) . '">下一页</a>';
            echo '</div>';
        }

        echo '<div class="ui-flex ui-gap-2 ui-mt-2"><a class="ui-btn ui-btn-ghost" href="point.php">继续兑换</a>';
        echo '<a class="ui-btn" href="../myaccount/index.php">我的账户</a></div>';
        $this->showDown();
    }
}

$page = new ShopPointOrdersPage();
$page->run();
