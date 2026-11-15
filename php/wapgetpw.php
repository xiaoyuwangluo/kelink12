<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 找回密码 (对应原 WapGetPW.aspx)
 * 用户名 + 注册邮箱匹配 → 重置密码为 123456
 */
require_once __DIR__ . '/includes/bootstrap.php';

class WapGetPwPage extends WapPage {
    public function run() {
        $this->showTop($this->GetLang('找回密码|找回密码|找回密码'));
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->doGetPw();
        } else {
            $this->showForm();
        }
        $this->showDown();
    }

    /** 找回表单 */
    private function showForm() {
        echo '<div class="subtitle">' . $this->GetLang('找回密码|找回密码|找回密码') . '</div>';
        echo '<div class="content"><form method="post" action="wapgetpw.php?siteid=' . $this->siteid . '">';
        echo '用户名:<input type="text" name="username" required/><br/>';
        echo '注册邮箱:<input type="text" name="email" required/><br/>';
        echo '<input type="submit" value="确认找回"/></form></div>';
        echo '<div class="tip">验证通过后将把密码重置为 123456，请尽快登录并修改密码。</div>';
    }

    /** 处理找回密码 */
    private function doGetPw() {
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        if ($username === '' || $email === '') {
            echo '<div class="error">请填写用户名和注册邮箱</div>';
            $this->showForm();
            return;
        }
        if (WapTool::checkSqlInject($username) || WapTool::checkSqlInject($email)) {
            echo '<div class="error">包含非法字符</div>';
            $this->showForm();
            return;
        }
        $db = $this->db;
        $row = $db->getOne("SELECT id, email FROM WapUser WHERE username=? AND email<>'' AND email=?", [$username, $email]);
        if (!$row) {
            echo '<div class="error">用户名与注册邮箱不匹配，找回失败</div>';
            $this->showForm();
            return;
        }
        // 重置密码为 123456
        $db->execute("UPDATE WapUser SET password=? WHERE id=?", [md5('123456'), $row['id']]);
        echo '<div class="success">验证成功！您的密码已重置为：<b>123456</b><br/>请立即登录并修改密码。</div>';
        echo '<div class="content"><a href="waplogin.php?siteid=' . $this->siteid . '">前往登录</a> | ';
        echo '<a href="wapindex.php?siteid=' . $this->siteid . '">返回首页</a></div>';
    }
}

$page = new WapGetPwPage();
$page->run();
