<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 管理后台 - 评论审核 (对应原 admin/reply 管理)
 * WapReply 与 WapBbsReply 两类评论：搜索 / 状态筛选 / 显示隐藏 / 编辑 / 批量删除 / 删除确认
 */
require_once __DIR__ . '/../includes/bootstrap.php';
require_once KL_ROOT . '/includes/Community.php';

class AdminReplyPage extends WapPage {
    /** 列表回跳地址（保留标签/状态/关键词） */
    private function backUrl($tab, $status, $q) {
        $url = 'reply.php?tab=' . urlencode((string)$tab) . '&status=' . urlencode((string)$status);
        if ($q !== '') $url .= '&q=' . urlencode($q);
        return $url;
    }

    /** 单条删除二次确认页 */
    private function showDeleteConfirm($table, $tab, $id, $back) {
        $row = $id > 0 ? $this->db->getOne("SELECT * FROM " . $table . " WHERE id=?", [$id]) : null;
        if (!$row) $this->showError('评论不存在', $back);
        $content = mb_substr(trim(preg_replace('/\s+/', ' ', (string)($row['content'] ?? ''))), 0, 50);

        $this->strTitle = '删除评论确认';
        $adminTitle = '删除评论确认';
        $activeNav = 'reply';
        require __DIR__ . '/inc/header.php';
        echo '<div class="subtitle">删除评论确认</div>';
        echo '<div><a href="' . $back . '">返回评论列表</a></div>';
        echo '<div class="content">类型：' . ($tab === 'bbs' ? '论坛回复' : '内容评论')
            . '　ID：' . (int)$row['id'] . '<br/>'
            . '会员：' . $this->ToHtm($row['nickname'] ?? '游客') . '<br/>'
            . '内容：' . $this->ToHtm($content) . '<br/>'
            . '删除后不可恢复，请谨慎操作。';
        echo '<div class="error">确认删除该评论？</div>';
        echo '<form method="post" action="' . $back . '">'
            . '<input type="hidden" name="action" value="deldone"/>'
            . '<input type="hidden" name="id" value="' . (int)$row['id'] . '"/>'
            . '<input type="hidden" name="from" value="' . $this->ToHtm($tab) . '"/>'
            . '<input type="submit" value="确认删除"/></form></div>';
        require __DIR__ . '/inc/footer.php';
        exit;
    }

    public function run() {
        $this->checkAdminLogin();
        $db = $this->db;
        $tab = ($_GET['tab'] ?? 'wap') === 'bbs' ? 'bbs' : 'wap';
        $table = $tab === 'bbs' ? 'WapBbsReply' : 'WapReply';
        $q = trim($_GET['q'] ?? '');
        $status = $_GET['status'] ?? 'all';
        if (!in_array($status, ['all', 'show', 'hidden'], true)) $status = 'all';
        $page = max(1, (int)($_GET['page'] ?? 1));
        $back = $this->backUrl($tab, $status, $q);

        // 删除二次确认页
        if (($_GET['act'] ?? '') === 'del') {
            $this->showDeleteConfirm($table, $tab, (int)($_GET['id'] ?? 0), $back);
            return;
        }

        // POST 操作（防重复提交）
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->userSession->checkSubmitLimit()) {
                $this->showError('提交过于频繁，请稍后再试', $back);
            }
            $action = $_POST['action'] ?? '';
            $id = (int)($_POST['id'] ?? 0);
            $fromBbs = ($_POST['from'] ?? $tab) === 'bbs';
            $ptable = $fromBbs ? 'WapBbsReply' : 'WapReply';
            if ($action === 'toggle_show') {
                $db->execute("UPDATE " . $ptable . " SET isshow=CASE WHEN isshow=1 THEN 0 ELSE 1 END WHERE id=?",[$id]);
                kl_cache_clear_site();
                $this->showSuccess('审核状态已切换', $back);
            } elseif ($action === 'deldone' || $action === 'delete') {
                $row = $id > 0 ? $db->getOne("SELECT id FROM " . $ptable . " WHERE id=?", [$id]) : null;
                if (!$row) $this->showError('评论不存在', $back);
                $db->execute("DELETE FROM " . $ptable . " WHERE id=?",[$id]);
                kl_cache_clear_site();
                $this->showSuccess('评论已删除', $back);
            } elseif ($action === 'delall') {
                $ids = array_filter(array_map('intval', (array)($_POST['ids'] ?? [])));
                if (!$ids) $this->showError('未选择评论', $back);
                $ph = '(' . rtrim(str_repeat('?,', count($ids)), ',') . ')';
                $db->execute("DELETE FROM " . $table . " WHERE id IN" . $ph, $ids);
                kl_cache_clear_site();
                $this->showSuccess('已批量删除 ' . count($ids) . ' 条评论', $back);
            } elseif ($action === 'edit') {
                $content = trim($_POST['content'] ?? '');
                if ($content === '') {
                    $this->showError('评论内容不能为空', $back);
                }
                $n = $db->execute("UPDATE " . $ptable . " SET content=? WHERE id=?",[$content,$id]);
                kl_cache_clear_site();
                $this->showSuccess($n > 0 ? '评论已保存' : '评论不存在或无变更', $back);
            } else {
                $this->showError('非法操作', $back);
            }
        }

        // GET 显示/隐藏切换
        $gact = $_GET['gact'] ?? '';
        $gid = (int)($_GET['gid'] ?? 0);
        if ($gact === 'tshow' && $gid > 0) {
            $db->execute("UPDATE " . $table . " SET isshow=CASE WHEN isshow=1 THEN 0 ELSE 1 END WHERE id=?",[$gid]);
            kl_cache_clear_site();
            $this->showSuccess('审核状态已切换', $back);
        }

        $this->strTitle = '评论审核';
        $adminTitle = '评论审核';
        $activeNav = 'reply';
        require __DIR__ . '/inc/header.php';
        echo '<div class="subtitle">评论审核</div><div><a href="index.php">返回后台首页</a></div>';

        // 切换标签
        echo '<div class="content">'
            . '<a href="reply.php?tab=wap">内容评论' . ($tab === 'wap' ? '√' : '') . '</a> | '
            . '<a href="reply.php?tab=bbs">论坛回复' . ($tab === 'bbs' ? '√' : '') . '</a></div>';

        // 编辑表单 (GET edit=ID)
        $editId = (int)($_GET['edit'] ?? 0);
        if ($editId > 0) {
            $row = $db->getOne("SELECT * FROM " . $table . " WHERE id=?",[$editId]);
            if ($row) {
                echo '<div class="content"><form method="post" action="' . $back . '">'
                    . '<input type="hidden" name="action" value="edit"/>'
                    . '<input type="hidden" name="id" value="' . $editId . '"/>'
                    . '<input type="hidden" name="from" value="' . $tab . '"/>'
                    . '<div>评论内容<br/><textarea name="content" rows="4" cols="40">' . $this->ToHtm($row['content'] ?? '') . '</textarea></div>'
                    . '<div><input type="submit" value="保存评论"/> <a href="' . $back . '">取消</a></div>'
                    . '</form></div>';
            } else {
                echo '<div class="error">评论不存在</div>';
            }
        }

        // 统计（标签 + 关键词，不含状态）
        $baseWhere = '1=1';
        $params = [];
        if ($q !== '') {
            $baseWhere .= ' AND (id=? OR content LIKE ? OR nickname LIKE ?)';
            $params[] = (int)$q;
            $params[] = '%' . $q . '%';
            $params[] = '%' . $q . '%';
        }
        $total = (int)$db->getScalar("SELECT COUNT(*) FROM " . $table . " WHERE " . $baseWhere, $params);
        $showCount = (int)$db->getScalar("SELECT COUNT(*) FROM " . $table . " WHERE " . $baseWhere . " AND isshow=1", $params);
        $hiddenCount = (int)$db->getScalar("SELECT COUNT(*) FROM " . $table . " WHERE " . $baseWhere . " AND isshow<>1", $params);

        // 状态筛选
        echo '<div class="content">筛选：';
        $filters = [
            'all' => '全部(' . $total . ')',
            'show' => '已显示(' . $showCount . ')',
            'hidden' => '已隐藏(' . $hiddenCount . ')',
        ];
        foreach ($filters as $k => $label) {
            echo ($status === $k)
                ? '<strong>[' . $label . ']</strong> '
                : '<a href="' . $this->backUrl($tab, $k, $q) . '">' . $label . '</a> ';
        }
        echo '</div>';

        // 搜索
        echo '<div class="content"><form method="get" action="reply.php">'
            . '搜索：<input type="text" name="q" value="' . $this->ToHtm($q) . '" placeholder="ID/内容/昵称"/>'
            . '<input type="hidden" name="tab" value="' . $this->ToHtm($tab) . '"/>'
            . '<input type="hidden" name="status" value="' . $this->ToHtm($status) . '"/>'
            . '<input type="submit" value="查询"/></form></div>';

        // 列表条件
        $where = $baseWhere;
        if ($status === 'show') {
            $where .= ' AND isshow=1';
        } elseif ($status === 'hidden') {
            $where .= ' AND isshow<>1';
        }
        $result = $db->getPage($table, '*', $where, 'id DESC', $page, 20, $params);

        // 列表（勾选 + 批量删除）
        echo '<form method="post" action="' . $back . '"><input type="hidden" name="action" value="delall"/>';
        echo '<div class="content"><ul class="list">';
        foreach ($result['list'] as $row) {
            $rid = (int)$row['id'];
            $hide = empty($row['isshow']) ? '<span style="color:red">[隐藏]</span>' : '';
            $src = $tab === 'bbs' ? ('帖' . (int)($row['bbsid'] ?? 0))
                                  : ($this->ToHtm($row['module'] ?? '') . '#' . (int)($row['contentid'] ?? 0));
            $time = !empty($row['addtime']) ? date('m-d H:i', strtotime($row['addtime'])) : '';
            $content = mb_substr(trim(preg_replace('/\s+/', ' ', (string)($row['content'] ?? ''))), 0, 50);
            echo '<li><input type="checkbox" name="ids[]" value="' . $rid . '"/>' . $hide
                . 'ID:' . $rid . ' [' . $src . '] ' . $this->ToHtm($row['nickname'] ?? '游客')
                . ': ' . $this->ToHtm($content) . ' <span class="time">' . $time . '</span> '
                . '<a href="' . $back . '&edit=' . $rid . '">编辑</a> '
                . '<a href="' . $back . '&act=del&id=' . $rid . '">删除</a><br/>';
            echo '<a href="' . $back . '&gact=tshow&gid=' . $rid . '">'
                . (empty($row['isshow']) ? '显示' : '隐藏') . '</a></li>';
        }
        if (empty($result['list'])) echo '<li>暂无评论</li>';
        echo '</ul>';
        if (!empty($result['list'])) echo '<input type="submit" value="删除选中"/>';
        echo '</div></form>';

        // 分页
        if ($result['pageCount'] > 1) {
            echo '<div class="pager">';
            if ($page > 1) echo '<a href="' . $back . '&page=' . ($page - 1) . '">上一页</a> ';
            echo '第' . $page . '/' . $result['pageCount'] . '页 ';
            if ($page < $result['pageCount']) echo '<a href="' . $back . '&page=' . ($page + 1) . '">下一页</a>';
            echo '</div>';
        }

        require __DIR__ . '/inc/footer.php';
    }
}

$page = new AdminReplyPage();
$page->run();
