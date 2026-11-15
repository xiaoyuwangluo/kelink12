<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 文章模块 - 管理首页 (对应原 article/index.aspx)
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class ArticleIndexPage extends WapPage {
    public function run() {
        $this->checkLogin();
        $this->showTop('文章管理');

        echo '<div class="subtitle">文章管理</div>';
        echo '<div class="content">';
        echo '<a href="admin_WAPadd.php?siteid=' . $this->siteid . '&classid=' . $this->classid . '">添加文章</a><br/>';
        echo '<a href="list.php?siteid=' . $this->siteid . '&classid=' . $this->classid . '">文章列表</a><br/>';
        echo '<a href="systemindex.php?siteid=' . $this->siteid . '&classid=' . $this->classid . '">栏目管理</a>';
        echo '</div>';
        $this->showDown();
    }
}

$page = new ArticleIndexPage();
$page->run();
