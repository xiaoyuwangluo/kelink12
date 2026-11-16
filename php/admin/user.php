<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 管理后台 - 会员管理 (对应原 admin/userlist.aspx)
 * 会员列表 / 状态筛选 / 关键词搜索 / 锁定解锁 / 重置密码(md5)
 * 积分余额调整(写余额与积分流水) / 会员分组(usergroup) / 删除(在途订单拦截, 二次确认)
 */
require_once __DIR__ . '/../includes/bootstrap.php';
require_once KL_ROOT . '/includes/Community.php';

class AdminUserPage extends WapPage {
    /** 会员分组名称映射 (1=普通会员 2=VIP 9=超级管理员) */
    private function groupName($ug) {
        $map = [1 => '普通会员', 2 => 'VIP会员', 9 => '超级管理员'];
        return $map[(int)$ug] ?? ('分组' . (int)$ug);
    }

    /** 列表回跳地址（保留状态筛选与关键词） */
    private function backUrl($status, $q) {
        $url = 'user.php?status=' . urlencode((string)$status);
        if ($q !== '') $url .= '&q=' . urlencode($q);
        return $url;
    }

    /** 导出全部会员 CSV (在任何 HTML 输出前调用) */
    private function exportCsv($db) {
        $rows = $db->getAll("SELECT id, username, email, money, point, usergroup, regtime AS addtime FROM WapUser ORDER BY id ASC");
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="users_' . date('Ymd') . '.csv"');
        $out = fopen('php://output', 'w');
        fwrite($out, "\xEF\xBB\xBF"); // BOM，兼容 Excel 中文
        fputcsv($out, ['id', 'username', 'email', 'money', 'point', 'usergroup', 'addtime'], ',', '"', '');
        foreach ($rows as $r) {
            fputcsv($out, [
                (int)$r['id'],
                (string)($r['username'] ?? ''),
                (string)($r['email'] ?? ''),
                (int)($r['money'] ?? 0),
                (int)($r['point'] ?? 0),
                (int)($r['usergroup'] ?? 1),
                (string)($r['addtime'] ?? ''),
            ], ',', '"', '');
        }
        fclose($out);
        exit;
    }

    public function run() {
        $this->checkAdminLogin();
        $db = $this->db;

        // 筛选与搜索参数
        $status = $_GET['status'] ?? 'all';
        if (!in_array($status, ['all', 'normal', 'locked'], true)) $status = 'all';
        $q = trim($_GET['q'] ?? '');
        $page = max(1, (int)($_GET['page'] ?? 1));
        $back = $this->backUrl($status, $q);

        // CSV 导出 (GET，需在输出前处理)
        if (($_GET['action'] ?? '') === 'export') {
            $this->exportCsv($db);
        }

        // 删除二次确认页
        if (($_GET['act'] ?? '') === 'del') {
            $this->showDeleteConfirm((int)($_GET['id'] ?? 0), $status, $q, $back);
            return;
        }

        // POST 操作（防重复提交）
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->userSession->checkSubmitLimit()) {
                $this->showError('提交过于频繁，请稍后再试', $back);
            }
            $action = $_POST['action'] ?? '';
            $id = (int)($_POST['id'] ?? 0);
            $row = $id > 0 ? $db->getOne("SELECT * FROM WapUser WHERE id=?", [$id]) : null;
            if ($id > 0 && !$row) {
                $this->showError('会员不存在', $back);
            }
            if ($action === 'lock') {
                $db->execute("UPDATE WapUser SET islock=1 WHERE id=?", [$id]);
                $this->showSuccess('会员已锁定', $back);
            } elseif ($action === 'unlock') {
                $db->execute("UPDATE WapUser SET islock=0 WHERE id=?", [$id]);
                $this->showSuccess('会员已解锁', $back);
            } elseif ($action === 'resetpwd') {
                $pwd = $_POST['newpwd'] ?? '';
                if (strlen($pwd) < 4) {
                    $this->showError('新密码不能少于4位', $back);
                }
                $db->execute("UPDATE WapUser SET password=? WHERE id=?", [md5($pwd), $id]);
                $this->showSuccess('密码已重置', $back);
            } elseif ($action === 'adjust') {
                $moneyRaw = trim($_POST['money'] ?? '');
                $pointRaw = trim($_POST['point'] ?? '');
                if (!preg_match('/^\d+$/', $moneyRaw) || !preg_match('/^\d+$/', $pointRaw)) {
                    $this->showError('余额与积分必须为非负整数', $back);
                }
                $this->doAdjust($row, (int)$moneyRaw, (int)$pointRaw, $back);
                $this->showSuccess('积分余额已调整', $back);
            } elseif ($action === 'setgroup') {
                // 设置会员分组 (1=普通会员 2=VIP 9=超级管理员)，用于栏目投稿/版主权限
                $ug = (int)($_POST['usergroup'] ?? 0);
                if (!in_array($ug, [1, 2, 9], true)) {
                    $this->showError('非法会员分组', $back);
                }
                $db->execute("UPDATE WapUser SET usergroup=? WHERE id=?", [$ug, $id]);
                $this->showSuccess('会员分组已更新为' . $this->groupName($ug), $back);
            } elseif ($action === 'deldone' || $action === 'delete') {
                $this->doDelete($row, $back);
                $this->showSuccess('会员已删除', $back);
            } else {
                $this->showError('非法操作', $back);
            }
        }

        // 统计
        $total = (int)$db->getScalar("SELECT COUNT(*) FROM WapUser");
        $normal = (int)$db->getScalar("SELECT COUNT(*) FROM WapUser WHERE islock=0");
        $locked = (int)$db->getScalar("SELECT COUNT(*) FROM WapUser WHERE islock<>0");

        $this->strTitle = '会员管理';
        $this->showTop($this->strTitle);
        echo '<div class="subtitle">会员管理</div><div><a href="index.php">返回后台首页</a></div>';

        // 状态筛选
        echo '<div class="content">筛选：';
        $filters = [
            'all' => '全部(' . $total . ')',
            'normal' => '正常(' . $normal . ')',
            'locked' => '已锁定(' . $locked . ')',
        ];
        foreach ($filters as $k => $label) {
            echo ($status === $k)
                ? '<strong>[' . $label . ']</strong> '
                : '<a href="' . $this->backUrl($k, $q) . '">' . $label . '</a> ';
        }
        echo '</div>';

        // 搜索
        echo '<div class="content"><form method="get" action="user.php">'
            . '搜索：<input type="text" name="q" value="' . $this->ToHtm($q) . '" placeholder="ID/用户名/邮箱/昵称"/>'
            . '<input type="hidden" name="status" value="' . $this->ToHtm($status) . '"/>'
            . '<input type="submit" value="查询"/></form></div>';

        // 导出
        echo '<form method="get" action="user.php" style="display:inline">'
            . '<input type="hidden" name="action" value="export"/>'
            . '<input type="submit" value="导出CSV"/></form>';

        // 列表（分页，保留筛选/搜索）
        $where = '1=1';
        $params = [];
        if ($status === 'normal') {
            $where .= ' AND islock=0';
        } elseif ($status === 'locked') {
            $where .= ' AND islock<>0';
        }
        if ($q !== '') {
            $where .= ' AND (id=? OR username LIKE ? OR email LIKE ? OR nickname LIKE ?)';
            $params[] = (int)$q;
            $like = '%' . $q . '%';
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
        }
        $result = $db->getPage('WapUser', 'id,username,nickname,email,money,point,islock,regtime,lastlogintime,usergroup',
            $where, 'id DESC', $page, 20, $params);
        echo '<div class="content"><ul class="list">';
        foreach ($result['list'] as $u) {
            $uid = (int)$u['id'];
            $lockFlag = empty($u['islock']) ? '' : '<span style="color:red">[已锁定]</span>';
            echo '<li>ID:' . $uid . ' ' . $this->ToHtm($u['username']) . $lockFlag
                . ' 余额:' . (int)$u['money'] . ' 积分:' . (int)$u['point']
                . ' 分组:' . $this->groupName((int)($u['usergroup'] ?? 1)) . '<br/>';
            // 锁定/解锁
            if (empty($u['islock'])) {
                echo '<form method="post" action="' . $back . '" style="display:inline">'
                    . '<input type="hidden" name="action" value="lock"/><input type="hidden" name="id" value="' . $uid . '"/>'
                    . '<input type="submit" value="锁定"/></form>';
            } else {
                echo '<form method="post" action="' . $back . '" style="display:inline">'
                    . '<input type="hidden" name="action" value="unlock"/><input type="hidden" name="id" value="' . $uid . '"/>'
                    . '<input type="submit" value="解锁"/></form>';
            }
            // 重置密码
            echo '<form method="post" action="' . $back . '" style="display:inline">'
                . '<input type="hidden" name="action" value="resetpwd"/><input type="hidden" name="id" value="' . $uid . '"/>'
                . '<input type="text" name="newpwd" size="8" placeholder="新密码"/><input type="submit" value="重置密码"/></form>';
            // 积分余额调整
            echo '<form method="post" action="' . $back . '" style="display:inline">'
                . '<input type="hidden" name="action" value="adjust"/><input type="hidden" name="id" value="' . $uid . '"/>'
                . '余额<input type="text" name="money" size="5" value="' . (int)$u['money'] . '"/>'
                . '积分<input type="text" name="point" size="5" value="' . (int)$u['point'] . '"/>'
                . '<input type="submit" value="调整"/></form>';
            // 会员分组 (usergroup: 1=普通会员 2=VIP 9=超级管理员)
            $curGroup = (int)($u['usergroup'] ?? 1);
            echo '<form method="post" action="' . $back . '" style="display:inline">'
                . '<input type="hidden" name="action" value="setgroup"/><input type="hidden" name="id" value="' . $uid . '"/>'
                . '<select name="usergroup">';
            foreach ([1 => '普通会员', 2 => 'VIP会员', 9 => '超级管理员'] as $gid => $gname) {
                $sel = ($curGroup === $gid) ? ' selected="selected"' : '';
                echo '<option value="' . $gid . '"' . $sel . '>' . $gname . '</option>';
            }
            echo '</select><input type="submit" value="设置分组"/></form>';
            // 删除（进入二次确认页）
            echo ' <a href="' . $back . '&act=del&id=' . $uid . '">删除</a>';
            echo '</li>';
        }
        if (empty($result['list'])) echo '<li>暂无会员</li>';
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

    /** 调整余额/积分并写流水 */
    private function doAdjust($row, $money, $point, $back) {
        $db = $this->db;
        $id = (int)$row['id'];
        $oldMoney = (int)$row['money'];
        $oldPoint = (int)$row['point'];
        $db->execute("UPDATE WapUser SET money=?, point=? WHERE id=?", [$money, $point, $id]);

        if ($money !== $oldMoney) {
            $delta = $money - $oldMoney;
            $db->insert("INSERT INTO WapAccountLog (userid,type,num,balance,remark,addtime) VALUES (?,?,?,?,?,?)",
                [$id, $delta >= 0 ? 'in' : 'out', abs($delta), $money, '后台调整余额', date('Y-m-d H:i:s')]);
        }
        if ($point !== $oldPoint) {
            $db->insert("INSERT INTO WapPointLog (userid,num,balance,remark,addtime) VALUES (?,?,?,?,?)",
                [$id, $point - $oldPoint, $point, '后台调整积分', date('Y-m-d H:i:s')]);
        }
        kl_cache_clear_site();
    }

    /** 删除二次确认页 */
    private function showDeleteConfirm($id, $status, $q, $back) {
        $db = $this->db;
        $row = $id > 0 ? $db->getOne("SELECT * FROM WapUser WHERE id=?", [$id]) : null;
        if (!$row) $this->showError('会员不存在', $back);
        $inFlight = (int)$db->getScalar(
            "SELECT COUNT(*) FROM WapShopOrder WHERE userid=? AND status IN (0,1,2)", [$id]);

        $this->strTitle = '删除会员确认';
        $this->showTop($this->strTitle);
        echo '<div class="subtitle">删除会员确认</div>';
        echo '<div><a href="' . $back . '">返回会员列表</a></div>';
        echo '<div class="content">'
            . '会员：' . $this->ToHtm($row['username']) . '（ID:' . (int)$row['id'] . '）<br/>'
            . '余额：' . (int)$row['money'] . ' 积分：' . (int)$row['point'] . '<br/>'
            . '删除后该会员账号不可恢复，请谨慎操作。';
        if ($inFlight > 0) {
            echo '<div class="error">该会员存在 ' . $inFlight . ' 笔在途订单（待付款/待发货/已发货），无法删除。</div></div>';
        } else {
            echo '<div class="error">确认删除该会员？</div>';
            echo '<form method="post" action="' . $back . '">'
                . '<input type="hidden" name="action" value="deldone"/>'
                . '<input type="hidden" name="id" value="' . (int)$row['id'] . '"/>'
                . '<input type="submit" value="确认删除"/></form></div>';
        }
        $this->showDown();
        exit;
    }

    /** 执行删除（在途订单拦截） */
    private function doDelete($row, $back) {
        $db = $this->db;
        $id = (int)$row['id'];
        $inFlight = (int)$db->getScalar(
            "SELECT COUNT(*) FROM WapShopOrder WHERE userid=? AND status IN (0,1,2)", [$id]);
        if ($inFlight > 0) {
            $this->showError('该会员存在 ' . $inFlight . ' 笔在途订单，无法删除', $back);
        }
        $db->execute("DELETE FROM WapUser WHERE id=?", [$id]);
        kl_cache_clear_site();
    }
}

$page = new AdminUserPage();
$page->run();
