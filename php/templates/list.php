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
                echo '<li>' . $name . ' — <a href="' . $this->http_start . 'wapindex.php?ver=4">预览触屏效果</a></li>';
            }
        }
        echo '</ul>';

        // 内置模板（按 templates/*/style.css 扫描，便于未入库时也能识别）
        $labels = ['default' => '默认模板（PC 简洁）', 'touch' => '触屏模板（移动深色）', 'modern' => '现代模板（移动卡片）'];
        $dirs = glob(KL_ROOT . '/templates/*/style.css') ?: [];
        echo '<div class="subtitle">内置模板</div><ul class="list">';
        $found = 0;
        foreach ($dirs as $file) {
            $dir = basename(dirname($file));
            if (!preg_match('/^[a-z0-9_\-]+$/i', $dir)) continue;
            $found++;
            $name = $this->ToHtm($labels[$dir] ?? $dir);
            if ($dir === $curTpl) {
                echo '<li><strong>' . $name . '</strong>（当前使用）</li>';
            } else {
                echo '<li>' . $name . ' — <a href="' . $this->http_start . 'wapindex.php">查看站点</a></li>';
            }
        }
        if ($found === 0) echo '<li>未发现内置模板</li>';
        echo '</ul>';
        echo '<div class="pagenav"><a href="../wapindex.php">返回首页</a></div></div>';
        $this->showDown();
    }
}
$page = new TemplateListPage();
$page->run();
