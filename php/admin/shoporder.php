<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 管理后台 - 商城订单管理
 * 订单状态: 0待付款 1已付款(待发货) 2已发货 3已完成 -1已取消 -2已退款
 * 操作: 发货(1→2) / 完成(2→3) / 取消待付款(0→-1) / 退款(1|2→-2, 退余额+回补库存+流水+通知)
 */
require_once __DIR__ . '/../includes/bootstrap.php';
require_once KL_ROOT . '/includes/Community.php';

class AdminShopOrderPage extends WapPage {
    const STATUS_MAP = [
        0 => '待付款', 1 => '已付款(待发货)', 2 => '已发货', 3 => '已完成', -1 => '已取消', -2 => '已退款',
    ];

    public function run() {
        $this->checkAdminLogin();
        $db = $this->db;
        $back = 'shoporder.php';

        $status = $_GET['status'] ?? 'all';
        if (!in_array((string)$status, ['0', '1', '2', '3', '-1', '-2', 'all'], true)) $status = 'all';
        $q = trim($_GET['q'] ?? '');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $act = $_POST['act'] ?? '';
            $id = (int)($_POST['id'] ?? 0);
            $target = $this->backUrl($status, $q);
            if ($act === 'ship') {
                $this->doShip($id, $target);
            } elseif ($act === 'complete') {
                $this->doComplete($id, $target);
            } elseif ($act === 'cancel') {
                $this->doCancel($id, $target);
            } elseif ($act === 'refund') {
                $this->doRefund($id, $target);
            } else {
                $this->showError('非法操作', $back);
            }
            header('Location: ' . $target);
            exit;
        }

        // 统计卡片
        $counts = [];
        foreach (array_keys(self::STATUS_MAP) as $st) {
            try {
                $counts[$st] = (int)$db->getScalar("SELECT COUNT(*) FROM WapShopOrder WHERE status=?", [$st]);
            } catch (Exception $e) {
                $counts[$st] = 0;
            }
        }

        // 列表
        $page = max(1, (int)($_GET['page'] ?? 1));
        $where = '1=1';
        $params = [];
        if ($status !== 'all') {
            $where .= ' AND status=?';
            $params[] = (int)$status;
        }
        if ($q !== '') {
            $where .= ' AND (id=? OR mobile LIKE ? OR userid IN (SELECT id FROM WapUser WHERE username LIKE ?))';
            $like = '%' . $q . '%';
            $params[] = (int)$q;
            $params[] = $like;
            $params[] = $like;
        }
        $result = $db->getPage('WapShopOrder',
            'WapShopOrder.*,'
            . ' (SELECT username FROM WapUser WHERE WapUser.id=WapShopOrder.userid) AS username,'
            . ' (SELECT title FROM WapShop WHERE WapShop.id=WapShopOrder.shopid) AS shoptitle',
            $where, 'id DESC', $page, 20, $params);

        $this->strTitle = '商城订单';
        $this->showTop($this->strTitle);
        echo '<div class="subtitle">商城订单</div>';
        echo '<div><a href="index.php">返回后台首页</a></div>';

        echo '<div class="content">筛选：';
        $filters = ['all' => '全部'] + self::STATUS_MAP;
        foreach ($filters as $k => $label) {
            $text = $label . '(' . ($k === 'all' ? array_sum($counts) : ($counts[$k] ?? 0)) . ')';
            $cur = ((string)$status === (string)$k) ? '<strong>[' . $text . ']</strong>' : '<a href="' . $back . '?status=' . $k . '">' . $text . '</a>';
            echo $cur . ' ';
        }
        echo '</div>';

        echo '<div class="content"><form method="get" action="' . $back . '">'
            . '搜索：<input type="text" name="q" value="' . $this->ToHtm($q) . '" placeholder="订单号/手机号/用户名"/>'
            . '<input type="hidden" name="status" value="' . $this->ToHtm((string)$status) . '"/>'
            . '<input type="submit" value="查询"/></form></div>';

        echo '<div class="content"><ul class="list">';
        foreach ($result['list'] as $o) {
            $id = (int)$o['id'];
            $st = (int)$o['status'];
            echo '<li>#' . $id . ' ' . $this->ToHtm($o['shoptitle'] ?? '已删除商品')
                . ' × ' . (int)$o['num'] . ' ￥' . number_format((float)$o['totalprice'], 2)
                . ' [' . (self::STATUS_MAP[$st] ?? '未知') . ']<br/>';
            echo '会员:' . $this->ToHtm($o['username'] ?? ('ID ' . (int)$o['userid']))
                . ' 电话:' . $this->ToHtm($o['mobile'] ?: '未填写')
                . ' 地址:' . $this->ToHtm($o['address'] ?: '未填写') . '<br/>';
            echo '<span class="time">' . (!empty($o['addtime']) ? date('m-d H:i', strtotime($o['addtime'])) : '') . '</span> ';

            if ($st === 1) {
                echo $this->postBtn($id, 'ship', '发货', $status, $q) . ' ';
                echo $this->postBtn($id, 'refund', '退款', $status, $q);
            } elseif ($st === 2) {
                echo $this->postBtn($id, 'complete', '完成', $status, $q) . ' ';
                echo $this->postBtn($id, 'refund', '退款', $status, $q);
            } elseif ($st === 0) {
                echo $this->postBtn($id, 'cancel', '取消订单', $status, $q);
            } else {
                echo '不可操作';
            }
            echo '</li>';
        }
        if (empty($result['list'])) echo '<li>暂无订单</li>';
        echo '</ul></div>';

        if ($result['pageCount'] > 1) {
            echo '<div class="pager">';
            if ($page > 1) echo '<a href="' . $this->backUrl($status, $q) . '&page=' . ($page - 1) . '">上一页</a> ';
            echo '第' . $page . '/' . $result['pageCount'] . '页 ';
            if ($page < $result['pageCount']) echo '<a href="' . $this->backUrl($status, $q) . '&page=' . ($page + 1) . '">下一页</a>';
            echo '</div>';
        }
        $this->showDown();
    }

    /** 列表回跳地址（保留筛选与搜索） */
    private function backUrl($status, $q) {
        $url = 'shoporder.php?status=' . urlencode((string)$status);
        if ($q !== '') $url .= '&q=' . urlencode($q);
        return $url;
    }

    /** 操作按钮（POST 表单，防误触 GET） */
    private function postBtn($id, $act, $label, $status, $q) {
        $html = '<form method="post" action="' . $this->backUrl($status, $q) . '" style="display:inline">';
        $html .= '<input type="hidden" name="act" value="' . $act . '"/>';
        $html .= '<input type="hidden" name="id" value="' . (int)$id . '"/>';
        $html .= '<input type="submit" value="' . $label . '"/></form>';
        return $html;
    }

    /** 发货：1→2 */
    private function doShip($id, $back) {
        $db = $this->db;
        $order = $db->getOne("SELECT * FROM WapShopOrder WHERE id=?", [$id]);
        if (!$order) $this->showError('订单不存在', $back);
        $n = $db->execute("UPDATE WapShopOrder SET status=2 WHERE id=? AND status=1", [$id]);
        if ($n != 1) $this->showError('仅已付款订单可发货', $back);
        kl_notice_add($db, (int)$order['userid'], 0, 'order', 'shop', $id,
            '您的订单 #' . $id . ' 已发货，请注意查收');
    }

    /** 完成：2→3 */
    private function doComplete($id, $back) {
        $db = $this->db;
        $order = $db->getOne("SELECT * FROM WapShopOrder WHERE id=?", [$id]);
        if (!$order) $this->showError('订单不存在', $back);
        $n = $db->execute("UPDATE WapShopOrder SET status=3 WHERE id=? AND status=2", [$id]);
        if ($n != 1) $this->showError('仅已发货订单可标记完成', $back);
        kl_notice_add($db, (int)$order['userid'], 0, 'order', 'shop', $id,
            '您的订单 #' . $id . ' 已完成，感谢您的购买');
    }

    /** 取消待付款订单：0→-1（未扣款未扣库存） */
    private function doCancel($id, $back) {
        $db = $this->db;
        $order = $db->getOne("SELECT * FROM WapShopOrder WHERE id=?", [$id]);
        if (!$order) $this->showError('订单不存在', $back);
        $n = $db->execute("UPDATE WapShopOrder SET status=-1 WHERE id=? AND status=0", [$id]);
        if ($n != 1) $this->showError('仅待付款订单可取消', $back);
        kl_notice_add($db, (int)$order['userid'], 0, 'order', 'shop', $id,
            '您的订单 #' . $id . ' 已取消');
    }

    /** 退款：1|2→-2，事务内退余额+写流水+回补库存+通知 */
    private function doRefund($id, $back) {
        $db = $this->db;
        $order = $db->getOne("SELECT * FROM WapShopOrder WHERE id=?", [$id]);
        if (!$order) $this->showError('订单不存在', $back);
        if (!in_array((int)$order['status'], [1, 2], true)) $this->showError('仅已付款/已发货订单可退款', $back);

        $money = (int)round((float)$order['totalprice']);
        $uid = (int)$order['userid'];
        try {
            $db->beginTransaction();
            $n = $db->execute("UPDATE WapShopOrder SET status=-2 WHERE id=? AND status IN (1,2)", [$id]);
            if ($n != 1) {
                $db->rollBack();
                $this->showError('订单状态已变更，退款失败', $back);
            }
            $db->execute("UPDATE WapUser SET money=money+? WHERE id=?", [$money, $uid]);
            $balance = (int)$db->getScalar("SELECT money FROM WapUser WHERE id=?", [$uid]);
            $db->insert("INSERT INTO WapAccountLog (userid,type,num,balance,remark,addtime) VALUES (?,?,?,?,?,NOW())",
                [$uid, 'in', $money, $balance, '订单 #' . $id . ' 退款']);
            $db->execute("UPDATE WapShop SET stock=stock+? WHERE id=?", [max(1, (int)$order['num']), (int)$order['shopid']]);
            $db->commit();
        } catch (Exception $e) {
            if ($db->getPdo()->inTransaction()) $db->rollBack();
            $this->showError('退款失败，请重试', $back);
        }
        kl_notice_add($db, $uid, 0, 'order', 'shop', $id,
            '您的订单 #' . $id . ' 已退款，￥' . $money . ' 已退回账户余额');
    }
}

$page = new AdminShopOrderPage();
$page->run();
