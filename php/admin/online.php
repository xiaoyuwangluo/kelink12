<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 管理后台 - 在线用户 (对应原 WapOnline 在线人数统计)
 * 在线用户列表
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class AdminOnlinePage extends WapPage {
    public function run() {
        $this->checkAdminLogin();
        $db = $this->db;

        $this->strTitle = '在线用户';
        $this->showTop($this->strTitle);
        echo '<div class="subtitle">在线用户</div><div><a href="index.php">返回后台首页</a></div>';

        // 列出全部在线记录
        $where = '1=1';
        $params = [];
        $list = $db->getAll("SELECT * FROM WapOnline WHERE " . $where . " ORDER BY lasttime DESC", $params);

        echo '<div class="content"><div class="info">当前在线：' . count($list) . ' 人</div><ul class="list">';
        foreach ($list as $row) {
            $who = intval($row['userid'] ?? 0) > 0 ? $this->ToHtm($row['username'] ?? '') : '游客';
            $time = !empty($row['lasttime']) ? date('m-d H:i:s', strtotime($row['lasttime'])) : '';
            echo '<li>ID:' . (int)$row['id'] . ' ' . $who . ' (' . $this->ToHtm($row['ip'] ?? '') . ')'
                . ' 页面:' . $this->ToHtm($row['page'] ?? '') . ' <span class="time">' . $time . '</span></li>';
        }
        if (empty($list)) echo '<li>当前无人在线</li>';
        echo '</ul></div>';
        echo '<div><a href="online.php">刷新</a></div>';

        $this->showDown();
    }
}

$page = new AdminOnlinePage();
$page->run();
