<?php
/** 小宇网络 WAP建站系统 PHP版 - 小说添加 */
require_once __DIR__ . '/../includes/bootstrap.php';
class NovelAddPage extends WapPage {
    public function run() {
        $this->checkLogin();
        $this->showTop('添加小说');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') $this->doAdd();
        else $this->showForm();
        $this->showDown();
    }
    private function showForm() {
        echo '<div class="subtitle">添加小说</div>';
        echo '<form method="post" action="admin_WAPadd.php?siteid=' . $this->siteid . '&classid=' . $this->classid . '" enctype="multipart/form-data">';
        echo '书名:<input type="text" name="title" required/><br/>';
        echo '作者:<input type="text" name="author"/><br/>';
        echo '简介:<textarea name="intro" rows="5"></textarea><br/>';
        echo '封面:<input type="file" name="pic"/><br/>';
        echo '<input type="submit" value="提交"/></form>';
    }
    private function doAdd() {
        $db = $this->db;
        $title = trim($_POST['title'] ?? '');
        $intro = $_POST['intro'] ?? '';
        if ($title === '') { echo '<div class="error">书名不能为空</div>'; $this->showForm(); return; }
        if (!$this->userSession->checkSubmitLimit()) { echo '<div class="error">操作过于频繁</div>'; return; }
        $picurl = '';
        if (!empty($_FILES['pic']['name'])) {
            $ext = strtolower(pathinfo($_FILES['pic']['name'], PATHINFO_EXTENSION));
            $dir = KL_UPLOAD_DIR . '/novel/' . date('Ym');
            if (!is_dir($dir)) @mkdir($dir, 0755, true);
            $fname = date('YmdHis') . '_' . mt_rand(1000,9999) . '.' . $ext;
            if (move_uploaded_file($_FILES['pic']['tmp_name'], $dir . '/' . $fname)) {
                $picurl = '/uploadfiles/novel/' . date('Ym') . '/' . $fname;
            }
        }
        $uid = $this->userSession->getUserId();
        $id = $db->insert("INSERT INTO WapNovel (siteid,classid,title,author,intro,picurl,isshow,hits,addtime) VALUES (?,?,?,?,?,?,1,0,NOW())",
            [$this->siteid, $this->classid, $title, $_POST['author'] ?? '', $intro, $picurl]);
        echo '<div class="success">添加成功 <a href="view.php?siteid=' . $this->siteid . '&classid=' . $this->classid . '&id=' . $id . '">查看</a></div>';
    }
}
$page = new NovelAddPage();
$page->run();
