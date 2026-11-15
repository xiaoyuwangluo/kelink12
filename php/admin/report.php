<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 管理后台 - 帖子举报管理 (列表含帖子标题，处理置 status=1，驳回置 status=2)
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class AdminReportPage extends WapPage {
    public function run() {
        $this->checkAdminLogin();
        $db = $this->db;
        $status = $_GET['status'] ?? '';
        $status = ($status === '0' || $status === '1' || $status === '2') ? $status : '';
        $back = 'report.php?siteid=' . (int)$this->siteid . ($status !== '' ? '&status=' . $status : '');

        // POST 操作（防重复提交）：处理/驳回，仅对未处理举报生效
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->userSession->checkSubmitLimit()) {
                $this->showError('提交过于频繁，请稍后再试', $back);
            }
            $action = $_POST['action'] ?? '';
            $id = (int)($_POST['id'] ?? 0);
            if ($action === 'handle') {
                $n = $db->execute("UPDATE WapReport SET status=1 WHERE id=? AND status=0", [$id]);
                $n ? $this->showSuccess('举报已处理', $back) : $this->showError('举报不存在或已处理', $back);
            } elseif ($action === 'reject') {
                $n = $db->execute("UPDATE WapReport SET status=2 WHERE id=? AND status=0", [$id]);
                $n ? $this->showSuccess('举报已驳回', $back) : $this->showError('举报不存在或已处理', $back);
            } else {
                $this->showError('非法操作', $back);
            }
        }

        $this->strTitle = '举报管理';
        $this->showTop($this->strTitle);
        echo '<div class="subtitle">帖子举报管理</div><div><a href="index.php">返回后台首页</a></div>';

        // 状态筛选
        echo '<div class="content">'
            . '<a href="report.php?siteid=' . (int)$this->siteid . '">全部' . ($status === '' ? '√' : '') . '</a> | '
            . '<a href="report.php?siteid=' . (int)$this->siteid . '&status=0">待处理' . ($status === '0' ? '√' : '') . '</a> | '
            . '<a href="report.php?siteid=' . (int)$this->siteid . '&status=1">已处理' . ($status === '1' ? '√' : '') . '</a> | '
            . '<a href="report.php?siteid=' . (int)$this->siteid . '&status=2">已驳回' . ($status === '2' ? '√' : '') . '</a></div>';

        // 分页列表（LEFT JOIN 带出帖子标题）
        $page = max(1, (int)($_GET['page'] ?? 1));
        $pageSize = 20;
        $where = 'r.siteid=?';
        $params = [$this->siteid];
        if ($status !== '') { $where .= ' AND r.status=?'; $params[] = (int)$status; }
        $total = (int)$db->getScalar("SELECT COUNT(*) FROM WapReport r WHERE $where", $params);
        $pageCount = max(1, (int)ceil($total / $pageSize));
        $offset = ($page - 1) * $pageSize;
        $rows = $db->getAll("SELECT r.*, b.title AS bbs_title FROM WapReport r "
            . "LEFT JOIN WapBbs b ON b.id=r.bbsid WHERE $where ORDER BY r.id DESC LIMIT $pageSize OFFSET $offset", $params);

        $labels = [0 => '待处理', 1 => '已处理', 2 => '已驳回'];
        echo '<div class="content"><ul class="list">';
        foreach ($rows as $row) {
            $rid = (int)$row['id'];
            $st = (int)$row['status'];
            $time = !empty($row['addtime']) ? date('m-d H:i', strtotime($row['addtime'])) : '';
            echo '<li>ID:' . $rid . ' [' . $labels[$st] . '] 帖子：<a href="../bbs/view.php?siteid=' . (int)$row['siteid'] . '&id=' . (int)$row['bbsid'] . '">'
                . $this->ToHtm($row['bbs_title'] ?? '(帖子已删除)') . '</a><br/>'
                . '举报人ID:' . (int)$row['userid'] . ' 原因:' . $this->ToHtm($row['reason'])
                . ($row['note'] !== '' && $row['note'] !== null ? ' 说明:' . $this->ToHtm(mb_substr((string)$row['note'], 0, 80)) : '')
                . ' <span class="time">' . $time . '</span><br/>';
            if ($st === 0) {
                echo '<form method="post" action="' . $back . '" style="display:inline">'
                    . '<input type="hidden" name="action" value="handle"/><input type="hidden" name="id" value="' . $rid . '"/>'
                    . '<input type="submit" value="处理"/></form> ';
                echo '<form method="post" action="' . $back . '" style="display:inline">'
                    . '<input type="hidden" name="action" value="reject"/><input type="hidden" name="id" value="' . $rid . '"/>'
                    . '<input type="submit" value="驳回"/></form>';
            }
            echo '</li>';
        }
        if (empty($rows)) echo '<li>暂无举报</li>';
        echo '</ul></div>';

        if ($pageCount > 1) {
            echo '<div class="pager">';
            if ($page > 1) echo '<a href="' . $back . '&page=' . ($page - 1) . '">上一页</a> ';
            echo '第' . $page . '/' . $pageCount . '页 ';
            if ($page < $pageCount) echo '<a href="' . $back . '&page=' . ($page + 1) . '">下一页</a>';
            echo '</div>';
        }
        $this->showDown();
    }
}

$page = new AdminReportPage();
$page->run();
