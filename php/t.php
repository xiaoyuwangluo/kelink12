<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 短链跳转 (对应原 T.aspx)
 * ?code= 按 WapLink.title 匹配 → 301 跳转到目标地址，否则 404 提示
 */
require_once __DIR__ . '/includes/bootstrap.php';

$code = isset($_GET['code']) ? trim((string)$_GET['code']) : '';

if ($code !== '' && !WapTool::checkSqlInject($code)) {
    try {
        $row = Database::getInstance()->getOne(
            "SELECT title, content, picurl, downurl FROM WapLink WHERE title=? AND isshow=1",
            [$code]
        );
        if ($row) {
            // 目标地址优先取 downurl，其次 picurl，再次 content
            $target = trim((string)($row['downurl'] ?? ''));
            if ($target === '') $target = trim((string)($row['picurl'] ?? ''));
            if ($target === '') $target = trim((string)($row['content'] ?? ''));
            if ($target !== '') {
                header('Location: ' . $target, true, 301);
                exit;
            }
        }
    } catch (Exception $e) {
        // 表结构异常或查询失败时按未找到处理
    }
}

// 未找到短链：返回 404 提示页
http_response_code(404);
?>
<!DOCTYPE html>
<html><head><meta charset="utf-8"><title>短链不存在 - 小宇网络</title></head>
<body>
<div class="error">短链接不存在或已失效。</div>
<div><a href="wapindex.php">返回站点首页</a></div>
</body></html>
