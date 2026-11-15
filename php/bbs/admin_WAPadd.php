<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 论坛模块 - 发表帖子 (对应原 bbs/admin_WAPadd.aspx)
 */
require_once __DIR__ . '/../includes/bootstrap.php';
require_once KL_ROOT . '/includes/Upload.php';
require_once KL_ROOT . '/includes/Community.php';

class BbsAddPage extends WapPage {
    public function run() {
        $this->checkLogin();
        $this->showTop('发表帖子');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->doAdd();
        } else {
            $this->showForm();
        }
        $this->showDown();
    }

    private function showForm() {
        echo '<div class="subtitle">发表帖子</div>';
        echo '<div class="content"><form method="post" enctype="multipart/form-data" action="admin_WAPadd.php?siteid=' . $this->siteid . '&classid=' . $this->classid . '">';
        echo '标题:<input type="text" name="title" required/><br/>';
        echo '内容:<textarea name="content" rows="8" required></textarea><br/>';
        echo '图片附件:<input type="file" name="attachfile"/><br/>';
        echo '<div class="time">单张最大 ' . Upload::formatSize(KL_UpMaxSize * 1024) . '，允许: ' . $this->ToHtm(KL_UpAllowExt) . '</div>';
        echo '<input type="submit" value="发表"/></form></div>';
    }

    private function doAdd() {
        $db = $this->db;
        $title = trim($_POST['title'] ?? '');
        $content = $_POST['content'] ?? '';
        if ($title === '' || $content === '') { echo '<div class="error">标题内容不能为空</div>'; $this->showForm(); return; }
        if (WapTool::checkSqlInject($title) || WapTool::checkSqlInject($content)) { echo '<div class="error">含非法字符</div>'; return; }
        if (!$this->userSession->checkSubmitLimit()) { echo '<div class="error">操作过于频繁</div>'; return; }

        $uid = $this->userSession->getUserId();
        $nick = $this->userSession->getUserName();

        // 每日发帖限制
        $todayCount = $db->getScalar("SELECT COUNT(*) FROM WapBbs WHERE userid=? AND DATE(addtime)=CURDATE()", [$uid]);
        if (KL_CheckBBSCount > 0 && $todayCount >= KL_CheckBBSCount) {
            echo '<div class="error">今日发帖已达上限</div>';
            return;
        }

        // 敏感词检测：命中则帖子仅自己/后台可见（isshow=0）并写审核记录
        $isSensitive = kl_has_sensitive($title . ' ' . $content, $this->db, $this->siteid);
        $isshow = $isSensitive ? 0 : 1;

        $id = $db->insert(
            "INSERT INTO WapBbs (siteid,classid,userid,nickname,title,content,istop,isgood,islock,isshow,addtime,hits,replynum) VALUES (?,?,?,?,?,?,0,0,0,?,NOW(),0,0)",
            [$this->siteid, $this->classid, $uid, $nick, $title, $content, $isshow]
        );
        if ($isSensitive) {
            kl_audit_add($db, $this->siteid, $uid, 'bbs', $id, $title, $content);
        }

        // 图片附件上传 (存文件 + 写 WapBbsFile)
        $fileCount = 0;
        $fileMsg = '';
        if (isset($_FILES['attachfile']) && $_FILES['attachfile']['error'] !== UPLOAD_ERR_NO_FILE) {
            list($ok, $msg, $info) = Upload::save($_FILES['attachfile']);
            if ($ok) {
                $db->insert("INSERT INTO WapBbsFile (siteid,bbsid,filename,filepath,filesize,addtime) VALUES (?,?,?,?,?,NOW())",
                    [$this->siteid, $id, $info['filename'], $info['relpath'], $info['size']]);
                $fileCount = 1;
            } else {
                $fileMsg = '（图片上传失败: ' . $msg . '）';
            }
        }

        echo '<div class="success">发表成功' . ($fileCount > 0 ? '，已上传附件 ' . $fileCount . ' 个' : '') . $this->ToHtm($fileMsg)
            . ($isSensitive ? '，内容含敏感词，待管理员审核' : '')
            . ' <a href="view.php?siteid=' . $this->siteid . '&classid=' . $this->classid . '&id=' . $id . '">查看</a></div>';
    }
}

$page = new BbsAddPage();
$page->run();
