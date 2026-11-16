<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 会员空间 - 空间留言 (对应原 zone/msg.aspx)
 * 写 WapZoneMsg，受每日留言数限制(KL_CheckZoneReCount)
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class ZoneMsgPage extends WapPage {
    public function run() {
        $db = $this->db;
        $this->checkLogin();

        $zoneid = (int)($_REQUEST['zoneid'] ?? 0);
        if ($zoneid <= 0) $this->showError('参数错误');
        $zone = $db->getOne("SELECT * FROM WapZone WHERE id=?", [$zoneid]);
        if (!$zone) $this->showError('空间不存在');

        $this->strTitle = '空间留言';
        $this->showTop($this->strTitle);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->doMsg($zone);
        } else {
            $this->showForm($zone);
        }
        $this->showDown();
    }

    private function showForm($zone) {
        echo '<div class="subtitle">留言到:' . $this->ToHtm($zone['zonename']) . '</div>';
        echo '<div class="content"><form method="post" action="msg.php?zoneid=' . (int)$zone['id'] . '">';
        echo '昵称:' . $this->ToHtm($this->userSession->getUserName()) . '<br/>';
        echo '内容:<textarea name="content" rows="5" required></textarea><br/>';
        echo '<input type="submit" value="发表留言"/></form></div>';
        echo '<div><a href="index.php?uid=' . (int)$zone['userid'] . '">返回空间</a></div>';
    }

    private function doMsg($zone) {
        $db = $this->db;
        $uid = (int)$this->userSession->getUserId();
        $content = trim($_POST['content'] ?? '');
        if ($content === '') { $this->showForm($zone); echo '<div class="error">内容不能为空</div>'; return; }
        if (WapTool::checkSqlInject($content)) { $this->showForm($zone); echo '<div class="error">含非法字符</div>'; return; }
        // 防刷：提交间隔 + 每日空间留言上限
        if (!$this->userSession->checkSubmitLimit()) { $this->showForm($zone); echo '<div class="error">操作过于频繁，请稍后再试</div>'; return; }
        $todayCount = (int)$db->getScalar("SELECT COUNT(*) FROM WapZoneMsg WHERE userid=? AND DATE(addtime)=CURDATE()", [$uid]);
        if (KL_CheckZoneReCount > 0 && $todayCount >= KL_CheckZoneReCount) {
            $this->showForm($zone); echo '<div class="error">今日留言已达上限</div>'; return;
        }

        $db->insert("INSERT INTO WapZoneMsg (zoneid,userid,nickname,content,addtime) VALUES (?,?,?,?,NOW())",[(int)$zone['id'],$uid,$this->userSession->getUserName(),$content]);
        $this->showSuccess('留言成功', 'index.php?uid=' . (int)$zone['userid']);
    }
}

$page = new ZoneMsgPage();
$page->run();
