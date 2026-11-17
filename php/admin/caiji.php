<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 后台 - 采集管理 (对应原 caiji/Caiji.aspx)
 * 采集源增删/搜索 / 触发采集 / 删除确认 / 批量导入文章
 */
require_once __DIR__ . '/../includes/bootstrap.php';
require_once KL_ROOT . '/includes/Community.php';

class AdminCaijiPage extends WapPage {
    /** 可选择文章栏目 */
    private function articleClasses() {
        return $this->db->getAll("SELECT id,classname FROM WapClass WHERE module='article' ORDER BY orderid ASC, id ASC", []);
    }

    /** 删除采集源二次确认页 */
    private function showDeleteConfirm($id, $back) {
        $row = $id > 0 ? $this->db->getOne("SELECT * FROM WapCaiji WHERE id=?", [$id]) : null;
        if (!$row) $this->showError('采集源不存在', $back);

        $this->strTitle = '删除采集源确认';
        $adminTitle = '删除采集源确认';
        $activeNav = 'caiji';
        require __DIR__ . '/inc/header.php';
        echo '<div class="subtitle">删除采集源确认</div>';
        echo '<div><a href="' . $back . '">返回采集管理</a></div>';
        echo '<div class="content">名称：' . $this->ToHtm($row['name']) . '<br/>'
            . '地址：' . $this->ToHtm($row['url']) . '<br/>'
            . '删除后该采集源配置不可恢复，已导入的文章不受影响。';
        echo '<div class="error">确认删除该采集源？</div>';
        echo '<form method="post" action="caiji.php">'
            . '<input type="hidden" name="act" value="deldone"/>'
            . '<input type="hidden" name="id" value="' . (int)$row['id'] . '"/>'
            . '<input type="submit" value="确认删除"/></form></div>';
        require __DIR__ . '/inc/footer.php';
        exit;
    }

    public function run() {
        $this->checkAdminLogin();
        $db = $this->db;
        $q = trim($_GET['q'] ?? '');
        $back = 'caiji.php' . ($q !== '' ? '?q=' . urlencode($q) : '');

        if (($_GET['act'] ?? '') === 'del') {
            $this->showDeleteConfirm((int)($_GET['id'] ?? 0), $back);
            return;
        }

        $msg = '';
        $msgType = 'success';
        if (isset($_POST['act'])) {
            if (!$this->userSession->checkSubmitLimit()) {
                $msg = '操作过于频繁'; $msgType = 'error';
            } elseif ($_POST['act'] === 'add') {
                $name = trim($_POST['name'] ?? '');
                $url = trim($_POST['url'] ?? '');
                $classid = (int)($_POST['classid'] ?? 0);
                $classes = $this->articleClasses();
                $validClass = false;
                foreach ($classes as $c) {
                    if ((int)$c['id'] === $classid) { $validClass = true; break; }
                }
                if ($name === '' || $url === '') {
                    $msg = '名称与地址必填'; $msgType = 'error';
                } elseif (!preg_match('~^https?://~i', $url)) {
                    $msg = '采集地址必须以 http:// 或 https:// 开头'; $msgType = 'error';
                } elseif (!$validClass) {
                    $msg = '请选择有效的文章栏目'; $msgType = 'error';
                } else {
                    $db->insert("INSERT INTO WapCaiji (name,url,classid,addtime) VALUES (?,?,?,?)",
                        [$name, $url, $classid, date('Y-m-d H:i:s')]);
                    $msg = '采集源添加成功';
                }
            } elseif ($_POST['act'] === 'run') {
                $id = (int)($_POST['id'] ?? 0);
                $src = $id > 0 ? $db->getOne("SELECT id FROM WapCaiji WHERE id=?", [$id]) : null;
                if (!$src) {
                    $msg = '采集源不存在'; $msgType = 'error';
                } else {
                    $db->execute("UPDATE WapCaiji SET lastrun=? WHERE id=?", [date('Y-m-d H:i:s'), $id]);
                    $msg = '已触发采集任务 #' . $id . '（正式环境需对接远程采集源）';
                }
            } elseif ($_POST['act'] === 'deldone' || $_POST['act'] === 'del') {
                $id = (int)($_POST['id'] ?? 0);
                $src = $id > 0 ? $db->getOne("SELECT id FROM WapCaiji WHERE id=?", [$id]) : null;
                if (!$src) {
                    $msg = '采集源不存在'; $msgType = 'error';
                } else {
                    $db->execute("DELETE FROM WapCaiji WHERE id=?", [$id]);
                    $msg = '采集源已删除';
                }
            } elseif ($_POST['act'] === 'batchimport') {
                $classid = (int)($_POST['classid'] ?? 0);
                $classes = $this->articleClasses();
                $validClass = false;
                foreach ($classes as $c) {
                    if ((int)$c['id'] === $classid) { $validClass = true; break; }
                }
                $raw = trim($_POST['lines'] ?? '');
                if (!$validClass) {
                    $msg = '请选择有效的文章栏目'; $msgType = 'error';
                } elseif ($raw === '') {
                    $msg = '请粘贴待导入内容'; $msgType = 'error';
                } else {
                    $lines = preg_split('/\r\n|\r|\n/', $raw);
                    $ok = 0; $skip = 0;
                    foreach ($lines as $line) {
                        $line = trim($line);
                        if ($line === '') continue;
                        if ($ok >= 100) { $skip++; continue; }
                        $parts = explode('|', $line, 2);
                        $title = trim($parts[0]);
                        $content = isset($parts[1]) ? trim($parts[1]) : '';
                        if ($title === '') { $skip++; continue; }
                        if (mb_strlen($title) > 200) $title = mb_substr($title, 0, 200);
                        $db->insert("INSERT INTO WapArticle (classid,userid,title,author,content,picurl,downurl,istop,isshow,hits,addtime)"
                            . " VALUES (?,0,?,?,?,'','',0,1,0,?)",
                            [$classid, $title, '采集导入', strip_tags($content), date('Y-m-d H:i:s')]);
                        $ok++;
                    }
                    if ($ok > 0) kl_cache_clear_site();
                    $msg = '批量导入完成：成功 ' . $ok . ' 篇' . ($skip > 0 ? '，跳过 ' . $skip . ' 行' : '');
                    if ($ok === 0) $msgType = 'error';
                }
            } else {
                $msg = '非法操作'; $msgType = 'error';
            }
        }

        // 搜索条件
        $where = '1=1';
        $params = [];
        if ($q !== '') {
            $where .= ' AND (id=? OR name LIKE ? OR url LIKE ?)';
            $params[] = (int)$q;
            $params[] = '%' . $q . '%';
            $params[] = '%' . $q . '%';
        }
        $total = (int)$db->getScalar("SELECT COUNT(*) FROM WapCaiji WHERE " . $where, $params);
        $list = $db->getAll("SELECT * FROM WapCaiji WHERE " . $where . " ORDER BY id DESC", $params);
        $classes = $this->articleClasses();
        $classNames = [];
        foreach ($classes as $c) $classNames[(int)$c['id']] = $c['classname'];
        $articleTotal = (int)$db->getScalar("SELECT COUNT(*) FROM WapArticle");

        $this->strTitle = '采集管理';
        $adminTitle = '采集管理';
        $activeNav = 'caiji';
        require __DIR__ . '/inc/header.php';
        echo '<div class="subtitle">采集管理</div><div><a href="index.php">返回后台首页</a></div>';
        if ($msg !== '') echo '<div class="' . ($msgType === 'error' ? 'error' : 'success') . '">' . $this->ToHtm($msg) . '</div>';

        // 搜索
        echo '<div class="content"><form method="get" action="caiji.php">'
            . '搜索：<input type="text" name="q" value="' . $this->ToHtm($q) . '" placeholder="ID/名称/地址"/>'
            . '<input type="submit" value="查询"/></form>';
        if ($q !== '') echo ' <a href="caiji.php">清除搜索</a>';
        echo '　共 ' . $total . ' 个采集源　文章总数 ' . $articleTotal . ' 篇</div>';

        // 采集源列表
        echo '<div class="content"><ul class="list">';
        foreach ($list as $r) {
            $cid = (int)$r['classid'];
            echo '<li>#' . (int)$r['id'] . ' ' . $this->ToHtm($r['name']) . ' → ' . $this->ToHtm($r['url'])
                . '（栏目：' . $this->ToHtm($classNames[$cid] ?? ('#' . $cid)) . '，最近：' . $this->ToHtm(!empty($r['lastrun']) ? $r['lastrun'] : '未运行') . '）'
                . '<br/><form method="post" action="' . $back . '" style="display:inline">'
                . '<input type="hidden" name="act" value="run"/><input type="hidden" name="id" value="' . (int)$r['id'] . '"/>'
                . '<input type="submit" value="采集"/></form> '
                . '<a href="caiji.php?act=del&id=' . (int)$r['id'] . '">删除</a></li>';
        }
        if (empty($list)) echo '<li>' . ($q !== '' ? '没有匹配的采集源' : '暂无采集源') . '</li>';
        echo '</ul></div>';

        // 新增采集源
        echo '<div class="content"><div class="subtitle">新增采集源</div><form method="post" action="' . $back . '">'
            . '<input type="hidden" name="act" value="add"/>'
            . '名称 <input type="text" name="name" size="16"/> '
            . '地址 <input type="text" name="url" size="30" placeholder="https://..."/> '
            . '栏目 <select name="classid">';
        foreach ($classes as $c) {
            echo '<option value="' . (int)$c['id'] . '">' . $this->ToHtm($c['classname']) . '</option>';
        }
        echo '</select> <input type="submit" value="添加"/></form></div>';

        // 批量导入文章
        echo '<div class="content"><div class="subtitle">批量导入文章</div>'
            . '<form method="post" action="' . $back . '">'
            . '<input type="hidden" name="act" value="batchimport"/>'
            . '导入栏目 <select name="classid">';
        foreach ($classes as $c) {
            echo '<option value="' . (int)$c['id'] . '">' . $this->ToHtm($c['classname']) . '</option>';
        }
        echo '</select><br/>'
            . '<textarea name="lines" rows="6" cols="46" placeholder="每行一篇：标题|正文内容（正文可省略）"></textarea><br/>'
            . '<div class="time">支持每行“标题|正文”，最多导入 100 篇；正文中的 HTML 标签会被清除。</div>'
            . '<input type="submit" value="开始导入"/></form></div>';

        require __DIR__ . '/inc/footer.php';
    }
}

$page = new AdminCaijiPage();
$page->run();
