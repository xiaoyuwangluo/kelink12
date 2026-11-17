<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 管理后台 - 系统公告 (对应原 WapSysMsg 管理)
 * 公告增删改 / 关键词搜索 / 分页 / 删除二次确认 / 清缓存
 */
require_once __DIR__ . '/../includes/bootstrap.php';
require_once KL_ROOT . '/includes/Community.php';

class AdminSysMsgPage extends WapPage {
    /** 列表回跳地址（保留关键词） */
    private function backUrl($q) {
        return $q !== '' ? 'sysmsg.php?q=' . urlencode($q) : 'sysmsg.php';
    }

    /** 删除二次确认页 */
    private function showDeleteConfirm($id, $back) {
        $row = $id > 0 ? $this->db->getOne("SELECT * FROM WapSysMsg WHERE id=?", [$id]) : null;
        if (!$row) $this->showError('公告不存在', $back);
        $content = mb_substr(trim(preg_replace('/\s+/', ' ', (string)($row['content'] ?? ''))), 0, 60);

        $this->strTitle = '删除公告确认';
        $adminTitle = '删除公告确认';
        $activeNav = 'sysmsg';
        require __DIR__ . '/inc/header.php';
        echo '<div class="subtitle">删除公告确认</div>';
        echo '<div><a href="' . $back . '">返回公告列表</a></div>';
        echo '<div class="content">ID：' . (int)$row['id'] . '<br/>'
            . '标题：' . $this->ToHtm($row['title']) . '<br/>'
            . '内容：' . $this->ToHtm($content) . '<br/>'
            . '删除后不可恢复，请谨慎操作。';
        echo '<div class="error">确认删除该公告？</div>';
        echo '<form method="post" action="' . $back . '">'
            . '<input type="hidden" name="action" value="deldone"/>'
            . '<input type="hidden" name="id" value="' . (int)$row['id'] . '"/>'
            . '<input type="submit" value="确认删除"/></form></div>';
        require __DIR__ . '/inc/footer.php';
        exit;
    }

    public function run() {
        $this->checkAdminLogin();
        $db = $this->db;
        $q = trim($_GET['q'] ?? '');
        $back = $this->backUrl($q);

        // 删除二次确认页
        if (($_GET['act'] ?? '') === 'del') {
            $this->showDeleteConfirm((int)($_GET['id'] ?? 0), $back);
            return;
        }

        // POST 操作：新增 / 编辑 / 删除（防重复提交）
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->userSession->checkSubmitLimit()) {
                $this->showError('提交过于频繁，请稍后再试', $back);
            }
            $action = $_POST['action'] ?? '';
            $id = (int)($_POST['id'] ?? 0);
            if ($action === 'save' || $action === 'add') {
                $title = trim($_POST['title'] ?? '');
                $content = trim($_POST['content'] ?? '');
                if ($title === '') {
                    $this->showError('公告标题不能为空', $back);
                }
                if ($action === 'save' && $id > 0) {
                    $exists = $db->getOne("SELECT id FROM WapSysMsg WHERE id=?", [$id]);
                    if (!$exists) $this->showError('公告不存在', $back);
                    $db->execute("UPDATE WapSysMsg SET title=?, content=? WHERE id=?", [$title, $content, $id]);
                    kl_cache_clear_site();
                    $this->showSuccess('公告保存成功', $back);
                } else {
                    $newId = $db->insert("INSERT INTO WapSysMsg (title,content,addtime) VALUES (?,?,?)",
                        [$title, $content, date('Y-m-d H:i:s')]);
                    kl_cache_clear_site();
                    $this->showSuccess('公告添加成功，ID=' . $newId, $back);
                }
            } elseif ($action === 'deldone' || $action === 'delete') {
                $row = $id > 0 ? $db->getOne("SELECT id FROM WapSysMsg WHERE id=?", [$id]) : null;
                if (!$row) $this->showError('公告不存在', $back);
                $db->execute("DELETE FROM WapSysMsg WHERE id=?", [$id]);
                kl_cache_clear_site();
                $this->showSuccess('公告已删除', $back);
            } else {
                $this->showError('非法操作', $back);
            }
        }

        $this->strTitle = '系统公告';
        $adminTitle = '系统公告';
        $activeNav = 'sysmsg';
        require __DIR__ . '/inc/header.php';
        echo '<div class="subtitle">系统公告</div><div><a href="index.php">返回后台首页</a></div>';

        // 新增/编辑表单
        $editId = (int)($_GET['edit'] ?? 0);
        $row = ['id' => 0, 'title' => '', 'content' => ''];
        $isEdit = $editId > 0;
        if ($isEdit) {
            $found = $db->getOne("SELECT * FROM WapSysMsg WHERE id=?", [$editId]);
            if ($found) {
                $row = $found;
            } else {
                $isEdit = false;
                echo '<div class="error">公告不存在，已切换为新增</div>';
            }
        }
        $act = $isEdit ? 'save' : 'add';
        echo '<div class="content"><form method="post" action="' . $back . '">'
            . '<input type="hidden" name="action" value="' . $act . '"/>'
            . '<input type="hidden" name="id" value="' . (int)$row['id'] . '"/>'
            . '<div>标题<br/><input type="text" name="title" value="' . $this->ToHtm($row['title'] ?? '') . '" size="30"/></div>'
            . '<div>内容<br/><textarea name="content" rows="4" cols="32">' . $this->ToHtm($row['content'] ?? '') . '</textarea></div>'
            . '<div><input type="submit" value="' . ($isEdit ? '保存公告' : '添加公告') . '"/>'
            . ($isEdit ? ' <a href="' . $back . '">取消编辑</a>' : '') . '</div>'
            . '</form></div>';

        // 搜索
        echo '<div class="content"><form method="get" action="sysmsg.php">'
            . '搜索：<input type="text" name="q" value="' . $this->ToHtm($q) . '" placeholder="公告ID/标题/内容"/>'
            . '<input type="submit" value="查询"/></form>';
        if ($q !== '') echo ' <a href="sysmsg.php">清除搜索</a>';
        echo '</div>';

        // 列表（搜索 + 分页）
        $where = '1=1';
        $params = [];
        if ($q !== '') {
            $where .= ' AND (id=? OR title LIKE ? OR content LIKE ?)';
            $params[] = (int)$q;
            $params[] = '%' . $q . '%';
            $params[] = '%' . $q . '%';
        }
        $page = max(1, (int)($_GET['page'] ?? 1));
        $result = $db->getPage('WapSysMsg', '*', $where, 'id DESC', $page, 20, $params);

        echo '<div class="content"><div class="info">共 ' . (int)$result['total'] . ' 条公告</div><ul class="list">';
        foreach ($result['list'] as $m) {
            $mid = (int)$m['id'];
            $time = !empty($m['addtime']) ? date('m-d H:i', strtotime($m['addtime'])) : '';
            echo '<li>ID:' . $mid . ' <a href="' . $back . ($q !== '' ? '&' : '?') . 'edit=' . $mid . '">' . $this->ToHtm($m['title']) . '</a>'
                . ' <span class="time">' . $time . '</span>'
                . ' <a href="' . $back . ($q !== '' ? '&' : '?') . 'act=del&id=' . $mid . '">删除</a></li>';
        }
        if (empty($result['list'])) echo '<li>暂无公告</li>';
        echo '</ul></div>';

        // 分页
        if ($result['pageCount'] > 1) {
            echo '<div class="pager">';
            if ($page > 1) echo '<a href="' . $back . ($q !== '' ? '&' : '?') . 'page=' . ($page - 1) . '">上一页</a> ';
            echo '第' . $page . '/' . $result['pageCount'] . '页 ';
            if ($page < $result['pageCount']) echo '<a href="' . $back . ($q !== '' ? '&' : '?') . 'page=' . ($page + 1) . '">下一页</a>';
            echo '</div>';
        }

        require __DIR__ . '/inc/footer.php';
    }
}

$page = new AdminSysMsgPage();
$page->run();
