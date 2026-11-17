<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 商城模块 - 订单支付 (对应原版 chinabank 支付网关页)
 * 途径a: 余额支付 —— 校验 WapUser.money >= 订单额，扣款+写AccountLog(out)+订单置1+扣库存
 * 途径b: 充值卡支付 —— 校验 WapCard(cardno+md5(cardpwd),isused=0)，卡置已用、加余额、写AccountLog(in)，再以余额完成支付
 * 途径c/d: 支付宝/微信第三方网关 —— 仅当 KL_AliPay_Open/KL_WxPay_Open=1 且凭据齐全时显示并跳转网关，
 *          否则显示"通道未启用"说明；实际结算由 shop/notify_ali.php、notify_wx.php 回调完成
 * 全程参数化SQL + 事务
 */
require_once __DIR__ . '/../includes/bootstrap.php';
require_once KL_ROOT . '/includes/PayGateway.php';

class ShopPayPage extends WapPage {
    public function run() {
        $this->checkLogin();
        $db = $this->db;
        $uid = $this->userSession->getUserId();
        $orderid = (int)($_GET['orderid'] ?? $_POST['orderid'] ?? 0);
        if ($orderid <= 0) $this->showError('参数错误', 'myorder.php?classid=' . $this->classid);

        $order = $db->getOne("SELECT * FROM WapShopOrder WHERE id=? AND userid=?",[$orderid,$uid]);
        if (!$order) $this->showError('订单不存在');
        if ((int)$order['status'] != 0) $this->showError('该订单当前状态不可支付', 'myorder.php?classid=' . $this->classid);

        $total = (int)round($order['totalprice']); // 金额按整数元处理

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $act = $_POST['act'] ?? '';
            if ($act === 'balance') {
                $this->payByBalance($uid, $order, $total);
            } elseif ($act === 'card') {
                $this->payByCard($uid, $order, $total);
            } elseif ($act === 'alipay') {
                $this->payByGateway('ali', $order, $total, $orderid);
            } elseif ($act === 'wxpay') {
                $this->payByGateway('wx', $order, $total, $orderid);
            }
            return;
        }

        // 支付页
        $user = $this->userVo;
        $this->strTitle = '订单支付';
        $this->showTop($this->strTitle);
        echo '<div class="ui-breadcrumb"><a href="' . $this->http_start . 'wapindex.php">首页</a> / <a href="list.php?classid=' . $this->classid . '">商城</a> / 订单支付</div>';
        echo '<div class="subtitle">订单支付 #' . $orderid . '</div>';

        echo '<div class="ui-card">';
        echo '<div class="ui-flex ui-between ui-center"><span class="ui-tag ui-tag-muted">应付金额</span><span class="price">￥' . $total . '</span></div>';
        echo '<div class="ui-flex ui-between ui-center ui-mt-2"><span class="ui-tag ui-tag-muted">账户余额</span><span class="ui-tag">￥' . (int)$user['money'] . '</span></div>';
        echo '</div>';

        echo '<div class="ui-card">';
        echo '<div class="ui-card-head"><span class="ui-card-title">余额支付</span><span class="ui-card-extra">即时到账</span></div>';
        echo '<form method="post" action="pay.php?classid=' . $this->classid . '&orderid=' . $orderid . '">';
        echo '<input type="hidden" name="act" value="balance"/><input type="hidden" name="orderid" value="' . $orderid . '"/>';
        echo '<input class="ui-btn ui-btn-primary ui-btn-block" type="submit" value="使用余额支付"/></form>';
        echo '</div>';

        echo '<div class="ui-card">';
        echo '<div class="ui-card-head"><span class="ui-card-title">充值卡支付</span><span class="ui-card-extra">先充值后扣款</span></div>';
        echo '<form method="post" action="pay.php?classid=' . $this->classid . '&orderid=' . $orderid . '">';
        echo '<input type="hidden" name="act" value="card"/><input type="hidden" name="orderid" value="' . $orderid . '"/>';
        echo '<div class="ui-field"><label for="cardno">充值卡卡号</label><input type="text" id="cardno" name="cardno" maxlength="32"/></div>';
        echo '<div class="ui-field"><label for="cardpwd">充值卡密码</label><input type="password" id="cardpwd" name="cardpwd" maxlength="32"/></div>';
        echo '<input class="ui-btn ui-btn-block" type="submit" value="充值卡支付"/></form>';
        echo '</div>';

        // 第三方网关选项卡: 仅当开关开启且凭据齐全时显示支付按钮，否则显示"通道未启用"说明
        echo '<div class="ui-card">';
        echo '<div class="ui-card-head"><span class="ui-card-title">第三方网关支付</span></div>';
        $aliReady = (new AliPay())->ready();
        $wxReady = (new WxPay())->ready();
        if ($aliReady) {
            echo '<form method="post" action="pay.php?classid=' . $this->classid . '&orderid=' . $orderid . '">';
            echo '<input type="hidden" name="act" value="alipay"/><input type="hidden" name="orderid" value="' . $orderid . '"/>';
            echo '<input class="ui-btn ui-btn-block" type="submit" value="支付宝支付"/></form>';
        } else {
            echo '<div class="ui-alert ui-alert-warning">支付宝：通道未启用（需 KL_AliPay_Open=1 且凭据齐全）</div>';
        }
        if ($wxReady) {
            echo '<form method="post" action="pay.php?classid=' . $this->classid . '&orderid=' . $orderid . '">';
            echo '<input type="hidden" name="act" value="wxpay"/><input type="hidden" name="orderid" value="' . $orderid . '"/>';
            echo '<input class="ui-btn ui-btn-block" type="submit" value="微信支付(扫码)"/></form>';
        } else {
            echo '<div class="ui-alert ui-alert-warning">微信支付：通道未启用（需 KL_WxPay_Open=1 且凭据齐全）</div>';
        }
        echo '</div>';

        echo '<div class="ui-flex ui-gap-2 ui-mt-2"><a class="ui-btn ui-btn-ghost" href="myorder.php?classid=' . $this->classid . '">我的订单</a></div>';
        $this->showDown();
    }

    /** 途径a：余额支付（事务：扣款+流水+订单置1+扣库存） */
    private function payByBalance($uid, $order, $total) {
        $db = $this->db;
        try {
            $db->beginTransaction();
            if (!$this->doPayCore($uid, $order, $total)) {
                $db->rollBack();
                return; // doPayCore 内部已报错输出
            }
            $db->commit();
        } catch (Exception $e) {
            if ($db->getPdo()->inTransaction()) $db->rollBack();
            $this->showError('支付失败，请重试');
            return;
        }
        $this->paySuccess($order['id'], $total);
    }

    /** 途径b：充值卡支付（事务：卡校验→置已用→加余额→流水in→余额扣款支付） */
    private function payByCard($uid, $order, $total) {
        $db = $this->db;
        $cardno = trim($_POST['cardno'] ?? '');
        $cardpwd = trim($_POST['cardpwd'] ?? '');
        if ($cardno === '' || $cardpwd === '') $this->showError('请填写卡号和密码');
        if (!preg_match('/^[A-Za-z0-9_\-]{4,32}$/', $cardno)) $this->showError('卡号格式不正确');

        // 参数化校验充值卡：卡号+密码md5+未使用
        $card = $db->getOne("SELECT * FROM WapCard WHERE cardno=? AND cardpwd=? AND isused=0",
            [$cardno, md5($cardpwd)]);
        if (!$card) $this->showError('充值卡无效或已被使用');

        $cardMoney = (int)$card['money'];
        try {
            $db->beginTransaction();
            // 卡置已用（并发保护：仅未使用卡可置位）
            $n = $db->execute("UPDATE WapCard SET isused=1, userid=?, usetime=NOW() WHERE id=? AND isused=0", [$uid, $card['id']]);
            if ($n != 1) { $db->rollBack(); $this->showError('充值卡已被使用'); return; }
            // 加余额 + 写充值流水(in)
            $db->execute("UPDATE WapUser SET money=money+? WHERE id=?", [$cardMoney, $uid]);
            $balance = (int)$db->getScalar("SELECT money FROM WapUser WHERE id=?", [$uid]);
            $db->insert("INSERT INTO WapAccountLog (userid,type,num,balance,remark,addtime) VALUES (?,?,?,?,?,NOW())",[$uid,'in',$cardMoney,$balance,'充值卡充值 ' . $cardno]);
            // 以余额完成订单支付
            if (!$this->doPayCore($uid, $order, $total)) {
                $db->rollBack();
                return;
            }
            $db->commit();
        } catch (Exception $e) {
            if ($db->getPdo()->inTransaction()) $db->rollBack();
            $this->showError('充值支付失败，请重试');
            return;
        }
        $this->paySuccess($order['id'], $total, '已使用充值卡充值￥' . $cardMoney . '，');
    }

    /**
     * 支付核心（须在事务内调用）：
     * 校验余额→扣款→流水out→订单0→1(重复支付被拒)→扣库存
     * @return bool 成功返回true；失败时已输出错误页(内部exit)或返回false由调用方回滚
     */
    private function doPayCore($uid, $order, $total) {
        $db = $this->db;
        $money = (int)$db->getScalar("SELECT money FROM WapUser WHERE id=?", [$uid]);
        if ($money < $total) {
            $db->rollBack();
            $this->showError('余额不足（当前￥' . $money . '，应付￥' . $total . '），请先充值');
            return false;
        }
        // 扣款（条件更新防并发扣负）
        $n = $db->execute("UPDATE WapUser SET money=money-? WHERE id=? AND money>=?", [$total, $uid, $total]);
        if ($n != 1) { $db->rollBack(); $this->showError('余额不足，支付失败'); return false; }
        $balance = $money - $total;
        // 支出流水(out)
        $db->insert("INSERT INTO WapAccountLog (userid,type,num,balance,remark,addtime) VALUES (?,?,?,?,?,NOW())",[$uid,'out',$total,$balance,'支付订单 #' . $order['id']]);
        // 订单置已付：仅状态0可置1，重复支付在此被拒
        $n = $db->execute("UPDATE WapShopOrder SET status=1 WHERE id=? AND status=0", [$order['id']]);
        if ($n != 1) { $db->rollBack(); $this->showError('订单已支付或已取消，不可重复支付'); return false; }
        // 扣库存（库存不足则回滚）
        $n = $db->execute("UPDATE WapShop SET stock=stock-? WHERE id=? AND stock>=?", [$order['num'], $order['shopid'], $order['num']]);
        if ($n != 1) { $db->rollBack(); $this->showError('商品库存不足，支付失败'); return false; }
        return true;
    }

    /**
     * 途径c/d：第三方网关支付（支付宝页面跳转 / 微信 Native 下单）
     * 本方法只发起网关请求，订单状态变更由异步回调 notify_ali.php / notify_wx.php 完成
     */
    private function payByGateway($channel, $order, $total, $orderid) {
        $db = $this->db;
        $shop = $db->getOne("SELECT title FROM WapShop WHERE id=?", [$order['shopid']]);
        $subject = mb_substr($shop['title'] ?? '商城订单', 0, 60);
        $notifyUrl = $this->http_start . 'shop/notify_' . $channel . '.php';
        if ($channel === 'ali') {
            $pay = new AliPay();
            $returnUrl = $this->http_start . 'shop/myorder.php?classid=' . $this->classid;
            $url = $pay->pagePay($orderid, $total, $subject, $notifyUrl, $returnUrl);
            if ($url === false) $this->showError($pay->getLastError() ?: '支付宝发起支付失败');
            header('Location: ' . $url);
            exit;
        }
        $pay = new WxPay();
        $totalFen = (int)round((float)$order['totalprice'] * 100); // 微信按分计价
        $codeUrl = $pay->nativePay($orderid, $totalFen, $subject, $notifyUrl);
        if ($codeUrl === false) $this->showError($pay->getLastError() ?: '微信发起支付失败');
        $this->strTitle = '微信扫码支付';
        $this->showTop($this->strTitle);
        echo '<div class="success">下单成功，请使用微信扫描二维码支付</div>';
        echo '<div class="content">订单 #' . $orderid . ' 金额￥' . $total . '<br/>';
        echo '支付链接: <a href="' . $this->ToHtm($codeUrl) . '">' . $this->ToHtm($codeUrl) . '</a></div>';
        echo '<div><a href="myorder.php?classid=' . $this->classid . '">支付完成后查看订单</a></div>';
        $this->showDown();
    }

    /** 支付成功提示页 */
    private function paySuccess($orderid, $total, $prefix = '') {
        $this->strTitle = '支付成功';
        $this->showTop($this->strTitle);
        echo '<div class="success">' . $prefix . '支付成功！订单 #' . $orderid . ' 金额￥' . $total . '</div>';
        echo '<div><a href="myorder.php?classid=' . $this->classid . '">我的订单</a> ';
        echo '<a href="list.php?classid=' . $this->classid . '">继续购物</a></div>';
        $this->showDown();
    }
}
$page = new ShopPayPage();
$page->run();
