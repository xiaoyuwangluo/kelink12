<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * album模块 - 内容页
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class AlbumViewPage extends WapPage {
    public function run() {
        $db = $this->db;
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) $this->showError('参数错误');
        $row = $db->getOne("SELECT * FROM WapAlbum WHERE id=?",[$id]);
        if (!$row) $this->showError('内容不存在');
        $db->execute("UPDATE WapAlbum SET hits=hits+1 WHERE id=?", [$id]);
        $this->strTitle = $row['title'];
        $this->showTop($this->strTitle);
        echo '<div class="subtitle">' . $this->ToHtm($row['title']) . '</div>';
        echo '<div class="info">时间:' . date('Y-m-d H:i', strtotime($row['addtime'])) . ' 点击:' . (int)$row['hits'] . '</div>';
        if (!empty($row['picurl'])) echo '<div class="pic"><img src="' . $this->ToHtm($row['picurl']) . '"/></div>';
        echo '<div class="content">' . $this->wapTool->ToWML($row['content'], $this->wmlVo) . '</div>';
        if (!empty($row['downurl'])) echo '<div class="download"><a href="' . $this->http_start . 'down.php?id=' . $id . '">下载</a></div>';
        echo '<div class="replybar"><a href="book_re.php?classid=' . $this->classid . '&id=' . $id . '">评论</a> ';
        echo '<a href="list.php?classid=' . $this->classid . '">返回列表</a></div>';
        $this->showDown();
    }
}
$page = new AlbumViewPage();
$page->run();
