<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 管理后台首页 (对应原 admin/index.aspx)
 */
require_once __DIR__ . '/../includes/bootstrap.php';

$page = new WapPage();
$page->checkAdminLogin();

$db = Database::getInstance();
$userCount = $db->getScalar("SELECT COUNT(*) FROM WapUser");
$articleCount = $db->getScalar("SELECT COUNT(*) FROM WapArticle");
$bbsCount = $db->getScalar("SELECT COUNT(*) FROM WapBbs");
$pointPending = 0;
try {
    $pointPending = (int)$db->getScalar("SELECT COUNT(*) FROM WapPointOrder WHERE status=0");
} catch (Exception $e) {
    $pointPending = 0;
}
$auditPending = 0;
try {
    $auditPending = (int)$db->getScalar("SELECT COUNT(*) FROM WapAudit WHERE status=0");
} catch (Exception $e) {
    $auditPending = 0;
}
$shopPending = 0;
try {
    $shopPending = (int)$db->getScalar("SELECT COUNT(*) FROM WapShopOrder WHERE status=1");
} catch (Exception $e) {
    $shopPending = 0;
}
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
<div class="nav"><a href="logout.php">退出</a></div>
</div>
<div class="container">
<div class="stats">
<div class="stat-card"><div class="num"><?php echo $userCount; ?></div><div class="label">会员数</div></div>
<div class="stat-card"><div class="num"><?php echo $articleCount; ?></div><div class="label">文章数</div></div>
<div class="stat-card"><div class="num"><?php echo $bbsCount; ?></div><div class="label">帖子数</div></div>
<div class="stat-card"><div class="num"><?php echo $shopPending; ?></div><div class="label">待发货订单</div></div>
</div>
<div class="menu">
<a href="../wapindex.php">站点首页</a>
<a href="class.php">栏目管理</a>
<a href="user.php">会员管理</a>
<a href="content.php?module=article">内容管理</a>
<a href="shoporder.php">商城订单（待发货 <?php echo $shopPending; ?>）</a>
<a href="reply.php">评论审核</a>
<a href="audit.php">内容审核（待审 <?php echo $auditPending; ?>）</a>
<a href="report.php">举报管理</a>
<a href="community.php">社区报表</a>
<a href="tag.php">标签管理</a>
<a href="online.php">在线用户</a>
<a href="sysmsg.php">系统公告</a>
<a href="card.php">充值卡</a>
<a href="pointgoods.php">积分商品</a>
<a href="pointorder.php">兑换订单（待发放 <?php echo $pointPending; ?>）</a>
<a href="filter.php">敏感词库</a>
<a href="upload.php">文件管理</a>
<a href="setting.php">系统设置</a>
</div>
</div>
</body>
</html>
