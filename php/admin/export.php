<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 管理后台 - 数据导出（CSV，Excel 兼容 UTF-8 BOM）
 * ?type=order  订单导出，支持 status/q 筛选
 * ?type=goods  商品导出，支持 status/low/q 筛选
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class AdminExportPage extends WapPage {
    const ORDER_STATUS = [
        0 => '待付款', 1 => '已付款(待发货)', 2 => '已发货', 3 => '已完成', -1 => '已取消', -2 => '已退款',
    ];

    /** @var resource CSV 输出流 */
    private $out = null;

    public function run() {
        $this->checkAdminLogin();
        $type = $_GET['type'] ?? '';
        if ($type === 'order') {
            $this->exportOrders();
        } elseif ($type === 'goods') {
            $this->exportGoods();
        } else {
            $this->showError('非法导出类型', 'index.php');
        }
    }

    private function exportOrders() {
        $db = $this->db;
        $status = $_GET['status'] ?? 'all';
        if (!in_array((string)$status, ['0', '1', '2', '3', '-1', '-2', 'all'], true)) $status = 'all';
        $q = trim($_GET['q'] ?? '');

        $where = '1=1';
        $params = [];
        if ($status !== 'all') {
            $where .= ' AND o.status=?';
            $params[] = (int)$status;
        }
        if ($q !== '') {
            $where .= ' AND (o.id=? OR o.mobile LIKE ? OR o.userid IN (SELECT id FROM WapUser WHERE username LIKE ?))';
            $params[] = (int)$q;
            $params[] = '%' . $q . '%';
            $params[] = '%' . $q . '%';
        }
        $rows = $db->getAll(
            "SELECT o.*,"
            . " (SELECT username FROM WapUser WHERE WapUser.id=o.userid) AS username,"
            . " (SELECT title FROM WapShop WHERE WapShop.id=o.shopid) AS shoptitle"
            . " FROM WapShopOrder o WHERE $where ORDER BY o.id DESC",
            $params
        );

        $this->sendCsvHeader('orders');
        $this->putRow(['订单号', '商品', '数量', '金额(元)', '状态', '会员ID', '用户名', '手机号', '收货地址', '下单时间']);
        foreach ($rows as $o) {
            $this->putRow([
                (int)$o['id'],
                $o['shoptitle'] ?? '已删除商品',
                (int)$o['num'],
                number_format((float)$o['totalprice'], 2, '.', ''),
                self::ORDER_STATUS[(int)$o['status']] ?? '未知',
                (int)$o['userid'],
                $o['username'] ?? '',
                $o['mobile'] ?? '',
                $o['address'] ?? '',
                (string)($o['addtime'] ?? ''),
            ]);
        }
    }

    private function exportGoods() {
        $db = $this->db;
        $status = $_GET['status'] ?? 'all';
        if (!in_array($status, ['all', 'onshow', 'offshelf'], true)) $status = 'all';
        $low = (int)($_GET['low'] ?? 0) === 1 ? 1 : 0;
        $q = trim($_GET['q'] ?? '');

        $where = '1=1';
        $params = [];
        if ($status === 'onshow') {
            $where .= ' AND g.isshow=1';
        } elseif ($status === 'offshelf') {
            $where .= ' AND g.isshow<>1';
        }
        if ($low === 1) {
            $where .= ' AND g.stock<5';
        }
        if ($q !== '') {
            $where .= ' AND (g.id=? OR g.title LIKE ?)';
            $params[] = (int)$q;
            $params[] = '%' . $q . '%';
        }
        $rows = $db->getAll(
            "SELECT g.*,"
            . " (SELECT classname FROM WapClass WHERE WapClass.id=g.classid) AS classname"
            . " FROM WapShop g WHERE $where ORDER BY g.id DESC",
            $params
        );

        $this->sendCsvHeader('goods');
        $this->putRow(['商品ID', '标题', '栏目', '价格(元)', '库存', '点击', '状态', '上架时间']);
        foreach ($rows as $g) {
            $this->putRow([
                (int)$g['id'],
                $g['title'],
                $g['classname'] ?? '未分类',
                number_format((float)$g['price'], 2, '.', ''),
                (int)$g['stock'],
                (int)$g['hits'],
                (int)$g['isshow'] === 1 ? '在售' : '已下架',
                (string)($g['addtime'] ?? ''),
            ]);
        }
    }

    /** 输出 CSV 响应头（UTF-8 BOM 便于 Excel 识别） */
    private function sendCsvHeader($prefix) {
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $prefix . '_' . date('Ymd_His') . '.csv"');
        header('Cache-Control: no-store');
        echo "\xEF\xBB\xBF";
        $this->out = fopen('php://output', 'w');
    }

    /** 写一行 CSV，防止公式注入 */
    private function putRow($row) {
        foreach ($row as $i => $v) {
            $v = (string)$v;
            if ($v !== '' && strpos('=+-@', $v[0]) !== false) $v = "'" . $v;
            $row[$i] = $v;
        }
        fputcsv($this->out, $row, ',', '"', '');
    }
}

$page = new AdminExportPage();
$page->run();
