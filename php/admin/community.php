<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 管理后台 - 社区运营报表 (7天/30天/全部)
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class AdminCommunityPage extends WapPage {
    public function run() {
        $this->checkAdminLogin();
        $db = $this->db;

        $days = (string)($_GET['days'] ?? '7');
        if (!in_array($days, ['7', '30', 'all'], true)) $days = '7';
        $start = $days === 'all' ? null : date('Y-m-d 00:00:00', strtotime('-' . ((int)$days - 1) . ' days'));

        $overview = [
            'newuser'  => $this->cnt($db, "SELECT COUNT(*) FROM WapUser WHERE 1=1", "regtime", $start, []),
            'bbs'      => $this->cnt($db, "SELECT COUNT(*) FROM WapBbs", "addtime", $start, []),
            'reply'    => $this->cnt($db, "SELECT COUNT(*) FROM WapBbsReply", "addtime", $start, []),
            'article'  => $this->cnt($db, "SELECT COUNT(*) FROM WapArticle", "addtime", $start, []),
            'follow'   => $this->cnt($db, "SELECT COUNT(*) FROM WapFollow", "addtime", $start, []),
            'like'     => $this->cnt($db, "SELECT COUNT(*) FROM WapLike", "addtime", $start, []),
            'message'  => $this->cnt($db, "SELECT COUNT(*) FROM WapMessage", "addtime", $start, []),
        ];

        $auditPending = 0;
        $blockCount = 0;
        try { $auditPending = (int)$db->getScalar("SELECT COUNT(*) FROM WapAudit WHERE status=0",[]); } catch (Exception $e) {}
        try { $blockCount = (int)$db->getScalar("SELECT COUNT(*) FROM WapBlock",[]); } catch (Exception $e) {}

        // 趋势：最近 7/30 天（全部时取 30 天）
        $trendDays = $days === '30' ? 30 : 7;
        if ($days === 'all') $trendDays = 30;
        $series = ['bbs' => [], 'reply' => [], 'user' => []];
        $since = date('Y-m-d 00:00:00', strtotime('-' . ($trendDays - 1) . ' days'));
        $this->fillSeries($series['bbs'], $db->getAll("SELECT DATE(addtime) AS d, COUNT(*) AS c FROM WapBbs WHERE addtime>=? GROUP BY DATE(addtime)",[$since]), 'd', 'c');
        $this->fillSeries($series['reply'], $db->getAll("SELECT DATE(addtime) AS d, COUNT(*) AS c FROM WapBbsReply WHERE addtime>=? GROUP BY DATE(addtime)",[$since]), 'd', 'c');
        $this->fillSeries($series['user'], $db->getAll(
            "SELECT DATE(regtime) AS d, COUNT(*) AS c FROM WapUser WHERE regtime>=? GROUP BY DATE(regtime)",
            [$since]), 'd', 'c');

        // 发帖 Top10
        $postSql = "SELECT userid, COUNT(*) AS c FROM WapBbs WHERE 1=1";
        $postParams = [];
        if ($start) { $postSql .= " AND addtime>=?"; $postParams[] = $start; }
        $postSql .= " GROUP BY userid ORDER BY c DESC LIMIT 10";
        $topPosters = $db->getAll($postSql, $postParams);
        $names = [];
        if ($topPosters) {
            $ids = [];
            foreach ($topPosters as $tp) { if ((int)$tp['userid'] > 0) $ids[] = (int)$tp['userid']; }
            if ($ids) {
                $in = implode(',', $ids);
                foreach ($db->getAll("SELECT id,username,nickname FROM WapUser WHERE id IN ($in)") as $u) {
                    $names[(int)$u['id']] = $u['nickname'] !== '' ? $u['nickname'] : $u['username'];
                }
            }
        }

        // 热门标签 Top10
        $topTags = [];
        try {
            $topTags = $db->getAll("SELECT t.tagname, COUNT(*) AS c FROM WapBbsTag t
 JOIN WapBbs b ON b.id=t.bbsid
 WHERE b.isshow=1 GROUP BY t.tagname ORDER BY c DESC LIMIT 10",[]);
        } catch (Exception $e) {}

        $this->strTitle = '社区报表';
        $adminTitle = '社区报表';
        $activeNav = 'community';
        require __DIR__ . '/inc/header.php';
        echo '<div class="subtitle">社区运营报表</div>';
        echo '<div><a href="index.php">返回后台首页</a></div>';
        $base = 'community.php';
        echo '<div class="content">';
        echo '<a href="' . $base . '&days=7">近7天</a> | ';
        echo '<a href="' . $base . '&days=30">近30天</a> | ';
        echo '<a href="' . $base . '&days=all">全部</a>';
        echo '</div>';

        echo '<div class="content"><table border="1" cellpadding="6" cellspacing="0">';
        echo '<tr><th>新增会员</th><th>新帖子</th><th>新回复</th><th>新文章</th><th>新增关注</th><th>新增点赞</th><th>私信</th><th>待审核</th><th>拉黑关系</th></tr>';
        echo '<tr>';
        foreach (['newuser', 'bbs', 'reply', 'article', 'follow', 'like', 'message'] as $k) {
            echo '<td>' . (int)$overview[$k] . '</td>';
        }
        echo '<td>' . $auditPending . '</td><td>' . $blockCount . '</td>';
        echo '</tr></table></div>';

        echo '<div class="subtitle">每日趋势（近 ' . $trendDays . ' 天）</div>';
        echo '<div class="content"><table border="1" cellpadding="4" cellspacing="0">';
        echo '<tr><th>日期</th><th>帖子</th><th>回复</th><th>新会员</th></tr>';
        for ($i = $trendDays - 1; $i >= 0; $i--) {
            $d = date('Y-m-d', strtotime('-' . $i . ' days'));
            echo '<tr><td>' . $d . '</td><td>' . (int)($series['bbs'][$d] ?? 0)
                . '</td><td>' . (int)($series['reply'][$d] ?? 0)
                . '</td><td>' . (int)($series['user'][$d] ?? 0) . '</td></tr>';
        }
        echo '</table></div>';

        echo '<div class="subtitle">发帖 Top10</div>';
        echo '<div class="content"><table border="1" cellpadding="4" cellspacing="0">';
        echo '<tr><th>用户</th><th>发帖数</th></tr>';
        if ($topPosters) {
            foreach ($topPosters as $tp) {
                $uid = (int)$tp['userid'];
                $name = $uid > 0 ? ($names[$uid] ?? ('用户' . $uid)) : '游客/已删除';
                echo '<tr><td>' . $this->ToHtm($name) . '</td><td>' . (int)$tp['c'] . '</td></tr>';
            }
        } else {
            echo '<tr><td colspan="2">暂无数据</td></tr>';
        }
        echo '</table></div>';

        echo '<div class="subtitle">热门标签 Top10</div>';
        echo '<div class="content"><table border="1" cellpadding="4" cellspacing="0">';
        echo '<tr><th>标签</th><th>帖子数</th></tr>';
        if ($topTags) {
            foreach ($topTags as $t) {
                echo '<tr><td>#' . $this->ToHtm($t['tagname']) . '#</td><td>' . (int)$t['c'] . '</td></tr>';
            }
        } else {
            echo '<tr><td colspan="2">暂无数据</td></tr>';
        }
        echo '</table></div>';

        require __DIR__ . '/inc/footer.php';
    }

    /** 统计：指定时间起点时追加时间条件（自动补 WHERE） */
    private function cnt($db, $sql, $timeField, $start, $params) {
        if ($start) {
            $sql .= (stripos($sql, ' WHERE ') === false ? " WHERE {$timeField}>=?" : " AND {$timeField}>=?");
            $params[] = $start;
        }
        return (int)$db->getScalar($sql, $params);
    }

    /** 把 GROUP BY DATE() 结果填入 map */
    private function fillSeries(&$map, $rows, $dk, $ck) {
        foreach ($rows as $r) {
            $d = substr((string)$r[$dk], 0, 10);
            if ($d !== '') $map[$d] = (int)$r[$ck];
        }
    }
}

$page = new AdminCommunityPage();
$page->run();
