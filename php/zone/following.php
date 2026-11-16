<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 会员空间 - 关注列表 (我关注的人)
 * ?uid= 查看指定用户的关注列表，未指定则查看自己(需登录)
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class ZoneFollowingPage extends WapPage {
    public function run() {
        $db = $this->db;
        $uid = (int)($_GET['uid'] ?? 0);

        if ($uid <= 0) {
            if (!$this->userSession->isLogin()) {
                $this->strTitle = '我关注的人';
                $this->showTop($this->strTitle);
                echo '<div class="content">请先登录后查看</div>';
                echo '<div><a href="../waplogin.php">去登录</a></div>';
                $this->showDown();
                return;
            }
            $uid = (int)$this->userSession->getUserId();
        }

        $user = $db->getOne("SELECT id,username,nickname FROM WapUser WHERE id=?", [$uid]);
        if (!$user) $this->showError('用户不存在');

        $isSelf = $this->userSession->isLogin() && (int)$this->userSession->getUserId() === $uid;
        $name = $user['nickname'] !== '' ? $user['nickname'] : $user['username'];
        $this->strTitle = ($isSelf ? '我关注的人' : $name . '关注的人');
        $this->showTop($this->strTitle);

        $total = (int)$db->getScalar("SELECT COUNT(*) FROM WapFollow WHERE userid=?",[$uid]);

        echo '<div class="subtitle">' . $this->ToHtm($this->strTitle) . '</div>';
        echo '<div class="content">共关注 ' . $total . ' 人</div>';

        $rows = $db->getAll("SELECT f.followuid, f.addtime, u.username, u.nickname
 FROM WapFollow f LEFT JOIN WapUser u ON u.id=f.followuid
 WHERE f.userid=? ORDER BY f.id DESC LIMIT 100",[$uid]);

        if (empty($rows)) {
            echo '<div class="content">暂无关注</div>';
        } else {
            echo '<div class="content"><ul class="list">';
            foreach ($rows as $r) {
                $rowName = ($r['nickname'] !== '' && $r['nickname'] !== null)
                    ? $r['nickname'] . '(' . $r['username'] . ')'
                    : ($r['username'] !== '' && $r['username'] !== null ? $r['username'] : '已注销用户');
                echo '<li><a href="index.php?uid=' . (int)$r['followuid'] . '">'
                    . $this->ToHtm($rowName) . '</a>';
                echo ' <span class="time">' . $this->ToHtm($r['addtime']) . '</span></li>';
            }
            echo '</ul></div>';
        }

        echo '<div class="actionbar"><a href="index.php?uid=' . $uid . '">返回空间</a></div>';
        $this->showDown();
    }
}

$page = new ZoneFollowingPage();
$page->run();
