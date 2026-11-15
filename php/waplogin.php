<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * WAP登录 (对应原 WapLogin.aspx)
 */
require_once __DIR__ . '/includes/bootstrap.php';

class WapLoginPage extends WapPage {
    public function run() {
        $this->showTop($this->GetLang('登录|登录|Login'));
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->doLogin();
        } else {
            $this->showForm();
        }
        $this->showDown();
    }

    private function showForm() {
        $backurl = $_GET['backurl'] ?? '';
        echo '<div class="subtitle">' . $this->GetLang('会员登录|会员登录|Member Login') . '</div>';
        echo '<div class="content">';
        echo '<form method="post" action="waplogin.php?siteid=' . $this->siteid . '">';
        echo '<input type="hidden" name="backurl" value="' . $this->ToHtm($backurl) . '"/>';
        echo '用户名:<input type="text" name="username" required/><br/>';
        echo '密 码:<input type="password" name="password" required/><br/>';
        if (KL_RegUser_CheckCode == 1) {
            $code = WapTool::makeCheckCode();
            $_SESSION['kl_login_code'] = $code;
            echo '验证码:<input type="text" name="checkcode"/> <span>' . $code . '</span><br/>';
        }
        echo '<input type="submit" value="' . $this->GetLang('登录|登录|Login') . '"/>';
        echo ' <a href="wapreg.php?siteid=' . $this->siteid . '">' . $this->GetLang('注册|注册|Register') . '</a>';
        echo '</form></div>';
        // QQ登录入口 (仅 KL_QQ_Login_Open=1 时显示)
        if (KL_QQ_Login_Open == 1) {
            echo '<div class="content"><a href="oauth/index.php?siteid=' . $this->siteid . '">QQ登录</a></div>';
        }
    }

    private function doLogin() {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        $checkcode = $_POST['checkcode'] ?? '';
        $backurl = $_POST['backurl'] ?? '';

        if (KL_RegUser_CheckCode == 1) {
            if ($checkcode !== ($_SESSION['kl_login_code'] ?? '')) {
                echo '<div class="error">验证码错误</div>';
                $this->showForm();
                return;
            }
        }
        // 防刷新
        if (!$this->userSession->checkSubmitLimit()) {
            echo '<div class="error">操作过于频繁，请稍后再试</div>';
            $this->showForm();
            return;
        }
        list($ok, $msg, $user) = $this->userSession->login($username, $password);
        if ($ok) {
            $url = $backurl ? $backurl : 'wapindex.php?siteid=' . $this->siteid;
            header('Location: ' . $url);
            exit;
        } else {
            echo '<div class="error">' . $this->ToHtm($msg) . '</div>';
            $this->showForm();
        }
    }
}

$page = new WapLoginPage();
$page->run();
