<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 全站搜索 (对应原 Search.aspx)
 */
require_once __DIR__ . '/includes/bootstrap.php';

class SearchPage extends WapPage {
    public function run() {
        if (KL_search_need_login == 1) {
            $this->checkLogin();
        }
        $keyword = trim($_GET['keyword'] ?? '');
        $this->showTop($this->GetLang('搜索|搜索|Search'));

        echo '<div class="subtitle">' . $this->GetLang('搜索|搜索|Search') . '</div>';
        echo '<div class="searchbar"><form method="get" action="search.php">';
        echo '<input type="hidden" name="siteid" value="' . $this->siteid . '"/>';
        echo '<input type="text" name="keyword" value="' . $this->ToHtm($keyword) . '"/>';
        echo '<input type="submit" value="搜索"/></form></div>';

        if ($keyword !== '') {
            if (WapTool::checkSqlInject($keyword)) {
                echo '<div class="error">关键词含非法字符</div>';
            } else {
                // 搜索多个模块
                $tables = ['WapArticle' => 'article', 'WapBbs' => 'bbs', 'WapShop' => 'shop', 'WapNovel' => 'novel', 'WapVideo' => 'video'];
                echo '<div class="content">';
                foreach ($tables as $table => $module) {
                    $rows = $this->db->getAll(
                        "SELECT id,title FROM $table WHERE siteid=? AND title LIKE ? AND isshow=1 ORDER BY id DESC LIMIT 10",
                        [$this->siteid, '%' . $keyword . '%']
                    );
                    if ($rows) {
                        echo '<div class="searchgroup"><b>' . $module . '</b><ul>';
                        foreach ($rows as $r) {
                            echo '<li><a href="' . $module . '/view.php?siteid=' . $this->siteid . '&id=' . $r['id'] . '">' . $this->ToHtm($r['title']) . '</a></li>';
                        }
                        echo '</ul></div>';
                    }
                }
                echo '</div>';
            }
        }
        $this->showDown();
    }
}
$page = new SearchPage();
$page->run();
