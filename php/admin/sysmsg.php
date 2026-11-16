<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 管理后台 - 系统公告 (对应原 WapSysMsg 管理)
 * 公告增删改
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class AdminSysMsgPage extends WapPage {
    public function run() {
        $this->checkAdminLogin();
        $db = $this->db;
        $back = 'sysmsg.php';

        // POST 操作：新增 / 编辑 / 删除（防重复提交）
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->userSession->checkSubmitLimit()) {
                $this->showError('提交过于频繁，请稍后再试', $back);
            }
            $action = $_POST['action'] ?? '';
            $id = (int)($_POST['id'] ?? 0);
            if ($action === 'save' || $action === 'add') {
                $title = trim($_POST['title'] ?? '');
                $content = trim($_POST['content'] ?? '');
                if ($title === '') {
                    $this->showError('公告标题不能为空', $back);
                }
                if ($action === 'save' && $id > 0) {
                    $db->execute("UPDATE WapSysMsg SET title=?, content=? WHERE id=?",[$title,$content,$id]);
                    $this->showSuccess('公告保存成功', $back);
                } else {
                    $newId = $db->insert("INSERT INTO WapSysMsg (title,content,addtime) VALUES (?,?,datetime('now','localtime'))",[$title,$content]);
                    $this->showSuccess('公告添加成功，ID=' . $newId, $back);
                }
            } elseif ($action === 'delete') {
                $db->execute("DELETE FROM WapSysMsg WHERE id=?",[$id]);
                $this->showSuccess('公告已删除', $back);
            } else {
                $this->showError('非法操作', $back);
            }
        }

        $this->strTitle = '系统公告';
        $this->showTop($this->strTitle);
        echo '<div class="subtitle">系统公告</div><div><a href="index.php">返回后台首页</a></div>';

        // 新增/编辑表单
        $editId = (int)($_GET['edit'] ?? 0);
        $row = ['id' => 0, 'title' => '', 'content' => ''];
        $isEdit = $editId > 0;
        if ($isEdit) {
            $found = $db->getOne("SELECT * FROM WapSysMsg WHERE id=?",[$editId]);
            if ($found) { $row = $found; } else { $isEdit = false; }
        }
        $act = $isEdit ? 'save' : 'add';
        echo '<div class="content"><form method="post" action="' . $back . '">'
            . '<input type="hidden" name="action" value="' . $act . '"/>'
            . '<input type="hidden" name="id" value="' . (int)$row['id'] . '"/>'
            . '<div>标题<br/><input type="text" name="title" value="' . $this->ToHtm($row['title'] ?? '') . '" size="30"/></div>'
            . '<div>内容<br/><textarea name="content" rows="4" cols="32">' . $this->ToHtm($row['content'] ?? '') . '</textarea></div>'
            . '<div><input type="submit" value="' . ($isEdit ? '保存公告' : '添加公告') . '"/></div>'
            . '</form></div>';

        // 公告列表
        $list = $db->getAll("SELECT * FROM WapSysMsg ORDER BY id DESC",[]);
        echo '<div class="content"><ul class="list">';
        foreach ($list as $m) {
            $mid = (int)$m['id'];
            $time = !empty($m['addtime']) ? date('m-d H:i', strtotime($m['addtime'])) : '';
            echo '<li>ID:' . $mid . ' <a href="' . $back . '&edit=' . $mid . '">' . $this->ToHtm($m['title']) . '</a>'
                . ' <span class="time">' . $time . '</span>';
            echo '<form method="post" action="' . $back . '" style="display:inline">'
                . '<input type="hidden" name="action" value="delete"/><input type="hidden" name="id" value="' . $mid . '"/>'
                . '<input type="submit" value="删除"/></form></li>';
        }
        if (empty($list)) echo '<li>暂无公告</li>';
        echo '</ul></div>';

        $this->showDown();
    }
}

$page = new AdminSysMsgPage();
$page->run();
