<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 管理后台 - 统一内容管理 (对应原各模块 admin 审核页)
 * ?module=xxx 分页列出该模块表内容，操作：显示/隐藏(isshow)、删除、置顶(有istop列的表)
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class AdminContentPage extends WapPage {
    /** 模块 => 数据表 白名单（内容型模块 + 扩展模块） */
    private $moduleTable = [
        'article' => 'WapArticle', 'bbs' => 'WapBbs', 'shop' => 'WapShop', 'novel' => 'WapNovel',
        'video' => 'WapVideo', 'picture' => 'WapPicture', 'download' => 'WapDownload', 'ring' => 'WapRing',
        'link' => 'WapLink', 'guessbook' => 'WapGuestbook', 'gongqiu' => 'WapGongqiu', 'paimai' => 'WapPaimai',
        'yuehui' => 'WapYuehui', 'wabao' => 'WapWabao', 'guangbo' => 'WapGuangbo', 'adlink' => 'WapAdlink',
        'airplane' => 'WapAirplane', 'album' => 'WapAlbum', 'hotel' => 'WapHotel', 'rizhi' => 'WapRizhi',
        'xinzhang' => 'WapXinzhang', 'chat' => 'WapChatSpeak', 'clan' => 'WapClan', 'games' => 'WapGames',
        'zhdao' => 'WapZhiDao', 'nongchang' => 'WapNongChang', 'signin' => 'WapSignin', 'form' => 'WapFormData',
    ];

    /** 获取表实际列名（表名来自白名单，无注入风险） */
    private function getColumns($table) {
        $cols = [];
        if ($this->db->getDbType() === 'sqlite') {
            foreach ($this->db->getAll("PRAGMA table_info(" . $table . ")") as $c) {
                $cols[] = $c['name'];
            }
        } else {
            $stmt = $this->db->query("SELECT * FROM " . $table . " WHERE 1=0");
            for ($i = 0; $i < $stmt->columnCount(); $i++) {
                $meta = $stmt->getColumnMeta($i);
                if (!empty($meta['name'])) $cols[] = $meta['name'];
            }
        }
        return $cols;
    }

    /** 取一行内容的展示标题 */
    private function displayTitle($row, $cols) {
        foreach (['title', 'name', 'content', 'signdate', 'data', 'plots'] as $c) {
            if (in_array($c, $cols, true) && isset($row[$c]) && trim((string)$row[$c]) !== '') {
                return mb_substr(trim(preg_replace('/\s+/', ' ', (string)$row[$c])), 0, 40);
            }
        }
        return '(无标题)';
    }

    public function run() {
        $this->checkAdminLogin();
        $db = $this->db;
        $module = $_GET['module'] ?? 'article';
        if (!isset($this->moduleTable[$module])) $module = 'article';
        $table = $this->moduleTable[$module];
        $cols = $this->getColumns($table);
        $hasShow = in_array('isshow', $cols, true);
        $hasTop = in_array('istop', $cols, true);
        $back = 'content.php?siteid=' . $this->siteid . '&module=' . $module;

        // POST 操作（防重复提交）
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->userSession->checkSubmitLimit()) {
                $this->showError('提交过于频繁，请稍后再试', $back);
            }
            $action = $_POST['action'] ?? '';
            $id = (int)($_POST['id'] ?? 0);
            if ($action === 'toggle_show' && $hasShow) {
                $db->execute("UPDATE " . $table . " SET isshow=CASE WHEN isshow=1 THEN 0 ELSE 1 END WHERE id=? AND siteid=?", [$id, $this->siteid]);
                $this->showSuccess('显示状态已切换', $back);
            } elseif ($action === 'toggle_top' && $hasTop) {
                $db->execute("UPDATE " . $table . " SET istop=CASE WHEN istop=1 THEN 0 ELSE 1 END WHERE id=? AND siteid=?", [$id, $this->siteid]);
                $this->showSuccess('置顶状态已切换', $back);
            } elseif ($action === 'delete') {
                $db->execute("DELETE FROM " . $table . " WHERE id=? AND siteid=?", [$id, $this->siteid]);
                $this->showSuccess('内容已删除', $back);
            } else {
                $this->showError('非法操作', $back);
            }
        }

        $this->strTitle = '内容管理 - ' . $module;
        $this->showTop($this->strTitle);
        echo '<div class="subtitle">内容管理</div><div><a href="index.php">返回后台首页</a></div>';

        // 模块切换下拉
        echo '<div class="content"><form method="get" action="content.php">'
            . '<input type="hidden" name="siteid" value="' . (int)$this->siteid . '"/>'
            . '<select name="module" onchange="this.form.submit()">';
        foreach (array_keys($this->moduleTable) as $m) {
            $sel = ($m === $module) ? ' selected="selected"' : '';
            echo '<option value="' . $m . '"' . $sel . '>' . $m . '</option>';
        }
        echo '</select> <input type="submit" value="切换"/></form></div>';

        // 分页列表
        $page = max(1, (int)($_GET['page'] ?? 1));
        $result = $db->getPage($table, '*', 'siteid=?', 'id DESC', $page, 20, [$this->siteid]);
        echo '<div class="content"><ul class="list">';
        foreach ($result['list'] as $row) {
            $rid = (int)$row['id'];
            $showFlag = $hasShow ? (empty($row['isshow']) ? '<span style="color:red">[隐藏]</span>' : '') : '';
            $topFlag = $hasTop && !empty($row['istop']) ? '<span style="color:orange">[顶]</span>' : '';
            $time = !empty($row['addtime']) ? date('m-d H:i', strtotime($row['addtime'])) : '';
            echo '<li>' . $topFlag . $showFlag . 'ID:' . $rid . ' ' . $this->ToHtm($this->displayTitle($row, $cols))
                . ' <span class="time">' . $time . '</span><br/>';
            if ($hasShow) {
                echo '<form method="post" action="' . $back . '" style="display:inline">'
                    . '<input type="hidden" name="action" value="toggle_show"/><input type="hidden" name="id" value="' . $rid . '"/>'
                    . '<input type="submit" value="' . (empty($row['isshow']) ? '显示' : '隐藏') . '"/></form>';
            }
            if ($hasTop) {
                echo '<form method="post" action="' . $back . '" style="display:inline">'
                    . '<input type="hidden" name="action" value="toggle_top"/><input type="hidden" name="id" value="' . $rid . '"/>'
                    . '<input type="submit" value="' . (empty($row['istop']) ? '置顶' : '取消置顶') . '"/></form>';
            }
            echo '<form method="post" action="' . $back . '" style="display:inline">'
                . '<input type="hidden" name="action" value="delete"/><input type="hidden" name="id" value="' . $rid . '"/>'
                . '<input type="submit" value="删除"/></form></li>';
        }
        if (empty($result['list'])) echo '<li>暂无内容</li>';
        echo '</ul></div>';

        // 分页
        if ($result['pageCount'] > 1) {
            echo '<div class="pager">';
            if ($page > 1) echo '<a href="' . $back . '&page=' . ($page - 1) . '">上一页</a> ';
            echo '第' . $page . '/' . $result['pageCount'] . '页 ';
            if ($page < $result['pageCount']) echo '<a href="' . $back . '&page=' . ($page + 1) . '">下一页</a>';
            echo '</div>';
        }

        $this->showDown();
    }
}

$page = new AdminContentPage();
$page->run();
