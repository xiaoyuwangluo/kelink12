<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 论坛模块 - 活跃榜
 * ?days=7 统计近 N 天，0=全部；活跃分=发帖*5+回复*2+被赞*1
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class BbsActivePage extends WapPage {
    public function run() {
        $db = $this->db;
        $days = isset($_GET['days']) ? (int)$_GET['days'] : 7;
        if ($days < 0) $days = 0;
        $cutoff = $days > 0 ? date('Y-m-d H:i:s', time() - $days * 86400) : '';

        $this->strTitle = '活跃榜';
        $this->showTop($this->strTitle);
        echo '<div class="subtitle">' . ($days > 0 ? '近' . $days . '天' : '全部') . '活跃榜</div>';
        echo '<div class="menu">';
        foreach ([7, 30, 0] as $d) {
            echo '<a href="active.php?days=' . $d . '">'
                . ($d > 0 ? '近' . $d . '天' : '全部') . '</a> ';
        }
        echo '</div>';

        // 发帖数
        $stat = [];
        $sql = "SELECT userid, COUNT(*) AS c FROM WapBbs WHERE isshow=1 AND userid>0";
        $params = [];
        if ($cutoff !== '') { $sql .= " AND addtime>=?"; $params[] = $cutoff; }
        $sql .= " GROUP BY userid";
        foreach ($db->getAll($sql, $params) as $row) {
            $u = (int)$row['userid'];
            if (!isset($stat[$u])) $stat[$u] = ['posts' => 0, 'replies' => 0, 'likes' => 0];
            $stat[$u]['posts'] = (int)$row['c'];
        }

        // 回复数
        $sql = "SELECT userid, COUNT(*) AS c FROM WapBbsReply WHERE isshow=1 AND userid>0";
        $params = [];
        if ($cutoff !== '') { $sql .= " AND addtime>=?"; $params[] = $cutoff; }
        $sql .= " GROUP BY userid";
        foreach ($db->getAll($sql, $params) as $row) {
            $u = (int)$row['userid'];
            if (!isset($stat[$u])) $stat[$u] = ['posts' => 0, 'replies' => 0, 'likes' => 0];
            $stat[$u]['replies'] = (int)$row['c'];
        }

        // 被点赞数 (主帖)
        $sql = "SELECT b.userid AS uid, COUNT(*) AS c FROM WapLike l
                INNER JOIN WapBbs b ON b.id=l.contentid
                WHERE l.module='bbs' AND l.replyid=0 AND b.userid>0";
        $params = [];
        if ($cutoff !== '') { $sql .= " AND l.addtime>=?"; $params[] = $cutoff; }
        $sql .= " GROUP BY b.userid";
        foreach ($db->getAll($sql, $params) as $row) {
            $u = (int)$row['uid'];
            if (!isset($stat[$u])) $stat[$u] = ['posts' => 0, 'replies' => 0, 'likes' => 0];
            $stat[$u]['likes'] += (int)$row['c'];
        }

        // 被点赞数 (回复)
        $sql = "SELECT r.userid AS uid, COUNT(*) AS c FROM WapLike l
                INNER JOIN WapBbsReply r ON r.id=l.replyid
                WHERE l.module='bbs' AND l.replyid>0 AND r.userid>0";
        $params = [];
        if ($cutoff !== '') { $sql .= " AND l.addtime>=?"; $params[] = $cutoff; }
        $sql .= " GROUP BY r.userid";
        foreach ($db->getAll($sql, $params) as $row) {
            $u = (int)$row['uid'];
            if (!isset($stat[$u])) $stat[$u] = ['posts' => 0, 'replies' => 0, 'likes' => 0];
            $stat[$u]['likes'] += (int)$row['c'];
        }

        if (empty($stat)) {
            echo '<div class="content">暂无活跃数据</div>';
            echo '<div><a href="list.php?classid=' . $this->classid . '">返回论坛</a></div>';
            $this->showDown();
            return;
        }

        // 计算活跃分并排序 (PHP 排序，兼容 SQLite/MySQL，避免窗口函数)
        foreach ($stat as $u => $s) {
            $stat[$u]['score'] = $s['posts'] * 5 + $s['replies'] * 2 + $s['likes'];
        }
        uasort($stat, function ($a, $b) {
            if ($a['score'] === $b['score']) return 0;
            return $a['score'] < $b['score'] ? 1 : -1;
        });
        $stat = array_slice($stat, 0, 20, true);

        // 批量取昵称
        $uids = array_keys($stat);
        $nameMap = [];
        $in = implode(',', array_map('intval', $uids));
        foreach ($db->getAll("SELECT id,username,nickname FROM WapUser WHERE id IN ($in)") as $u) {
            $nameMap[(int)$u['id']] = $u['nickname'] !== '' ? $u['nickname'] : $u['username'];
        }

        echo '<div class="content"><ul class="activelist">';
        $rank = 1;
        foreach ($stat as $u => $s) {
            $name = $nameMap[$u] ?? '已注销用户';
            echo '<li>#' . $rank . ' <a href="../zone/index.php?uid=' . $u . '">'
                . $this->ToHtm($name) . '</a>';
            echo ' <span class="info">发帖' . $s['posts'] . ' 回复' . $s['replies'] . ' 获赞' . $s['likes'] . '</span>';
            echo ' <span class="score">活跃分' . $s['score'] . '</span></li>';
            $rank++;
        }
        echo '</ul></div>';

        echo '<div><a href="list.php?classid=' . $this->classid . '">返回论坛</a></div>';
        $this->showDown();
    }
}

$page = new BbsActivePage();
$page->run();
