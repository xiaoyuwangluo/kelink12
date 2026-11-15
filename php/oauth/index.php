<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * QQ 第三方登录 (对应原 OAuth 目录，QQ登录 stub)
 * 展示"QQ授权登录"链接 → 模拟回调 act=cb&openid=xxx：
 * openid 对应用户存在则直接登录，否则自动注册 qq_用户 并登录。
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class OauthPage extends WapPage {
    public function run() {
        $act = $_GET['act'] ?? '';
        if ($act === 'cb') {
            $this->doCallback();
            return;
        }
        $this->showTop($this->GetLang('QQ登录|QQ登录|QQ登录'));
        echo '<div class="subtitle">' . $this->GetLang('QQ授权登录(模拟)|QQ授权登录(模拟)|QQ授权登录(模拟)') . '</div>';
        echo '<div class="content">';
        // 模拟 QQ 授权：点击后以固定 openid 回调本页
        echo '<a href="index.php?siteid=' . $this->siteid . '&act=cb&openid=100000001">QQ授权登录</a><br/>';
        echo '<a href="index.php?siteid=' . $this->siteid . '&act=cb&openid=' . mt_rand(100000002, 999999999) . '">新用户QQ登录(模拟)</a>';
        echo '</div>';
        echo '<div class="content"><a href="../waplogin.php?siteid=' . $this->siteid . '">普通登录</a> | ';
        echo '<a href="../wapreg.php?siteid=' . $this->siteid . '">注册</a> | ';
        echo '<a href="../wapindex.php?siteid=' . $this->siteid . '">返回首页</a></div>';
        $this->showDown();
    }

    /** 模拟授权回调：openid → 登录或自动注册 */
    private function doCallback() {
        $openid = trim($_GET['openid'] ?? '');
        if ($openid === '' || !preg_match('/^\d{1,20}$/', $openid)) {
            $this->showError('无效的QQ授权参数', 'index.php?siteid=' . $this->siteid);
            return;
        }
        $username = 'qq_' . $openid;
        $db = $this->db;
        $row = $db->getOne("SELECT id, username FROM WapUser WHERE username=?", [$username]);
        if ($row) {
            $uid = (int)$row['id'];
        } else {
            // 自动注册 qq_用户 (随机密码，仅凭QQ授权登录)
            $uid = $db->insert(
                "INSERT INTO WapUser (username, password, nickname, email, regtime, regip, islock, money) VALUES (?,?,?,?,NOW(),?,0,0)",
                [$username, md5(uniqid('qq', true)), 'QQ用户' . $openid, '', UserSession::getClientIp()]
            );
        }
        // 写入会话完成登录
        $_SESSION['kl_userid'] = $uid;
        $_SESSION['kl_username'] = $username;
        header('Location: ../wapindex.php?siteid=' . $this->siteid);
        exit;
    }
}

$page = new OauthPage();
$page->run();
