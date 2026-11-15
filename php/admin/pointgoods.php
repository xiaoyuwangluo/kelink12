<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 管理后台 - 积分商品管理
 */
require_once __DIR__ . '/../includes/bootstrap.php';
require_once KL_ROOT . '/includes/Community.php';

class AdminPointGoodsPage extends WapPage {
    public function run() {
        $this->checkAdminLogin();
        $db = $this->db;
        $back = 'pointgoods.php?siteid=' . $this->siteid;

        // 保存（新增/编辑）
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['act'] ?? '') === 'save') {
            $this->doSave($back);
        }
        // 删除确认页提交
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['act'] ?? '') === 'deldone') {
            $id = (int)($_POST['id'] ?? 0);
            $db->execute("DELETE FROM WapPointGoods WHERE id=? AND siteid=?", [$id, $this->siteid]);
            kl_cache_clear_site($this->siteid);
            $this->showSuccess('商品已删除', $back);
        }

        $act = $_GET['act'] ?? '';
        $id = (int)($_GET['id'] ?? 0);

        // 删除二次确认页
        if ($act === 'del' && $id > 0) {
            $goods = $db->getOne("SELECT * FROM WapPointGoods WHERE id=? AND siteid=?", [$id, $this->siteid]);
            if (!$goods) $this->showError('商品不存在', $back);
            $this->strTitle = '删除积分商品';
            $this->showTop($this->strTitle);
            echo '<div class="subtitle">删除确认</div><div class="content">';
            echo '确定删除商品「' . $this->ToHtm($goods['name']) . '」吗？删除后不可恢复。<br/>';
            echo '<form method="post" action="' . $back . '">';
            echo '<input type="hidden" name="act" value="deldone"/>';
            echo '<input type="hidden" name="id" value="' . (int)$goods['id'] . '"/>';
            echo '<input type="submit" value="确认删除"/> <a href="' . $back . '">取消</a>';
            echo '</form></div>';
            $this->showDown();
            return;
        }

        // 上下架切换
        if ($act === 'toggle' && $id > 0) {
            $db->execute("UPDATE WapPointGoods SET isshow=CASE WHEN isshow=1 THEN 0 ELSE 1 END WHERE id=? AND siteid=?", [$id, $this->siteid]);
            kl_cache_clear_site($this->siteid);
            $this->showSuccess('上下架状态已切换', $back);
        }

        // 新增/编辑表单
        if ($act === 'add' || ($act === 'edit' && $id > 0)) {
            $goods = ['id' => 0, 'name' => '', 'intro' => '', 'points' => 0, 'stock' => 0, 'image' => '', 'isshow' => 1, 'sortnum' => 0];
            if ($act === 'edit') {
                $row = $db->getOne("SELECT * FROM WapPointGoods WHERE id=? AND siteid=?", [$id, $this->siteid]);
                if (!$row) $this->showError('商品不存在', $back);
                $goods = $row;
            }
            $this->showForm($goods, $back);
            return;
        }

        // 列表
        $page = max(1, (int)($_GET['page'] ?? 1));
        $result = $db->getPage('WapPointGoods', '*', 'siteid=?', 'sortnum DESC, id DESC', $page, 20, [$this->siteid]);

        $this->strTitle = '积分商品管理';
        $this->showTop($this->strTitle);
        echo '<div class="subtitle">积分商品管理</div>';
        echo '<div><a href="index.php">返回后台首页</a> | <a href="' . $back . '&act=add">新增商品</a></div>';
        echo '<div class="content"><ul class="list">';
        foreach ($result['list'] as $g) {
            $id = (int)$g['id'];
            echo '<li>ID:' . $id . ' ' . $this->ToHtm($g['name'])
                . ' 积分:' . (int)$g['points'] . ' 库存:' . (int)$g['stock']
                . ' 状态:' . ((int)$g['isshow'] === 1 ? '上架' : '<span style="color:red">下架</span>')
                . ' 排序:' . (int)$g['sortnum']
                . ' <span class="time">' . (!empty($g['addtime']) ? date('m-d H:i', strtotime($g['addtime'])) : '') . '</span><br/>';
            echo '<a href="' . $back . '&act=edit&id=' . $id . '">编辑</a> ';
            echo '<a href="' . $back . '&act=toggle&id=' . $id . '">' . ((int)$g['isshow'] === 1 ? '下架' : '上架') . '</a> ';
            echo '<a href="' . $back . '&act=del&id=' . $id . '">删除</a>';
            echo '</li>';
        }
        if (empty($result['list'])) echo '<li>暂无商品</li>';
        echo '</ul></div>';

        if ($result['pageCount'] > 1) {
            echo '<div class="pager">';
            if ($page > 1) echo '<a href="' . $back . '&page=' . ($page - 1) . '">上一页</a> ';
            echo '第' . $page . '/' . $result['pageCount'] . '页 ';
            if ($page < $result['pageCount']) echo '<a href="' . $back . '&page=' . ($page + 1) . '">下一页</a>';
            echo '</div>';
        }
        $this->showDown();
    }

    /** 新增/编辑表单 */
    private function showForm($goods, $back) {
        $isEdit = (int)$goods['id'] > 0;
        $this->strTitle = $isEdit ? '编辑积分商品' : '新增积分商品';
        $this->showTop($this->strTitle);
        echo '<div class="subtitle">' . $this->strTitle . '</div>';
        echo '<div><a href="' . $back . '">返回列表</a></div>';
        if (isset($_GET['err'])) echo '<div class="error">' . $this->ToHtm($_GET['err']) . '</div>';
        echo '<div class="content"><form method="post" action="' . $back . '">';
        echo '<input type="hidden" name="act" value="save"/>';
        echo '<input type="hidden" name="id" value="' . (int)$goods['id'] . '"/>';
        echo '名称:<input type="text" name="name" maxlength="100" value="' . $this->ToHtm($goods['name']) . '"/><br/>';
        echo '简介:<input type="text" name="intro" maxlength="255" value="' . $this->ToHtm($goods['intro']) . '"/><br/>';
        echo '所需积分:<input type="text" name="points" value="' . (int)$goods['points'] . '"/><br/>';
        echo '库存:<input type="text" name="stock" value="' . (int)$goods['stock'] . '"/><br/>';
        echo '排序:<input type="text" name="sortnum" value="' . (int)$goods['sortnum'] . '"/><br/>';
        echo '图片地址:<input type="text" name="image" maxlength="255" value="' . $this->ToHtm($goods['image']) . '"/><br/>';
        echo '上架:<input type="checkbox" name="isshow" value="1"' . ((int)$goods['isshow'] === 1 ? ' checked="checked"' : '') . '/><br/>';
        echo '<input type="submit" value="保存"/></form></div>';
        $this->showDown();
    }

    /** 保存并校验 */
    private function doSave($back) {
        $db = $this->db;
        $id = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $intro = trim($_POST['intro'] ?? '');
        $points = trim($_POST['points'] ?? '');
        $stock = trim($_POST['stock'] ?? '');
        $sortnum = trim($_POST['sortnum'] ?? '0');
        $image = trim($_POST['image'] ?? '');
        $isshow = isset($_POST['isshow']) ? 1 : 0;

        $err = '';
        if ($name === '') {
            $err = '商品名称不能为空';
        } elseif (mb_strlen($name) > 100) {
            $err = '商品名称不能超过100字';
        } elseif (!preg_match('/^\d+$/', $points)) {
            $err = '所需积分必须为非负整数';
        } elseif (!preg_match('/^-?\d+$/', $sortnum)) {
            $err = '排序必须为整数';
        } elseif (!preg_match('/^\d+$/', $stock)) {
            $err = '库存必须为非负整数';
        }
        if ($err !== '') {
            $url = $back . '&act=' . ($id > 0 ? 'edit&id=' . $id : 'add') . '&err=' . urlencode($err);
            header('Location: ' . $url);
            exit;
        }

        $points = (int)$points;
        $stock = (int)$stock;
        $sortnum = (int)$sortnum;

        if ($id > 0) {
            $n = $db->execute(
                "UPDATE WapPointGoods SET name=?,intro=?,points=?,stock=?,sortnum=?,isshow=?,image=? WHERE id=? AND siteid=?",
                [$name, $intro, $points, $stock, $sortnum, $isshow, $image, $id, $this->siteid]
            );
            if ($n < 0) $this->showError('保存失败', $back);
            $msg = '商品已更新';
        } else {
            $db->insert(
                "INSERT INTO WapPointGoods (siteid,name,intro,points,stock,image,isshow,sortnum,addtime) VALUES (?,?,?,?,?,?,?,?,NOW())",
                [$this->siteid, $name, $intro, $points, $stock, $image, $isshow, $sortnum]
            );
            $msg = '商品已新增';
        }
        kl_cache_clear_site($this->siteid);
        $this->showSuccess($msg, $back);
    }
}

$page = new AdminPointGoodsPage();
$page->run();
