<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 管理员登录 (对应原 admin/login.aspx)
 */
require_once __DIR__ . '/../includes/bootstrap.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $checkcode = $_POST['checkcode'] ?? '';

    if (KL_CHECKCODE !== '' && $checkcode !== KL_CHECKCODE) {
        $error = '超级验证码错误';
    } else {
        $sess = new UserSession();
        list($ok, $msg) = $sess->adminLogin($username, $password);
        if ($ok) {
            header('Location: index.php');
            exit;
        }
        $error = $msg;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8"/>
<title>小宇网络 - 管理后台登录</title>
<style>
body{font-family:"微软雅黑",Arial;background:#f5f5f5;margin:0;padding:0}
.login-box{width:360px;margin:120px auto;background:#fff;padding:30px;border-radius:8px;box-shadow:0 2px 10px rgba(0,0,0,.1)}
.login-box h2{text-align:center;color:#333;margin:0 0 20px}
.login-box .form-group{margin-bottom:15px}
.login-box label{display:block;margin-bottom:5px;color:#666}
.login-box input{width:100%;padding:8px;border:1px solid #ddd;border-radius:4px;box-sizing:border-box}
.login-box input[type=submit]{background:#1890ff;color:#fff;border:none;cursor:pointer;padding:10px}
.login-box input[type=submit]:hover{background:#40a9ff}
.error{color:#f5222d;text-align:center;margin-bottom:10px}
.copyright{text-align:center;color:#999;font-size:12px;margin-top:20px}
</style>
</head>
<body>
<div class="login-box">
<h2>小宇网络管理后台</h2>
<?php if ($error): ?><div class="error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
<form method="post" action="">
<div class="form-group"><label>账号</label><input type="text" name="username" required/></div>
<div class="form-group"><label>密码</label><input type="password" name="password" required/></div>
<?php if (KL_CHECKCODE !== ''): ?>
<div class="form-group"><label>超级验证码</label><input type="text" name="checkcode"/></div>
<?php endif; ?>
<div class="form-group"><input type="submit" value="登录"/></div>
</form>
<div class="copyright">Copyright (C) 小宇网络 All Rights Reserved.</div>
</div>
</body>
</html>
