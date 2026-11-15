<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * WAP注册 (对应原 WapReg.aspx)
 */
require_once __DIR__ . '/includes/bootstrap.php';
require_once KL_ROOT . '/includes/Captcha.php';

class WapRegPage extends WapPage {
    public function run() {
        $this->showTop($this->GetLang('注册|注册|Register'));
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->doReg();
        } else {
            $this->showForm();
        }
        $this->showDown();
    }

    private function showForm() {
        echo '<div class="subtitle">' . $this->GetLang('会员注册|会员注册|Register') . '</div>';
        echo '<div class="content"><form method="post" action="wapreg.php?siteid=' . $this->siteid . '">';
        echo '用户名:<input type="text" name="username" required/><br/>';
        echo '密 码:<input type="password" name="password" required/><br/>';
        echo '确认密码:<input type="password" name="password2" required/><br/>';
        echo '昵称:<input type="text" name="nickname"/><br/>';
        echo '邮箱:<input type="email" name="email"/><br/>';
        if (KL_RegUser_CheckCode == 1) {
            echo '验证码:<input type="text" name="checkcode" required/>'
                . '<img src="captcha_img.php?siteid=' . $this->siteid . '&t=' . time() . '" alt="验证码" '
                . 'onclick="this.src=\'captcha_img.php?siteid=' . $this->siteid . '&t=\'+Date.now()"/><br/>';
        }
        echo '<input type="submit" value="注册"/></form></div>';
    }

    private function doReg() {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $password2 = $_POST['password2'] ?? '';
        $nickname = $_POST['nickname'] ?? '';
        $email = $_POST['email'] ?? '';
        $checkcode = $_POST['checkcode'] ?? '';

        if ($password !== $password2) {
            echo '<div class="error">两次密码不一致</div>';
            $this->showForm();
            return;
        }
        if (KL_RegUser_CheckCode == 1 && !Captcha::verify($checkcode)) {
            echo '<div class="error">验证码错误</div>';
            $this->showForm();
            return;
        }
        list($ok, $msg) = $this->userSession->register($username, $password, $nickname, $email);
        if ($ok) {
            // 自动登录
            $this->userSession->login($username, $password);
            header('Location: wapindex.php?siteid=' . $this->siteid);
            exit;
        }
        echo '<div class="error">' . $this->ToHtm($msg) . '</div>';
        $this->showForm();
    }
}

$page = new WapRegPage();
$page->run();
