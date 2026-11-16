<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * guessbook模块 - 添加
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class GuessbookAddPage extends WapPage {
    public function run() {
        $this->checkLogin();
        $this->showTop('添加内容');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') $this->doAdd();
        else $this->showForm();
        $this->showDown();
    }
    private function showForm() {
        echo '<div class="subtitle">添加内容</div>';
        echo '<form method="post" action="admin_WAPadd.php?classid=' . $this->classid . '" enctype="multipart/form-data">';
        echo '标题:<input type="text" name="title" required/><br/>';
        echo '内容:<textarea name="content" rows="8" required></textarea><br/>';
        echo '图片:<input type="file" name="pic"/><br/>';
        echo '<input type="submit" value="提交"/></form>';
    }
    private function doAdd() {
        $db = $this->db;
        $title = trim($_POST['title'] ?? '');
        $content = $_POST['content'] ?? '';
        if ($title === '' || $content === '') { echo '<div class="error">标题内容不能为空</div>'; $this->showForm(); return; }
        if (WapTool::checkSqlInject($title) || WapTool::checkSqlInject($content)) { echo '<div class="error">含非法字符</div>'; return; }
        if (!$this->userSession->checkSubmitLimit()) { echo '<div class="error">操作过于频繁</div>'; return; }
        $picurl = '';
        if (!empty($_FILES['pic']['name'])) {
            $ext = strtolower(pathinfo($_FILES['pic']['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, explode('|', KL_NotDownAndUpload))) {
                $dir = KL_UPLOAD_DIR . '/guessbook/' . date('Ym');
                if (!is_dir($dir)) @mkdir($dir, 0755, true);
                $fname = date('YmdHis') . '_' . mt_rand(1000,9999) . '.' . $ext;
                if (move_uploaded_file($_FILES['pic']['tmp_name'], $dir . '/' . $fname)) {
                    $picurl = '/uploadfiles/guessbook/' . date('Ym') . '/' . $fname;
                }
            }
        }
        $uid = $this->userSession->getUserId();
        $id = $db->insert("INSERT INTO WapGuestbook (classid,userid,title,content,picurl,istop,isshow,addtime,hits) VALUES (?,?,?,?,?,0,1,NOW(),0)",[$this->classid,$uid,$title,$content,$picurl]);
        echo '<div class="success">添加成功 <a href="view.php?classid=' . $this->classid . '&id=' . $id . '">查看</a></div>';
    }
}
$page = new GuessbookAddPage();
$page->run();
