<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 管理后台 - 统一外壳头部（tokens + components + admin.css）
 * 用法：设置 $adminTitle / $activeNav 后 require 本文件
 */
$adminTitle = $adminTitle ?? '管理后台';
$activeNav = $activeNav ?? '';
$navBadge = function ($n) {
    $n = (int)$n;
    return $n > 0 ? '<span class="badge">' . $n . '</span>' : '';
};
$navActive = function ($key) use ($activeNav) {
    return $activeNav === $key ? ' class="is-active"' : '';
};
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<title>管理后台 - <?php echo htmlspecialchars($adminTitle, ENT_QUOTES, 'UTF-8'); ?></title>
<link rel="stylesheet" href="../assets/css/tokens.css"/>
<link rel="stylesheet" href="../assets/css/components.css"/>
<link rel="stylesheet" href="../assets/css/admin.css"/>
</head>
<body class="admin-body">
<div class="admin-shell">
<aside class="admin-sidebar">
<div class="admin-brand"><a href="index.php">小宇建站 · 管理后台</a></div>
<nav class="admin-nav">
<div class="admin-nav-group">
<span class="admin-nav-title">概览</span>
<a href="index.php"<?php echo $navActive('index'); ?>>仪表盘</a>
</div>
<div class="admin-nav-group">
<span class="admin-nav-title">内容</span>
<a href="class.php"<?php echo $navActive('class'); ?>>栏目管理</a>
<a href="content.php?module=article"<?php echo $navActive('article'); ?>>文章内容</a>
<a href="content.php?module=bbs"<?php echo $navActive('bbs'); ?>>论坛内容</a>
<a href="reply.php"<?php echo $navActive('reply'); ?>>评论审核</a>
<a href="audit.php"<?php echo $navActive('audit'); ?>>内容审核<?php echo $navBadge($auditPending ?? 0); ?></a>
<a href="tag.php"<?php echo $navActive('tag'); ?>>标签管理</a>
<a href="filter.php"<?php echo $navActive('filter'); ?>>敏感词库</a>
</div>
<div class="admin-nav-group">
<span class="admin-nav-title">会员</span>
<a href="user.php"<?php echo $navActive('user'); ?>>会员管理</a>
<a href="online.php"<?php echo $navActive('online'); ?>>在线用户</a>
<a href="community.php"<?php echo $navActive('community'); ?>>社区报表</a>
</div>
<div class="admin-nav-group">
<span class="admin-nav-title">商城</span>
<a href="goods.php"<?php echo $navActive('goods'); ?>>商品库存</a>
<a href="shoporder.php"<?php echo $navActive('shoporder'); ?>>商城订单<?php echo $navBadge($shopPending ?? 0); ?></a>
<a href="pointgoods.php"<?php echo $navActive('pointgoods'); ?>>积分商品</a>
<a href="pointorder.php"<?php echo $navActive('pointorder'); ?>>兑换订单<?php echo $navBadge($pointPending ?? 0); ?></a>
</div>
<div class="admin-nav-group">
<span class="admin-nav-title">运营</span>
<a href="card.php"<?php echo $navActive('card'); ?>>充值卡</a>
<a href="sysmsg.php"<?php echo $navActive('sysmsg'); ?>>系统公告</a>
<a href="report.php"<?php echo $navActive('report'); ?>>举报管理</a>
<a href="caiji.php"<?php echo $navActive('caiji'); ?>>采集管理</a>
<a href="upload.php"<?php echo $navActive('upload'); ?>>文件管理</a>
</div>
<div class="admin-nav-group">
<span class="admin-nav-title">系统</span>
<a href="setting.php"<?php echo $navActive('setting'); ?>>系统设置</a>
<a href="export.php?type=order"<?php echo $navActive('export'); ?>>数据导出</a>
<a href="backup.php"<?php echo $navActive('backup'); ?>>数据库维护</a>
</div>
</nav>
</aside>
<div class="admin-main">
<header class="admin-topbar">
<div class="admin-topbar-title"><?php echo htmlspecialchars($adminTitle, ENT_QUOTES, 'UTF-8'); ?></div>
<div class="admin-topbar-actions">
<a href="../wapindex.php" target="_blank" rel="noopener">站点首页</a>
<a href="logout.php">退出</a>
</div>
</header>
<main class="admin-content">
