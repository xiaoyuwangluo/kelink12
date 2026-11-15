<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 用户中心 (对应原 MyFile.aspx)
 */
require_once __DIR__ . '/includes/bootstrap.php';

class MyFilePage extends WapPage {
    public function run() {
        $this->checkLogin();
        $this->userVo = $this->userSession->getUserVo();
        $this->showTop($this->GetLang('用户中心|用户中心|User Center'));

        echo '<div class="subtitle">' . $this->GetLang('用户中心|用户中心|User Center') . '</div>';
        echo '<div class="content">';
        echo '用户名:' . $this->ToHtm($this->userVo['username'] ?? '') . '<br/>';
        echo '昵称:' . $this->ToHtm($this->userVo['nickname'] ?? '') . '<br/>';
        echo '余额:' . (int)($this->userVo['money'] ?? 0) . '<br/>';
        echo '积分:' . (int)($this->userVo['point'] ?? 0) . '<br/>';
        echo '</div>';

        echo '<div class="menu">';
        echo '<a href="modifyinfo.php?siteid=' . $this->siteid . '">修改资料</a><br/>';
        echo '<a href="modifypw.php?siteid=' . $this->siteid . '">修改密码</a><br/>';

        // 深度功能入口：签到/站内消息(未读数)/好友/收藏/空间
        $db = $this->db;
        $uid = (int)($this->userVo['id'] ?? $this->userSession->getUserId());
        $unread = (int)$db->getScalar("SELECT COUNT(*) FROM WapMessage WHERE toid=? AND isread=0", [$uid]);
        $signedToday = (int)$db->getScalar("SELECT COUNT(*) FROM WapSignin WHERE userid=? AND signdate=?", [$uid, date('Y-m-d')]);
        $friendNum = (int)$db->getScalar("SELECT COUNT(*) FROM WapFriend WHERE userid=? AND status=1", [$uid]);
        $favNum = (int)$db->getScalar("SELECT COUNT(*) FROM WapFav WHERE userid=?", [$uid]);
        echo '<a href="signin/index.php?siteid=' . $this->siteid . '">每日签到' . ($signedToday ? '(已签)' : '') . '</a><br/>';
        echo '<a href="message/index.php?siteid=' . $this->siteid . '">站内消息' . ($unread > 0 ? '(' . $unread . '未读)' : '') . '</a><br/>';
        echo '<a href="friend/index.php?siteid=' . $this->siteid . '">我的好友(' . $friendNum . ')</a><br/>';
        echo '<a href="bbs/favlist.php?siteid=' . $this->siteid . '">我的收藏(' . $favNum . ')</a><br/>';
        echo '<a href="zone/index.php?siteid=' . $this->siteid . '&uid=' . $uid . '">我的空间</a><br/>';

        // 第三方账号绑定状态 (微信 wx_openid / QQ qq_openid)
        $wxBind = trim((string)($this->userVo['wx_openid'] ?? '')) !== '';
        $qqBind = trim((string)($this->userVo['qq_openid'] ?? '')) !== '';
        echo '微信:' . ($wxBind ? '已绑定' : '未绑定') . ' | QQ:' . ($qqBind ? '已绑定' : '未绑定') . '<br/>';
        echo '<a href="oauth/index.php?siteid=' . $this->siteid . '&act=bind">绑定微信/绑定QQ</a><br/>';

        echo '<a href="waplogout.php?siteid=' . $this->siteid . '">退出登录</a>';
        echo '</div>';
        $this->showDown();
    }
}
$page = new MyFilePage();
$page->run();
