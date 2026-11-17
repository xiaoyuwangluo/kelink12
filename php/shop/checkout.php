<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 商城模块 - 结算下单 (对应原 chinabank 下单流程)
 * 两种来源：a) 购物车勾选条目(ids[])  b) 直接购买(shopid+num)
 * 填写地址手机后生成 WapShopOrder(状态0)，并清除对应购物车项
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class ShopCheckoutPage extends WapPage {
    public function run() {
        $this->checkLogin();
        $db = $this->db;
        $uid = $this->userSession->getUserId();

        // 解析下单条目: [shopid, title, price, num]
        $items = $this->collectItems($uid);
        if (!$items) $this->showError('没有可结算的商品', 'cart.php?classid=' . $this->classid);

        // 校验在售/库存，并按商品当前价格刷新条目
        list($items, $errors) = $this->validateItems($items);
        if ($errors) {
            $this->strTitle = '无法结算';
            $this->showTop($this->strTitle);
            echo '<div class="subtitle">无法结算</div><div class="content"><div class="error">以下商品暂不可结算：</div><ul>';
            foreach ($errors as $e) echo '<li>' . $this->ToHtm($e) . '</li>';
            echo '</ul><a href="cart.php?classid=' . $this->classid . '">返回购物车调整</a></div>';
            $this->showDown();
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->doSubmit($uid, $items);
            return;
        }

        // 结算确认页：商品清单 + 收货地址/手机
        $total = 0;
        foreach ($items as $it) $total += $it['price'] * $it['num'];
        // 预填最近一次订单的收货信息
        $last = $db->getOne("SELECT address,mobile FROM WapShopOrder WHERE userid=? AND address<>'' ORDER BY id DESC LIMIT 1", [$uid]);
        $lastAddr = $last['address'] ?? '';
        $lastMobile = $last['mobile'] ?? '';
        $money = (int)$db->getScalar("SELECT money FROM WapUser WHERE id=?", [$uid]);

        $this->strTitle = '确认订单';
        $this->showTop($this->strTitle);
        echo '<div class="subtitle">确认订单</div><div class="content">';
        echo '<ul class="checkoutlist">';
        foreach ($items as $it) {
            echo '<li>' . $this->ToHtm($it['title']) . ' ￥' . $it['price'] . ' × ' . $it['num'] . ' = ￥' . ($it['price'] * $it['num']) . '</li>';
        }
        echo '</ul>';
        echo '<div class="total">应付合计:￥' . $total . '</div>';
        echo '<div class="time">当前余额:￥' . $money
            . ($money < $total ? '（余额不足，可在支付页选择其他方式）' : '') . '</div>';
        echo '<form method="post" action="checkout.php?classid=' . $this->classid . '">';
        // 隐藏条目参数（直接购买模式）
        if (!empty($_GET['shopid'])) {
            echo '<input type="hidden" name="shopid" value="' . (int)$_GET['shopid'] . '"/>';
            echo '<input type="hidden" name="num" value="' . max(1, (int)($_GET['num'] ?? 1)) . '"/>';
        }
        // 购物车模式：传递勾选的购物车ID
        foreach ($items as $it) {
            if (!empty($it['cartid'])) echo '<input type="hidden" name="ids[]" value="' . (int)$it['cartid'] . '"/>';
        }
        echo '收货地址:<input type="text" name="address" maxlength="100" value="' . $this->ToHtm($lastAddr) . '" required/><br/>';
        echo '联系电话:<input type="text" name="mobile" maxlength="20" value="' . $this->ToHtm($lastMobile) . '" required/>'
            . '<br/><span class="time">请填写 6-20 位数字，可含 + 或 -</span><br/>';
        echo '<input type="submit" value="提交订单（￥' . $total . '）"/></form>';
        echo '</div>';
        echo '<div><a href="cart.php?classid=' . $this->classid . '">返回购物车</a></div>';
        $this->showDown();
    }

    /** 校验条目在售与库存，并用商品当前标题/价格刷新 */
    private function validateItems($items) {
        $db = $this->db;
        $errors = [];
        $ok = [];
        foreach ($items as $it) {
            $p = $db->getOne("SELECT title,price,stock,isshow FROM WapShop WHERE id=?", [$it['shopid']]);
            if (!$p) { $errors[] = '商品「' . $it['title'] . '」已不存在'; continue; }
            if ((int)$p['isshow'] !== 1) { $errors[] = '商品「' . $p['title'] . '」已下架'; continue; }
            $stock = (int)$p['stock'];
            if ($stock <= 0) { $errors[] = '商品「' . $p['title'] . '」已售罄'; continue; }
            if ($it['num'] > $stock) {
                $errors[] = '商品「' . $p['title'] . '」库存不足，仅剩 ' . $stock . ' 件';
                continue;
            }
            $it['title'] = $p['title'];
            $it['price'] = (int)round($p['price']);
            $ok[] = $it;
        }
        return [$ok, $errors];
    }

    /** 收集待结算条目，来源：POST/GET 的 ids[] 或 shopid+num */
    private function collectItems($uid) {
        $db = $this->db;
        $items = [];

        // 购物车条目
        $ids = [];
        $raw = $_POST['ids'] ?? $_GET['ids'] ?? null;
        if (is_array($raw)) $ids = array_map('intval', $raw);
        elseif (is_string($raw) && $raw !== '') $ids = array_map('intval', explode(',', $raw));
        $ids = array_values(array_filter(array_unique($ids), fn($v) => $v > 0));

        if ($ids) {
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $rows = $db->getAll("SELECT * FROM WapShopCart WHERE userid=? AND id IN ($placeholders)",
                array_merge([$uid], $ids));
            foreach ($rows as $r) {
                $items[] = ['cartid' => (int)$r['id'], 'shopid' => (int)$r['shopid'],
                            'title' => $r['title'], 'price' => (int)$r['price'], 'num' => (int)$r['num']];
            }
        }

        // 直接购买
        $shopid = (int)($_POST['shopid'] ?? $_GET['shopid'] ?? 0);
        if ($shopid > 0) {
            $num = max(1, min(999, (int)($_POST['num'] ?? $_GET['num'] ?? 1)));
            $p = $db->getOne("SELECT * FROM WapShop WHERE id=? AND isshow=1",[$shopid]);
            if ($p) {
                $items[] = ['cartid' => 0, 'shopid' => (int)$p['id'], 'title' => $p['title'],
                            'price' => (int)round($p['price']), 'num' => $num];
            }
        }
        return $items;
    }

    /** 提交订单：事务生成订单(状态0)+清购物车 */
    private function doSubmit($uid, $items) {
        $db = $this->db;
        $address = trim($_POST['address'] ?? '');
        $mobile = trim($_POST['mobile'] ?? '');
        if ($address === '' || mb_strlen($address) > 100) $this->showError('请填写正确的收货地址');
        if (!preg_match('/^[0-9+\-]{6,20}$/', $mobile)) $this->showError('请填写正确的联系电话');
        if (!$this->userSession->checkSubmitLimit()) $this->showError('提交过于频繁，请稍后再试');

        $orderIds = [];
        $cartIds = [];
        try {
            $db->beginTransaction();
            foreach ($items as $it) {
                $total = $it['price'] * $it['num'];
                $oid = $db->insert("INSERT INTO WapShopOrder (userid,shopid,num,totalprice,status,address,mobile,addtime) VALUES (?,?,?,?,0,?,?,NOW())",[$uid,$it['shopid'],$it['num'],$total,$address,$mobile]);
                $orderIds[] = $oid;
                if ($it['cartid'] > 0) $cartIds[] = $it['cartid'];
            }
            // 清除已结算的购物车条目
            if ($cartIds) {
                $placeholders = implode(',', array_fill(0, count($cartIds), '?'));
                $db->execute("DELETE FROM WapShopCart WHERE userid=? AND id IN ($placeholders)",
                    array_merge([$uid], $cartIds));
            }
            $db->commit();
        } catch (Exception $e) {
            if ($db->getPdo()->inTransaction()) $db->rollBack();
            $this->showError('下单失败，请重试');
        }

        $this->strTitle = '下单成功';
        $this->showTop($this->strTitle);
        echo '<div class="success">下单成功！共 ' . count($orderIds) . ' 个订单，请前往支付。</div><div class="content">';
        foreach ($orderIds as $oid) {
            echo '<div><a href="pay.php?classid=' . $this->classid . '&orderid=' . $oid . '">支付订单 #' . $oid . '</a></div>';
        }
        echo '</div>';
        echo '<div><a href="myorder.php?classid=' . $this->classid . '">我的订单</a> ';
        echo '<a href="cart.php?classid=' . $this->classid . '">购物车</a></div>';
        $this->showDown();
    }
}
$page = new ShopCheckoutPage();
$page->run();
