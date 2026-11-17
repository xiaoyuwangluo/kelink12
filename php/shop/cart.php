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

        // 处理 POST 操作：add(加入) / update(改数量) / del(删除) / clear(清空)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $act = $_POST['act'] ?? '';
            $msg = '';
            if ($act === 'add') {
                $msg = $this->doAdd($uid);
            } elseif ($act === 'update') {
                $msg = $this->doUpdate($uid);
            } elseif ($act === 'del') {
                $this->doDel($uid);
                $msg = '已删除该商品';
            } elseif ($act === 'clear') {
                $db->execute("DELETE FROM WapShopCart WHERE userid=?", [$uid]);
                $msg = '购物车已清空';
            }
            // 操作后重定向回购物车，防止刷新重复提交
            header('Location: cart.php?classid=' . $this->classid . ($msg !== '' ? '&msg=' . urlencode($msg) : ''));
            exit;
        }

        // 购物车列表（带商品库存/上架状态）
        $rows = $db->getAll("SELECT c.*, s.stock AS sstock, s.isshow AS sshow"
            . " FROM WapShopCart c LEFT JOIN WapShop s ON s.id=c.shopid"
            . " WHERE c.userid=? ORDER BY c.id DESC", [$uid]);
        $this->strTitle = '购物车';
        $this->showTop($this->strTitle);
        echo '<div class="subtitle">购物车</div>';
        if (isset($_GET['msg'])) echo '<div class="success">' . $this->ToHtm($_GET['msg']) . '</div>';

        if (!$rows) {
            echo '<div class="content">购物车是空的。<a href="list.php?classid=' . $this->classid . '">去逛逛</a></div>';
        } else {
            $total = 0;
            $totalNum = 0;
            $invalid = 0;
            echo '<div class="content"><ul class="cartlist">';
            foreach ($rows as $r) {
                $price = (int)$r['price'];
                $num = (int)$r['num'];
                $subtotal = $price * $num;
                $total += $subtotal;
                $totalNum += $num;
                $stock = isset($r['sstock']) ? (int)$r['sstock'] : 0;
                $onShow = isset($r['sshow']) && (int)$r['sshow'] === 1;
                $bad = '';
                if (!$onShow) {
                    $bad = '<span style="color:red">[已下架]</span>';
                } elseif ($stock <= 0) {
                    $bad = '<span style="color:red">[已售罄]</span>';
                } elseif ($num > $stock) {
                    $bad = '<span style="color:red">[库存不足，仅剩 ' . $stock . ']</span>';
                }
                if ($bad !== '') $invalid++;
                echo '<li>' . $bad . ' ' . $this->ToHtm($r['title']) . ' ￥' . $price . ' × ';
                echo '<form method="post" action="cart.php?classid=' . $this->classid . '" style="display:inline">';
                echo '<input type="hidden" name="act" value="update"/>';
                echo '<input type="hidden" name="cartid" value="' . (int)$r['id'] . '"/>';
                echo '<input type="number" name="num" value="' . $num . '" min="1" max="' . max(1, $stock) . '" style="width:4em"/>';
                echo '<input type="submit" value="改"/>';
                echo '</form>';
                echo ' 小计￥' . $subtotal;
                if ($bad !== '') echo ' <a href="view.php?classid=' . $this->classid . '&id=' . (int)$r['shopid'] . '">查看商品</a>';
                echo '<form method="post" action="cart.php?classid=' . $this->classid . '" style="display:inline">';
                echo '<input type="hidden" name="act" value="del"/>';
                echo '<input type="hidden" name="cartid" value="' . (int)$r['id'] . '"/>';
                echo '<input type="submit" value="删除"/>';
                echo '</form>';
                echo '</li>';
            }
            echo '</ul>';
            echo '<div class="total">共 ' . $totalNum . ' 件，合计:￥' . $total . '</div>';
            if ($invalid > 0) {
                echo '<div class="error">有 ' . $invalid . ' 件商品已下架/售罄/库存不足，已置灰不可结算，请先调整或删除。</div>';
            }
            echo '<form method="post" action="checkout.php?classid=' . $this->classid . '">';
            echo '<div>勾选结算:</div>';
            foreach ($rows as $r) {
                $stock = isset($r['sstock']) ? (int)$r['sstock'] : 0;
                $onShow = isset($r['sshow']) && (int)$r['sshow'] === 1;
                $ok = $onShow && $stock > 0 && (int)$r['num'] <= $stock;
                echo '<label><input type="checkbox" name="ids[]" value="' . (int)$r['id'] . '"'
                    . ($ok ? ' checked' : ' disabled') . '/>' . $this->ToHtm($r['title'])
                    . ($ok ? '' : '（暂不可购买）') . '</label><br/>';
            }
            echo '<input type="submit" value="去结算"/></form>';
            echo '<form method="post" action="cart.php?classid=' . $this->classid . '" onsubmit="return confirm(\'确定清空购物车？\');">'
                . '<input type="hidden" name="act" value="clear"/><input type="submit" value="清空购物车"/></form>';
            echo '</div>';
        }

        echo '<div class="nav">';
        echo '<a href="list.php?classid=' . $this->classid . '">商城</a> ';
        echo '<a href="myorder.php?classid=' . $this->classid . '">我的订单</a> ';
        echo '<a href="../myaccount/index.php">余额充值</a>';
        echo '</div>';
        $this->showDown();
    }

    /** 加入购物车：POST shopid,num；返回提示文案 */
    private function doAdd($uid) {
        $db = $this->db;
        $shopid = (int)($_POST['shopid'] ?? 0);
        $num = max(1, (int)($_POST['num'] ?? 1));
        if ($shopid <= 0) return '商品参数错误';
        if ($num > 999) return '单次最多加入 999 件';

        $product = $db->getOne("SELECT * FROM WapShop WHERE id=? AND isshow=1", [$shopid]);
        if (!$product) return '商品不存在或已下架';

        $stock = (int)$product['stock'];
        if ($stock <= 0) return '商品已售罄，无法加入购物车';

        // 库存校验（含已加购数量）
        $oldNum = (int)$db->getScalar("SELECT num FROM WapShopCart WHERE userid=? AND shopid=?", [$uid, $shopid]);
        if ($oldNum + $num > $stock) {
            $can = $stock - $oldNum;
            return $can > 0 ? ('库存不足，最多还可加入 ' . $can . ' 件') : '购物车中该商品数量已达库存上限';
        }

        $price = (int)round($product['price']); // 金额按整数元处理
        if ($oldNum > 0) {
            // 已存在同款商品，累加数量
            $db->execute("UPDATE WapShopCart SET num=?, price=?, title=? WHERE userid=? AND shopid=?", [$oldNum + $num, $price, $product['title'], $uid, $shopid]);
        } else {
            $db->insert("INSERT INTO WapShopCart (userid,shopid,title,price,num,addtime) VALUES (?,?,?,?,?,NOW())", [$uid, $shopid, $product['title'], $price, $num]);
        }
        return '已加入购物车（' . $num . ' 件）';
    }

    /** 修改数量；返回提示文案 */
    private function doUpdate($uid) {
        $db = $this->db;
        $cartid = (int)($_POST['cartid'] ?? 0);
        $num = max(1, (int)($_POST['num'] ?? 1));
        if ($cartid <= 0) return '购物车记录不存在';
        $row = $db->getOne("SELECT * FROM WapShopCart WHERE id=? AND userid=?", [$cartid, $uid]);
        if (!$row) return '购物车记录不存在';
        if ($num > 999) { $num = 999; }
        $stock = (int)$db->getScalar("SELECT stock FROM WapShop WHERE id=?", [$row['shopid']]);
        $capped = false;
        if ($stock > 0 && $num > $stock) { $num = $stock; $capped = true; }
        $db->execute("UPDATE WapShopCart SET num=? WHERE id=?", [$num, $cartid]);
        return $capped ? ('数量已按库存调整为 ' . $num . ' 件') : '数量已更新';
    }

    /** 删除条目 */
    private function doDel($uid) {
        $db = $this->db;
        $cartid = (int)($_POST['cartid'] ?? 0);
        if ($cartid <= 0) return;
        $db->execute("DELETE FROM WapShopCart WHERE id=? AND userid=?", [$cartid, $uid]);
    }
}
$page = new ShopCartPage();
$page->run();
