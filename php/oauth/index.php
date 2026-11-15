<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * QQ 第三方登录 (对应原 OAuth 目录)
 * KL_QQ_Login_Open=1 且 KL_QQ_AppID/KL_QQ_AppKey 配置齐全时走真实 QQ 互联流程：
 *   authorize → code 换 access_token → 获取 openid → get_user_info →
 *   按 qq_openid 匹配或自动注册并登录；act=bind 为当前登录用户绑定 QQ。
 * 开关关闭时保留模拟回调流程 (act=cb&openid=xxx)。
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class OauthPage extends WapPage {
    /** 是否启用真实 QQ 互联对接 */
    private function realMode() {
        return KL_QQ_Login_Open == 1
            && trim(KL_QQ_AppID) !== ''
            && trim(KL_QQ_AppKey) !== '';
    }

    /** 真实模式回调地址 */
    private function redirect_uri() {
        return $this->http_start . 'oauth/index.php?siteid=' . $this->siteid . '&act=cb';
    }

    public function run() {
        $act = $_GET['act'] ?? '';
        if ($act === 'cb') {
            $this->doCallback();
            return;
        }
        if ($act === 'bind') {
            $this->doBind();
            return;
        }
        if ($this->realMode()) {
            // 真实模式：直接进入 QQ 互联授权页
            $this->redirectToAuthorize('login');
            return;
        }
        // ===== 模拟入口 (原行为保留) =====
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

    /** 跳转 QQ 互联授权页 */
    private function redirectToAuthorize($mode) {
        $state = md5(uniqid('qq_state_', true) . mt_rand());
        $_SESSION['kl_qq_state'] = $state;
        $_SESSION['kl_qq_mode'] = $mode; // login|bind
        $url = 'https://graph.qq.com/oauth2.0/authorize?response_type=code'
             . '&client_id=' . urlencode(KL_QQ_AppID)
             . '&redirect_uri=' . urlencode($this->redirect_uri())
             . '&state=' . $state
             . '&scope=get_user_info'
             . '&display=mobile';
        header('Location: ' . $url);
        exit;
    }

    /** 绑定入口：需登录 */
    private function doBind() {
        if (!$this->userSession->isLogin()) {
            $backurl = urlencode('oauth/index.php?siteid=' . $this->siteid . '&act=bind');
            header('Location: ' . $this->http_start . 'waplogin.php?siteid=' . $this->siteid . '&backurl=' . $backurl);
            exit;
        }
        if ($this->realMode()) {
            $this->redirectToAuthorize('bind');
            return;
        }
        // 模拟绑定：点击后以随机 openid 回调并写入当前用户 qq_openid
        $this->showTop('绑定QQ');
        echo '<div class="content"><a href="index.php?siteid=' . $this->siteid
            . '&act=cb&openid=' . mt_rand(100000002, 999999999) . '&bind=1">模拟QQ授权并绑定</a></div>';
        echo '<div class="content"><a href="../myfile.php?siteid=' . $this->siteid . '">返回用户中心</a></div>';
        $this->showDown();
    }

    /** 生成唯一自动注册用户名 (前缀+openid摘要) */
    private function uniqueUsername($prefix, $openid) {
        $username = $prefix . substr(md5($openid), 0, 12);
        $i = 0;
        while ((int)$this->db->getScalar("SELECT COUNT(*) FROM WapUser WHERE username=?", [$username]) > 0) {
            $i++;
            $username = $prefix . substr(md5($openid . $i), 0, 12);
        }
        return $username;
    }

    /** 将 openid 绑定到当前登录用户 */
    private function saveBind($uid, $openid) {
        $other = $this->db->getOne("SELECT id FROM WapUser WHERE qq_openid=? AND id<>?", [$openid, $uid]);
        if ($other) {
            $this->showError('该QQ号已绑定其他账号', 'index.php?siteid=' . $this->siteid);
            return;
        }
        $this->db->execute("UPDATE WapUser SET qq_openid=? WHERE id=?", [$openid, $uid]);
        $this->showSuccess('QQ绑定成功', '../myfile.php?siteid=' . $this->siteid);
    }

    /** 回调分发 */
    private function doCallback() {
        if ($this->realMode()) {
            $this->doRealCallback();
            return;
        }
        $this->doMockCallback();
    }

    /** 模拟授权回调：openid → 登录或自动注册 (原流程保留) */
    private function doMockCallback() {
        $openid = trim($_GET['openid'] ?? '');
        if ($openid === '' || !preg_match('/^\d{1,20}$/', $openid)) {
            $this->showError('无效的QQ授权参数', 'index.php?siteid=' . $this->siteid);
            return;
        }
        // 模拟绑定模式：写入当前登录用户 qq_openid，不改变登录身份
        if (isset($_GET['bind']) && (int)$_GET['bind'] === 1) {
            if (!$this->userSession->isLogin()) {
                $this->showError('请先登录后再绑定QQ', '../waplogin.php?siteid=' . $this->siteid);
                return;
            }
            $this->saveBind((int)$this->userSession->getUserId(), $openid);
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

    /** 真实 QQ 互联授权回调：code → token → openid → userinfo → 登录/绑定 */
    private function doRealCallback() {
        $code = trim($_GET['code'] ?? '');
        $state = trim($_GET['state'] ?? '');
        $mode = (($_SESSION['kl_qq_mode'] ?? '') === 'bind') ? 'bind' : 'login';
        if ($code === '') {
            $this->showError('未获取到QQ授权码', 'index.php?siteid=' . $this->siteid);
            return;
        }
        if ($state === '' || !isset($_SESSION['kl_qq_state']) || $state !== $_SESSION['kl_qq_state']) {
            $this->showError('QQ授权state校验失败，请重试', 'index.php?siteid=' . $this->siteid);
            return;
        }
        unset($_SESSION['kl_qq_state'], $_SESSION['kl_qq_mode']);

        // 1. code 换 access_token
        $token_url = 'https://graph.qq.com/oauth2.0/token?grant_type=authorization_code'
                   . '&client_id=' . urlencode(KL_QQ_AppID)
                   . '&client_secret=' . urlencode(KL_QQ_AppKey)
                   . '&code=' . urlencode($code)
                   . '&redirect_uri=' . urlencode($this->redirect_uri());
        $resp = @file_get_contents($token_url, false, stream_context_create(['http' => ['timeout' => 5]]));
        if ($resp === false) {
            $this->showError('无法连接QQ互联服务器', 'index.php?siteid=' . $this->siteid);
            return;
        }
        $access_token = '';
        $json = json_decode($resp, true);
        if (is_array($json) && !empty($json['access_token'])) {
            $access_token = (string)$json['access_token'];
        } else {
            // 官方默认返回 form 格式: access_token=xxx&expires_in=7776000
            parse_str($resp, $form);
            $access_token = (string)($form['access_token'] ?? '');
        }
        if ($access_token === '') {
            $this->showError('QQ授权token获取失败', 'index.php?siteid=' . $this->siteid);
            return;
        }

        // 2. access_token 换 openid (JSONP 响应)
        $openid = '';
        $oid_resp = @file_get_contents('https://graph.qq.com/oauth2.0/me?access_token='
            . urlencode($access_token), false, stream_context_create(['http' => ['timeout' => 5]]));
        if ($oid_resp !== false && preg_match('/"openid"\s*:\s*"([^"]+)"/', $oid_resp, $m)) {
            $openid = $m[1];
        }
        if ($openid === '' || !preg_match('/^[A-Za-z0-9_\-]{8,64}$/', $openid)) {
            $this->showError('QQ openid获取失败', 'index.php?siteid=' . $this->siteid);
            return;
        }

        // 3. 拉取用户资料 (失败不阻断登录流程)
        $nickname = 'QQ用户';
        $ui_resp = @file_get_contents('https://graph.qq.com/user/get_user_info?access_token='
            . urlencode($access_token) . '&oauth_consumer_key=' . urlencode(KL_QQ_AppID)
            . '&openid=' . urlencode($openid), false, stream_context_create(['http' => ['timeout' => 5]]));
        if ($ui_resp !== false) {
            $ui = json_decode($ui_resp, true);
            if (is_array($ui) && (int)($ui['ret'] ?? -1) === 0 && !empty($ui['nickname'])) {
                $nickname = mb_substr((string)$ui['nickname'], 0, 30);
            }
        }

        // 4. 绑定模式：写入当前登录用户 qq_openid
        if ($mode === 'bind') {
            $uid = (int)$this->userSession->getUserId();
            if ($uid <= 0) {
                $this->showError('登录状态已失效，请重新登录后再绑定', '../waplogin.php?siteid=' . $this->siteid);
                return;
            }
            $this->saveBind($uid, $openid);
            return;
        }

        // 5. 登录模式：按 qq_openid 匹配，不存在则自动注册
        $db = $this->db;
        $row = $db->getOne("SELECT id, username FROM WapUser WHERE qq_openid=?", [$openid]);
        if ($row) {
            $uid = (int)$row['id'];
            $username = (string)$row['username'];
        } else {
            $username = $this->uniqueUsername('qq_', $openid);
            $uid = $db->insert(
                "INSERT INTO WapUser (username, password, nickname, email, regtime, regip, islock, money, qq_openid) VALUES (?,?,?,?,NOW(),?,0,0,?)",
                [$username, md5(uniqid('qq', true)), $nickname, '', UserSession::getClientIp(), $openid]
            );
        }
        $_SESSION['kl_userid'] = $uid;
        $_SESSION['kl_username'] = $username;
        header('Location: ../wapindex.php?siteid=' . $this->siteid);
        exit;
    }
}

$page = new OauthPage();
$page->run();
