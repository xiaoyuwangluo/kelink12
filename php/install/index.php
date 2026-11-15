<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 数据库安装向导
 */
require_once __DIR__ . '/../includes/bootstrap.php';

$step = $_GET['step'] ?? '1';
$msg = '';

/** MySQL 方言转 SQLite */
function mysqlToSqlite($sql) {
    $sql = preg_replace('/\bINT\s+NOT\s+NULL\s+AUTO_INCREMENT\s+PRIMARY\s+KEY\b/i', 'INTEGER PRIMARY KEY AUTOINCREMENT', $sql);
    $sql = preg_replace('/`id`\s+int\(\d+\)\s+NOT\s+NULL\s+AUTO_INCREMENT/i', '`id` INTEGER PRIMARY KEY AUTOINCREMENT', $sql);
    $sql = preg_replace('/AUTO_INCREMENT/i', '', $sql);
    $sql = preg_replace('/,\s*PRIMARY KEY\s*\(\s*`?id`?\s*\)/i', '', $sql);
    $sql = preg_replace('/,\s*(?:UNIQUE\s+)?KEY\s+`?\w+`?\s*\([^)]*\)/i', '', $sql);
    $sql = preg_replace('/\b(tiny|small|medium|big)?int\(\d+\)/i', 'INTEGER', $sql);
    $sql = preg_replace("/\s*COMMENT\s+'[^']*'/i", '', $sql);
    $sql = preg_replace('/\bENGINE\s*=\s*\w+[^;]*/i', '', $sql);
    $sql = preg_replace('/\bON DUPLICATE KEY UPDATE[^;]*/i', '', $sql);
    $sql = preg_replace('/DEFAULT\s+CURRENT_TIMESTAMP\s+ON\s+UPDATE\s+CURRENT_TIMESTAMP/i', 'DEFAULT CURRENT_TIMESTAMP', $sql);
    return trim($sql);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $step == '2') {
    $dbtype = $_POST['dbtype'] ?? 'mysql';
    $host = $_POST['host'] ?? 'localhost';
    $dbname = $_POST['dbname'] ?? '';
    $user = $_POST['user'] ?? 'root';
    $pass = $_POST['pass'] ?? '';

    // 写入配置
    $configFile = KL_ROOT . '/config.php';
    $config = file_get_contents($configFile);
    $config = preg_replace("/define\('KL_DB_TYPE',\s*'[^']*'\);/", "define('KL_DB_TYPE', '$dbtype');", $config);
    $config = preg_replace("/define\('KL_SQL_SERVERIP',\s*'[^']*'\);/", "define('KL_SQL_SERVERIP', '$host');", $config);
    $config = preg_replace("/define\('KL_SQL_UserName',\s*'[^']*'\);/", "define('KL_SQL_UserName', '$user');", $config);
    $config = preg_replace("/define\('KL_SQL_PassWord',\s*'[^']*'\);/", "define('KL_SQL_PassWord', '$pass');", $config);
    $config = preg_replace("/define\('KL_DatabaseName',\s*'[^']*'\);/", "define('KL_DatabaseName', '$dbname');", $config);
    file_put_contents($configFile, $config);

    // 执行建表
    try {
        if ($dbtype === 'sqlite') {
            $dbFile = KL_ROOT . '/data/' . preg_replace('/[^a-zA-Z0-9_]/', '', $dbname) . '.db';
            if (!is_dir(dirname($dbFile))) @mkdir(dirname($dbFile), 0755, true);
            $pdo = new PDO('sqlite:' . $dbFile);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } else {
            $dsn = ($dbtype === 'mysql')
                ? "mysql:host=$host;dbname=$dbname;charset=utf8mb4"
                : "sqlsrv:Server=$host;Database=$dbname";
            $pdo = new PDO($dsn, $user, $pass);
        }
        $sql = file_get_contents(KL_ROOT . '/install/schema.sql');
        // 按分号拆分执行 (简化处理)
        $statements = array_filter(array_map('trim', explode(";\n", $sql)));
        foreach ($statements as $stmt) {
            if ($stmt === '') continue;
            if ($dbtype === 'sqlite') {
                if (stripos($stmt, 'ADD INDEX') !== false) continue; // SQLite 不支持
                $stmt = mysqlToSqlite($stmt);
            }
            $pdo->exec($stmt);
        }
        $msg = '<div class="success">数据库安装成功！默认管理员账号: admin / admin123</div>';
        $step = '3';
    } catch (Exception $e) {
        $msg = '<div class="error">安装失败: ' . $e->getMessage() . '</div>';
        $step = '2';
    }
}
?>
<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title>小宇网络 - 安装向导</title>
<style>
body{font-family:"微软雅黑";max-width:600px;margin:50px auto;padding:20px;background:#fff;border-radius:8px;box-shadow:0 2px 10px rgba(0,0,0,.1)}
h1{color:#1890ff;text-align:center}
.form-group{margin-bottom:15px}
label{display:block;margin-bottom:5px;color:#666}
input,select{width:100%;padding:8px;border:1px solid #ddd;border-radius:4px;box-sizing:border-box}
button{background:#1890ff;color:#fff;border:none;padding:10px 30px;border-radius:4px;cursor:pointer;width:100%}
.success{color:#52c41a;padding:10px;background:#f6ffed;border-radius:4px}
.error{color:#f5222d;padding:10px;background:#fff1f0;border-radius:4px}
</style></head>
<body>
<h1>小宇网络 WAP建站系统 安装</h1>
<?php echo $msg; ?>
<?php if ($step == '1'): ?>
<p>欢迎使用小宇网络WAP建站系统PHP版。本向导将引导您完成数据库配置。</p>
<p><a href="?step=2">开始安装</a></p>
<?php elseif ($step == '2'): ?>
<form method="post" action="?step=2">
<div class="form-group"><label>数据库类型</label>
<select name="dbtype"><option value="mysql">MySQL</option><option value="mssql">SQL Server</option><option value="sqlite">SQLite (免装数据库)</option></select></div>
<div class="form-group"><label>数据库主机</label><input type="text" name="host" value="localhost"/></div>
<div class="form-group"><label>数据库名</label><input type="text" name="dbname" required/></div>
<div class="form-group"><label>用户名</label><input type="text" name="user" value="root"/></div>
<div class="form-group"><label>密码</label><input type="password" name="pass"/></div>
<button type="submit">安装</button>
</form>
<?php else: ?>
<p>安装完成！请删除 install 目录以确保安全。</p>
<p><a href="../index.php">进入首页</a> | <a href="../admin/login.php">管理后台</a></p>
<?php endif; ?>
</body></html>
