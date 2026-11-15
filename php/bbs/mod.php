<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 论坛模块 - 帖子管理 (对应原 bbs/mod.aspx)
 * 帖子作者或管理员(session kl_adminid)可编辑/删除/置顶/加精/锁帖
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class BbsModPage extends WapPage {
    public function run() {
        $db = $this->db;
        $act = $_GET['act'] ?? '';
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) $this->showError('参数错误');

        $thread = $db->getOne("SELECT * FROM WapBbs WHERE id=?", [$id]);
        if (!$thread) $this->showError('帖子不存在');

        // 权限：管理员或帖子作者
        $isAdmin = !empty($_SESSION['kl_adminid']);
        if (!$isAdmin) {
            $this->checkLogin(); // 未登录跳转登录页
            if ((int)$thread['userid'] !== (int)$this->userSession->getUserId()) {
                $this->showError('无权限操作此帖子', '../myfile.php?siteid=' . $this->siteid);
            }
        }

        $backUrl = 'view.php?siteid=' . $this->siteid . '&classid=' . $thread['classid'] . '&id=' . $id;

        switch ($act) {
            case 'edit':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $this->doEdit($id, $backUrl);
                }
                $this->showEditForm($thread);
                break;
            case 'del':
                $db->execute("DELETE FROM WapBbs WHERE id=?", [$id]);
                $db->execute("DELETE FROM WapBbsReply WHERE bbsid=?", [$id]);
                $this->showSuccess('帖子已删除', $backUrl);
                break;
            case 'top':
                $new = $thread['istop'] ? 0 : 1;
                $db->execute("UPDATE WapBbs SET istop=? WHERE id=?", [$new, $id]);
                $this->showSuccess($new ? '已置顶' : '已取消置顶', $backUrl);
                break;
            case 'good':
                $new = $thread['isgood'] ? 0 : 1;
                $db->execute("UPDATE WapBbs SET isgood=? WHERE id=?", [$new, $id]);
                $this->showSuccess($new ? '已加精' : '已取消精华', $backUrl);
                break;
            case 'lock':
                $new = $thread['islock'] ? 0 : 1;
                $db->execute("UPDATE WapBbs SET islock=? WHERE id=?", [$new, $id]);
                $this->showSuccess($new ? '已锁帖' : '已解锁', $backUrl);
                break;
            default:
                $this->showError('未知操作', $backUrl);
        }
        $this->showDown();
    }

    /** 编辑表单 */
    private function showEditForm($thread) {
        $this->showTop('编辑帖子');
        echo '<div class="subtitle">编辑帖子</div>';
        echo '<div class="content"><form method="post" action="mod.php?siteid=' . $this->siteid . '&act=edit&id=' . (int)$thread['id'] . '">';
        echo '标题:<input type="text" name="title" value="' . $this->ToHtm($thread['title']) . '" required/><br/>';
        echo '内容:<textarea name="content" rows="8" required>' . $this->ToHtm($thread['content']) . '</textarea><br/>';
        echo '<input type="submit" value="保存修改"/></form></div>';
        echo '<div><a href="view.php?siteid=' . $this->siteid . '&id=' . (int)$thread['id'] . '">返回帖子</a></div>';
    }

    /** 执行编辑 */
    private function doEdit($id, $backUrl) {
        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');
        if ($title === '' || $content === '') $this->showError('标题内容不能为空');
        if (WapTool::checkSqlInject($title) || WapTool::checkSqlInject($content)) $this->showError('含非法字符');
        if (!$this->userSession->checkSubmitLimit()) $this->showError('操作过于频繁，请稍后再试');
        $this->db->execute("UPDATE WapBbs SET title=?, content=? WHERE id=?", [$title, $content, $id]);
        $this->showSuccess('修改成功', $backUrl);
    }
}

$page = new BbsModPage();
$page->run();
