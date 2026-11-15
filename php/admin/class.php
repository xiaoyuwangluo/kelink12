<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 管理后台 - 栏目管理 (对应原 admin/classlist.aspx)
 * 栏目增删改，module 下拉指定栏目对应模块
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class AdminClassPage extends WapPage {
    /** 可选栏目模块（与 wapindex.php/各模块目录对应） */
    private $modules = ['article','bbs','shop','novel','video','picture','download','ring','link',
        'guessbook','gongqiu','paimai','yuehui','wabao','guangbo','adlink','airplane','album','hotel',
        'rizhi','xinzhang','chat','clan','games','zhdao','nongchang','signin','form'];

    public function run() {
        $this->checkAdminLogin();
        $db = $this->db;
        $siteid = $this->siteid > 0 ? $this->siteid : 1;
        $back = 'class.php?siteid=' . $siteid;

        // POST 操作：新增 / 编辑 / 删除（防重复提交）
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->userSession->checkSubmitLimit()) {
                $this->showError('提交过于频繁，请稍后再试', $back);
            }
            $action = $_POST['action'] ?? '';
            $id = (int)($_POST['id'] ?? 0);
            if ($action === 'save' || $action === 'add') {
                $classname = trim($_POST['classname'] ?? '');
                $module = in_array($_POST['module'] ?? '', $this->modules, true) ? $_POST['module'] : 'article';
                if ($classname === '') {
                    $this->showError('栏目名称不能为空', $back);
                }
                $orderid = (int)($_POST['orderid'] ?? 0);
                $parentid = (int)($_POST['parentid'] ?? 0);
                $isshow = isset($_POST['isshow']) ? 1 : 0;
                if ($action === 'save' && $id > 0) {
                    $db->execute("UPDATE WapClass SET classname=?, module=?, orderid=?, parentid=?, isshow=? WHERE id=? AND siteid=?",
                        [$classname, $module, $orderid, $parentid, $isshow, $id, $siteid]);
                    $this->showSuccess('栏目保存成功', $back);
                } else {
                    $newId = $db->insert("INSERT INTO WapClass (siteid,parentid,classname,module,orderid,isshow) VALUES (?,?,?,?,?,?)",
                        [$siteid, $parentid, $classname, $module, $orderid, $isshow]);
                    $this->showSuccess('栏目添加成功，ID=' . $newId, $back);
                }
            } elseif ($action === 'delete') {
                $db->execute("DELETE FROM WapClass WHERE id=? AND siteid=?", [$id, $siteid]);
                $this->showSuccess('栏目已删除', $back);
            } else {
                $this->showError('非法操作', $back);
            }
        }

        $this->strTitle = '栏目管理';
        $this->showTop($this->strTitle);
        echo '<div class="subtitle">栏目管理 (站点' . $siteid . ')</div><div><a href="index.php">返回后台首页</a></div>';

        // 新增/编辑表单
        $editId = (int)($_GET['edit'] ?? 0);
        $row = null;
        $isEdit = true;
        if ($editId > 0) {
            $row = $db->getOne("SELECT * FROM WapClass WHERE id=? AND siteid=?", [$editId, $siteid]);
            if (!$row) { echo '<div class="error">栏目不存在</div>'; $row = []; }
        } else {
            $isEdit = false;
            $row = ['id' => 0, 'classname' => '', 'module' => 'article', 'orderid' => 0, 'parentid' => 0, 'isshow' => 1];
        }
        $act = $isEdit ? 'save' : 'add';
        echo '<div class="content"><form method="post" action="' . $back . '">'
            . '<input type="hidden" name="action" value="' . $act . '"/>'
            . '<input type="hidden" name="id" value="' . (int)$row['id'] . '"/>'
            . '<div>栏目名称<br/><input type="text" name="classname" value="' . $this->ToHtm($row['classname'] ?? '') . '" size="20"/></div>'
            . '<div>所属模块 <select name="module">';
        foreach ($this->modules as $m) {
            $sel = (($row['module'] ?? '') === $m) ? ' selected="selected"' : '';
            echo '<option value="' . $m . '"' . $sel . '>' . $m . '</option>';
        }
        echo '</select></div>'
            . '<div>排序 <input type="text" name="orderid" value="' . (int)($row['orderid'] ?? 0) . '" size="5"/></div>'
            . '<div>父栏目 <input type="text" name="parentid" value="' . (int)($row['parentid'] ?? 0) . '" size="5"/></div>'
            . '<div><label><input type="checkbox" name="isshow" value="1"' . (empty($row['isshow']) ? '' : ' checked="checked"') . '/>前台显示</label></div>'
            . '<div><input type="submit" value="' . ($isEdit ? '保存栏目' : '添加栏目') . '"/></div>'
            . '</form></div>';

        // 栏目列表
        $list = $db->getAll("SELECT * FROM WapClass WHERE siteid=? ORDER BY orderid ASC, id ASC", [$siteid]);
        echo '<div class="content"><ul class="list">';
        foreach ($list as $c) {
            $hide = empty($c['isshow']) ? '<span style="color:red">[隐藏]</span>' : '';
            echo '<li>ID:' . (int)$c['id'] . ' <a href="' . $back . '&edit=' . (int)$c['id'] . '">' . $this->ToHtm($c['classname']) . '</a>' . $hide
                . ' <span class="info">[' . $this->ToHtm($c['module'] ?? '') . ']</span> 排序:' . (int)$c['orderid'];
            echo ' <form method="post" action="' . $back . '" style="display:inline">'
                . '<input type="hidden" name="action" value="delete"/>'
                . '<input type="hidden" name="id" value="' . (int)$c['id'] . '"/>'
                . '<input type="submit" value="删除"/></form></li>';
        }
        echo '</ul></div>';

        $this->showDown();
    }
}

$page = new AdminClassPage();
$page->run();
