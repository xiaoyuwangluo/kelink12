<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 站点动态 (对应原 WebNews.aspx)
 * 系统公告(WapSysMsg)列表 + 文章模块最新5篇
 */
require_once __DIR__ . '/includes/bootstrap.php';

class WebNewsPage extends WapPage {
    public function run() {
        $this->showTop($this->GetLang('站点动态|站点动态|站点动态'));
        $db = $this->db;

        // 系统公告列表
        echo '<div class="subtitle">' . $this->GetLang('系统公告|系统公告|系统公告') . '</div>';
        if ($this->siteid > 0) {
            $msgs = $db->getAll(
                "SELECT id, title, content, addtime FROM WapSysMsg WHERE siteid=? ORDER BY id DESC LIMIT 20",
                [$this->siteid]
            );
        } else {
            $msgs = $db->getAll(
                "SELECT id, title, content, addtime FROM WapSysMsg ORDER BY id DESC LIMIT 20"
            );
        }
        echo '<div class="content"><ul class="list">';
        if (empty($msgs)) {
            echo '<li>暂无公告</li>';
        }
        foreach ($msgs as $m) {
            $time = !empty($m['addtime']) ? substr($m['addtime'], 0, 10) : '';
            echo '<li><a href="webnews.php?siteid=' . $this->siteid . '&id=' . (int)$m['id'] . '">' . $this->ToHtm($m['title']) . '</a>';
            if ($time !== '') echo ' <span class="time">[' . $this->ToHtm($time) . ']</span>';
            echo '</li>';
        }
        echo '</ul></div>';

        // 公告详情 (带 id 参数时)
        if (!empty($_GET['id'])) {
            $one = $db->getOne("SELECT id, title, content, addtime FROM WapSysMsg WHERE id=?", [(int)$_GET['id']]);
            if ($one) {
                echo '<div class="subtitle">' . $this->ToHtm($one['title']) . '</div>';
                echo '<div class="content">' . $this->wapTool->ToWML($one['content'] ?? '', $this->wmlVo) . '</div>';
            }
        }

        // 文章模块最新5篇 (万能调用: article 模块)
        echo '<div class="subtitle">' . $this->GetLang('最新文章|最新文章|最新文章') . '</div>';
        echo '<div class="content">' . $this->wapTool->callModule(5, 1, max(1, $this->siteid), 0, $this->wmlVo) . '</div>';

        echo '<div class="content"><a href="wapindex.php?siteid=' . $this->siteid . '">返回首页</a> | ';
        echo '<a href="webhelp.php?siteid=' . $this->siteid . '">帮助中心</a></div>';
        $this->showDown();
    }
}

$page = new WebNewsPage();
$page->run();
