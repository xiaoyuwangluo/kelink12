<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 实用工具箱 (对应原 Utility/ 目录核心功能)
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class UtilityPage extends WapPage {
    public function run() {
        $this->strTitle = '实用工具箱';
        $this->showTop('实用工具箱');
        echo '<div class="content">';
        // MD5 转换 (对应原 PwdTool)
        echo '<div class="subtitle">MD5 加密转换</div><form method="post">';
        echo '<input type="hidden" name="act" value="md5"/><input name="txt"/> <input type="submit" value="转换"/><br/>';
        if (($_POST['act'] ?? '') === 'md5' && isset($_POST['txt'])) {
            echo '结果: <code>' . md5($_POST['txt']) . '</code><br/>';
        }
        echo '</form>';
        // UBB 预览 (对应原 UBB 编辑器)
        echo '<div class="subtitle">UBB 代码预览</div><form method="post">';
        echo '<input type="hidden" name="act" value="ubb"/><textarea name="ubb" rows="4" style="width:90%">[b]粗体[/b] [url=http://localhost]链接[/url]</textarea><br/><input type="submit" value="预览"/></form>';
        if (($_POST['act'] ?? '') === 'ubb' && isset($_POST['ubb'])) {
            echo '<div class="callmodule">' . $this->wapTool->ToWML($_POST['ubb'], $this->wmlVo) . '</div>';
        }
        // 系统信息 (对应原 SysInfo)
        echo '<div class="subtitle">系统信息</div><ul class="list">';
        echo '<li>PHP版本: ' . PHP_VERSION . '</li>';
        echo '<li>数据库类型: ' . KL_DB_TYPE . '</li>';
        echo '<li>系统版本: ' . KL_Version . '</li>';
        echo '<li>服务器时间: ' . date('Y-m-d H:i:s') . '</li>';
        $online = $this->wapTool->onlineCount();
        echo '<li>当前在线: ' . $online . ' 人</li>';
        echo '</ul></div>';
        $this->showDown();
    }
}
$page = new UtilityPage();
$page->run();
