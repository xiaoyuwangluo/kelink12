<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 后台 - 采集管理 (对应原 caiji/Caiji.aspx)
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class AdminCaijiPage extends WapPage {
    public function run() {
        $this->checkAdminLogin();
        $db = $this->db;
        $msg = '';
        if (isset($_POST['act'])) {
            if (!$this->userSession->checkSubmitLimit()) { $msg = '<div class="error">操作过于频繁</div>'; }
            elseif ($_POST['act'] === 'add') {
                $name = trim($_POST['name'] ?? ''); $url = trim($_POST['url'] ?? ''); $classid = (int)($_POST['classid'] ?? 0);
                if ($name === '' || $url === '') $msg = '<div class="error">名称与地址必填</div>';
                else {
                    $db->insert("INSERT INTO WapCaiji(siteid,name,url,classid,addtime) VALUES (?,?,?,?,NOW())", [$this->siteid ?: 1, $name, $url, $classid]);
                    $msg = '<div class="success">添加成功</div>';
                }
            } elseif ($_POST['act'] === 'run') {
                $id = (int)$_POST['id'];
                $db->execute("UPDATE WapCaiji SET lastrun=NOW() WHERE id=? AND siteid=?", [$id, $this->siteid ?: 1]);
                $msg = '<div class="success">已触发采集任务 ' . $id . '（正式环境需对接远程采集源）</div>';
            } elseif ($_POST['act'] === 'del') {
                $db->execute("DELETE FROM WapCaiji WHERE id=? AND siteid=?", [(int)$_POST['id'], $this->siteid ?: 1]);
                $msg = '<div class="success">已删除</div>';
            }
        }
        $this->strTitle = '采集管理';
        $this->showTop('采集管理');
        echo $msg;
        $list = $db->getAll("SELECT * FROM WapCaiji WHERE siteid=? ORDER BY id DESC", [$this->siteid ?: 1]);
        echo '<div class="content"><ul class="list">';
        foreach ($list as $r) {
            echo '<li>#' . $r['id'] . ' ' . $this->ToHtm($r['name']) . ' → ' . $this->ToHtm($r['url'])
                . ' (栏目' . (int)$r['classid'] . '，最近:' . $this->ToHtm($r['lastrun'] ?: '未运行') . ') ';
            echo '<form method="post" style="display:inline"><input type="hidden" name="act" value="run"><input type="hidden" name="id" value="' . $r['id'] . '"><input type="submit" value="采集"></form> ';
            echo '<form method="post" style="display:inline" onsubmit="return confirm(\'删除?\')"><input type="hidden" name="act" value="del"><input type="hidden" name="id" value="' . $r['id'] . '"><input type="submit" value="删除"></form></li>';
        }
        echo '</ul>';
        echo '<div class="subtitle">新增采集源</div><form method="post">名称:<input name="name"/> 地址:<input name="url"/> 栏目ID:<input name="classid" size="3"/><input type="hidden" name="act" value="add"/><input type="submit" value="添加"/></form>';
        echo '<div class="pagenav"><a href="index.php">返回后台</a></div></div>';
        $this->showDown();
    }
}
$page = new AdminCaijiPage();
$page->run();
