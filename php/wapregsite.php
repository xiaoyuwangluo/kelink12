<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 建站申请 (对应原 WapRegSite.aspx)
 * 提交站名/管理员用户名/密码 → 创建站点 + 默认栏目(文章/论坛)
 */
require_once __DIR__ . '/includes/bootstrap.php';

class WapRegSitePage extends WapPage {
    public function run() {
        $this->showTop($this->GetLang('建站申请|建站申请|建站申请'));
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->doRegSite();
        } else {
            $this->showForm();
        }
        $this->showDown();
    }

    /** 申请表单 */
    private function showForm() {
        echo '<div class="subtitle">' . $this->GetLang('免费建站申请|免费建站申请|免费建站申请') . '</div>';
        echo '<div class="content"><form method="post" action="wapregsite.php?siteid=' . $this->siteid . '">';
        echo '站点名称:<input type="text" name="sitename" maxlength="50" required/><br/>';
        echo '管理员用户名:<input type="text" name="username" maxlength="30" required/><br/>';
        echo '管理员密码:<input type="password" name="password" required/><br/>';
        echo '<input type="submit" value="提交申请"/></form></div>';
        echo '<div class="tip">申请成功后系统自动开通文章、论坛两个默认栏目，请牢记您的站点ID。</div>';
    }

    /** 处理建站申请 */
    private function doRegSite() {
        // 防刷新提交
        if (!$this->userSession->checkSubmitLimit()) {
            echo '<div class="error">提交过于频繁，请稍后再试</div>';
            $this->showForm();
            return;
        }
        $sitename = trim($_POST['sitename'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        if ($sitename === '' || $username === '' || $password === '') {
            echo '<div class="error">请完整填写站点名称、管理员用户名和密码</div>';
            $this->showForm();
            return;
        }
        if (WapTool::checkSqlInject($sitename)) {
            echo '<div class="error">站点名称包含非法字符</div>';
            $this->showForm();
            return;
        }
        // 创建管理员会员
        list($ok, $msg) = $this->userSession->register($username, $password, $sitename, '');
        if (!$ok) {
            echo '<div class="error">' . $this->ToHtm($msg) . '</div>';
            $this->showForm();
            return;
        }
        // 创建站点
        $db = $this->db;
        $siteid = $db->insert("INSERT INTO WapSite (sitename, copyright) VALUES (?, ?)", [$sitename, KL_COPYRIGHT]);
        // 默认栏目：文章、论坛
        $db->insert("INSERT INTO WapClass (siteid, parentid, classname, module, orderid, isshow) VALUES (?,0,'文章栏目','article',1,1)", [$siteid]);
        $db->insert("INSERT INTO WapClass (siteid, parentid, classname, module, orderid, isshow) VALUES (?,0,'论坛','bbs',2,1)", [$siteid]);

        echo '<div class="success">建站申请成功！您的站点ID为：<b>' . (int)$siteid . '</b><br/>';
        echo '管理员账号：' . $this->ToHtm($username) . '<br/>';
        echo '请牢记站点ID，用于登录与后台管理。</div>';
        echo '<div class="content">';
        echo '<a href="wapindex.php?siteid=' . (int)$siteid . '">访问我的站点</a> | ';
        echo '<a href="waplogin.php?siteid=' . (int)$siteid . '">登录管理中心</a> | ';
        echo '<a href="wapindex.php?siteid=' . $this->siteid . '">返回首页</a></div>';
    }
}

$page = new WapRegSitePage();
$page->run();
