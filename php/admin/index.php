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
// 订单状态标签样式
$orderTagMap = [0 => 'warning', 1 => '', 2 => '', 3 => 'success', -1 => 'muted', -2 => 'danger'];
// 最近订单会员名映射
$orderUsers = [];
foreach ($recentOrders as $o) {
    $ouid = (int)($o['userid'] ?? 0);
    if ($ouid > 0 && !isset($orderUsers[$ouid])) {
        $uname = $db->getScalar("SELECT username FROM WapUser WHERE id=?", [$ouid]);
        $orderUsers[$ouid] = $uname !== false && $uname !== null ? $uname : ('#' . $ouid);
    }
}

$adminTitle = '仪表盘';
$activeNav = 'index';
require __DIR__ . '/inc/header.php';
?>
<section class="admin-section">
  <div class="ui-stat-grid">
    <div class="ui-stat"><div class="num"><?php echo (int)$userCount; ?></div><div class="label">会员数</div></div>
    <div class="ui-stat"><div class="num"><?php echo (int)$articleCount; ?></div><div class="label">文章数</div></div>
    <div class="ui-stat"><div class="num"><?php echo (int)$bbsCount; ?></div><div class="label">帖子数</div></div>
    <div class="ui-stat"><div class="num"><?php echo (int)$todayReg; ?></div><div class="label">今日注册</div></div>
    <div class="ui-stat"><div class="num"><?php echo (int)$todayOrders; ?></div><div class="label">今日订单</div></div>
    <div class="ui-stat is-success"><div class="num">￥<?php echo number_format($todayRevenue, 2); ?></div><div class="label">今日成交额</div></div>
    <div class="ui-stat<?php echo $shopPending > 0 ? ' is-warning' : ''; ?>"><div class="num"><?php echo (int)$shopPending; ?></div><div class="label">待发货订单</div></div>
    <div class="ui-stat<?php echo $lowStock > 0 ? ' is-danger' : ''; ?>"><div class="num"><?php echo (int)$lowStock; ?></div><div class="label">库存预警</div></div>
  </div>
</section>

<section class="admin-section">
  <div class="admin-panels">
    <div class="ui-card">
      <div class="ui-card-head">
        <h2 class="ui-card-title">最近订单</h2>
        <a class="ui-card-extra" href="shoporder.php">查看全部</a>
      </div>
      <div class="ui-table-wrap">
        <table class="ui-table">
          <thead><tr><th>ID</th><th>会员</th><th class="num">金额</th><th>状态</th><th>时间</th></tr></thead>
          <tbody>
<?php if (empty($recentOrders)) { ?>
            <tr><td colspan="5"><div class="ui-empty">暂无订单</div></td></tr>
<?php } else {
    foreach ($recentOrders as $o) {
        $ouid = (int)($o['userid'] ?? 0);
        $ost = (int)($o['status'] ?? 0);
        $otime = !empty($o['addtime']) ? date('m-d H:i', strtotime($o['addtime'])) : '';
        $tag = $orderTagMap[$ost] ?? 'muted';
?>
            <tr>
              <td><a href="shoporder.php?q=<?php echo (int)$o['id']; ?>">#<?php echo (int)$o['id']; ?></a></td>
              <td><?php echo $page->ToHtm($ouid > 0 ? ($orderUsers[$ouid] ?? ('#' . $ouid)) : '游客'); ?></td>
              <td class="num">￥<?php echo number_format((float)($o['totalprice'] ?? 0), 2); ?></td>
              <td><span class="ui-tag<?php echo $tag !== '' ? ' ui-tag-' . $tag : ''; ?>"><?php echo $page->ToHtm($orderStatusMap[$ost] ?? ('状态' . $ost)); ?></span></td>
              <td><?php echo $page->ToHtm($otime); ?></td>
            </tr>
<?php
    }
} ?>
          </tbody>
        </table>
      </div>
    </div>
    <div class="ui-card">
      <div class="ui-card-head">
        <h2 class="ui-card-title">最新会员</h2>
        <a class="ui-card-extra" href="user.php">查看全部</a>
      </div>
      <div class="ui-table-wrap">
        <table class="ui-table">
          <thead><tr><th>ID</th><th>用户名</th><th>注册时间</th></tr></thead>
          <tbody>
<?php if (empty($recentUsers)) { ?>
            <tr><td colspan="3"><div class="ui-empty">暂无会员</div></td></tr>
<?php } else {
    foreach ($recentUsers as $u) {
?>
            <tr>
              <td><?php echo (int)$u['id']; ?></td>
              <td><a href="user.php?q=<?php echo $page->ToHtm($u['username']); ?>"><?php echo $page->ToHtm($u['username']); ?></a></td>
              <td><?php echo !empty($u['regtime']) ? $page->ToHtm(date('Y-m-d H:i', strtotime($u['regtime']))) : ''; ?></td>
            </tr>
<?php
    }
} ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</section>

<section class="admin-section">
  <div class="ui-card">
    <div class="ui-card-head">
      <h2 class="ui-card-title">快捷入口</h2>
      <span class="ui-card-extra">按当前待办数量排序</span>
    </div>
    <div class="admin-quick">
      <a class="ui-btn ui-btn-primary" href="shoporder.php?status=1">待发货订单<?php echo $shopPending > 0 ? '（' . (int)$shopPending . '）' : ''; ?></a>
      <a class="ui-btn" href="audit.php">待审核内容<?php echo $auditPending > 0 ? '（' . (int)$auditPending . '）' : ''; ?></a>
      <a class="ui-btn" href="pointorder.php?status=0">待发放兑换<?php echo $pointPending > 0 ? '（' . (int)$pointPending . '）' : ''; ?></a>
      <a class="ui-btn" href="report.php?status=0">待处理举报<?php echo $pendingReports > 0 ? '（' . (int)$pendingReports . '）' : ''; ?></a>
      <a class="ui-btn" href="goods.php?low=1">库存预警<?php echo $lowStock > 0 ? '（' . (int)$lowStock . '）' : ''; ?></a>
      <a class="ui-btn ui-btn-ghost" href="goods.php?act=add">新增商品</a>
      <a class="ui-btn ui-btn-ghost" href="content.php?module=article">发布内容</a>
      <a class="ui-btn ui-btn-ghost" href="backup.php">数据库维护</a>
    </div>
  </div>
</section>
<?php require __DIR__ . '/inc/footer.php'; ?>
