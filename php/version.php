<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 版本信息 (对应原 Version.aspx)
 */
require_once __DIR__ . '/includes/bootstrap.php';

class VersionPage extends WapPage {
    public function run() {
        $this->updateTime = KL_Version;
        $this->latest = '0';
        $this->needUpdate = false;

        $this->showTop($this->GetLang('版本信息|版本信息|Version Info'));

        if ($this->ver == '1') {
            echo '<p>';
            if ($this->classid == '0') {
                echo '当前版本号:<b>' . $this->updateTime . '</b><br/>';
                echo '最新版本号:<b>' . $this->latest . '</b><br/>';
                echo ($this->needUpdate ? '需要升级，' : '暂无更新，');
                echo '<a href="http://www.xiaoyu.com/download/update.php">查看官方升级</a><br/>';
                echo $this->wapTool->ToWML($this->KL_Site_INFO, $this->wmlVo) . '<br/>';
                echo '----------<br/>';
                echo '小宇网络 WAP建站系统 PHP版<br/>';
                echo '----------<br/>';
            }
            echo 'PHP版本:' . phpversion() . '<br/>';
            echo '服务器IP:' . ($_SERVER['SERVER_ADDR'] ?? '') . '<br/>';
            echo 'IP 地址:' . UserSession::getClientIp() . '<br/>';
            echo '手机型号:' . ($_SERVER['HTTP_USER_AGENT'] ?? '') . '<br/>';
            echo '</p>';
        } else {
            echo '<div class="subtitle">' . $this->GetLang('版本信息|版本信息|Version Info') . '</div>';
            echo '<div class="content">';
            if ($this->classid == '0') {
                echo '当前版本号:<b>' . $this->updateTime . '</b><br/>';
                echo '最新版本号:<b>' . $this->latest . '</b><br/>';
                echo ($this->needUpdate ? '需要升级，' : '暂无更新，');
                echo '<a href="http://www.xiaoyu.com/download/update.php">查看官方升级</a><br/>';
                echo $this->wapTool->ToWML($this->KL_Site_INFO, $this->wmlVo) . '<br/>';
                echo '<hr/>';
                echo '小宇网络 WAP建站系统 PHP版<br/>';
                echo '<hr/>';
            }
            echo 'PHP版本:' . phpversion() . '<br/>';
            echo '服务器IP:' . ($_SERVER['SERVER_ADDR'] ?? '') . '<br/>';
            echo 'IP 地址:' . UserSession::getClientIp() . '<br/>';
            echo '手机型号:' . ($_SERVER['HTTP_USER_AGENT'] ?? '') . '<br/>';
            echo '</div>';
        }
        $this->showDown();
    }
}
$page = new VersionPage();
$page->run();
