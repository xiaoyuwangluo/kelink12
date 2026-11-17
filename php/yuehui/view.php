<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * yuehui模块 - 内容页
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class YuehuiViewPage extends WapPage {
    public function run() {
        $db = $this->db;
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) $this->showError('参数错误');
        $row = $db->getOne("SELECT * FROM WapYuehui WHERE id=?",[$id]);
        if (!$row) $this->showError('内容不存在');
        $db->execute("UPDATE WapYuehui SET hits=hits+1 WHERE id=?", [$id]);
        $classname = $this->classVo->classname ?? '交友活动';
        $this->strTitle = $row['title'];
        $this->showTop($this->strTitle);
        echo '<div class="ui-breadcrumb"><a href="' . $this->http_start . 'wapindex.php">首页</a> / <a href="list.php?classid=' . $this->classid . '">' . $this->ToHtm($classname) . '</a> / 详情</div>';
        echo '<div class="subtitle">' . $this->ToHtm($row['title']) . '</div>';
        echo '<div class="ui-card">';
        echo '<div class="ui-flex ui-flex-wrap ui-gap-2">';
        echo '<span class="ui-tag ui-tag-muted">' . date('Y-m-d H:i', strtotime($row['addtime'])) . '</span>';
        echo '<span class="ui-tag ui-tag-muted">点击 ' . ((int)$row['hits'] + 1) . '</span>';
        echo '<span class="ui-tag">' . $this->ToHtm($classname) . '</span>';
        echo '</div>';
        if (!empty($row['picurl'])) echo '<div class="pic ui-mt-2"><img src="' . $this->ToHtm($row['picurl']) . '"/></div>';
        echo '<div class="content ui-mt-2">' . $this->wapTool->ToWML($row['content'], $this->wmlVo) . '</div>';
        echo '</div>';
        echo '<div class="replybar ui-flex ui-flex-wrap ui-gap-2 ui-mt-2">';
        if (!empty($row['downurl'])) echo '<a class="ui-btn ui-btn-primary" href="' . $this->http_start . 'down.php?id=' . $id . '">下载</a>';
        echo '<a class="ui-btn" href="book_re.php?classid=' . $this->classid . '&id=' . $id . '">评论</a>';
        echo '<a class="ui-btn ui-btn-ghost" href="list.php?classid=' . $this->classid . '">返回列表</a></div>';
        $this->showDown();
    }
}
$page = new YuehuiViewPage();
$page->run();
