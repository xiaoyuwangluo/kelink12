<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 管理后台 - 敏感词审核队列 (待审/通过/拒绝/全部)
 */
require_once __DIR__ . '/../includes/bootstrap.php';
require_once KL_ROOT . '/includes/Community.php';

class AdminAuditPage extends WapPage {
    /** 模块标识 => 展示名 */
    private $modules = ['bbs' => '帖子', 'bbs_re' => '回复'];
    /** 状态标识 => 展示名 */
    private $statusMap = [0 => '待审核', 1 => '已通过', 2 => '已拒绝'];

    public function run() {
        $this->checkAdminLogin();
        $db = $this->db;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->doPost();
            return;
        }

        $status = $_GET['status'] ?? 'pending';
        if (!in_array($status, ['pending', 'passed', 'rejected', 'all'], true)) $status = 'pending';
        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = 20;
        $offset = ($page - 1) * $limit;
        $sid = (int)$this->siteid;

        $where = 'siteid=?';
        $params = [$sid];
        if ($status === 'pending')  { $where .= ' AND status=0'; }
        if ($status === 'passed')   { $where .= ' AND status=1'; }
        if ($status === 'rejected') { $where .= ' AND status=2'; }

        $total = (int)$db->getScalar("SELECT COUNT(*) FROM WapAudit WHERE $where", $params);
        $rows = $db->getAll(
            "SELECT * FROM WapAudit WHERE $where ORDER BY id DESC LIMIT $limit OFFSET $offset",
            $params
        );

        $counts = ['pending' => 0, 'passed' => 0, 'rejected' => 0, 'all' => 0];
        $counts['pending']  = (int)$db->getScalar("SELECT COUNT(*) FROM WapAudit WHERE siteid=? AND status=0", [$sid]);
        $counts['passed']   = (int)$db->getScalar("SELECT COUNT(*) FROM WapAudit WHERE siteid=? AND status=1", [$sid]);
        $counts['rejected'] = (int)$db->getScalar("SELECT COUNT(*) FROM WapAudit WHERE siteid=? AND status=2", [$sid]);
        $counts['all']      = $counts['pending'] + $counts['passed'] + $counts['rejected'];

        $msg = (string)($_GET['msg'] ?? '');
        $err = (string)($_GET['err'] ?? '');

        $this->strTitle = '内容审核';
        $this->showTop($this->strTitle);
        echo '<div class="subtitle">内容审核</div>';
        echo '<div><a href="index.php">返回后台首页</a></div>';
        if ($msg !== '') echo '<div class="success">' . $this->ToHtm($msg) . '</div>';
        if ($err !== '') echo '<div class="error">' . $this->ToHtm($err) . '</div>';

        $base = 'audit.php?siteid=' . $sid;
        echo '<div class="content">';
        echo '<a href="' . $base . '&status=pending">待审核(' . $counts['pending'] . ')</a> | ';
        echo '<a href="' . $base . '&status=passed">已通过(' . $counts['passed'] . ')</a> | ';
        echo '<a href="' . $base . '&status=rejected">已拒绝(' . $counts['rejected'] . ')</a> | ';
        echo '<a href="' . $base . '&status=all">全部(' . $counts['all'] . ')</a>';
        echo '</div>';

        if (!$rows) {
            echo '<div class="content">暂无记录</div>';
            $this->showDown();
            return;
        }

        echo '<div class="content">';
        echo '<form id="batchForm" method="post" action="' . $base . '&status=' . $status . '&page=' . $page . '" style="display:inline">';
        echo '<input type="hidden" name="act" value="pass"/>';
        echo '</form>';
        echo '<ul class="list">';
        foreach ($rows as $r) {
            $rid = (int)$r['id'];
            $module = (string)$r['module'];
            $mtxt = $this->modules[$module] ?? $module;
            $stxt = $this->statusMap[(int)$r['status']] ?? '未知';
            echo '<li>';
            if ((int)$r['status'] === 0 && in_array($module, ['bbs', 'bbs_re'], true)) {
                echo '<input type="checkbox" form="batchForm" name="ids[]" value="' . $rid . '"/> ';
            }
            echo '#' . $rid . ' [' . $this->ToHtm($mtxt) . '] ' . $this->ToHtm($stxt);
            echo ' 作者UID:' . (int)$r['userid'] . ' 内容ID:' . (int)$r['contentid'];
            echo '<br/>标题：' . $this->ToHtm($r['title']);
            echo '<br/>内容：' . $this->ToHtm(mb_substr((string)$r['content'], 0, 80));
            if (!empty($r['word'])) echo '<br/>命中词：' . $this->ToHtm($r['word']);
            echo '<br/><span class="time">' . (!empty($r['addtime']) ? date('Y-m-d H:i', strtotime($r['addtime'])) : '') . '</span>';

            if ((int)$r['status'] === 0) {
                echo '<div style="margin-top:6px">';
                echo '<form method="post" action="' . $base . '&status=' . $status . '&page=' . $page . '" style="display:inline">';
                echo '<input type="hidden" name="act" value="pass"/><input type="hidden" name="ids[]" value="' . $rid . '"/>';
                echo '<input type="submit" value="通过"/></form> ';
                echo '<form method="post" action="' . $base . '&status=' . $status . '&page=' . $page . '" style="display:inline" onsubmit="return true">';
                echo '<input type="hidden" name="act" value="reject"/><input type="hidden" name="ids[]" value="' . $rid . '"/>';
                echo '理由：<input type="text" name="reason" size="20" maxlength="100"/>';
                echo '<input type="submit" value="拒绝"/></form>';
                echo '</div>';
            }
            echo '</li>';
        }
        echo '</ul>';
        echo '<div><input type="submit" form="batchForm" value="批量通过选中项"/></div>';
        echo '</div>';

        // 分页
        $pages = (int)ceil($total / $limit);
        if ($pages > 1) {
            echo '<div class="content">';
            for ($i = 1; $i <= $pages; $i++) {
                if ($i === $page) {
                    echo '[' . $i . '] ';
                } else {
                    echo '<a href="' . $base . '&status=' . $status . '&page=' . $i . '">' . $i . '</a> ';
                }
            }
            echo '</div>';
        }
        $this->showDown();
    }

    /** 处理通过/拒绝，完成后重定向 (PRG) */
    private function doPost() {
        $db = $this->db;
        $act = $_POST['act'] ?? '';
        $ids = $_POST['ids'] ?? [];
        if (!is_array($ids)) $ids = [$ids];
        $ids = array_values(array_unique(array_filter(array_map('intval', $ids), function ($v) { return $v > 0; })));
        $status = in_array($_GET['status'] ?? '', ['pending', 'passed', 'rejected', 'all'], true) ? $_GET['status'] : 'pending';
        $page = max(1, (int)($_GET['page'] ?? 1));
        $back = 'audit.php?siteid=' . (int)$this->siteid . '&status=' . $status . '&page=' . $page;

        if (!in_array($act, ['pass', 'reject'], true) || !$ids) {
            header('Location: ' . $back . '&err=' . urlencode('请先选择要处理的内容'));
            exit;
        }

        $reason = trim((string)($_POST['reason'] ?? ''));
        if ($act === 'reject' && $reason === '') $reason = '内容不符合社区规范';

        $ok = 0;
        $skip = 0;
        $adminid = (int)($_SESSION['kl_adminid'] ?? 0);
        foreach ($ids as $id) {
            $row = $db->getOne("SELECT * FROM WapAudit WHERE id=? AND siteid=? AND status=0", [$id, $this->siteid]);
            if (!$row) { $skip++; continue; }

            $newStatus = $act === 'pass' ? 1 : 2;
            $db->execute("UPDATE WapAudit SET status=? WHERE id=? AND siteid=?", [$newStatus, $id, $this->siteid]);

            if ($act === 'pass') {
                $this->applyVisible($row);
            } else {
                $this->logAction($id, 'reject', $reason, $adminid);
                kl_notice_add($db, $this->siteid, (int)$row['userid'], 0, 'audit', (string)$row['module'],
                    (int)$row['contentid'], '您发布的内容未通过审核：' . $reason);
            }
            if ($act === 'pass') $this->logAction($id, 'pass', '', $adminid);
            $ok++;
        }

        kl_cache_clear_site($this->siteid);
        if ($ok === 0) {
            header('Location: ' . $back . '&err=' . urlencode('所选内容已被处理或不存在'));
            exit;
        }
        $word = $act === 'pass' ? '已通过' : '已拒绝';
        $msg = $word . ' ' . $ok . ' 条' . ($skip > 0 ? '，跳过 ' . $skip . ' 条' : '');
        header('Location: ' . $back . '&msg=' . urlencode($msg));
        exit;
    }

    /** 通过：恢复对应内容表可见性 */
    private function applyVisible($row) {
        $cid = (int)$row['contentid'];
        if ($cid <= 0) return;
        try {
            if ($row['module'] === 'bbs') {
                $this->db->execute("UPDATE WapBbs SET isshow=1 WHERE id=? AND siteid=?", [$cid, $this->siteid]);
            } elseif ($row['module'] === 'bbs_re') {
                $this->db->execute("UPDATE WapBbsReply SET isshow=1 WHERE id=? AND siteid=?", [$cid, $this->siteid]);
            }
        } catch (Exception $e) {
            // 内容表不可用时跳过，不阻断审核
        }
    }

    /** 写审核操作日志（表不存在时静默跳过） */
    private function logAction($auditid, $act, $reason, $adminid) {
        try {
            $this->db->insert(
                "INSERT INTO WapAuditLog (siteid,auditid,adminid,act,reason,addtime) VALUES (?,?,?,?,?,?)",
                [(int)$this->siteid, (int)$auditid, (int)$adminid, (string)$act, (string)$reason, date('Y-m-d H:i:s')]
            );
        } catch (Exception $e) {
            // 日志表不可用时不影响审核结果
        }
    }
}

$page = new AdminAuditPage();
$page->run();
