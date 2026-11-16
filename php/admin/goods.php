<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 管理后台 - 商城商品库存管理
 * 支持: 关键词搜索 / 库存预警(stock<5) / 下架筛选 / 快捷改价改库存 / 上下架切换
 */
require_once __DIR__ . '/../includes/bootstrap.php';
require_once KL_ROOT . '/includes/Community.php';

class AdminGoodsPage extends WapPage {
    const LOW_STOCK = 5;

    public function run() {
        $this->checkAdminLogin();
        $db = $this->db;
        $back = 'goods.php';

        $q = trim($_GET['q'] ?? '');
        $status = $_GET['status'] ?? 'all';
        if (!in_array($status, ['all', 'onshow', 'offshelf'], true)) $status = 'all';
        $low = (int)($_GET['low'] ?? 0) === 1 ? 1 : 0;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $act = $_POST['act'] ?? '';
            $id = (int)($_POST['id'] ?? 0);
            $target = $this->backUrl($q, $status, $low);
            if ($act === 'save') {
                $this->doSave($id, $target);
            } elseif ($act === 'toggle') {
                $this->doToggle($id, $target);
            } else {
                $this->showError('非法操作', $back);
            }
            header('Location: ' . $target);
            exit;
        }

        // 统计
        $total = (int)$db->getScalar("SELECT COUNT(*) FROM WapShop");
        $onshow = (int)$db->getScalar("SELECT COUNT(*) FROM WapShop WHERE isshow=1");
        $offshelf = (int)$db->getScalar("SELECT COUNT(*) FROM WapShop WHERE isshow<>1");
        $lowCount = (int)$db->getScalar("SELECT COUNT(*) FROM WapShop WHERE stock<" . self::LOW_STOCK);

        // 列表
        $page = max(1, (int)($_GET['page'] ?? 1));
        $where = '1=1';
        $params = [];
        if ($status === 'onshow') {
            $where .= ' AND isshow=1';
        } elseif ($status === 'offshelf') {
            $where .= ' AND isshow<>1';
        }
        if ($low === 1) {
            $where .= ' AND stock<' . self::LOW_STOCK;
        }
        if ($q !== '') {
            $where .= ' AND (id=? OR title LIKE ?)';
            $params[] = (int)$q;
            $params[] = '%' . $q . '%';
        }
        $fields = 'WapShop.*,'
            . ' (SELECT classname FROM WapClass WHERE WapClass.id=WapShop.classid) AS classname';
        $result = $db->getPage('WapShop', $fields, $where,
            ($low === 1 ? 'stock ASC, id DESC' : 'id DESC'), $page, 20, $params);

        $this->strTitle = '商品库存';
        $this->showTop($this->strTitle);
        echo '<div class="subtitle">商品库存</div>';
        echo '<div><a href="index.php">返回后台首页</a></div>';

        if (isset($_GET['err'])) echo '<div class="error">' . $this->ToHtm($_GET['err']) . '</div>';
        if (isset($_GET['ok'])) echo '<div class="ok">' . $this->ToHtm($_GET['ok']) . '</div>';

        echo '<div class="content">筛选：';
        $filters = [
            'all' => '全部(' . $total . ')',
            'onshow' => '在售(' . $onshow . ')',
            'offshelf' => '已下架(' . $offshelf . ')',
        ];
        foreach ($filters as $k => $label) {
            $cur = ($status === $k && $low === 0) ? '<strong>[' . $label . ']</strong>' : '<a href="' . $this->backUrl($q, $k, 0) . '">' . $label . '</a>';
            echo $cur . ' ';
        }
        $lowLabel = '库存预警(' . $lowCount . ')';
        echo ($low === 1) ? '<strong>[' . $lowLabel . ']</strong>' : '<a href="' . $this->backUrl($q, 'all', 1) . '">' . $lowLabel . '</a>';
        echo '</div>';

        echo '<div class="content"><form method="get" action="' . $back . '">'
            . '搜索：<input type="text" name="q" value="' . $this->ToHtm($q) . '" placeholder="商品ID/标题"/>'
            . '<input type="hidden" name="status" value="' . $this->ToHtm($status) . '"/>'
            . '<input type="hidden" name="low" value="' . $low . '"/>'
            . '<input type="submit" value="查询"/></form></div>';

        echo '<div class="content"><ul class="list">';
        foreach ($result['list'] as $g) {
            $id = (int)$g['id'];
            $stock = (int)$g['stock'];
            $warn = $stock < self::LOW_STOCK ? ' <span style="color:red">预警</span>' : '';
            echo '<li>ID:' . $id . ' ' . $this->ToHtm($g['title'])
                . '<br/>栏目:' . $this->ToHtm($g['classname'] ?? '未分类')
                . ' 价格:￥' . number_format((float)$g['price'], 2)
                . ' 库存:' . $stock . $warn
                . ' 点击:' . (int)$g['hits']
                . ' 状态:' . ((int)$g['isshow'] === 1 ? '在售' : '<span style="color:red">下架</span>')
                . ' <span class="time">' . (!empty($g['addtime']) ? date('m-d H:i', strtotime($g['addtime'])) : '') . '</span><br/>';

            echo '<form method="post" action="' . $this->backUrl($q, $status, $low) . '" style="display:inline">';
            echo '<input type="hidden" name="act" value="save"/><input type="hidden" name="id" value="' . $id . '"/>';
            echo '价格:<input type="text" name="price" size="6" value="' . number_format((float)$g['price'], 2, '.', '') . '"/> ';
            echo '库存:<input type="text" name="stock" size="6" value="' . $stock . '"/> ';
            echo '<input type="submit" value="保存"/></form> ';
            echo '<form method="post" action="' . $this->backUrl($q, $status, $low) . '" style="display:inline">';
            echo '<input type="hidden" name="act" value="toggle"/><input type="hidden" name="id" value="' . $id . '"/>';
            echo '<input type="submit" value="' . ((int)$g['isshow'] === 1 ? '下架' : '上架') . '"/></form>';
            echo '</li>';
        }
        if (empty($result['list'])) echo '<li>暂无商品</li>';
        echo '</ul></div>';

        if ($result['pageCount'] > 1) {
            echo '<div class="pager">';
            if ($page > 1) echo '<a href="' . $this->backUrl($q, $status, $low) . '&page=' . ($page - 1) . '">上一页</a> ';
            echo '第' . $page . '/' . $result['pageCount'] . '页 ';
            if ($page < $result['pageCount']) echo '<a href="' . $this->backUrl($q, $status, $low) . '&page=' . ($page + 1) . '">下一页</a>';
            echo '</div>';
        }
        $this->showDown();
    }

    /** 列表回跳地址（保留筛选/搜索/预警） */
    private function backUrl($q, $status, $low) {
        $url = 'goods.php?status=' . urlencode((string)$status) . '&low=' . (int)$low;
        if ($q !== '') $url .= '&q=' . urlencode($q);
        return $url;
    }

    /** 快捷保存价格与库存（非负校验） */
    private function doSave($id, $back) {
        $db = $this->db;
        if ($id <= 0) $this->showError('商品不存在', $back);
        $goods = $db->getOne("SELECT * FROM WapShop WHERE id=?", [$id]);
        if (!$goods) $this->showError('商品不存在', $back);

        $price = trim($_POST['price'] ?? '');
        $stock = trim($_POST['stock'] ?? '');
        if (!preg_match('/^\d+(\.\d{1,2})?$/', $price)) {
            header('Location: ' . $back . '&err=' . urlencode('价格必须为非负数字，最多两位小数'));
            exit;
        }
        if (!preg_match('/^\d+$/', $stock)) {
            header('Location: ' . $back . '&err=' . urlencode('库存必须为非负整数'));
            exit;
        }
        $db->execute("UPDATE WapShop SET price=?,stock=? WHERE id=?",
            [number_format((float)$price, 2, '.', ''), (int)$stock, $id]);
        kl_cache_clear_site();
        header('Location: ' . $back . '&ok=' . urlencode('商品 #' . $id . ' 已更新'));
        exit;
    }

    /** 上下架切换 */
    private function doToggle($id, $back) {
        $db = $this->db;
        $goods = $db->getOne("SELECT id FROM WapShop WHERE id=?", [$id]);
        if (!$goods) $this->showError('商品不存在', $back);
        $db->execute("UPDATE WapShop SET isshow=CASE WHEN isshow=1 THEN 0 ELSE 1 END WHERE id=?", [$id]);
        kl_cache_clear_site();
        header('Location: ' . $back . '&ok=' . urlencode('商品 #' . $id . ' 上下架状态已切换'));
        exit;
    }
}

$page = new AdminGoodsPage();
$page->run();
