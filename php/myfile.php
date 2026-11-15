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
        echo '<a href="waplogout.php?siteid=' . $this->siteid . '">退出登录</a>';
        echo '</div>';
        $this->showDown();
    }
}
$page = new MyFilePage();
$page->run();
