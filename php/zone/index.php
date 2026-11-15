<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 会员空间 - 空间首页 (对应原 zone/index.aspx)
 * ?uid= 查看用户信息、其文章/帖子、空间留言列表
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class ZoneIndexPage extends WapPage {
    public function run() {
        $db = $this->db;
        $uid = (int)($_GET['uid'] ?? 0);
        if ($uid <= 0) {
            // 未指定则访问自己的空间（需登录）
            $this->checkLogin();
            $uid = (int)$this->userSession->getUserId();
        }

        $user = $db->getOne("SELECT * FROM WapUser WHERE id=?", [$uid]);
        if (!$user) $this->showError('用户不存在');

        // 空间不存在则自动开通
        $zone = $db->getOne("SELECT * FROM WapZone WHERE userid=?", [$uid]);
        if (!$zone) {
            $zonename = ($user['nickname'] !== '' ? $user['nickname'] : $user['username']) . '的空间';
            $zid = $db->insert("INSERT INTO WapZone (siteid,userid,zonename,intro,pic,addtime) VALUES (?,?,?,?,?,NOW())",
                [$this->siteid, $uid, $zonename, '', '']);
            $zone = $db->getOne("SELECT * FROM WapZone WHERE id=?", [$zid]);
        }

        $this->strTitle = $zone['zonename'];
        $this->showTop($this->strTitle);
        echo '<div class="subtitle">' . $this->ToHtm($zone['zonename']) . '</div>';

        // 会员信息
        echo '<div class="content">';
        echo '用户名:' . $this->ToHtm($user['username']) . '<br/>';
        echo '昵称:' . $this->ToHtm($user['nickname']) . '<br/>';
        echo '积分:' . (int)$user['point'] . ' 余额:' . (int)$user['money'] . '<br/>';
        echo '注册时间:' . $this->ToHtm($user['regtime']) . '<br/>';
        if (!empty($zone['intro'])) echo '简介:' . $this->ToHtm($zone['intro']) . '<br/>';
        echo '</div>';

        // TA的文章
        $arts = $db->getAll("SELECT id,title,addtime FROM WapArticle WHERE userid=? AND isshow=1 ORDER BY id DESC LIMIT 10", [$uid]);
        echo '<div class="subtitle">TA的文章</div><div class="content">';
        if (empty($arts)) {
            echo '暂无文章';
        } else {
            foreach ($arts as $a) {
                echo '<a href="../article/view.php?siteid=' . $this->siteid . '&id=' . (int)$a['id'] . '">' . $this->ToHtm($a['title']) . '</a>';
                echo ' <span class="time">' . $this->ToHtm(substr($a['addtime'], 0, 10)) . '</span><br/>';
            }
        }
        echo '</div>';

        // TA的帖子
        $threads = $db->getAll("SELECT id,title,addtime FROM WapBbs WHERE userid=? AND isshow=1 ORDER BY id DESC LIMIT 10", [$uid]);
        echo '<div class="subtitle">TA的帖子</div><div class="content">';
        if (empty($threads)) {
            echo '暂无帖子';
        } else {
            foreach ($threads as $t) {
                echo '<a href="../bbs/view.php?siteid=' . $this->siteid . '&id=' . (int)$t['id'] . '">' . $this->ToHtm($t['title']) . '</a>';
                echo ' <span class="time">' . $this->ToHtm(substr($t['addtime'], 0, 10)) . '</span><br/>';
            }
        }
        echo '</div>';

        // 空间留言列表
        $msgs = $db->getAll("SELECT * FROM WapZoneMsg WHERE zoneid=? ORDER BY id DESC LIMIT 20", [$zone['id']]);
        echo '<div class="subtitle">空间留言</div><div class="content">';
        if (empty($msgs)) {
            echo '暂无留言';
        } else {
            foreach ($msgs as $m) {
                echo '<div class="reply">' . $this->ToHtm($m['nickname']) . ' ' . $this->ToHtm($m['addtime']) . '<br/>'
                    . $this->wapTool->ToWML($m['content'], $this->wmlVo) . '</div>';
            }
        }
        echo '</div>';

        echo '<div class="actionbar"><a href="msg.php?siteid=' . $this->siteid . '&zoneid=' . (int)$zone['id'] . '">给TA留言</a> ';
        echo '<a href="../myfile.php?siteid=' . $this->siteid . '">返回用户中心</a></div>';
        $this->showDown();
    }
}

$page = new ZoneIndexPage();
$page->run();
