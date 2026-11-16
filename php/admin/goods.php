<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 管理后台 - 商城商品管理
 * 支持: 关键词搜索 / 库存预警(stock<5) / 下架筛选 / 快捷改价改库存 / 上下架切换
 *       新增商品 / 编辑商品 / 删除商品(在途订单拦截, 二次确认)
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
            } elseif ($act === 'savefull') {
                $this->doSaveFull($id, $target);
            } elseif ($act === 'toggle') {
                $this->doToggle($id, $target);
            } elseif ($act === 'deldone') {
                $this->doDelete($id, $target);
            } else {
                $this->showError('非法操作', $back);
            }
            header('Location: ' . $target);
            exit;
        }

        // 新增/编辑表单
        $act = $_GET['act'] ?? '';
        $id = (int)($_GET['id'] ?? 0);
        if ($act === 'add' || ($act === 'edit' && $id > 0)) {
            $this->showForm($act === 'edit' ? $id : 0);
            return;
        }
        // 删除二次确认页
        if ($act === 'del' && $id > 0) {
            $this->showDeleteConfirm($id);
            return;
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

        echo '<div class="content"><a href="' . $this->backUrl($q, $status, $low) . '&act=add">新增商品</a> | <a href="export.php?type=goods&status=' . urlencode((string)$status)
            . '&low=' . $low . ($q !== '' ? '&q=' . urlencode($q) : '') . '">导出当前筛选商品 CSV</a></div>';

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
            echo ' <a href="' . $this->backUrl($q, $status, $low) . '&act=edit&id=' . $id . '">编辑</a>';
            echo ' <a href="' . $this->backUrl($q, $status, $low) . '&act=del&id=' . $id . '">删除</a>';
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

    /** 新增/编辑表单页 */
    private function showForm($id) {
        $db = $this->db;
        $isEdit = $id > 0;
        if ($isEdit) {
            $row = $db->getOne("SELECT * FROM WapShop WHERE id=?", [$id]);
            if (!$row) $this->showError('商品不存在', 'goods.php');
        } else {
            $row = ['id' => 0, 'classid' => 0, 'title' => '', 'content' => '',
                'picurl' => '', 'price' => '0.00', 'stock' => 0, 'isshow' => 1];
        }
        $classes = $db->getAll("SELECT id,classname FROM WapClass WHERE module='shop' ORDER BY orderid ASC, id ASC", []);

        $this->strTitle = $isEdit ? '编辑商品' : '新增商品';
        $this->showTop($this->strTitle);
        echo '<div class="subtitle">' . $this->strTitle . '</div>';
        echo '<div><a href="goods.php">返回商品列表</a></div>';
        if (isset($_GET['err'])) echo '<div class="error">' . $this->ToHtm($_GET['err']) . '</div>';

        echo '<div class="content"><form method="post" action="goods.php">';
        echo '<input type="hidden" name="act" value="savefull"/>';
        echo '<input type="hidden" name="id" value="' . (int)$row['id'] . '"/>';
        echo '<div>商品标题<br/><input type="text" name="title" maxlength="255" size="30" value="' . $this->ToHtm($row['title']) . '"/></div>';

        echo '<div>所属栏目 <select name="classid"><option value="0">未分类</option>';
        $classFound = false;
        foreach ($classes as $c) {
            $sel = ((int)$row['classid'] === (int)$c['id']) ? ' selected="selected"' : '';
            if ($sel !== '') $classFound = true;
            echo '<option value="' . (int)$c['id'] . '"' . $sel . '>' . $this->ToHtm($c['classname']) . '</option>';
        }
        // 编辑时若原栏目不属于商城模块，补一个选项避免选择丢失
        if (!$classFound && (int)$row['classid'] > 0) {
            $cn = $db->getScalar("SELECT classname FROM WapClass WHERE id=?", [(int)$row['classid']]);
            echo '<option value="' . (int)$row['classid'] . '" selected="selected">'
                . $this->ToHtm($cn !== null ? $cn : ('栏目#' . (int)$row['classid'])) . '</option>';
        }
        echo '</select></div>';

        echo '<div>价格(元)<br/><input type="text" name="price" size="10" value="'
            . number_format((float)$row['price'], 2, '.', '') . '"/></div>';
        echo '<div>库存<br/><input type="text" name="stock" size="10" value="' . (int)$row['stock'] . '"/></div>';
        echo '<div>图片地址<br/><input type="text" name="picurl" maxlength="255" size="30" value="' . $this->ToHtm($row['picurl']) . '"/></div>';
        echo '<div>商品简介<br/><textarea name="content" rows="4" cols="30">' . $this->ToHtm($row['content']) . '</textarea></div>';
        echo '<div><label><input type="checkbox" name="isshow" value="1"'
            . ((int)$row['isshow'] === 1 ? ' checked="checked"' : '') . '/>上架销售</label></div>';
        echo '<div><input type="submit" value="' . ($isEdit ? '保存修改' : '确认新增') . '"/></div>';
        echo '</form></div>';
        $this->showDown();
    }

    /** 新增/编辑保存 */
    private function doSaveFull($id, $back) {
        $db = $this->db;
        $title = trim($_POST['title'] ?? '');
        $classid = (int)($_POST['classid'] ?? 0);
        $price = trim($_POST['price'] ?? '');
        $stock = trim($_POST['stock'] ?? '');
        $picurl = trim($_POST['picurl'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $isshow = isset($_POST['isshow']) ? 1 : 0;

        $err = '';
        if ($title === '') {
            $err = '商品标题不能为空';
        } elseif (mb_strlen($title) > 255) {
            $err = '商品标题不能超过255字';
        } elseif (!preg_match('/^\d+(\.\d{1,2})?$/', $price)) {
            $err = '价格必须为非负数字，最多两位小数';
        } elseif (!preg_match('/^\d+$/', $stock)) {
            $err = '库存必须为非负整数';
        } elseif ($classid > 0 && !$db->getOne("SELECT id FROM WapClass WHERE id=? AND module='shop'", [$classid])) {
            $err = '所选栏目不存在或不是商城栏目';
        }
        if ($err !== '') {
            $formUrl = $id > 0 ? 'goods.php?act=edit&id=' . $id : 'goods.php?act=add';
            header('Location: ' . $formUrl . '&err=' . urlencode($err));
            exit;
        }

        $priceV = number_format((float)$price, 2, '.', '');
        $stockV = (int)$stock;

        if ($id > 0) {
            if (!$db->getOne("SELECT id FROM WapShop WHERE id=?", [$id])) {
                $this->showError('商品不存在', $back);
            }
            $db->execute("UPDATE WapShop SET classid=?,title=?,content=?,picurl=?,price=?,stock=?,isshow=? WHERE id=?",
                [$classid, $title, $content, $picurl, $priceV, $stockV, $isshow, $id]);
            $msg = '商品 #' . $id . ' 已保存';
        } else {
            $newId = $db->insert(
                "INSERT INTO WapShop (classid,userid,title,content,picurl,price,stock,isshow,hits,addtime)"
                . " VALUES (?,0,?,?,?,?,?,?,0,NOW())",
                [$classid, $title, $content, $picurl, $priceV, $stockV, $isshow]
            );
            $msg = '商品已新增，ID=' . $newId;
        }
        kl_cache_clear_site();
        header('Location: ' . $back . '&ok=' . urlencode($msg));
        exit;
    }

    /** 删除二次确认页（存在在途订单时直接拦截） */
    private function showDeleteConfirm($id) {
        $db = $this->db;
        $goods = $db->getOne("SELECT id,title FROM WapShop WHERE id=?", [$id]);
        if (!$goods) $this->showError('商品不存在', 'goods.php');
        $lockCount = (int)$db->getScalar(
            "SELECT COUNT(*) FROM WapShopOrder WHERE shopid=? AND status IN (0,1,2)", [$id]);

        $this->strTitle = '删除商品';
        $this->showTop($this->strTitle);
        echo '<div class="subtitle">删除确认</div><div class="content">';
        echo '确定删除商品「' . $this->ToHtm($goods['title']) . '」（ID:' . (int)$goods['id'] . '）吗？删除后不可恢复。<br/>';
        if ($lockCount > 0) {
            echo '<div class="error">该商品存在 ' . $lockCount . ' 个未完成订单，暂不能删除；请先处理订单或改为下架。</div>';
            echo '<a href="goods.php">返回商品列表</a>';
        } else {
            echo '<form method="post" action="goods.php">';
            echo '<input type="hidden" name="act" value="deldone"/>';
            echo '<input type="hidden" name="id" value="' . (int)$goods['id'] . '"/>';
            echo '<input type="submit" value="确认删除"/> <a href="goods.php">取消</a>';
            echo '</form>';
        }
        echo '</div>';
        $this->showDown();
    }

    /** 删除商品：存在待付款/待发货/已发货订单时拒绝 */
    private function doDelete($id, $back) {
        $db = $this->db;
        if ($id <= 0) $this->showError('商品不存在', $back);
        if (!$db->getOne("SELECT id FROM WapShop WHERE id=?", [$id])) {
            $this->showError('商品不存在', $back);
        }
        $lockCount = (int)$db->getScalar(
            "SELECT COUNT(*) FROM WapShopOrder WHERE shopid=? AND status IN (0,1,2)", [$id]);
        if ($lockCount > 0) {
            $this->showError('该商品存在 ' . $lockCount . ' 个未完成订单，无法删除，请先处理订单或改为下架', $back);
        }
        $db->execute("DELETE FROM WapShop WHERE id=?", [$id]);
        kl_cache_clear_site();
        header('Location: ' . $back . '&ok=' . urlencode('商品 #' . $id . ' 已删除'));
        exit;
    }
}

$page = new AdminGoodsPage();
$page->run();
