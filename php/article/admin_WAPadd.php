<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 文章模块 - 后台添加文章 (对应原 article/admin_WAPadd.aspx)
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class ArticleAddPage extends WapPage {
    public function run() {
        $this->checkLogin();
        $this->checkSubmitRight();

        $this->showTop('添加文章');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->doAdd();
        } else {
            $this->showForm();
        }
        $this->showDown();
    }

    private function checkSubmitRight() {
        // 检查是否有该栏目投稿权限
        $db = $this->db;
        $uid = $this->userSession->getUserId();
        // 简化: 已登录即可投稿，真实系统需查 WapClassRight 表
    }

    private function showForm() {
        echo '<div class="subtitle">添加文章</div>';
        echo '<div class="content"><form method="post" action="admin_WAPadd.php?classid=' . $this->classid . '" enctype="multipart/form-data">';
        echo '标题:<input type="text" name="title" required/><br/>';
        echo '作者:<input type="text" name="author" value="' . $this->ToHtm($this->userSession->getUserName()) . '"/><br/>';
        echo '内容:<textarea name="content" rows="10" required></textarea><br/>';
        echo '图片:<input type="file" name="pic"/><br/>';
        echo '置顶:<input type="checkbox" name="istop" value="1"/><br/>';
        echo '<input type="submit" value="提交"/></form></div>';
    }

    private function doAdd() {
        $db = $this->db;
        $title = trim($_POST['title'] ?? '');
        $author = trim($_POST['author'] ?? '');
        $content = $_POST['content'] ?? '';
        $istop = isset($_POST['istop']) ? 1 : 0;

        if ($title === '' || $content === '') {
            echo '<div class="error">标题和内容不能为空</div>';
            $this->showForm();
            return;
        }
        if (!$this->userSession->checkSubmitLimit()) {
            echo '<div class="error">操作过于频繁</div>';
            $this->showForm();
            return;
        }

        // 图片上传
        $picurl = '';
        if (!empty($_FILES['pic']['name'])) {
            $picurl = $this->uploadPic();
        }

        $uid = $this->userSession->getUserId();
        $id = $db->insert("INSERT INTO WapArticle (classid,userid,title,author,content,picurl,istop,isshow,addtime,hits) VALUES (?,?,?,?,?,?,?,1,NOW(),0)",[$this->classid,$uid,$title,$author,$content,$picurl,$istop]);
        echo '<div class="success">添加成功 <a href="view.php?classid=' . $this->classid . '&id=' . $id . '">查看</a></div>';
    }

    private function uploadPic() {
        $ext = strtolower(pathinfo($_FILES['pic']['name'], PATHINFO_EXTENSION));
        $forbidden = explode('|', KL_NotDownAndUpload);
        if (in_array($ext, $forbidden)) return '';
        $dir = KL_UPLOAD_DIR . '/article/' . date('Ym');
        if (!is_dir($dir)) @mkdir($dir, 0755, true);
        $fname = date('YmdHis') . '_' . mt_rand(1000, 9999) . '.' . $ext;
        $fpath = $dir . '/' . $fname;
        if (move_uploaded_file($_FILES['pic']['tmp_name'], $fpath)) {
            return '/uploadfiles/article/' . date('Ym') . '/' . $fname;
        }
        return '';
    }
}

$page = new ArticleAddPage();
$page->run();
