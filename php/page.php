<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 自定义页面 (对应原 PageModel/ShowClass.aspx)
 * ?id= 显示指定页面，否则列出本站全部自定义页
 */
require_once __DIR__ . '/includes/bootstrap.php';

class PageTplPage extends WapPage {
    public function run() {
        $db = $this->db;
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if ($id > 0) {
            $row = $db->getOne("SELECT * FROM WapPageTpl WHERE id=?",[$id]);
            if (!$row) $this->showError('页面不存在', 'page.php');
            $this->strTitle = $row['name'];
            $this->showTop($row['name']);
            // 内容经 ToWML 解析，支持 UBB 与万能调用 [功能模块=N_M_X_Y]
            echo '<div class="content">' . $this->wapTool->ToWML($row['content'], $this->wmlVo) . '</div>';
            echo '<div class="pagenav"><a href="wapindex.php">回首页</a></div>';
            $this->showDown();
            return;
        }

        $this->strTitle = '自定义页面';
        $this->showTop('自定义页面');
        $list = $db->getAll("SELECT id,name FROM WapPageTpl ORDER BY id DESC",[]);
        echo '<div class="content"><ul class="list">';
        if (!$list) echo '<li>暂无自定义页面</li>';
        foreach ($list as $row) {
            echo '<li><a href="page.php?id=' . $row['id'] . '">' . $this->ToHtm($row['name']) . '</a></li>';
        }
        echo '</ul></div>';
        $this->showDown();
    }
}
$page = new PageTplPage();
$page->run();
