<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 商城模块 - 购物车 (对应原 shop/cart 系列，加入购物车/列表/改数量/删除)
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class ShopCartPage extends WapPage {
    public function run() {
        $this->checkLogin();
        $db = $this->db;
        $uid = $this->userSession->getUserId();

        // 处理 POST 操作：add(加入) / update(改数量) / del(删除)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $act = $_POST['act'] ?? '';
            if ($act === 'add') {
                $this->doAdd($uid);
            } elseif ($act === 'update') {
                $this->doUpdate($uid);
            } elseif ($act === 'del') {
                $this->doDel($uid);
            }
            // 操作后重定向回购物车，防止刷新重复提交
            header('Location: cart.php?siteid=' . $this->siteid . '&classid=' . $this->classid);
            exit;
        }

        // 购物车列表
        $rows = $db->getAll("SELECT * FROM WapShopCart WHERE userid=? AND siteid=? ORDER BY id DESC", [$uid, $this->siteid]);
        $this->strTitle = '购物车';
        $this->showTop($this->strTitle);
        echo '<div class="subtitle">购物车</div>';

        if (!$rows) {
            echo '<div class="content">购物车是空的。<a href="list.php?siteid=' . $this->siteid . '&classid=' . $this->classid . '">去逛逛</a></div>';
        } else {
            $total = 0;
            echo '<div class="content"><ul class="cartlist">';
            foreach ($rows as $r) {
                $subtotal = (int)$r['price'] * (int)$r['num'];
                $total += $subtotal;
                echo '<li>';
                echo $this->ToHtm($r['title']) . ' ￥' . (int)$r['price'] . ' × ';
                echo '<form method="post" action="cart.php?siteid=' . $this->siteid . '&classid=' . $this->classid . '" style="display:inline">';
                echo '<input type="hidden" name="act" value="update"/>';
                echo '<input type="hidden" name="cartid" value="' . (int)$r['id'] . '"/>';
                echo '<input type="number" name="num" value="' . (int)$r['num'] . '" min="1" style="width:4em"/>';
                echo '<input type="submit" value="改"/>';
                echo '</form>';
                echo ' 小计￥' . $subtotal;
                echo '<form method="post" action="cart.php?siteid=' . $this->siteid . '&classid=' . $this->classid . '" style="display:inline">';
                echo '<input type="hidden" name="act" value="del"/>';
                echo '<input type="hidden" name="cartid" value="' . (int)$r['id'] . '"/>';
                echo '<input type="submit" value="删除"/>';
                echo '</form>';
                echo '</li>';
            }
            echo '</ul>';
            echo '<div class="total">合计:￥' . $total . '</div>';
            echo '<form method="post" action="checkout.php?siteid=' . $this->siteid . '&classid=' . $this->classid . '">';
            echo '<div>勾选结算:</div>';
            foreach ($rows as $r) {
                echo '<label><input type="checkbox" name="ids[]" value="' . (int)$r['id'] . '" checked/>' . $this->ToHtm($r['title']) . '</label><br/>';
            }
            echo '<input type="submit" value="去结算"/></form>';
            echo '</div>';
        }

        echo '<div class="nav">';
        echo '<a href="list.php?siteid=' . $this->siteid . '&classid=' . $this->classid . '">商城</a> ';
        echo '<a href="myorder.php?siteid=' . $this->siteid . '&classid=' . $this->classid . '">我的订单</a> ';
        echo '<a href="../myaccount/index.php?siteid=' . $this->siteid . '">余额充值</a>';
        echo '</div>';
        $this->showDown();
    }

    /** 加入购物车：POST shopid,num */
    private function doAdd($uid) {
        $db = $this->db;
        $shopid = (int)($_POST['shopid'] ?? 0);
        $num = max(1, (int)($_POST['num'] ?? 1));
        if ($shopid <= 0 || $num > 999) return;

        $product = $db->getOne("SELECT * FROM WapShop WHERE id=? AND siteid=? AND isshow=1", [$shopid, $this->siteid]);
        if (!$product) return;

        // 库存校验（含已加购数量）
        $oldNum = (int)$db->getScalar("SELECT num FROM WapShopCart WHERE userid=? AND siteid=? AND shopid=?", [$uid, $this->siteid, $shopid]);
        if ((int)$product['stock'] > 0 && $oldNum + $num > (int)$product['stock']) return;

        $price = (int)round($product['price']); // 金额按整数元处理
        if ($oldNum > 0) {
            // 已存在同款商品，累加数量
            $db->execute("UPDATE WapShopCart SET num=?, price=?, title=? WHERE userid=? AND siteid=? AND shopid=?",
                [$oldNum + $num, $price, $product['title'], $uid, $this->siteid, $shopid]);
        } else {
            $db->insert("INSERT INTO WapShopCart (siteid,userid,shopid,title,price,num,addtime) VALUES (?,?,?,?,?,?,NOW())",
                [$this->siteid, $uid, $shopid, $product['title'], $price, $num]);
        }
    }

    /** 修改数量 */
    private function doUpdate($uid) {
        $db = $this->db;
        $cartid = (int)($_POST['cartid'] ?? 0);
        $num = max(1, (int)($_POST['num'] ?? 1));
        if ($cartid <= 0 || $num > 999) return;
        $row = $db->getOne("SELECT * FROM WapShopCart WHERE id=? AND userid=? AND siteid=?", [$cartid, $uid, $this->siteid]);
        if (!$row) return;
        $stock = (int)$db->getScalar("SELECT stock FROM WapShop WHERE id=?", [$row['shopid']]);
        if ($stock > 0 && $num > $stock) $num = $stock;
        $db->execute("UPDATE WapShopCart SET num=? WHERE id=?", [$num, $cartid]);
    }

    /** 删除条目 */
    private function doDel($uid) {
        $db = $this->db;
        $cartid = (int)($_POST['cartid'] ?? 0);
        if ($cartid <= 0) return;
        $db->execute("DELETE FROM WapShopCart WHERE id=? AND userid=? AND siteid=?", [$cartid, $uid, $this->siteid]);
    }
}
$page = new ShopCartPage();
$page->run();
