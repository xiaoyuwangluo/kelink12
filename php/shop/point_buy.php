<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 积分商城 - 积分兑换 (GET 确认表单 / POST 事务扣减)
 */
require_once __DIR__ . '/../includes/bootstrap.php';
require_once KL_ROOT . '/includes/Community.php';

class ShopPointBuyPage extends WapPage {
    public function run() {
        $this->checkLogin();
        $db = $this->db;
        $uid = $this->userSession->getUserId();

        $id = (int)($_GET['id'] ?? $_POST['goodsid'] ?? 0);
        if ($id <= 0) $this->showError('参数错误', 'point.php');

        $goods = $db->getOne("SELECT * FROM WapPointGoods WHERE id=?",[$id]);
        if (!$goods || (int)$goods['isshow'] !== 1) {
            $this->showError('商品不存在或已下架', 'point.php');
        }
        if ((int)$goods['stock'] <= 0) {
            $this->showError('库存不足', 'point.php');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->doExchange($uid, $goods);
            return;
        }

        $user = $this->userSession->getUserVo();
        $this->strTitle = '确认兑换';
        $this->showTop($this->strTitle);
        echo '<div class="ui-breadcrumb"><a href="' . $this->http_start . 'wapindex.php">首页</a> / <a href="point.php">积分商城</a> / 确认兑换</div>';
        echo '<div class="subtitle">确认兑换</div>';
        echo '<div class="ui-card">';
        echo '<div class="ui-card-head"><span class="ui-card-title">' . $this->ToHtm($goods['name']) . '</span>'
            . '<span class="ui-card-extra">剩余库存 ' . (int)$goods['stock'] . '</span></div>';
        echo '<div class="ui-stat-grid">';
        echo '<div class="ui-stat"><div class="num">' . (int)$goods['points'] . '</div><div class="label">所需积分</div></div>';
        echo '<div class="ui-stat"><div class="num">' . (int)($user['point'] ?? 0) . '</div><div class="label">我的积分</div></div>';
        echo '</div>';
        if ((int)($user['point'] ?? 0) < (int)$goods['points']) {
            echo '<div class="ui-alert ui-alert-warning">积分不足，无法兑换该商品。</div>';
        }
        echo '<form method="post" action="point_buy.php?id=' . (int)$goods['id'] . '">';
        echo '<input type="hidden" name="goodsid" value="' . (int)$goods['id'] . '"/>';
        echo '<input class="ui-btn ui-btn-primary ui-btn-block ui-mt-2" type="submit" value="确认兑换"/></form>';
        echo '</div>';
        echo '<div class="ui-flex ui-gap-2 ui-mt-2"><a class="ui-btn ui-btn-ghost" href="point.php">返回积分商城</a>';
        echo '<a class="ui-btn" href="point_orders.php">我的兑换记录</a></div>';
        $this->showDown();
    }

    /**
     * 执行兑换：事务内条件更新防并发超扣/超卖，任一失败整体回滚
     */
    private function doExchange($uid, $goods) {
        $db = $this->db;
        $points = (int)$goods['points'];
        $gid = (int)$goods['id'];
        if ($points <= 0) $this->showError('商品积分配置错误', 'point.php');

        // 降低 SQLite 并发写锁冲突概率
        if ($db->getDbType() === 'sqlite') {
            try { $db->getPdo()->exec('PRAGMA busy_timeout=5000'); } catch (Exception $e) {}
        }

        try {
            $db->beginTransaction();

            // 1) 扣积分：仅当余额足够才生效
            $n = $db->execute("UPDATE WapUser SET point=point-? WHERE id=? AND point>=?", [$points, $uid, $points]);
            if ($n != 1) {
                $db->rollBack();
                $this->showError('积分不足', 'point.php');
            }

            // 2) 扣库存：仅当仍上架且有库存才生效
            $n = $db->execute("UPDATE WapPointGoods SET stock=stock-1 WHERE id=? AND stock>0 AND isshow=1", [$gid]);
            if ($n != 1) {
                $db->rollBack();
                $this->showError('库存不足', 'point.php');
            }

            // 3) 读最新积分余额并写订单/积分流水
            $balance = (int)$db->getScalar("SELECT point FROM WapUser WHERE id=?", [$uid]);
            $db->insert("INSERT INTO WapPointOrder (userid,goodsid,goodsname,points,num,status,note,addtime) VALUES (?,?,?,?,1,0,'',NOW())",[$uid,$gid,$goods['name'],$points]);
            $db->insert("INSERT INTO WapPointLog (userid,num,balance,remark,addtime) VALUES (?,?,?,?,NOW())",[$uid,-$points,$balance,'兑换：' . $goods['name']]);

            $db->commit();
        } catch (Exception $e) {
            if ($db->getPdo()->inTransaction()) $db->rollBack();
            $this->showError('兑换失败，请重试', 'point.php');
        }

        kl_cache_clear_site();
        $this->showSuccess('兑换成功，等待管理员发放', 'point_orders.php');
    }
}

$page = new ShopPointBuyPage();
$page->run();
