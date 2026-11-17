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

        $class = $db->getOne("SELECT classname FROM WapClass WHERE id=?", [(int)$video['classid']]);
        $classname = $class['classname'] ?? '视频';

        $this->strTitle = $video['title'];
        $this->showTop($this->strTitle);
        echo '<div class="ui-breadcrumb"><a href="' . $this->http_start . 'wapindex.php">首页</a> / <a href="list.php?classid=' . $this->classid . '">' . $this->ToHtm($classname) . '</a> / 视频</div>';
        echo '<div class="subtitle">' . $this->ToHtm($video['title']) . '</div>';
        // 视频播放
        if (!empty($video['videourl'])) {
            echo '<div class="player ui-card">';
            echo '<video src="' . $this->ToHtm($video['videourl']) . '" controls="controls" width="100%"></video>';
            echo '</div>';
        }
        echo '<div class="ui-flex ui-flex-wrap ui-gap-2 ui-mt-2">';
        echo '<span class="ui-tag ui-tag-muted">点击 ' . ((int)$video['hits'] + 1) . '</span>';
        echo '<span class="ui-tag ui-tag-muted">' . $this->ToHtm($classname) . '</span>';
        echo '</div>';
        echo '<div class="content ui-card ui-mt-2">' . $this->wapTool->ToWML($video['content'], $this->wmlVo) . '</div>';
        echo '<div class="ui-flex ui-flex-wrap ui-gap-2 ui-mt-2"><a class="ui-btn ui-btn-primary" href="download.php?id=' . $id . '">下载视频</a>';
        echo '<a class="ui-btn ui-btn-ghost" href="list.php?classid=' . $this->classid . '">返回列表</a></div>';
        $this->showDown();
    }
}
$page = new VideoViewPage();
$page->run();
