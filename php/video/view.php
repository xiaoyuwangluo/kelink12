<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 视频模块 - 播放页 (对应原 video/view.aspx)
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class VideoViewPage extends WapPage {
    public function run() {
        $db = $this->db;
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) $this->showError('参数错误');
        $video = $db->getOne("SELECT * FROM WapVideo WHERE id=?",[$id]);
        if (!$video) $this->showError('视频不存在');
        $db->execute("UPDATE WapVideo SET hits=hits+1 WHERE id=?", [$id]);

        $this->strTitle = $video['title'];
        $this->showTop($this->strTitle);
        echo '<div class="subtitle">' . $this->ToHtm($video['title']) . '</div>';
        // 视频播放
        if (!empty($video['videourl'])) {
            echo '<div class="player">';
            echo '<video src="' . $this->ToHtm($video['videourl']) . '" controls="controls" width="100%"></video>';
            echo '</div>';
        }
        echo '<div class="content">' . $this->wapTool->ToWML($video['content'], $this->wmlVo) . '</div>';
        echo '<div><a href="download.php?id=' . $id . '">下载视频</a> | <a href="list.php?classid=' . $this->classid . '">返回列表</a></div>';
        $this->showDown();
    }
}
$page = new VideoViewPage();
$page->run();
