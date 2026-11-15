<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 帮助中心 (对应原 WebHelp.aspx)
 * 静态 UBB 帮助内容与常见问题
 */
require_once __DIR__ . '/includes/bootstrap.php';

class WebHelpPage extends WapPage {
    public function run() {
        $this->showTop($this->GetLang('帮助中心|帮助中心|帮助中心'));

        // 帮助正文 (UBB 内容，经 ToWML 解析输出)
        $help = "[b]小宇网络WAP建站系统 使用说明[/b]\n"
            . "[hr]\n"
            . "[url=wapregsite.php?siteid=" . $this->siteid . "]点击此处申请建站[/url]，填写站点名称、管理员用户名和密码即可免费开通WAP站点。\n"
            . "[list]\n"
            . "[*]建站成功后系统自动开通[文章]、[论坛]两个默认栏目\n"
            . "[*]会员登录后可在用户中心管理自己的站点栏目\n"
            . "[*]支持文章、论坛、商城、小说、视频、图片、下载等21个功能模块\n"
            . "[*]支持WAP1.0/WAP2.0/触屏版/电脑版多版本自动切换\n"
            . "[/list]\n"
            . "[hr]\n"
            . "[b]万能调用语法[/b]\n"
            . "[code][功能模块=N_M_X_Y][/code]\n"
            . "N=调用条数，M=模块ID(1文章 2论坛 3商城 4小说 5视频 6图片 7下载...)，X=站点ID，Y=栏目ID(0为不限)。\n"
            . "[hr]\n"
            . "[b]常见问题[/b]\n"
            . "[quote]问：忘记了登录密码怎么办？[/quote]\n"
            . "答：可通过[url=wapgetpw.php?siteid=" . $this->siteid . "]找回密码[/url]功能，用注册邮箱验证后将密码重置为123456。\n"
            . "[quote]问：如何访问我的站点？[/quote]\n"
            . "答：访问 [url=wapindex.php?siteid=站点ID]wapindex.php?siteid=站点ID[/url] 即可打开您的WAP站点首页。\n"
            . "[quote]问：注册提示用户名已存在？[/quote]\n"
            . "答：全站用户名唯一，请更换其他用户名后重新提交。\n"
            . "[quote]问：提交太快被拒绝？[/quote]\n"
            . "答：系统开启了防刷限制，请间隔" . KL_CheckIPTime . "秒后重新提交。";

        echo '<div class="content">' . $this->wapTool->ToWML($help, $this->wmlVo) . '</div>';
        echo '<div class="content"><a href="wapindex.php?siteid=' . $this->siteid . '">返回首页</a> | ';
        echo '<a href="webnews.php?siteid=' . $this->siteid . '">站点动态</a></div>';

        $this->showDown();
    }
}

$page = new WebHelpPage();
$page->run();
