<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 会员中心 - 我的钱包 (余额/积分、WapAccountLog 收支明细、点卡充值)
 * 点卡校验逻辑与 shop/pay.php 充值卡支付一致：cardno + md5(cardpwd) + isused=0
 */
require_once __DIR__ . '/../includes/bootstrap.php';
require_once KL_ROOT . '/includes/Level.php';

class MyAccountPage extends WapPage {
    public function run() {
        $this->checkLogin();
        $db = $this->db;
        $uid = $this->userSession->getUserId();

        // 点卡充值
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['act'] ?? '') === 'recharge') {
            $this->doRecharge($uid);
            return;
        }

        $user = $this->userVo;
        $this->strTitle = '我的钱包';
        $this->showTop($this->strTitle);
        if (isset($_GET['recharged'])) echo '<div class="success">充值成功，已到账￥' . (int)$_GET['recharged'] . '</div>';
        echo '<div class="subtitle">我的钱包</div><div class="content">';
        echo '账户余额:￥' . (int)$user['money'] . '<br/>';
        echo '积分:' . (int)$user['point'] . ' ' . kl_level_tag_html((int)$user['point'])
            . ' <a href="../shop/point_orders.php?siteid=' . $this->siteid . '">积分兑换记录</a><br/>';

        // 充值表单
        echo '<hr/><form method="post" action="index.php?siteid=' . $this->siteid . '">';
        echo '<input type="hidden" name="act" value="recharge"/>';
        echo '充值卡卡号:<input type="text" name="cardno" maxlength="32"/><br/>';
        echo '充值卡密码:<input type="password" name="cardpwd" maxlength="32"/><br/>';
        echo '<input type="submit" value="卡号充值"/></form>';
        echo '</div>';

        // 收支明细
        $page = max(1, (int)($_GET['page'] ?? 1));
        $result = $db->getPage('WapAccountLog', '*', 'userid=? AND siteid=?', 'id DESC', $page, 20, [$uid, $this->siteid]);
        echo '<div class="subtitle">收支明细</div><div class="content">';
        if (!$result['list']) {
            echo '暂无收支记录。';
        } else {
            echo '<ul class="loglist">';
            foreach ($result['list'] as $log) {
                $type = $log['type'] === 'in' ? '收入' : '支出';
                $sign = $log['type'] === 'in' ? '+' : '-';
                echo '<li>' . $this->ToHtm($log['remark']) . ' [' . $type . ' ' . $sign . (int)$log['num'] . '] 余额￥' . (int)$log['balance']
                   . ' ' . date('m-d H:i', strtotime($log['addtime'])) . '</li>';
            }
            echo '</ul>';
        }
        echo '</div>';
        if ($result['pageCount'] > 1) {
            echo '<div class="pager">';
            if ($page > 1) echo '<a href="?siteid=' . $this->siteid . '&page=' . ($page-1) . '">上一页</a> ';
            echo '第' . $page . '/' . $result['pageCount'] . '页 ';
            if ($page < $result['pageCount']) echo '<a href="?siteid=' . $this->siteid . '&page=' . ($page+1) . '">下一页</a>';
            echo '</div>';
        }
        echo '<div><a href="../shop/cart.php?siteid=' . $this->siteid . '&classid=3">购物车</a> ';
        echo '<a href="../shop/myorder.php?siteid=' . $this->siteid . '&classid=3">我的订单</a> ';
        echo '<a href="../myfile.php?siteid=' . $this->siteid . '">用户中心</a></div>';
        $this->showDown();
    }

    /** 点卡充值：事务校验卡→置已用→加余额→写流水(in) */
    private function doRecharge($uid) {
        $db = $this->db;
        $cardno = trim($_POST['cardno'] ?? '');
        $cardpwd = trim($_POST['cardpwd'] ?? '');
        if ($cardno === '' || $cardpwd === '') $this->showError('请填写卡号和密码');
        if (!preg_match('/^[A-Za-z0-9_\-]{4,32}$/', $cardno)) $this->showError('卡号格式不正确');

        // 参数化校验充值卡
        $card = $db->getOne("SELECT * FROM WapCard WHERE cardno=? AND cardpwd=? AND isused=0",
            [$cardno, md5($cardpwd)]);
        if (!$card) $this->showError('充值卡无效或已被使用');

        try {
            $db->beginTransaction();
            $n = $db->execute("UPDATE WapCard SET isused=1, userid=?, usetime=NOW() WHERE id=? AND isused=0", [$uid, $card['id']]);
            if ($n != 1) { $db->rollBack(); $this->showError('充值卡已被使用'); return; }
            $money = (int)$card['money'];
            $db->execute("UPDATE WapUser SET money=money+? WHERE id=?", [$money, $uid]);
            $balance = (int)$db->getScalar("SELECT money FROM WapUser WHERE id=?", [$uid]);
            $db->insert("INSERT INTO WapAccountLog (siteid,userid,type,num,balance,remark,addtime) VALUES (?,?,?,?,?,?,NOW())",
                [$this->siteid, $uid, 'in', $money, $balance, '点卡充值 ' . $cardno]);
            $db->commit();
        } catch (Exception $e) {
            if ($db->getPdo()->inTransaction()) $db->rollBack();
            $this->showError('充值失败，请重试');
            return;
        }
        header('Location: index.php?siteid=' . $this->siteid . '&recharged=' . $money);
        exit;
    }
}
$page = new MyAccountPage();
$page->run();
