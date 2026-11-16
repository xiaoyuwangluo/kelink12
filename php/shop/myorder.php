<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 商城模块 - 我的订单 (列表/详情/取消/确认收货)
 * 订单状态: 0待付款 1已付款(待发货) 2已发货 3已完成 -1已取消 -2已退款
 */
require_once __DIR__ . '/../includes/bootstrap.php';
require_once KL_ROOT . '/includes/Community.php';

class ShopMyOrderPage extends WapPage {
    const STATUS_MAP = [
        0 => '待付款', 1 => '已付款(待发货)', 2 => '已发货', 3 => '已完成', -1 => '已取消', -2 => '已退款',
    ];

    public function run() {
        $this->checkLogin();
        $db = $this->db;
        $uid = $this->userSession->getUserId();

        // 取消订单（仅待付款可取消）
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['act'] ?? '') === 'cancel') {
            $oid = (int)($_POST['orderid'] ?? 0);
            $n = $db->execute("UPDATE WapShopOrder SET status=-1 WHERE id=? AND userid=? AND status=0",[$oid,$uid]);
            header('Location: myorder.php?classid=' . $this->classid . ($n == 1 ? '&done=1' : '&err=1'));
            exit;
        }
        // 确认收货（仅已发货可确认）
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['act'] ?? '') === 'confirm') {
            $oid = (int)($_POST['orderid'] ?? 0);
            $n = $db->execute("UPDATE WapShopOrder SET status=3 WHERE id=? AND userid=? AND status=2",[$oid,$uid]);
            header('Location: myorder.php?classid=' . $this->classid . ($n == 1 ? '&confirmed=1' : '&err=1'));
            exit;
        }

        $orderid = (int)($_GET['orderid'] ?? 0);
        if ($orderid > 0) {
            $this->showDetail($uid, $orderid);
            return;
        }

        // 订单列表（状态筛选 + 分页）
        $status = (string)($_GET['status'] ?? 'all');
        if (!in_array($status, ['all', '0', '1', '2', '3', '-1', '-2'], true)) $status = 'all';
        $where = 'userid=?';
        $params = [$uid];
        if ($status !== 'all') {
            $where .= ' AND status=?';
            $params[] = (int)$status;
        }
        $page = max(1, (int)($_GET['page'] ?? 1));
        $result = $db->getPage('WapShopOrder',
            'WapShopOrder.*, (SELECT title FROM WapShop WHERE WapShop.id=WapShopOrder.shopid) AS shoptitle',
            $where, 'id DESC', $page, 20, $params);

        // 各状态数量
        $statusCounts = [];
        foreach ($db->getAll("SELECT status, COUNT(*) AS c FROM WapShopOrder WHERE userid=? GROUP BY status", [$uid]) as $r) {
            $statusCounts[(int)$r['status']] = (int)$r['c'];
        }
        $totalCount = array_sum($statusCounts);
        $tabUrl = function ($st) {
            return 'myorder.php?classid=' . $this->classid . ($st === 'all' ? '' : '&status=' . $st);
        };

        $this->strTitle = '我的订单';
        $this->showTop($this->strTitle);
        if (($_GET['done'] ?? '') === '1') echo '<div class="success">订单已取消</div>';
        if (($_GET['confirmed'] ?? '') === '1') echo '<div class="success">已确认收货，交易完成</div>';
        if (($_GET['err'] ?? '') === '1') echo '<div class="error">操作失败：订单不存在或状态不允许该操作</div>';
        echo '<div class="subtitle">我的订单</div><div class="content">';
        $tabs = ['all' => '全部'] + self::STATUS_MAP;
        foreach ($tabs as $st => $label) {
            $cnt = ($st === 'all') ? $totalCount : ($statusCounts[(int)$st] ?? 0);
            $txt = $label . '(' . $cnt . ')';
            echo ((string)$status === (string)$st)
                ? '<strong>[' . $txt . ']</strong> '
                : '<a href="' . $tabUrl((string)$st) . '">' . $txt . '</a> ';
        }
        echo '<div class="time">共 ' . $totalCount . ' 单，当前显示 ' . count($result['list']) . ' 单</div>';
        if (!$result['list']) {
            echo $status === 'all' ? '暂无订单。' : '该状态下暂无订单。';
            echo '<a href="list.php?classid=' . $this->classid . '">去购物</a>';
        } else {
            echo '<ul class="orderlist">';
            foreach ($result['list'] as $o) {
                $st = (int)$o['status'];
                $stName = self::STATUS_MAP[$st] ?? '未知';
                echo '<li>#' . (int)$o['id'] . ' ' . $this->ToHtm($o['shoptitle'] ?? '') . ' ￥' . (int)round($o['totalprice'])
                    . ' ×' . (int)$o['num'] . ' [' . $stName . ']'
                    . '<br/><span class="time">' . $this->ToHtm($o['addtime'] ?? '') . '</span>';
                echo ' <a href="myorder.php?classid=' . $this->classid . '&orderid=' . (int)$o['id'] . '">详情</a>';
                if ($st === 0) {
                    echo ' <a href="pay.php?classid=' . $this->classid . '&orderid=' . (int)$o['id'] . '">支付</a>';
                }
                echo '</li>';
            }
            echo '</ul>';
        }
        echo '</div>';
        if ($result['pageCount'] > 1) {
            $pageUrl = function ($p) use ($status) {
                return '?classid=' . $this->classid . ($status === 'all' ? '' : '&status=' . $status) . '&page=' . $p;
            };
            echo '<div class="pager">';
            if ($page > 1) echo '<a href="' . $pageUrl($page - 1) . '">上一页</a> ';
            echo '第' . $page . '/' . $result['pageCount'] . '页 ';
            if ($page < $result['pageCount']) echo '<a href="' . $pageUrl($page + 1) . '">下一页</a>';
            echo '</div>';
        }
        echo '<div><a href="cart.php?classid=' . $this->classid . '">购物车</a> ';
        echo '<a href="list.php?classid=' . $this->classid . '">商城</a></div>';
        $this->showDown();
    }

    /** 订单详情 */
    private function showDetail($uid, $orderid) {
        $db = $this->db;
        $o = $db->getOne("SELECT * FROM WapShopOrder WHERE id=? AND userid=?",[$orderid,$uid]);
        if (!$o) $this->showError('订单不存在', 'myorder.php?classid=' . $this->classid);
        $shop = $db->getOne("SELECT title FROM WapShop WHERE id=?", [$o['shopid']]);
        $st = (int)$o['status'];
        $stName = self::STATUS_MAP[$st] ?? '未知';

        $this->strTitle = '订单详情';
        $this->showTop($this->strTitle);
        echo '<div class="subtitle">订单详情 #' . $orderid . '</div><div class="content">';
        echo '商品:' . $this->ToHtm($shop['title'] ?? '已删除商品') . '<br/>';
        echo '数量:' . (int)$o['num'] . '<br/>';
        echo '金额:￥' . (int)round($o['totalprice']) . '<br/>';
        echo '状态:' . $stName . '<br/>';
        echo '地址:' . $this->ToHtm($o['address']) . '<br/>';
        echo '电话:' . $this->ToHtm($o['mobile']) . '<br/>';
        echo '下单时间:' . $this->ToHtm($o['addtime']) . '<br/>';
        if ($st === 0) {
            echo '<form method="post" action="myorder.php?classid=' . $this->classid . '">';
            echo '<input type="hidden" name="act" value="cancel"/><input type="hidden" name="orderid" value="' . $orderid . '"/>';
            echo '<input type="submit" value="取消订单"/></form>';
            echo '<a href="pay.php?classid=' . $this->classid . '&orderid=' . $orderid . '">去支付</a><br/>';
        } elseif ($st === 2) {
            echo '<form method="post" action="myorder.php?classid=' . $this->classid . '">';
            echo '<input type="hidden" name="act" value="confirm"/><input type="hidden" name="orderid" value="' . $orderid . '"/>';
            echo '<input type="submit" value="确认收货"/></form>';
        } elseif ($st === 1) {
            echo '<div class="time">商家正在备货，请耐心等待发货</div>';
        } elseif ($st === -2) {
            echo '<div class="success">退款已退回账户余额</div>';
        }
        echo '</div>';
        echo '<div><a href="myorder.php?classid=' . $this->classid . '">返回订单列表</a></div>';
        $this->showDown();
    }
}
$page = new ShopMyOrderPage();
$page->run();
