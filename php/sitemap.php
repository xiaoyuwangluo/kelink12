<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * XML 站点地图 (SEO)
 */
require_once __DIR__ . '/includes/bootstrap.php';

header('Content-Type: application/xml; charset=utf-8');

$db = Database::getInstance();
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$base = $scheme . $host . '/';

$urls = [];
$urls[] = $base . 'wapindex.php';
// 栏目列表页
$classes = $db->getAll("SELECT id,module FROM WapClass WHERE isshow=1", []);
foreach ($classes as $c) {
    $urls[] = $base . $c['module'] . '/list.php?classid=' . $c['id'];
}
// 内容页 (21个内容模块)
foreach (WapTool::$MODULE_TABLE as $module => $table) {
    $rows = $db->getAll("SELECT id FROM $table WHERE isshow=1 ORDER BY id DESC LIMIT 500", []);
    foreach ($rows as $r) {
        $urls[] = $base . $module . '/view.php?id=' . $r['id'];
    }
}
// 自定义页面
$tpls = $db->getAll("SELECT id FROM WapPageTpl", []);
foreach ($tpls as $t) {
    $urls[] = $base . 'page.php?id=' . $t['id'];
}

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
foreach ($urls as $u) {
    echo '<url><loc>' . htmlspecialchars($u, ENT_XML1) . '</loc></url>' . "\n";
}
echo '</urlset>';
