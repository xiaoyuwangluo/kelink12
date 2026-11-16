<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 管理后台 - 充值卡管理 (对应原 WapCard 点卡管理)
 * 批量生成(随机卡号/密码md5存储) / 列表 / 作废(删除未使用卡)
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class AdminCardPage extends WapPage {
    /** 生成随机卡号：12位数字 */
    private function makeCardNo() {
        $no = '';
        for ($i = 0; $i < 12; $i++) $no .= (string)random_int(0, 9);
        return $no;
    }

    /** 生成随机卡密明文：10位字母数字 */
    private function makeCardPwd() {
        $chars = 'abcdefghjkmnpqrstuvwxyz23456789ABCDEFGHJKLMNPQRSTUVWXYZ';
        $pwd = '';
        $max = strlen($chars) - 1;
        for ($i = 0; $i < 10; $i++) $pwd .= $chars[random_int(0, $max)];
        return $pwd;
    }

    public function run() {
        $this->checkAdminLogin();
        $db = $this->db;
        $back = 'card.php';
        $generated = [];

        // POST 操作：批量生成 / 作废（防重复提交）
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->userSession->checkSubmitLimit()) {
                $this->showError('提交过于频繁，请稍后再试', $back);
            }
            $action = $_POST['action'] ?? '';
            if ($action === 'gen') {
                $num = min(100, max(1, (int)($_POST['num'] ?? 1)));
                $cardtype = trim($_POST['cardtype'] ?? '') ?: 'money';
                $money = max(0, (int)($_POST['money'] ?? 0));
                for ($i = 0; $i < $num; $i++) {
                    // 卡号查重
                    $cardno = $this->makeCardNo();
                    $try = 0;
                    while ($db->getScalar("SELECT COUNT(*) FROM WapCard WHERE cardno=?", [$cardno]) > 0 && $try < 10) {
                        $cardno = $this->makeCardNo();
                        $try++;
                    }
                    $plain = $this->makeCardPwd();
                    $db->insert("INSERT INTO WapCard (cardno,cardpwd,cardtype,money,isused,addtime) VALUES (?,?,?,?,0,datetime('now','localtime'))",[$cardno,md5($plain),$cardtype,$money]);
                    $generated[] = [$cardno, $plain];
                }
            } elseif ($action === 'void') {
                $id = (int)($_POST['id'] ?? 0);
                $row = $db->getOne("SELECT * FROM WapCard WHERE id=?",[$id]);
                if (!$row) {
                    $this->showError('充值卡不存在', $back);
                }
                if (!empty($row['isused'])) {
                    $this->showError('该卡已被使用，不能作废', $back);
                }
                // 作废：删除未使用的卡密记录
                $db->execute("DELETE FROM WapCard WHERE id=?",[$id]);
                $this->showSuccess('充值卡已作废', $back);
            } else {
                $this->showError('非法操作', $back);
            }
        }

        $this->strTitle = '充值卡管理';
        $this->showTop($this->strTitle);
        echo '<div class="subtitle">充值卡管理</div><div><a href="index.php">返回后台首页</a></div>';

        // 批量生成表单
        echo '<div class="content"><form method="post" action="' . $back . '">'
            . '<input type="hidden" name="action" value="gen"/>'
            . '数量 <input type="text" name="num" size="4" value="5"/> '
            . '类型 <input type="text" name="cardtype" size="8" value="money"/> '
            . '面值 <input type="text" name="money" size="5" value="100"/> '
            . '<input type="submit" value="批量生成"/></form></div>';

        // 生成结果（明文卡密仅本次展示，数据库存md5）
        if ($generated) {
            echo '<div class="success">已生成 ' . count($generated) . ' 张充值卡，请保存卡密：<br/>';
            foreach ($generated as $g) {
                echo $this->ToHtm($g[0]) . ' - ' . $this->ToHtm($g[1]) . '<br/>';
            }
            echo '</div>';
        }

        // 列表（分页）
        $page = max(1, (int)($_GET['page'] ?? 1));
        $result = $db->getPage('WapCard', '*','', 'id DESC', $page, 20,[]);
        echo '<div class="content">共' . $result['total'] . '张<br/><ul class="list">';
        foreach ($result['list'] as $c) {
            $cid = (int)$c['id'];
            $status = empty($c['isused']) ? '<span style="color:green">未使用</span>' : '<span style="color:gray">已使用</span>';
            $time = !empty($c['addtime']) ? date('m-d H:i', strtotime($c['addtime'])) : '';
            echo '<li>' . $this->ToHtm($c['cardno']) . ' 类型:' . $this->ToHtm($c['cardtype'] ?? '')
                . ' 面值:' . (int)$c['money'] . ' ' . $status . ' <span class="time">' . $time . '</span>';
            if (empty($c['isused'])) {
                echo ' <form method="post" action="' . $back . '" style="display:inline">'
                    . '<input type="hidden" name="action" value="void"/><input type="hidden" name="id" value="' . $cid . '"/>'
                    . '<input type="submit" value="作废"/></form>';
            }
            echo '</li>';
        }
        if (empty($result['list'])) echo '<li>暂无充值卡</li>';
        echo '</ul></div>';

        // 分页
        if ($result['pageCount'] > 1) {
            echo '<div class="pager">';
            if ($page > 1) echo '<a href="' . $back . '&page=' . ($page - 1) . '">上一页</a> ';
            echo '第' . $page . '/' . $result['pageCount'] . '页 ';
            if ($page < $result['pageCount']) echo '<a href="' . $back . '&page=' . ($page + 1) . '">下一页</a>';
            echo '</div>';
        }

        $this->showDown();
    }
}

$page = new AdminCardPage();
$page->run();
