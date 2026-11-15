<?php
/** 小宇网络 - 注册站长 (对应原 WebReg.aspx) */
require_once __DIR__ . '/includes/bootstrap.php';
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sitename = trim($_POST['sitename'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    if ($sitename && $username && $password) {
        $db = Database::getInstance();
        $db->insert("INSERT INTO WapSite (sitename,copyright) VALUES (?,?)", [$sitename, KL_COPYRIGHT]);
        $siteid = $db->getPdo()->lastInsertId();
        $msg = '<div class="success">注册成功！您的站点ID: ' . $siteid . '</div>';
    } else {
        $msg = '<div class="error">请填写完整信息</div>';
    }
}
?>
<!DOCTYPE HTML>
<html><head><meta charset="utf-8"><title>注册站长 - 小宇网络</title>
<link rel="stylesheet" href="/php/assets/css/index.css"/>
<style>.reg-box{max-width:400px;margin:40px auto;padding:30px;background:#fff;border-radius:8px;box-shadow:0 2px 10px rgba(0,0,0,.1)}
.reg-box input{width:100%;padding:8px;margin:8px 0;border:1px solid #ddd;border-radius:4px;box-sizing:border-box}
.reg-box button{width:100%;padding:10px;background:#1890ff;color:#fff;border:none;border-radius:4px;cursor:pointer}</style></head>
<body>
<header><div class="header"><div class="top"><div class="top_con"><span class="Belongs">小宇网络</span></div></div>
<div class="head_bg"><div class="head"><div class="logo"><h1><a href="index.php">小宇网络WAP建站</a></h1></div></div></div></div></header>
<div class="reg-box">
<h2>注册站长</h2>
<?php echo $msg; ?>
<form method="post" action="">
<input type="text" name="sitename" placeholder="站点名称" required/>
<input type="text" name="username" placeholder="管理员账号" required/>
<input type="password" name="password" placeholder="管理员密码" required/>
<button type="submit">立即注册</button>
</form>
</div>
<footer><div class="bottom"><div class="bottom_con">Copyright (C) 小宇网络 All Rights Reserved.</div></div></footer>
</body></html>
