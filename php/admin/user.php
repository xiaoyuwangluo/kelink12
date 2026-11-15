<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 管理后台 - 会员管理 (对应原 admin/userlist.aspx)
 * 会员列表 / 锁定解锁 / 重置密码(md5) / 删除 / 积分余额调整
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class AdminUserPage extends WapPage {
    public function run() {
        $this->checkAdminLogin();
        $db = $this->db;
        $back = 'user.php?siteid=' . $this->siteid;

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
                $money = (int)($_POST['money'] ?? 0);
                $point = (int)($_POST['point'] ?? 0);
                $db->execute("UPDATE WapUser SET money=?, point=? WHERE id=?", [$money, $point, $id]);
                $this->showSuccess('积分余额已调整', $back);
            } elseif ($action === 'delete') {
                $db->execute("DELETE FROM WapUser WHERE id=?", [$id]);
                $this->showSuccess('会员已删除', $back);
            } else {
                $this->showError('非法操作', $back);
            }
        }

        $this->strTitle = '会员管理';
        $this->showTop($this->strTitle);
        echo '<div class="subtitle">会员管理</div><div><a href="index.php">返回后台首页</a></div>';

        // 列表（分页）
        $page = max(1, (int)($_GET['page'] ?? 1));
        $result = $db->getPage('WapUser', 'id,username,nickname,money,point,islock,regtime,lastlogintime',
            '', 'id DESC', $page, 20);
        echo '<div class="content"><ul class="list">';
        foreach ($result['list'] as $u) {
            $uid = (int)$u['id'];
            $lockFlag = empty($u['islock']) ? '' : '<span style="color:red">[已锁定]</span>';
            echo '<li>ID:' . $uid . ' ' . $this->ToHtm($u['username']) . $lockFlag
                . ' 余额:' . (int)$u['money'] . ' 积分:' . (int)$u['point'] . '<br/>';
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
            // 删除
            echo '<form method="post" action="' . $back . '" style="display:inline">'
                . '<input type="hidden" name="action" value="delete"/><input type="hidden" name="id" value="' . $uid . '"/>'
                . '<input type="submit" value="删除"/></form>';
            echo '</li>';
        }
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

$page = new AdminUserPage();
$page->run();
