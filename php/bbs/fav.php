<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 论坛模块 - 内容收藏/取消收藏 (对应原 bbs/fav.aspx)
 * 参数 module(模块名,默认bbs) + id(内容ID)，已收藏则取消，未收藏则添加
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class BbsFavPage extends WapPage {
    public function run() {
        $db = $this->db;
        $this->checkLogin();
        $uid = (int)$this->userSession->getUserId();

        $module = preg_replace('/[^a-z]/', '', strtolower($_GET['module'] ?? 'bbs'));
        if ($module === '') $module = 'bbs';
        $contentid = (int)($_GET['id'] ?? 0);
        if ($contentid <= 0) $this->showError('参数错误');

        $table = WapTool::$MODULE_TABLE[$module] ?? '';
        if ($table === '') $this->showError('不支持的收藏模块');
        $row = $db->getOne("SELECT id,title FROM $table WHERE id=?", [$contentid]);
        if (!$row) $this->showError('内容不存在');

        $backUrl = ($module === 'bbs')
            ? 'view.php?id=' . $contentid
            : '../' . $module . '/view.php?id=' . $contentid;

        $fav = $db->getOne("SELECT id FROM WapFav WHERE userid=? AND module=? AND contentid=?",
            [$uid, $module, $contentid]);
        if ($fav) {
            $db->execute("DELETE FROM WapFav WHERE id=?", [$fav['id']]);
            $this->showSuccess('已取消收藏', $backUrl);
        } else {
            $db->insert("INSERT INTO WapFav (userid,module,contentid,title,addtime) VALUES (?,?,?,?,NOW())",[$uid,$module,$contentid,$row['title']]);
            $this->showSuccess('收藏成功', 'favlist.php');
        }
    }
}

$page = new BbsFavPage();
$page->run();
