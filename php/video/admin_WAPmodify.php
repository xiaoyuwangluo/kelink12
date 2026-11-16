<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 视频模块 - 后台修改视频 (对应原 video/admin_WAPmodify.aspx)
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class VideoModifyPage extends WapPage {
    public function run() {
        // 鉴权：后台管理员(kl_adminid)直接放行，否则走会员登录校验（未登录跳转）
        if (!$this->userSession->isAdminLogin()) {
            $this->checkLogin();
        }
        $db = $this->db;
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) $this->showError('参数错误');

        $video = $db->getOne("SELECT * FROM WapVideo WHERE id=?", [$id]);
        if (!$video) $this->showError('视频不存在');

        $this->showTop('修改视频');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->doModify($id);
        } else {
            $this->showForm($video);
        }
        $this->showDown();
    }

    private function showForm($a) {
        echo '<div class="subtitle">修改视频</div>';
        echo '<form method="post" action="admin_WAPmodify.php?classid=' . $this->classid . '&id=' . $a['id'] . '">';
        echo '标题:<input type="text" name="title" value="' . $this->ToHtm($a['title']) . '"/><br/>';
        echo '封面:<input type="text" name="picurl" value="' . $this->ToHtm($a['picurl'] ?? '') . '"/><br/>';
        echo '播放地址:<input type="text" name="videourl" value="' . $this->ToHtm($a['videourl'] ?? '') . '"/><br/>';
        echo '简介:<textarea name="content" rows="10">' . $this->ToHtm($a['content'] ?? '') . '</textarea><br/>';
        echo '<input type="submit" value="保存修改"/></form>';
    }

    private function doModify($id) {
        $db = $this->db;
        $title = trim($_POST['title'] ?? '');
        $content = $_POST['content'] ?? '';
        $picurl = trim($_POST['picurl'] ?? '');
        $videourl = trim($_POST['videourl'] ?? '');

        if ($title === '') {
            echo '<div class="error">标题不能为空</div>';
            return;
        }
        $db->execute(
            "UPDATE WapVideo SET title=?,content=?,picurl=?,videourl=? WHERE id=?",
            [$title, $content, $picurl, $videourl, $id]
        );
        echo '<div class="success">修改成功 <a href="view.php?classid=' . $this->classid . '&id=' . $id . '">查看</a></div>';
    }
}

$page = new VideoModifyPage();
$page->run();
