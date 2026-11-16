<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 家族模块 - 创建家族 (族长=创建者)
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class ClanCreatePage extends WapPage {
    public function run() {
        $this->checkLogin();
        $this->showTop('创建家族');

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            $this->doCreate();
        } else {
            $this->showForm();
        }
        $this->showDown();
    }

    private function showForm() {
        echo '<div class="subtitle">创建家族</div>';
        echo '<div class="content"><form method="post" action="create.php?classid=' . $this->classid . '">';
        echo '家族名称:<input type="text" name="name" maxlength="50" required/><br/>';
        echo '家族简介:<textarea name="intro" rows="4" maxlength="500"></textarea><br/>';
        echo '<input type="submit" value="创建"/>';
        echo '</form></div>';
        echo '<div class="actionbar"><a href="index.php?classid=' . $this->classid . '">返回列表</a></div>';
    }

    private function doCreate() {
        $db = $this->db;
        $name = trim($_POST['name'] ?? '');
        $intro = trim($_POST['intro'] ?? '');

        if ($name === '' || mb_strlen($name) > 50) {
            echo '<div class="error">家族名称不能为空且不超过50字</div>';
            $this->showForm();
            return;
        }
        if (mb_strlen($intro) > 500) {
            echo '<div class="error">家族简介不能超过500字</div>';
            $this->showForm();
            return;
        }
        if (WapTool::checkSqlInject($name) || WapTool::checkSqlInject($intro)) {
            echo '<div class="error">包含非法字符</div>';
            return;
        }
        if (!$this->userSession->checkSubmitLimit()) {
            echo '<div class="error">操作过于频繁，请稍后再试</div>';
            return;
        }
        if ($db->getScalar("SELECT COUNT(*) FROM WapClan WHERE name=?",[$name])) {
            echo '<div class="error">该家族名称已存在</div>';
            $this->showForm();
            return;
        }

        $uid = (int)$this->userSession->getUserId();
        $nick = $this->userVo['nickname'] ?? '';
        if ($nick === '' || $nick === null) $nick = $this->userVo['username'] ?? '会员';

        // 创建家族，族长即创建者，成员数记1
        $clanid = $db->insert("INSERT INTO WapClan (userid,name,intro,members,point,addtime) VALUES (?,?,?,1,0,NOW())",[$uid,$name,$intro]);
        $db->insert(
            "INSERT INTO WapClanMember (clanid,userid,nickname,role,addtime) VALUES (?,?,?,1,NOW())",
            [$clanid, $uid, $nick]
        );
        header('Location: view.php?classid=' . $this->classid . '&id=' . $clanid);
        exit;
    }
}

$page = new ClanCreatePage();
$page->run();
