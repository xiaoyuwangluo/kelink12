<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 家族模块 - 家族详情与成员列表
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class ClanViewPage extends WapPage {
    public function run() {
        $db = $this->db;
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) $this->showError('参数错误');

        $clan = $db->getOne("SELECT * FROM WapClan WHERE id=? AND siteid=?", [$id, $this->siteid]);
        if (!$clan) $this->showError('家族不存在');

        $this->strTitle = $clan['name'];
        $this->showTop($this->strTitle);

        echo '<div class="subtitle">' . $this->ToHtm($clan['name']) . '</div>';
        echo '<div class="content">';
        echo '<div>家族简介：' . $this->ToHtm($clan['intro'] !== '' && $clan['intro'] !== null ? $clan['intro'] : '无') . '</div>';
        echo '<div>成员人数：' . (int)$clan['members'] . '</div>';
        echo '<div>家族积分：' . (int)$clan['point'] . '</div>';
        if (!empty($clan['addtime'])) {
            echo '<div>创建时间：' . date('Y-m-d H:i', strtotime($clan['addtime'])) . '</div>';
        }
        echo '</div>';

        // 成员列表 (族长在前)
        $members = $db->getAll(
            "SELECT userid,nickname,role,addtime FROM WapClanMember WHERE clanid=? ORDER BY role DESC, id ASC",
            [$id]
        );
        echo '<div class="subtitle">家族成员</div>';
        echo '<div class="content"><ul class="list">';
        foreach ($members as $m) {
            $role = (int)$m['role'] === 1 ? '[族长]' : '[成员]';
            echo '<li>' . $role . $this->ToHtm($m['nickname'] !== '' && $m['nickname'] !== null ? $m['nickname'] : '会员') . '</li>';
        }
        echo '</ul></div>';

        // 操作区
        $isMember = false;
        if ($this->userSession->isLogin()) {
            $uid = (int)$this->userSession->getUserId();
            $isMember = (bool)$db->getScalar("SELECT COUNT(*) FROM WapClanMember WHERE clanid=? AND userid=?", [$id, $uid]);
        }
        echo '<div class="actionbar">';
        if ($isMember) {
            echo '<a href="chat.php?siteid=' . $this->siteid . '&classid=' . $this->classid . '&id=' . $id . '">家族聊天</a> ';
        } elseif ($this->userSession->isLogin()) {
            echo '<a href="join.php?siteid=' . $this->siteid . '&classid=' . $this->classid . '&id=' . $id . '">加入家族</a> ';
        } else {
            echo '<a href="../waplogin.php?siteid=' . $this->siteid . '">登录后加入家族</a> ';
        }
        echo '<a href="index.php?siteid=' . $this->siteid . '&classid=' . $this->classid . '">返回列表</a>';
        echo '</div>';

        $this->showDown();
    }
}

$page = new ClanViewPage();
$page->run();
