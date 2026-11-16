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
$lowStock = 0;
try {
    $lowStock = (int)$db->getScalar("SELECT COUNT(*) FROM WapShop WHERE stock<5");
} catch (Exception $e) {
    $lowStock = 0;
}
// 今日数据（按站点本地时区零点起算）
$todayStart = date('Y-m-d 00:00:00');
$todayReg = 0;
$todayOrders = 0;
$todayRevenue = 0;
$pendingReports = 0;
$recentOrders = [];
$recentUsers = [];
try {
    $todayReg = (int)$db->getScalar("SELECT COUNT(*) FROM WapUser WHERE regtime>=?", [$todayStart]);
    $todayOrders = (int)$db->getScalar("SELECT COUNT(*) FROM WapShopOrder WHERE addtime>=?", [$todayStart]);
    $todayRevenue = (float)$db->getScalar("SELECT COALESCE(SUM(totalprice),0) FROM WapShopOrder WHERE status IN(1,2,3) AND addtime>=?", [$todayStart]);
    $recentOrders = $db->getAll("SELECT id,userid,totalprice,status,addtime FROM WapShopOrder ORDER BY id DESC LIMIT 5");
    $recentUsers = $db->getAll("SELECT id,username,regtime FROM WapUser ORDER BY id DESC LIMIT 5");
} catch (Exception $e) {
    $recentOrders = [];
    $recentUsers = [];
}
try {
    $pendingReports = (int)$db->getScalar("SELECT COUNT(*) FROM WapReport WHERE status=0");
} catch (Exception $e) {
    $pendingReports = 0;
}
// 订单状态文案
$orderStatusMap = [0 => '待付款', 1 => '待发货', 2 => '已发货', 3 => '已完成', -1 => '已取消', -2 => '已退款'];
// 最近订单会员名映射
$orderUsers = [];
foreach ($recentOrders as $o) {
    $ouid = (int)($o['userid'] ?? 0);
    if ($ouid > 0 && !isset($orderUsers[$ouid])) {
        $uname = $db->getScalar("SELECT username FROM WapUser WHERE id=?", [$ouid]);
        $orderUsers[$ouid] = $uname !== false && $uname !== null ? $uname : ('#' . $ouid);
    }
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
.panels{display:flex;gap:20px;flex-wrap:wrap;margin-top:20px}
.panel{background:#fff;padding:15px;border-radius:6px;flex:1;min-width:320px;box-shadow:0 1px 4px rgba(0,0,0,.08)}
.panel h2{font-size:15px;margin:0 0 10px;color:#333}
.panel table{width:100%;border-collapse:collapse;font-size:13px}
.panel th,.panel td{padding:6px;border-bottom:1px solid #f0f0f0;text-align:left}
.panel th{color:#999;font-weight:normal}
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
<div class="stat-card"><div class="num"><?php echo $todayReg; ?></div><div class="label">今日注册</div></div>
<div class="stat-card"><div class="num"><?php echo $todayOrders; ?></div><div class="label">今日订单</div></div>
<div class="stat-card"><div class="num">￥<?php echo number_format($todayRevenue, 2); ?></div><div class="label">今日成交额</div></div>
<div class="stat-card"><div class="num"><?php echo $shopPending; ?></div><div class="label">待发货订单</div></div>
<div class="stat-card"><div class="num"><?php echo $lowStock; ?></div><div class="label">库存预警</div></div>
</div>
<div class="panels">
<div class="panel">
<h2>最近订单</h2>
<table>
<tr><th>ID</th><th>会员</th><th>金额</th><th>状态</th><th>时间</th></tr>
<?php if (empty($recentOrders)) { ?>
<tr><td colspan="5">暂无订单</td></tr>
<?php } else { foreach ($recentOrders as $o) {
    $ouid = (int)($o['userid'] ?? 0);
    $ost = (int)($o['status'] ?? 0);
    $otime = !empty($o['addtime']) ? date('m-d H:i', strtotime($o['addtime'])) : '';
?>
<tr>
<td><a href="shoporder.php?q=<?php echo (int)$o['id']; ?>">#<?php echo (int)$o['id']; ?></a></td>
<td><?php echo $page->ToHtm($ouid > 0 ? ($orderUsers[$ouid] ?? ('#' . $ouid)) : '游客'); ?></td>
<td>￥<?php echo number_format((float)($o['totalprice'] ?? 0), 2); ?></td>
<td><?php echo $page->ToHtm($orderStatusMap[$ost] ?? ('状态' . $ost)); ?></td>
<td><?php echo $otime; ?></td>
</tr>
<?php } } ?>
</table>
</div>
<div class="panel">
<h2>最新会员</h2>
<table>
<tr><th>ID</th><th>用户名</th><th>注册时间</th></tr>
<?php if (empty($recentUsers)) { ?>
<tr><td colspan="3">暂无会员</td></tr>
<?php } else { foreach ($recentUsers as $u) { ?>
<tr>
<td><?php echo (int)$u['id']; ?></td>
<td><a href="user.php?q=<?php echo $page->ToHtm($u['username']); ?>"><?php echo $page->ToHtm($u['username']); ?></a></td>
<td><?php echo !empty($u['regtime']) ? date('Y-m-d H:i', strtotime($u['regtime'])) : ''; ?></td>
</tr>
<?php } } ?>
</table>
</div>
</div>
<div class="menu">
<a href="../wapindex.php">站点首页</a>
<a href="class.php">栏目管理</a>
<a href="user.php">会员管理</a>
<a href="content.php?module=article">内容管理</a>
<a href="shoporder.php">商城订单（待发货 <?php echo $shopPending; ?>）</a>
<a href="goods.php">商品库存（预警 <?php echo $lowStock; ?>）</a>
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
<a href="backup.php">数据库维护</a>
</div>
</div>
</body>
</html>
