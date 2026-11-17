<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 管理后台 - 在线用户 (对应原 WapOnline 在线人数统计)
 * 在线列表 / 会员游客筛选 / 搜索 / 踢下线(二次确认) / 清理超时记录
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class AdminOnlinePage extends WapPage {
    /** 后台清理阈值（分钟）：超过视为离线记录，可手动清理 */
    private function staleMinutes() {
        return 30;
    }

    /** 列表回跳地址（保留类型/关键词） */
    private function backUrl($type, $q) {
        $url = 'online.php?type=' . urlencode((string)$type);
        if ($q !== '') $url .= '&q=' . urlencode($q);
        return $url;
    }

    public function run() {
        $this->checkAdminLogin();
        $db = $this->db;

        $type = $_GET['type'] ?? 'all';
        if (!in_array($type, ['all', 'member', 'guest'], true)) $type = 'all';
        $q = trim($_GET['q'] ?? '');
        $page = max(1, (int)($_GET['page'] ?? 1));
        $back = $this->backUrl($type, $q);

        // 踢下线二次确认页
        if (($_GET['act'] ?? '') === 'kick') {
            $this->showKickConfirm((int)($_GET['id'] ?? 0), $back);
            return;
        }

        // POST 操作（防重复提交）
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->userSession->checkSubmitLimit()) {
                $this->showError('提交过于频繁，请稍后再试', $back);
            }
            $action = $_POST['action'] ?? '';
            $id = (int)($_POST['id'] ?? 0);
            if ($action === 'kickdone') {
                $row = $id > 0 ? $db->getOne("SELECT id FROM WapOnline WHERE id=?", [$id]) : null;
                if (!$row) $this->showError('在线记录不存在', $back);
                $db->execute("DELETE FROM WapOnline WHERE id=?", [$id]);
                $this->showSuccess('已将该用户踢下线', $back);
            } elseif ($action === 'clean') {
                $limit = date('Y-m-d H:i:s', time() - $this->staleMinutes() * 60);
                $n = $db->execute("DELETE FROM WapOnline WHERE lasttime < ?", [$limit]);
                $this->showSuccess('已清理 ' . (int)$n . ' 条超时在线记录', $back);
            } else {
                $this->showError('非法操作', $back);
            }
        }

        // 统计（随搜索条件联动）
        $baseWhere = '1=1';
        $params = [];
        if ($q !== '') {
            $baseWhere .= ' AND (id=? OR username LIKE ? OR ip LIKE ? OR page LIKE ?)';
            $params[] = (int)$q;
            $like = '%' . $q . '%';
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
        }
        $total = (int)$db->getScalar("SELECT COUNT(*) FROM WapOnline WHERE " . $baseWhere, $params);
        $memberCount = (int)$db->getScalar("SELECT COUNT(*) FROM WapOnline WHERE " . $baseWhere . " AND userid>0", $params);
        $guestCount = (int)$db->getScalar("SELECT COUNT(*) FROM WapOnline WHERE " . $baseWhere . " AND (userid IS NULL OR userid=0)", $params);

        $this->strTitle = '在线用户';
        $adminTitle = '在线用户';
        $activeNav = 'online';
        require __DIR__ . '/inc/header.php';
        echo '<div class="subtitle">在线用户</div><div><a href="index.php">返回后台首页</a></div>';

        // 类型筛选
        echo '<div class="content">筛选：';
        $filters = [
            'all' => '全部(' . $total . ')',
            'member' => '会员(' . $memberCount . ')',
            'guest' => '游客(' . $guestCount . ')',
        ];
        foreach ($filters as $k => $label) {
            echo ($type === $k)
                ? '<strong>[' . $label . ']</strong> '
                : '<a href="' . $this->backUrl($k, $q) . '">' . $label . '</a> ';
        }
        echo '</div>';

        // 搜索 + 清理
        echo '<div class="content"><form method="get" action="online.php">'
            . '搜索：<input type="text" name="q" value="' . $this->ToHtm($q) . '" placeholder="ID/用户名/IP/页面"/>'
            . '<input type="hidden" name="type" value="' . $this->ToHtm($type) . '"/>'
            . '<input type="submit" value="查询"/></form></div>';
        echo '<div class="content"><form method="post" action="' . $back . '" style="display:inline">'
            . '<input type="hidden" name="action" value="clean"/>'
            . '<input type="submit" value="清理 ' . $this->staleMinutes() . ' 分钟无活动记录"/></form>'
            . ' <a href="online.php">刷新</a></div>';

        // 列表条件
        $where = $baseWhere;
        if ($type === 'member') {
            $where .= ' AND userid>0';
        } elseif ($type === 'guest') {
            $where .= ' AND (userid IS NULL OR userid=0)';
        }
        $result = $db->getPage('WapOnline', '*', $where, 'lasttime DESC', $page, 30, $params);

        echo '<div class="content"><ul class="list">';
        foreach ($result['list'] as $row) {
            $rid = (int)$row['id'];
            $who = (int)($row['userid'] ?? 0) > 0 ? $this->ToHtm($row['username'] ?? '') : '游客';
            $time = !empty($row['lasttime']) ? date('m-d H:i:s', strtotime($row['lasttime'])) : '';
            echo '<li>ID:' . $rid . ' ' . $who . ' (' . $this->ToHtm($row['ip'] ?? '') . ')'
                . ' 页面:' . $this->ToHtm($row['page'] ?? '')
                . ' <span class="time">' . $time . '</span> '
                . '<a href="' . $back . '&act=kick&id=' . $rid . '">踢下线</a></li>';
        }
        if (empty($result['list'])) echo '<li>当前无人在线</li>';
        echo '</ul></div>';

        // 分页
        if ($result['pageCount'] > 1) {
            echo '<div class="pager">';
            if ($page > 1) echo '<a href="' . $back . '&page=' . ($page - 1) . '">上一页</a> ';
            echo '第' . $page . '/' . $result['pageCount'] . '页 ';
            if ($page < $result['pageCount']) echo '<a href="' . $back . '&page=' . ($page + 1) . '">下一页</a>';
            echo '</div>';
        }

        require __DIR__ . '/inc/footer.php';
    }

    /** 踢下线二次确认页 */
    private function showKickConfirm($id, $back) {
        $row = $id > 0 ? $this->db->getOne("SELECT * FROM WapOnline WHERE id=?", [$id]) : null;
        if (!$row) $this->showError('在线记录不存在', $back);
        $who = (int)($row['userid'] ?? 0) > 0 ? $this->ToHtm($row['username'] ?? '') : '游客';

        $this->strTitle = '踢下线确认';
        $adminTitle = '踢下线确认';
        $activeNav = 'online';
        require __DIR__ . '/inc/header.php';
        echo '<div class="subtitle">踢下线确认</div>';
        echo '<div><a href="' . $back . '">返回在线列表</a></div>';
        echo '<div class="content">用户：' . $who . '（ID:' . (int)$row['id'] . '）<br/>'
            . 'IP：' . $this->ToHtm($row['ip'] ?? '') . '　页面：' . $this->ToHtm($row['page'] ?? '') . '<br/>'
            . '仅删除当前在线记录，用户再次访问会重新上线。';
        echo '<div class="error">确认将该用户踢下线？</div>';
        echo '<form method="post" action="' . $back . '">'
            . '<input type="hidden" name="action" value="kickdone"/>'
            . '<input type="hidden" name="id" value="' . (int)$row['id'] . '"/>'
            . '<input type="submit" value="确认踢下线"/></form></div>';
        require __DIR__ . '/inc/footer.php';
        exit;
    }
}

$page = new AdminOnlinePage();
$page->run();
