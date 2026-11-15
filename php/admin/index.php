<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 管理后台首页 (对应原 admin/index.aspx)
 */
require_once __DIR__ . '/../includes/bootstrap.php';

$page = new WapPage();
$page->checkAdminLogin();

$db = Database::getInstance();
$siteCount = $db->getScalar("SELECT COUNT(*) FROM WapSite");
$userCount = $db->getScalar("SELECT COUNT(*) FROM WapUser");
$articleCount = $db->getScalar("SELECT COUNT(*) FROM WapArticle");
$bbsCount = $db->getScalar("SELECT COUNT(*) FROM WapBbs");
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8"/>
<title>小宇网络 - 管理后台</title>
<style>
body{font-family:"微软雅黑",Arial;margin:0;background:#f0f2f5}
.header{background:#001529;color:#fff;padding:15px 30px}
.header h1{margin:0;font-size:18px}
.header .nav{float:right}
.header .nav a{color:#fff;margin-left:15px;text-decoration:none}
.container{padding:20px}
.stats{display:flex;gap:20px;flex-wrap:wrap}
.stat-card{background:#fff;padding:20px;border-radius:6px;flex:1;min-width:150px;box-shadow:0 1px 4px rgba(0,0,0,.08)}
.stat-card .num{font-size:28px;color:#1890ff;font-weight:bold}
.stat-card .label{color:#999;margin-top:5px}
.menu{background:#fff;margin-top:20px;padding:15px;border-radius:6px}
.menu a{display:inline-block;margin:5px 10px;padding:6px 14px;background:#1890ff;color:#fff;text-decoration:none;border-radius:4px}
</style>
</head>
<body>
<div class="header">
<h1>小宇网络 WAP建站系统 管理后台</h1>
<div class="nav"><a href="login.php?action=logout">退出</a></div>
</div>
<div class="container">
<div class="stats">
<div class="stat-card"><div class="num"><?php echo $siteCount; ?></div><div class="label">站点数</div></div>
<div class="stat-card"><div class="num"><?php echo $userCount; ?></div><div class="label">会员数</div></div>
<div class="stat-card"><div class="num"><?php echo $articleCount; ?></div><div class="label">文章数</div></div>
<div class="stat-card"><div class="num"><?php echo $bbsCount; ?></div><div class="label">帖子数</div></div>
</div>
<div class="menu">
<a href="../wapindex.php">站点首页</a>
<a href="sitelist.php">站点管理</a>
<a href="userlist.php">会员管理</a>
<a href="classlist.php">栏目管理</a>
<a href="sysconfig.php">系统设置</a>
</div>
</div>
</body>
</html>
