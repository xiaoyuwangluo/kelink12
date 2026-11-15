<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * guangbo模块 - 后台修改内容 (对应原 guangbo/admin_WAPmodify.aspx)
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class GuangboModifyPage extends WapPage {
    public function run() {
        $this->guardAuth();
        $db = $this->db;
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) $this->showError('参数错误');

        $row = $db->getOne("SELECT * FROM WapGuangbo WHERE id=?", [$id]);
        if (!$row) $this->showError('记录不存在');

        $this->showTop('修改广播信息');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->doModify($id);
        } else {
            $this->showForm($row);
        }
        $this->showDown();
    }

    /** 鉴权：后台管理员(kl_adminid)或已登录会员可操作，未登录跳转登录页 */
    private function guardAuth() {
        if ($this->userSession->isAdminLogin()) return;
        $this->checkLogin();
    }

    private function showForm($a) {
        echo '<div class="subtitle">修改广播信息</div>';
        echo '<form method="post" action="admin_WAPmodify.php?siteid=' . $this->siteid . '&classid=' . $this->classid . '&id=' . $a['id'] . '">';
        echo '标题:<input type="text" name="title" value="' . $this->ToHtm($a['title']) . '"/><br/>';
        echo '内容:<textarea name="content" rows="10">' . $this->ToHtm($a['content']) . '</textarea><br/>';
        echo '图片地址:<input type="text" name="picurl" value="' . $this->ToHtm($a['picurl'] ?? '') . '"/><br/>';
        echo '链接地址:<input type="text" name="downurl" value="' . $this->ToHtm($a['downurl'] ?? '') . '"/><br/>';
        echo '置顶:<input type="checkbox" name="istop" value="1" ' . ($a['istop'] ? 'checked' : '') . '/><br/>';
        echo '<input type="submit" value="保存修改"/></form>';
    }

    private function doModify($id) {
        $db = $this->db;
        $title = trim($_POST['title'] ?? '');
        $content = $_POST['content'] ?? '';
        $picurl = trim($_POST['picurl'] ?? '');
        $downurl = trim($_POST['downurl'] ?? '');
        $istop = isset($_POST['istop']) ? 1 : 0;

        if ($title === '') {
            echo '<div class="error">标题不能为空</div>';
            return;
        }
        $db->execute(
            "UPDATE WapGuangbo SET title=?,content=?,picurl=?,downurl=?,istop=? WHERE id=?",
            [$title, $content, $picurl, $downurl, $istop, $id]
        );
        echo '<div class="success">修改成功 <a href="view.php?siteid=' . $this->siteid . '&classid=' . $this->classid . '&id=' . $id . '">查看</a></div>';
    }
}

$page = new GuangboModifyPage();
$page->run();
