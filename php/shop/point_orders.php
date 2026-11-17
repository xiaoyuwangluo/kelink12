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
        echo '<div class="subtitle">我的兑换记录</div><div class="content">';
        echo '我的积分：' . (int)($user['point'] ?? 0) . '<br/>';

        $tabs = ['all' => '全部'] + self::STATUS_TEXT;
        foreach ($tabs as $st => $label) {
            $cnt = ($st === 'all') ? $total : ($counts[(int)$st] ?? 0);
            $txt = $label . '(' . $cnt . ')';
            echo ((string)$status === (string)$st)
                ? '<strong>[' . $txt . ']</strong> '
                : '<a href="point_orders.php' . ((string)$st === 'all' ? '' : '?status=' . $st) . '">' . $txt . '</a> ';
        }

        echo '<div class="time">共 ' . $total . ' 条兑换记录，当前条件 ' . (int)$sum['c'] . ' 条，累计消耗积分 '
            . (int)$sum['sp'] . '，本页 ' . count($result['list']) . ' 条</div>';

        if (!$result['list']) {
            echo $status === 'all' ? '暂无兑换记录。' : '该状态下暂无兑换记录。';
        } else {
            echo '<ul class="loglist">';
            foreach ($result['list'] as $o) {
                $st = (int)$o['status'];
                echo '<li>#' . (int)$o['id'] . ' ' . $this->ToHtm($o['goodsname'])
                    . ' 积分 ' . (int)$o['points'] . ' × ' . (int)$o['num']
                    . ' <strong>[' . (self::STATUS_TEXT[$st] ?? '未知') . ']</strong> '
                    . ($o['addtime'] ? date('m-d H:i', strtotime($o['addtime'])) : '');
                if (!empty($o['note'])) echo '<br/><span class="time">备注：' . $this->ToHtm($o['note']) . '</span>';
                echo '</li>';
            }
            echo '</ul>';
        }
        echo '</div>';

        if ($result['pageCount'] > 1) {
            $pageUrl = function ($p) use ($status) {
                return 'point_orders.php?' . ($status === 'all' ? '' : 'status=' . $status . '&') . 'page=' . $p;
            };
            echo '<div class="pager">';
            if ($page > 1) echo '<a href="' . $pageUrl($page - 1) . '">上一页</a> ';
            echo '第' . $page . '/' . $result['pageCount'] . '页 ';
            if ($page < $result['pageCount']) echo '<a href="' . $pageUrl($page + 1) . '">下一页</a>';
            echo '</div>';
        }

        echo '<div class="nav"><a href="point.php">继续兑换</a> ';
        echo '<a href="../myaccount/index.php">我的账户</a></div>';
        $this->showDown();
    }
}

$page = new ShopPointOrdersPage();
$page->run();
