<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 积分商城 - 我的兑换记录
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class ShopPointOrdersPage extends WapPage {
    public function run() {
        $this->checkLogin();
        $db = $this->db;
        $uid = $this->userSession->getUserId();
        $page = max(1, (int)($_GET['page'] ?? 1));

        $this->strTitle = '我的兑换记录';
        $this->showTop($this->strTitle);
        echo '<div class="subtitle">我的兑换记录</div>';

        $result = $db->getPage('WapPointOrder', 'id,goodsname,points,num,status,addtime',
            'userid=? AND siteid=?', 'id DESC', $page, 20, [$uid, $this->siteid]);

        echo '<div class="content">';
        if (!$result['list']) {
            echo '暂无兑换记录';
        } else {
            $statusText = [0 => '待发放', 1 => '已发放', 2 => '已取消'];
            echo '<ul class="loglist">';
            foreach ($result['list'] as $o) {
                $st = (int)$o['status'];
                echo '<li>#' . (int)$o['id'] . ' ' . $this->ToHtm($o['goodsname'])
                    . ' 积分 ' . (int)$o['points'] . ' × ' . (int)$o['num']
                    . ' [' . ($statusText[$st] ?? '未知') . '] '
                    . ($o['addtime'] ? date('m-d H:i', strtotime($o['addtime'])) : '') . '</li>';
            }
            echo '</ul>';
        }
        echo '</div>';

        if ($result['pageCount'] > 1) {
            echo '<div class="pager">';
            if ($page > 1) echo '<a href="?siteid=' . $this->siteid . '&page=' . ($page - 1) . '">上一页</a> ';
            echo '第' . $page . '/' . $result['pageCount'] . '页 ';
            if ($page < $result['pageCount']) echo '<a href="?siteid=' . $this->siteid . '&page=' . ($page + 1) . '">下一页</a>';
            echo '</div>';
        }

        echo '<div class="nav"><a href="point.php?siteid=' . $this->siteid . '">继续兑换</a> ';
        echo '<a href="../myaccount/index.php?siteid=' . $this->siteid . '">我的账户</a></div>';
        $this->showDown();
    }
}

$page = new ShopPointOrdersPage();
$page->run();
