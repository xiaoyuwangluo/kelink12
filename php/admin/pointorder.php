<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 管理后台 - 积分兑换订单管理
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class AdminPointOrderPage extends WapPage {
    public function run() {
        $this->checkAdminLogin();
        $db = $this->db;
        $back = 'pointorder.php';

        $status = $_GET['status'] ?? 'all';
        if (!in_array($status, ['0', '1', '2', 'all'], true)) $status = 'all';

        // 操作：finish 发放 / cancel 取消
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $act = $_POST['act'] ?? '';
            $id = (int)($_POST['id'] ?? 0);
            if ($act === 'finish') {
                $n = $db->execute("UPDATE WapPointOrder SET status=1 WHERE id=? AND status=0",[$id]);
                if ($n != 1) $this->showError('订单不存在或状态已变更', $back . '&status=' . $status);
            } elseif ($act === 'cancel') {
                $this->doCancel($id, $back . '&status=' . $status);
            } else {
                $this->showError('非法操作', $back);
            }
            header('Location: ' . $back . '&status=' . $status);
            exit;
        }

        // 列表
        $page = max(1, (int)($_GET['page'] ?? 1));
        $where = '1=1';
        $params = [];
        if ($status !== 'all') {
            $where .= ' AND status=?';
            $params[] = (int)$status;
        }
        $result = $db->getPage('WapPointOrder', '*', $where, 'id DESC', $page, 20, $params);

        $this->strTitle = '积分兑换订单';
        $adminTitle = '积分兑换订单';
        $activeNav = 'pointorder';
        require __DIR__ . '/inc/header.php';
        echo '<div class="subtitle">积分兑换订单</div>';
        echo '<div><a href="index.php">返回后台首页</a></div>';
        echo '<div class="content">筛选：';
        $filters = ['all' => '全部', '0' => '待发放', '1' => '已发放', '2' => '已取消'];
        foreach ($filters as $k => $label) {
            $cur = ((string)$status === (string)$k) ? '<strong>[' . $label . ']</strong>' : '<a href="' . $back . '&status=' . $k . '">' . $label . '</a>';
            echo $cur . ' ';
        }
        echo '</div>';

        $statusText = [0 => '待发放', 1 => '已发放', 2 => '已取消'];
        echo '<div class="content"><ul class="list">';
        foreach ($result['list'] as $o) {
            $id = (int)$o['id'];
            $st = (int)$o['status'];
            echo '<li>#' . $id . ' 会员ID:' . (int)$o['userid'] . ' ' . $this->ToHtm($o['goodsname'])
                . ' 积分:' . (int)$o['points'] . ' 数量:' . (int)$o['num']
                . ' 状态:' . ($statusText[$st] ?? '未知')
                . ' <span class="time">' . (!empty($o['addtime']) ? date('m-d H:i', strtotime($o['addtime'])) : '') . '</span><br/>';
            if ($st === 0) {
                echo '<form method="post" action="' . $back . '&status=' . $status . '" style="display:inline">';
                echo '<input type="hidden" name="act" value="finish"/><input type="hidden" name="id" value="' . $id . '"/>';
                echo '<input type="submit" value="发放"/></form> ';
                echo '<form method="post" action="' . $back . '&status=' . $status . '" style="display:inline">';
                echo '<input type="hidden" name="act" value="cancel"/><input type="hidden" name="id" value="' . $id . '"/>';
                echo '<input type="submit" value="取消并退回积分"/></form>';
            } else {
                echo '不可操作';
            }
            echo '</li>';
        }
        if (empty($result['list'])) echo '<li>暂无订单</li>';
        echo '</ul></div>';

        if ($result['pageCount'] > 1) {
            echo '<div class="pager">';
            if ($page > 1) echo '<a href="' . $back . '&status=' . $status . '&page=' . ($page - 1) . '">上一页</a> ';
            echo '第' . $page . '/' . $result['pageCount'] . '页 ';
            if ($page < $result['pageCount']) echo '<a href="' . $back . '&status=' . $status . '&page=' . ($page + 1) . '">下一页</a>';
            echo '</div>';
        }
        require __DIR__ . '/inc/footer.php';
    }

    /** 取消兑换：事务内订单置2 + 退积分 + 回补库存 + 积分流水 */
    private function doCancel($id, $back) {
        $db = $this->db;
        $order = $db->getOne("SELECT * FROM WapPointOrder WHERE id=?",[$id]);
        if (!$order) $this->showError('订单不存在', $back);
        if ((int)$order['status'] !== 0) $this->showError('仅待发放订单可取消', $back);

        $total = (int)$order['points'] * max(1, (int)$order['num']);
        try {
            $db->beginTransaction();
            $n = $db->execute("UPDATE WapPointOrder SET status=2 WHERE id=? AND status=0",[$id]);
            if ($n != 1) {
                $db->rollBack();
                $this->showError('订单状态已变更', $back);
            }
            $db->execute("UPDATE WapUser SET point=point+? WHERE id=?", [$total, (int)$order['userid']]);
            $balance = (int)$db->getScalar("SELECT point FROM WapUser WHERE id=?", [(int)$order['userid']]);
            $db->insert("INSERT INTO WapPointLog (userid,num,balance,remark,addtime) VALUES (?,?,?,?,NOW())",[(int)$order['userid'],$total,$balance,'取消兑换退回：' . $order['goodsname']]);
            $db->execute("UPDATE WapPointGoods SET stock=stock+? WHERE id=?",[max(1, (int)$order['num']),(int)$order['goodsid']]);
            $db->commit();
        } catch (Exception $e) {
            if ($db->getPdo()->inTransaction()) $db->rollBack();
            $this->showError('取消兑换失败，请重试', $back);
        }
    }
}

$page = new AdminPointOrderPage();
$page->run();
