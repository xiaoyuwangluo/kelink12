<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 管理后台 - 举报管理 (对应原 WapReport 举报处理)
 * 举报列表 / 待处理与已处理筛选 / 搜索 / 标记已处理 / 删除确认
 */
require_once __DIR__ . '/../includes/bootstrap.php';
require_once KL_ROOT . '/includes/Community.php';

class AdminReportPage extends WapPage {
    /** 列表回跳地址（保留状态/关键词） */
    private function backUrl($status, $q) {
        $url = 'report.php?status=' . urlencode((string)$status);
        if ($q !== '') $url .= '&q=' . urlencode($q);
        return $url;
    }

    /** 删除二次确认页 */
    private function showDeleteConfirm($id, $back) {
        $row = $id > 0 ? $this->db->getOne("SELECT * FROM WapReport WHERE id=?", [$id]) : null;
        if (!$row) $this->showError('举报记录不存在', $back);
        $reason = mb_substr(trim(preg_replace('/\s+/', ' ', (string)($row['reason'] ?? ''))), 0, 60);

        $this->strTitle = '删除举报确认';
        $this->showTop($this->strTitle);
        echo '<div class="subtitle">删除举报确认</div>';
        echo '<div><a href="' . $back . '">返回举报列表</a></div>';
        echo '<div class="content">ID：' . (int)$row['id'] . '　帖子ID：' . (int)($row['bbsid'] ?? 0) . '<br/>'
            . '举报理由：' . $this->ToHtm($reason) . '<br/>'
            . '删除后不可恢复，请谨慎操作。';
        echo '<div class="error">确认删除该举报记录？</div>';
        echo '<form method="post" action="' . $back . '">'
            . '<input type="hidden" name="action" value="deldone"/>'
            . '<input type="hidden" name="id" value="' . (int)$row['id'] . '"/>'
            . '<input type="submit" value="确认删除"/></form></div>';
        $this->showDown();
        exit;
    }

    public function run() {
        $this->checkAdminLogin();
        $db = $this->db;

        $q = trim($_GET['q'] ?? '');
        $status = $_GET['status'] ?? 'all';
        if (!in_array($status, ['all', 'pending', 'handled'], true)) $status = 'all';
        $back = $this->backUrl($status, $q);

        // 删除二次确认页
        if (($_GET['act'] ?? '') === 'del') {
            $this->showDeleteConfirm((int)($_GET['id'] ?? 0), $back);
            return;
        }

        // POST 操作（防重复提交）
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->userSession->checkSubmitLimit()) {
                $this->showError('提交过于频繁，请稍后再试', $back);
            }
            $action = $_POST['action'] ?? '';
            $id = (int)($_POST['id'] ?? 0);
            if ($action === 'handle' || $action === 'unhandle') {
                $row = $id > 0 ? $db->getOne("SELECT id FROM WapReport WHERE id=?", [$id]) : null;
                if (!$row) $this->showError('举报记录不存在', $back);
                $db->execute("UPDATE WapReport SET status=? WHERE id=?", [$action === 'handle' ? 1 : 0, $id]);
                $this->showSuccess($action === 'handle' ? '举报已标记为已处理' : '举报已恢复为待处理', $back);
            } elseif ($action === 'deldone' || $action === 'delete') {
                $row = $id > 0 ? $db->getOne("SELECT id FROM WapReport WHERE id=?", [$id]) : null;
                if (!$row) $this->showError('举报记录不存在', $back);
                $db->execute("DELETE FROM WapReport WHERE id=?", [$id]);
                $this->showSuccess('举报记录已删除', $back);
            } else {
                $this->showError('非法操作', $back);
            }
        }

        $this->strTitle = '举报管理';
        $this->showTop($this->strTitle);
        echo '<div class="subtitle">举报管理</div><div><a href="index.php">返回后台首页</a></div>';

        // 统计（随搜索条件联动）
        $baseWhere = '1=1';
        $params = [];
        if ($q !== '') {
            $baseWhere .= ' AND (id=? OR reason LIKE ? OR note LIKE ? OR bbsid=?)';
            $params[] = (int)$q;
            $params[] = '%' . $q . '%';
            $params[] = '%' . $q . '%';
            $params[] = (int)$q;
        }
        $total = (int)$db->getScalar("SELECT COUNT(*) FROM WapReport WHERE " . $baseWhere, $params);
        $pendingCount = (int)$db->getScalar("SELECT COUNT(*) FROM WapReport WHERE " . $baseWhere . " AND status=0", $params);
        $handledCount = (int)$db->getScalar("SELECT COUNT(*) FROM WapReport WHERE " . $baseWhere . " AND status<>0", $params);

        // 状态筛选
        echo '<div class="content">筛选：';
        $filters = [
            'all' => '全部(' . $total . ')',
            'pending' => '待处理(' . $pendingCount . ')',
            'handled' => '已处理(' . $handledCount . ')',
        ];
        foreach ($filters as $k => $label) {
            echo ($status === $k)
                ? '<strong>[' . $label . ']</strong> '
                : '<a href="' . $this->backUrl($k, $q) . '">' . $label . '</a> ';
        }
        echo '</div>';

        // 搜索
        echo '<div class="content"><form method="get" action="report.php">'
            . '搜索：<input type="text" name="q" value="' . $this->ToHtm($q) . '" placeholder="ID/帖子ID/理由/备注"/>'
            . '<input type="hidden" name="status" value="' . $this->ToHtm($status) . '"/>'
            . '<input type="submit" value="查询"/></form>';
        if ($q !== '') echo ' <a href="report.php">清除搜索</a>';
        echo '</div>';

        // 列表条件
        $where = $baseWhere;
        if ($status === 'pending') {
            $where .= ' AND status=0';
        } elseif ($status === 'handled') {
            $where .= ' AND status<>0';
        }
        $page = max(1, (int)($_GET['page'] ?? 1));
        $result = $db->getPage('WapReport', '*', $where, 'id DESC', $page, 20, $params);

        // 举报人名称映射
        $users = [];
        foreach ($db->getAll("SELECT id,username FROM WapUser") as $u) {
            $users[(int)$u['id']] = $u['username'];
        }

        echo '<div class="content">共' . $result['total'] . '条<br/><ul class="list">';
        foreach ($result['list'] as $r) {
            $rid = (int)$r['id'];
            $uid = (int)($r['userid'] ?? 0);
            $userTxt = $uid > 0 ? $this->ToHtm($users[$uid] ?? ('#' . $uid)) : '游客';
            $state = empty($r['status'])
                ? '<span style="color:red">待处理</span>'
                : '<span style="color:green">已处理</span>';
            $time = !empty($r['addtime']) ? date('m-d H:i', strtotime($r['addtime'])) : '';
            $bbsid = (int)($r['bbsid'] ?? 0);
            echo '<li>ID:' . $rid . ' 帖子:'
                . ($bbsid > 0 ? '<a href="../bbs/view.php?id=' . $bbsid . '" target="_blank">#' . $bbsid . '</a>' : '无')
                . ' 举报人:' . $userTxt . ' ' . $state . ' <span class="time">' . $time . '</span><br/>'
                . '理由:' . $this->ToHtm($r['reason'] ?? '')
                . (!empty($r['note']) ? ' 备注:' . $this->ToHtm($r['note']) : '') . '<br/>';
            echo empty($r['status'])
                ? '<form method="post" action="' . $back . '" style="display:inline"><input type="hidden" name="action" value="handle"/><input type="hidden" name="id" value="' . $rid . '"/><input type="submit" value="标记已处理"/></form> '
                : '<form method="post" action="' . $back . '" style="display:inline"><input type="hidden" name="action" value="unhandle"/><input type="hidden" name="id" value="' . $rid . '"/><input type="submit" value="恢复待处理"/></form> ';
            echo '<a href="' . $back . ($q !== '' ? '&' : '?') . 'act=del&id=' . $rid . '">删除</a></li>';
        }
        if (empty($result['list'])) echo '<li>暂无举报记录</li>';
        echo '</ul></div>';

        // 分页
        if ($result['pageCount'] > 1) {
            echo '<div class="pager">';
            if ($page > 1) echo '<a href="' . $back . '&page=' . ($page - 1) . '">上一页</a> ';
            echo '第' . $page . '/' . $result['pageCount'] . '页 ';
            if ($page < $result['pageCount']) echo '<a href="' . $back . '&page=' . ($page + 1) . '">下一页</a>';
            echo '</div>';
        }

        $this->showDown();
    }
}

$page = new AdminReportPage();
$page->run();
