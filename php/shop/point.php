<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 积分商城 - 积分商品列表 (无需登录即可浏览)
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class ShopPointPage extends WapPage {
    public function run() {
        $db = $this->db;
        $page = max(1, (int)($_GET['page'] ?? 1));

        $this->strTitle = '积分商城';
        $this->showTop($this->strTitle);
        echo '<div class="subtitle">积分商城</div>';

        $isLogin = $this->userSession->isLogin();
        $user = $isLogin ? $this->userSession->getUserVo() : null;
        if ($isLogin) {
            echo '<div class="content">我的积分：' . (int)($user['point'] ?? 0) . ' <a href="point_orders.php">我的兑换记录</a></div>';
        }

        $result = $db->getPage('WapPointGoods', 'id,name,intro,points,stock,image,addtime','isshow=1 AND stock>0', 'sortnum DESC, id DESC', $page, 20,[]);

        echo '<div class="content">';
        if (!$result['list']) {
            echo '暂无可兑换商品';
        } else {
            echo '<ul class="shoplist">';
            foreach ($result['list'] as $g) {
                echo '<li>';
                if (!empty($g['image'])) echo '<img src="' . $this->ToHtm($g['image']) . '" alt=""/>';
                echo '<strong>' . $this->ToHtm($g['name']) . '</strong><br/>';
                if (!empty($g['intro'])) echo $this->ToHtm($g['intro']) . '<br/>';
                echo '所需积分：' . (int)$g['points'] . ' 剩余库存：' . (int)$g['stock'] . '<br/>';
                if ($isLogin) {
                    echo '<a href="point_buy.php?id=' . (int)$g['id'] . '">兑换</a>';
                } else {
                    echo '<a href="../waplogin.php">登录后兑换</a>';
                }
                echo '</li>';
            }
            echo '</ul>';
        }
        echo '</div>';

        if ($result['pageCount'] > 1) {
            echo '<div class="pager">';
            if ($page > 1) echo '<a href="?page=' . ($page - 1) . '">上一页</a> ';
            echo '第' . $page . '/' . $result['pageCount'] . '页 ';
            if ($page < $result['pageCount']) echo '<a href="?page=' . ($page + 1) . '">下一页</a>';
            echo '</div>';
        }

        echo '<div class="nav"><a href="../wapindex.php">返回首页</a>';
        if ($isLogin) echo ' <a href="point_orders.php">我的兑换记录</a>';
        echo '</div>';
        $this->showDown();
    }
}

$page = new ShopPointPage();
$page->run();
