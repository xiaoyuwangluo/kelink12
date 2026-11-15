<?php
/** 小宇网络 WAP建站系统 PHP版 - 商城添加商品 */
require_once __DIR__ . '/../includes/bootstrap.php';
class ShopAddPage extends WapPage {
    public function run() {
        $this->checkLogin();
        $this->showTop('添加商品');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') $this->doAdd();
        else $this->showForm();
        $this->showDown();
    }
    private function showForm() {
        echo '<div class="subtitle">添加商品</div>';
        echo '<form method="post" action="admin_WAPadd.php?siteid=' . $this->siteid . '&classid=' . $this->classid . '" enctype="multipart/form-data">';
        echo '商品名:<input type="text" name="title" required/><br/>';
        echo '价格:<input type="text" name="price" value="0.00"/><br/>';
        echo '库存:<input type="number" name="stock" value="0"/><br/>';
        echo '详情:<textarea name="content" rows="8" required></textarea><br/>';
        echo '图片:<input type="file" name="pic"/><br/>';
        echo '<input type="submit" value="提交"/></form>';
    }
    private function doAdd() {
        $db = $this->db;
        $title = trim($_POST['title'] ?? '');
        $content = $_POST['content'] ?? '';
        $price = (float)($_POST['price'] ?? 0);
        $stock = (int)($_POST['stock'] ?? 0);
        if ($title === '') { echo '<div class="error">商品名不能为空</div>'; $this->showForm(); return; }
        if (!$this->userSession->checkSubmitLimit()) { echo '<div class="error">操作过于频繁</div>'; return; }
        $picurl = '';
        if (!empty($_FILES['pic']['name'])) {
            $ext = strtolower(pathinfo($_FILES['pic']['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, explode('|', KL_NotDownAndUpload))) {
                $dir = KL_UPLOAD_DIR . '/shop/' . date('Ym');
                if (!is_dir($dir)) @mkdir($dir, 0755, true);
                $fname = date('YmdHis') . '_' . mt_rand(1000,9999) . '.' . $ext;
                if (move_uploaded_file($_FILES['pic']['tmp_name'], $dir . '/' . $fname)) {
                    $picurl = '/uploadfiles/shop/' . date('Ym') . '/' . $fname;
                }
            }
        }
        $uid = $this->userSession->getUserId();
        $id = $db->insert("INSERT INTO WapShop (siteid,classid,userid,title,content,picurl,price,stock,isshow,hits,addtime) VALUES (?,?,?,?,?,?,?,?,1,0,NOW())",
            [$this->siteid, $this->classid, $uid, $title, $content, $picurl, $price, $stock]);
        echo '<div class="success">添加成功 <a href="view.php?siteid=' . $this->siteid . '&classid=' . $this->classid . '&id=' . $id . '">查看</a></div>';
    }
}
$page = new ShopAddPage();
$page->run();
