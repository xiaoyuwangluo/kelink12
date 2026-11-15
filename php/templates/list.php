<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 模板展示页 (对应原 WapStyle/Lang.aspx)
 * 列出全部模板，高亮当前站点所用模板
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class TemplateListPage extends WapPage {
    public function run() {
        $db = $this->db;
        $this->strTitle = '模板中心';
        $this->showTop('模板中心');

        $curTpl = $this->siteVo->template ?? 'default';
        $list = $db->getAll("SELECT * FROM WapTemplate ORDER BY id DESC", []);
        echo '<div class="content"><ul class="list">';
        foreach ($list as $row) {
            $name = $this->ToHtm($row['name']);
            if ($row['dir'] === $curTpl) {
                echo '<li><strong>' . $name . '</strong>（当前使用）</li>';
            } else {
                echo '<li>' . $name . ' — <a href="' . $this->http_start . 'wapindex.php?siteid=' . $this->siteid . '&ver=4">预览触屏效果</a></li>';
            }
        }
        echo '</ul>';
        echo '<div class="pagenav"><a href="../wapindex.php?siteid=' . $this->siteid . '">返回首页</a></div></div>';
        $this->showDown();
    }
}
$page = new TemplateListPage();
$page->run();
