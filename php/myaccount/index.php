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
            . ' <a href="../shop/point_orders.php">积分兑换记录</a><br/>';

        // 充值表单
        echo '<hr/><form method="post" action="index.php">';
        echo '<input type="hidden" name="act" value="recharge"/>';
        echo '充值卡卡号:<input type="text" name="cardno" maxlength="32"/><br/>';
        echo '充值卡密码:<input type="password" name="cardpwd" maxlength="32"/><br/>';
        echo '<input type="submit" value="卡号充值"/></form>';
        echo '</div>';

        // 收支明细（类型筛选 + 分页）
        $type = (string)($_GET['type'] ?? 'all');
        if (!in_array($type, ['all', 'in', 'out'], true)) $type = 'all';
        $where = 'userid=?';
        $params = [$uid];
        if ($type !== 'all') {
            $where .= ' AND type=?';
            $params[] = $type;
        }
        $page = max(1, (int)($_GET['page'] ?? 1));
        $result = $db->getPage('WapAccountLog', '*', $where, 'id DESC', $page, 20, $params);

        // 各类型累计
        $sums = ['in' => [0, 0], 'out' => [0, 0]];
        foreach ($db->getAll("SELECT type, COUNT(*) AS c, SUM(num) AS s FROM WapAccountLog WHERE userid=? GROUP BY type", [$uid]) as $r) {
            $t = ($r['type'] === 'in') ? 'in' : 'out';
            $sums[$t] = [(int)$r['c'], (int)$r['s']];
        }
        $typeUrl = function ($t) {
            return 'index.php' . ($t === 'all' ? '' : '?type=' . $t);
        };

        echo '<div class="subtitle">收支明细</div><div class="content">';
        echo '筛选：';
        $tabs = ['all' => '全部', 'in' => '收入', 'out' => '支出'];
        foreach ($tabs as $t => $label) {
            $cnt = ($t === 'all') ? ($sums['in'][0] + $sums['out'][0]) : $sums[$t][0];
            $txt = $label . '(' . $cnt . ')';
            echo ($type === $t) ? '<strong>[' . $txt . ']</strong> ' : '<a href="' . $typeUrl($t) . '">' . $txt . '</a> ';
        }
        echo '<div class="time">累计收入￥' . $sums['in'][1] . '　累计支出￥' . $sums['out'][1]
            . '　当前显示 ' . count($result['list']) . ' 条</div>';
        if (!$result['list']) {
            echo $type === 'all' ? '暂无收支记录。' : '该类型下暂无收支记录。';
        } else {
            echo '<ul class="loglist">';
            foreach ($result['list'] as $log) {
                $isIn = $log['type'] === 'in';
                $typeName = $isIn ? '收入' : '支出';
                $sign = $isIn ? '+' : '-';
                echo '<li>' . $this->ToHtm($log['remark']) . ' [' . $typeName . ' ' . $sign . (int)$log['num'] . '] 余额￥' . (int)$log['balance']
                   . ' ' . date('m-d H:i', strtotime($log['addtime'])) . '</li>';
            }
            echo '</ul>';
        }
        echo '</div>';
        if ($result['pageCount'] > 1) {
            $pageUrl = function ($p) use ($type) {
                return '?' . ($type === 'all' ? '' : 'type=' . $type . '&') . 'page=' . $p;
            };
            echo '<div class="pager">';
            if ($page > 1) echo '<a href="' . $pageUrl($page - 1) . '">上一页</a> ';
            echo '第' . $page . '/' . $result['pageCount'] . '页 ';
            if ($page < $result['pageCount']) echo '<a href="' . $pageUrl($page + 1) . '">下一页</a>';
            echo '</div>';
        }
        echo '<div><a href="../shop/cart.php?classid=3">购物车</a> ';
        echo '<a href="../shop/myorder.php?classid=3">我的订单</a> ';
        echo '<a href="../myfile.php">用户中心</a></div>';
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
            $db->insert("INSERT INTO WapAccountLog (userid,type,num,balance,remark,addtime) VALUES (?,?,?,?,?,NOW())",[$uid,'in',$money,$balance,'点卡充值 ' . $cardno]);
            $db->commit();
        } catch (Exception $e) {
            if ($db->getPdo()->inTransaction()) $db->rollBack();
            $this->showError('充值失败，请重试');
            return;
        }
        header('Location: index.php?recharged=' . $money);
        exit;
    }
}
$page = new MyAccountPage();
$page->run();
