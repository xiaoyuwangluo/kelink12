<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * UBB 预览接口 (对应原 ajax_ubb.aspx)
 * POST content → 输出 ToWML 解析后的预览结果，供前台 AJAX 调用
 */
require_once __DIR__ . '/includes/bootstrap.php';

class AjaxUbbPage extends WapPage {
    public function run() {
        // 仅接受 POST 内容，输出解析结果片段 (不输出页面头尾)
        $content = isset($_POST['content']) ? (string)$_POST['content'] : '';
        if ($content === '') return;
        echo $this->wapTool->ToWML($content, $this->wmlVo);
    }
}

$page = new AjaxUbbPage();
$page->run();
