<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 管理后台 - 统一内容管理 (对应原各模块 admin 审核页)
 * ?module=xxx 分页列出该模块表内容，操作：显示/隐藏(isshow)、删除、置顶(有istop列的表)
 */
require_once __DIR__ . '/../includes/bootstrap.php';
require_once KL_ROOT . '/includes/Upload.php';
require_once KL_ROOT . '/includes/Community.php';

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

    /** 模块 => 附件表配置（article 附件存 WapArticleFile，bbs 图片存 WapBbsFile） */
    private $attachTable = [
        'article' => ['table' => 'WapArticleFile', 'fk' => 'articleid'],
        'bbs'     => ['table' => 'WapBbsFile', 'fk' => 'bbsid'],
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

    /** 列表回跳地址（保留模块/关键词/栏目/状态） */
    private function backUrl($module, $q, $classid, $status) {
        $url = 'content.php?module=' . urlencode((string)$module)
            . '&status=' . urlencode((string)$status)
            . '&classid=' . (int)$classid;
        if ($q !== '') $url .= '&q=' . urlencode($q);
        return $url;
    }

    /** 处理附件上传并写入对应附件表 */
    private function doUploadFile($module, $table, $id) {
        $conf = $this->attachTable[$module];
        $back = 'content.php?module=' . $module . '&act=files&id=' . $id;
        $row = $this->db->getOne("SELECT id,classid FROM " . $table . " WHERE id=?",[$id]);
        if (!$row) $this->showError('内容不存在', 'content.php?module=' . $module);
        if (!isset($_FILES['userfile'])) $this->showError('未选择文件', $back);
        list($ok, $msg, $info) = Upload::save($_FILES['userfile']);
        if (!$ok) $this->showError($msg, $back);
        if ($conf['fk'] === 'articleid') {
            $this->db->insert("INSERT INTO WapArticleFile (classid,articleid,filename,filepath,filesize,addtime) VALUES (?,?,?,?,?,NOW())",[(int)($row['classid'] ?? 0),$id,$info['filename'],$info['relpath'],$info['size']]);
        } else {
            $this->db->insert("INSERT INTO WapBbsFile (bbsid,filename,filepath,filesize,addtime) VALUES (?,?,?,?,NOW())",[$id,$info['filename'],$info['relpath'],$info['size']]);
        }
        $this->showSuccess('附件上传成功', $back);
    }

    /** 附件查看/上传页 */
    private function showAttachPage($module, $back) {
        $conf = $this->attachTable[$module];
        $fk = $conf['fk'];
        $id = (int)($_GET['id'] ?? 0);
        $rows = $this->db->getAll("SELECT * FROM " . $conf['table'] . " WHERE $fk=? ORDER BY id DESC",[$id]);

        $this->strTitle = '附件管理';
        $this->showTop($this->strTitle);
        echo '<div class="subtitle">' . $this->ToHtm($module) . ' ID:' . $id . ' 附件</div>';
        echo '<div><a href="' . $back . '">返回内容列表</a></div>';

        echo '<div class="content"><form method="post" action="' . $back . '&act=files&id=' . $id . '" enctype="multipart/form-data">'
            . '<input type="hidden" name="action" value="upload_file"/>'
            . '<input type="hidden" name="id" value="' . $id . '"/>'
            . '选择附件: <input type="file" name="userfile"/><input type="submit" value="上传附件"/>'
            . '<div class="time">限制: 单文件最大 ' . Upload::formatSize(KL_UpMaxSize * 1024) . '，允许扩展: ' . $this->ToHtm(KL_UpAllowExt) . '</div>'
            . '</form></div>';

        echo '<div class="content"><ul class="list">';
        foreach ($rows as $f) {
            $url = '../' . Upload::url($f['filepath']);
            echo '<li><a href="' . $url . '" target="_blank">' . $this->ToHtm($f['filename'] ? $f['filename'] : $f['filepath']) . '</a>'
                . ' [' . Upload::formatSize($f['filesize']) . '] '
                . '<span class="time">' . ($f['addtime'] ? date('m-d H:i', strtotime($f['addtime'])) : '') . '</span></li>';
        }
        if (empty($rows)) echo '<li>暂无附件</li>';
        echo '</ul></div>';
        $this->showDown();
    }

    /** 单条删除二次确认页 */
    private function showDeleteConfirm($module, $table, $cols, $id, $back) {
        $row = $id > 0 ? $this->db->getOne("SELECT * FROM " . $table . " WHERE id=?", [$id]) : null;
        if (!$row) $this->showError('内容不存在', $back);

        $this->strTitle = '删除内容确认';
        $this->showTop($this->strTitle);
        echo '<div class="subtitle">删除内容确认</div>';
        echo '<div><a href="' . $back . '">返回内容列表</a></div>';
        echo '<div class="content">模块：' . $this->ToHtm($module) . '　ID：' . (int)$row['id'] . '<br/>'
            . '标题：' . $this->ToHtm($this->displayTitle($row, $cols)) . '<br/>'
            . '删除后不可恢复，请谨慎操作。';
        echo '<div class="error">确认删除该内容？</div>';
        echo '<form method="post" action="' . $back . '">'
            . '<input type="hidden" name="action" value="deldone"/>'
            . '<input type="hidden" name="id" value="' . (int)$row['id'] . '"/>'
            . '<input type="submit" value="确认删除"/></form></div>';
        $this->showDown();
        exit;
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
        $hasClassid = in_array('classid', $cols, true);
        // 关键词搜索列（优先标题，其次名称/正文）
        $titleCol = '';
        foreach (['title', 'name', 'content', 'data'] as $c) {
            if (in_array($c, $cols, true)) { $titleCol = $c; break; }
        }
        $q = trim($_GET['q'] ?? '');
        $classid = max(0, (int)($_GET['classid'] ?? 0));
        $status = $_GET['status'] ?? 'all';
        if (!in_array($status, ['all', 'show', 'hidden'], true)) $status = 'all';
        if (!$hasShow) $status = 'all';
        $back = $this->backUrl($module, $q, $classid, $status);

        // POST 操作（批量删除，对应原 admin_WAPdelALL）
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->userSession->checkSubmitLimit()) {
                $this->showError('提交过于频繁，请稍后再试', $back);
            }
            $action = $_POST['action'] ?? '';
            if ($action === 'delall') {
                $ids = array_filter(array_map('intval', (array)($_POST['ids'] ?? [])));
                if (!$ids) $this->showError('未选择内容', $back);
                $ph = '(' . rtrim(str_repeat('?,', count($ids)), ',') . ')';
                $db->execute("DELETE FROM " . $table . " WHERE id IN" . $ph, $ids);
                kl_cache_clear_site();
                $this->showSuccess('已批量删除 ' . count($ids) . ' 条', $back);
            } elseif ($action === 'deldone') {
                $did = (int)($_POST['id'] ?? 0);
                $row = $did > 0 ? $db->getOne("SELECT id FROM " . $table . " WHERE id=?", [$did]) : null;
                if (!$row) $this->showError('内容不存在', $back);
                $db->execute("DELETE FROM " . $table . " WHERE id=?", [$did]);
                kl_cache_clear_site();
                $this->showSuccess('内容已删除', $back);
            } elseif ($action === 'upload_file' && isset($this->attachTable[$module])) {
                $this->doUploadFile($module, $table, (int)($_POST['id'] ?? 0));
            } else {
                $this->showError('非法操作', $back);
            }
        }

        // 单条删除二次确认页
        if (($_GET['act'] ?? '') === 'del') {
            $this->showDeleteConfirm($module, $table, $cols, (int)($_GET['id'] ?? 0), $back);
            return;
        }

        // GET 单条操作（对应原 admin_WAPmodify/del 链接风格）
        $gact = $_GET['gact'] ?? '';
        $gid = (int)($_GET['gid'] ?? 0);
        if ($gact !== '' && $gid > 0) {
            if ($gact === 'tshow' && $hasShow) {
                $db->execute("UPDATE " . $table . " SET isshow=CASE WHEN isshow=1 THEN 0 ELSE 1 END WHERE id=?",[$gid]);
                kl_cache_clear_site();
                $this->showSuccess('显示状态已切换', $back);
            } elseif ($gact === 'ttop' && $hasTop) {
                $db->execute("UPDATE " . $table . " SET istop=CASE WHEN istop=1 THEN 0 ELSE 1 END WHERE id=?",[$gid]);
                kl_cache_clear_site();
                $this->showSuccess('置顶状态已切换', $back);
            } elseif ($gact === 'del') {
                $db->execute("DELETE FROM " . $table . " WHERE id=?",[$gid]);
                kl_cache_clear_site();
                $this->showSuccess('内容已删除', $back);
            }
        }

        // 附件管理页 (仅 article/bbs)
        if (($_GET['act'] ?? '') === 'files' && isset($this->attachTable[$module])) {
            $this->showAttachPage($module, $back);
            exit;
        }

        $this->strTitle = '内容管理 - ' . $module;
        $this->showTop($this->strTitle);
        echo '<div class="subtitle">内容管理</div><div><a href="index.php">返回后台首页</a></div>';

        // 模块切换下拉
        echo '<div class="content"><form method="get" action="content.php">'
            . '<select name="module" onchange="this.form.submit()">';
        foreach (array_keys($this->moduleTable) as $m) {
            $sel = ($m === $module) ? ' selected="selected"' : '';
            echo '<option value="' . $m . '"' . $sel . '>' . $m . '</option>';
        }
        echo '</select> <input type="submit" value="切换"/></form></div>';

        // 统计（模块 + 搜索 + 栏目，不含状态）
        $baseWhere = '1=1';
        $baseParams = [];
        if ($hasClassid && $classid > 0) {
            $baseWhere .= ' AND classid=?';
            $baseParams[] = $classid;
        }
        if ($q !== '' && $titleCol !== '') {
            $baseWhere .= " AND (id=? OR $titleCol LIKE ?)";
            $baseParams[] = (int)$q;
            $baseParams[] = '%' . $q . '%';
        }
        $total = (int)$db->getScalar("SELECT COUNT(*) FROM $table WHERE $baseWhere", $baseParams);
        $showCount = 0;
        $hiddenCount = 0;
        if ($hasShow) {
            $showCount = (int)$db->getScalar("SELECT COUNT(*) FROM $table WHERE $baseWhere AND isshow=1", $baseParams);
            $hiddenCount = (int)$db->getScalar("SELECT COUNT(*) FROM $table WHERE $baseWhere AND isshow<>1", $baseParams);
        }

        // 状态筛选
        if ($hasShow) {
            echo '<div class="content">筛选：';
            $filters = [
                'all' => '全部(' . $total . ')',
                'show' => '已显示(' . $showCount . ')',
                'hidden' => '已隐藏(' . $hiddenCount . ')',
            ];
            foreach ($filters as $k => $label) {
                echo ($status === $k)
                    ? '<strong>[' . $label . ']</strong> '
                    : '<a href="' . $this->backUrl($module, $q, $classid, $k) . '">' . $label . '</a> ';
            }
            echo '</div>';
        }

        // 栏目筛选（仅含 classid 列的表）
        if ($hasClassid) {
            $classList = $db->getAll("SELECT id,classname FROM WapClass WHERE module=? ORDER BY orderid ASC, id ASC", [$module]);
            if ($classList) {
                echo '<div class="content"><form method="get" action="content.php">栏目：<select name="classid">'
                    . '<option value="0">全部栏目</option>';
                foreach ($classList as $c) {
                    $sel = ($classid === (int)$c['id']) ? ' selected="selected"' : '';
                    echo '<option value="' . (int)$c['id'] . '"' . $sel . '>' . $this->ToHtm($c['classname']) . '</option>';
                }
                echo '</select>'
                    . '<input type="hidden" name="module" value="' . $this->ToHtm($module) . '"/>'
                    . '<input type="hidden" name="q" value="' . $this->ToHtm($q) . '"/>'
                    . '<input type="hidden" name="status" value="' . $this->ToHtm($status) . '"/>'
                    . '<input type="submit" value="筛选"/></form></div>';
            }
        }

        // 关键词搜索
        if ($titleCol !== '') {
            echo '<div class="content"><form method="get" action="content.php">'
                . '搜索：<input type="text" name="q" value="' . $this->ToHtm($q) . '" placeholder="内容ID/关键词"/>'
                . '<input type="hidden" name="module" value="' . $this->ToHtm($module) . '"/>'
                . '<input type="hidden" name="status" value="' . $this->ToHtm($status) . '"/>'
                . '<input type="hidden" name="classid" value="' . $classid . '"/>'
                . '<input type="submit" value="查询"/></form></div>';
        }

        // 分页列表（勾选 + 批量删除表单）
        $page = max(1, (int)($_GET['page'] ?? 1));
        $where = $baseWhere;
        $params = $baseParams;
        if ($hasShow && $status === 'show') {
            $where .= ' AND isshow=1';
        } elseif ($hasShow && $status === 'hidden') {
            $where .= ' AND isshow<>1';
        }
        $result = $db->getPage($table, '*', $where, 'id DESC', $page, 20, $params);

        // 栏目名称映射
        $classNames = [];
        if ($hasClassid) {
            foreach ($db->getAll("SELECT id,classname FROM WapClass") as $c) {
                $classNames[(int)$c['id']] = $c['classname'];
            }
        }
        echo '<form method="post" action="' . $back . '"><input type="hidden" name="action" value="delall"/>';
        echo '<div class="content"><ul class="list">';
        foreach ($result['list'] as $row) {
            $rid = (int)$row['id'];
            $showFlag = $hasShow ? (empty($row['isshow']) ? '<span style="color:red">[隐藏]</span>' : '') : '';
            $topFlag = $hasTop && !empty($row['istop']) ? '<span style="color:orange">[顶]</span>' : '';
            $time = !empty($row['addtime']) ? date('m-d H:i', strtotime($row['addtime'])) : '';
            $classTxt = '';
            if ($hasClassid) {
                $rowClassid = (int)($row['classid'] ?? 0);
                $classTxt = ' 栏目:' . $this->ToHtm($classNames[$rowClassid] ?? ($rowClassid > 0 ? '#' . $rowClassid : '未分类'));
            }
            echo '<li><input type="checkbox" name="ids[]" value="' . $rid . '"/>'
                . $topFlag . $showFlag . 'ID:' . $rid . ' ' . $this->ToHtm($this->displayTitle($row, $cols)) . $classTxt
                . ' <span class="time">' . $time . '</span><br/>';
            if ($hasShow) echo '<a href="' . $back . '&gact=tshow&gid=' . $rid . '">' . (empty($row['isshow']) ? '显示' : '隐藏') . '</a> ';
            if ($hasTop) echo '<a href="' . $back . '&gact=ttop&gid=' . $rid . '">' . (empty($row['istop']) ? '置顶' : '取消置顶') . '</a> ';
            echo '<a href="' . $back . '&act=del&id=' . $rid . '">删除</a>';
            if (isset($this->attachTable[$module])) {
                echo ' <a href="' . $back . '&act=files&id=' . $rid . '">附件</a>';
            }
            echo '</li>';
        }
        if (empty($result['list'])) echo '<li>暂无内容</li>';
        echo '</ul>';
        if (!empty($result['list'])) echo '<input type="submit" value="删除选中" onclick="return confirm(\'批量删除选中内容?\')"/>';
        echo '</div></form>';

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
