<?php
/** 小宇网络 WAP建站系统 PHP版 - 视频添加 */
require_once __DIR__ . '/../includes/bootstrap.php';
class VideoAddPage extends WapPage {
    public function run() {
        $this->checkLogin();
        $this->showTop('添加视频');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') $this->doAdd();
        else $this->showForm();
        $this->showDown();
    }
    private function showForm() {
        echo '<div class="subtitle">添加视频</div>';
        echo '<form method="post" action="admin_WAPadd.php?siteid=' . $this->siteid . '&classid=' . $this->classid . '" enctype="multipart/form-data">';
        echo '标题:<input type="text" name="title" required/><br/>';
        echo '视频地址:<input type="text" name="videourl"/><br/>';
        echo '封面:<input type="file" name="pic"/><br/>';
        echo '描述:<textarea name="content" rows="5"></textarea><br/>';
        echo '<input type="submit" value="提交"/></form>';
    }
    private function doAdd() {
        $db = $this->db;
        $title = trim($_POST['title'] ?? '');
        if ($title === '') { echo '<div class="error">标题不能为空</div>'; $this->showForm(); return; }
        if (!$this->userSession->checkSubmitLimit()) { echo '<div class="error">操作过于频繁</div>'; return; }
        $picurl = '';
        if (!empty($_FILES['pic']['name'])) {
            $ext = strtolower(pathinfo($_FILES['pic']['name'], PATHINFO_EXTENSION));
            $dir = KL_UPLOAD_DIR . '/video/' . date('Ym');
            if (!is_dir($dir)) @mkdir($dir, 0755, true);
            $fname = date('YmdHis') . '_' . mt_rand(1000,9999) . '.' . $ext;
            if (move_uploaded_file($_FILES['pic']['tmp_name'], $dir . '/' . $fname)) {
                $picurl = '/uploadfiles/video/' . date('Ym') . '/' . $fname;
            }
        }
        $id = $db->insert("INSERT INTO WapVideo (siteid,classid,title,content,picurl,videourl,isshow,hits,addtime) VALUES (?,?,?,?,?,?,1,0,NOW())",
            [$this->siteid, $this->classid, $title, $_POST['content'] ?? '', $picurl, $_POST['videourl'] ?? '']);
        echo '<div class="success">添加成功 <a href="view.php?siteid=' . $this->siteid . '&classid=' . $this->classid . '&id=' . $id . '">查看</a></div>';
    }
}
$page = new VideoAddPage();
$page->run();
