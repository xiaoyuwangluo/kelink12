<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 管理后台 - 站点管理 (对应原 admin/sitelist.aspx)
 * 站点列表 / 编辑(名称/版权/关闭开关/关闭提示) / 删除
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class AdminSitePage extends WapPage {
    public function run() {
        $this->checkAdminLogin();
        $db = $this->db;
        $back = 'site.php?siteid=' . $this->siteid;

        // POST 操作：保存编辑 / 删除（防重复提交）
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->userSession->checkSubmitLimit()) {
                $this->showError('提交过于频繁，请稍后再试', $back);
            }
            $action = $_POST['action'] ?? '';
            $id = (int)($_POST['id'] ?? 0);
            if ($action === 'save') {
                $sitename = trim($_POST['sitename'] ?? '');
                if ($sitename === '') {
                    $this->showError('站点名称不能为空', $back);
                }
                $isclose = isset($_POST['isclose']) ? 1 : 0;
                $n = $db->execute("UPDATE WapSite SET sitename=?, copyright=?, closetip=?, isclose=? WHERE id=?",
                    [$sitename, trim($_POST['copyright'] ?? ''), trim($_POST['closetip'] ?? ''), $isclose, $id]);
                $this->showSuccess($n > 0 ? '站点保存成功' : '站点不存在或无变更', $back);
            } elseif ($action === 'delete') {
                $db->execute("DELETE FROM WapSite WHERE id=?", [$id]);
                $this->showSuccess('站点已删除', $back);
            } else {
                $this->showError('非法操作', $back);
            }
        }

        $this->strTitle = '站点管理';
        $this->showTop($this->strTitle);
        echo '<div class="subtitle">站点管理</div><div><a href="index.php">返回后台首页</a></div>';

        // 编辑表单
        $editId = (int)($_GET['edit'] ?? 0);
        if ($editId > 0) {
            $row = $db->getOne("SELECT * FROM WapSite WHERE id=?", [$editId]);
            if ($row) {
                echo '<div class="content"><form method="post" action="' . $back . '">'
                    . '<input type="hidden" name="action" value="save"/>'
                    . '<input type="hidden" name="id" value="' . $editId . '"/>'
                    . '<div>站点名称<br/><input type="text" name="sitename" value="' . $this->ToHtm($row['sitename'] ?? '') . '" size="30"/></div>'
                    . '<div>版权信息<br/><input type="text" name="copyright" value="' . $this->ToHtm($row['copyright'] ?? '') . '" size="30"/></div>'
                    . '<div>关闭提示语<br/><input type="text" name="closetip" value="' . $this->ToHtm($row['closetip'] ?? '') . '" size="30"/></div>'
                    . '<div><label><input type="checkbox" name="isclose" value="1"' . (empty($row['isclose']) ? '' : ' checked="checked"') . '/>关闭站点</label></div>'
                    . '<div><input type="submit" value="保存站点"/></div>'
                    . '</form></div>';
            } else {
                echo '<div class="error">站点不存在</div>';
            }
        }

        // 站点列表
        $list = $db->getAll("SELECT * FROM WapSite ORDER BY id ASC");
        echo '<div class="content"><ul class="list">';
        foreach ($list as $row) {
            $closeFlag = empty($row['isclose']) ? '<span style="color:green">开放</span>' : '<span style="color:red">已关闭</span>';
            echo '<li>ID:' . (int)$row['id'] . ' <a href="site.php?siteid=' . $this->siteid . '&edit=' . (int)$row['id'] . '">' . $this->ToHtm($row['sitename']) . '</a> ' . $closeFlag;
            echo ' <form method="post" action="' . $back . '" style="display:inline">'
                . '<input type="hidden" name="action" value="delete"/>'
                . '<input type="hidden" name="id" value="' . (int)$row['id'] . '"/>'
                . '<input type="submit" value="删除"/></form></li>';
        }
        echo '</ul></div>';

        $this->showDown();
    }
}

$page = new AdminSitePage();
$page->run();
