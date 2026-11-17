<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 管理后台 - 帖子标签管理 (列表/搜索/改名合并/删除)
 */
require_once __DIR__ . '/../includes/bootstrap.php';
require_once KL_ROOT . '/includes/Community.php';

class AdminTagPage extends WapPage {
    private $pageSize = 30;

    public function run() {
        $this->checkAdminLogin();
        $db = $this->db;
        $q = trim((string)($_GET['q'] ?? ''));
        $back = 'tag.php' . ($q !== '' ? '&q=' . urlencode($q) : '');
        $listBack = 'tag.php';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $act = (string)($_POST['act'] ?? '');
            if ($act === 'rename') {
                $this->doRename($listBack);
            } elseif ($act === 'del') {
                $name = trim((string)($_POST['tagname'] ?? ''));
                if ($name === '') $this->showError('标签不能为空', $listBack);
                $db->execute("DELETE FROM WapBbsTag WHERE tagname=?",[$name]);
                kl_cache_clear_site();
                $this->showSuccess('标签“' . $name . '”已删除', $listBack);
            }
            $this->showError('非法操作', $listBack);
        }

        $page = max(1, (int)($_GET['page'] ?? 1));
        $offset = ($page - 1) * $this->pageSize;

        $where = '1=1';
        $params = [];
        if ($q !== '') {
            $where .= ' AND t.tagname LIKE ?';
            $params[] = '%' . mb_substr($q, 0, 30) . '%';
        }

        $total = (int)$db->getScalar(
            "SELECT COUNT(DISTINCT t.tagname) FROM WapBbsTag t WHERE $where", $params);
        $rows = $db->getAll(
            "SELECT t.tagname, COUNT(*) AS cnt,
                    SUM(CASE WHEN b.isshow=1 THEN 1 ELSE 0 END) AS visible,
                    MAX(t.addtime) AS lasttime
             FROM WapBbsTag t
             LEFT JOIN WapBbs b ON b.id=t.bbsid
             WHERE $where
             GROUP BY t.tagname
             ORDER BY cnt DESC, t.tagname ASC
             LIMIT " . (int)$this->pageSize . " OFFSET " . (int)$offset,
            $params
        );

        $this->strTitle = '标签管理';
        $adminTitle = '标签管理';
        $activeNav = 'tag';
        require __DIR__ . '/inc/header.php';
        echo '<div class="subtitle">帖子标签管理</div>';
        echo '<div><a href="index.php">返回后台首页</a> | <a href="../bbs/tags.php">前台热门话题</a></div>';

        echo '<div class="content"><form method="get" action="tag.php">';
        echo '搜索标签：<input type="text" name="q" value="' . $this->ToHtm($q) . '" maxlength="30"/>';
        echo ' <input type="submit" value="搜索"/></form></div>';

        echo '<div class="content">共 ' . $total . ' 个标签（含隐藏帖子的标签，改名会合并到目标标签）</div>';

        echo '<div class="content"><ul class="list">';
        foreach ($rows as $r) {
            $name = (string)$r['tagname'];
            echo '<li>';
            echo '<strong>#' . $this->ToHtm($name) . '#</strong> ';
            echo '<span class="info">帖子 ' . (int)$r['cnt'] . ' / 可见 ' . (int)$r['visible'] . '</span> ';
            echo '<span class="time">' . (!empty($r['lasttime']) ? date('m-d H:i', strtotime($r['lasttime'])) : '') . '</span>';
            echo ' <a href="../bbs/tag.php?name=' . rawurlencode($name) . '">查看</a>';

            echo '<div class="actionbar">';
            echo '<form method="post" action="tag.php" style="display:inline">';
            echo '<input type="hidden" name="act" value="rename"/>';
            echo '<input type="hidden" name="oldname" value="' . $this->ToHtm($name) . '"/>';
            echo '改为 <input type="text" name="newname" value="" maxlength="12" size="12"/> ';
            echo '<input type="submit" value="改名/合并"/></form> ';
            echo '<form method="post" action="tag.php" style="display:inline" onsubmit="return confirm(\'删除该标签?\')">';
            echo '<input type="hidden" name="act" value="del"/>';
            echo '<input type="hidden" name="tagname" value="' . $this->ToHtm($name) . '"/>';
            echo '<input type="submit" value="删除"/></form>';
            echo '</div>';
            echo '</li>';
        }
        if (empty($rows)) echo '<li>暂无标签</li>';
        echo '</ul></div>';

        $pageCount = (int)ceil($total / $this->pageSize);
        if ($pageCount > 1) {
            echo '<div class="pager">';
            $qs = 'tag.php' . ($q !== '' ? '&q=' . urlencode($q) : '');
            if ($page > 1) echo '<a href="' . $qs . '&page=' . ($page - 1) . '">上一页</a> ';
            echo '第' . $page . '/' . $pageCount . '页 ';
            if ($page < $pageCount) echo '<a href="' . $qs . '&page=' . ($page + 1) . '">下一页</a>';
            echo '</div>';
        }

        require __DIR__ . '/inc/footer.php';
    }

    /** 标签改名 / 合并（同帖已有目标标签时删除旧标签行，避免唯一键冲突） */
    private function doRename($back) {
        $db = $this->db;
        $old = trim((string)($_POST['oldname'] ?? ''));
        $new = trim((string)($_POST['newname'] ?? ''));

        if ($old === '' || $new === '') $this->showError('标签名不能为空', $back);
        if ($new === $old) $this->showSuccess('标签名未变化', $back);
        if (!preg_match('/^[\x{4e00}-\x{9fa5}A-Za-z0-9_]{1,12}$/u', $new)) {
            $this->showError('新标签仅支持中英文、数字、下划线，长度 1-12', $back);
        }

        $exists = $db->getOne("SELECT id FROM WapBbsTag WHERE tagname=? LIMIT 1",[$old]);
        if (!$exists) $this->showError('原标签不存在', $back);

        // 已有目标标签的帖子ID
        $hasNew = [];
        foreach ($db->getAll("SELECT bbsid FROM WapBbsTag WHERE tagname=?",[$new]) as $r) {
            $hasNew[(int)$r['bbsid']] = true;
        }

        $rows = $db->getAll("SELECT id,bbsid FROM WapBbsTag WHERE tagname=?",[$old]);
        $moved = 0;
        $merged = 0;
        $db->beginTransaction();
        try {
            foreach ($rows as $r) {
                $bbsid = (int)$r['bbsid'];
                if (isset($hasNew[$bbsid])) {
                    $db->execute("DELETE FROM WapBbsTag WHERE id=?", [(int)$r['id']]);
                    $merged++;
                } else {
                    $db->execute("UPDATE WapBbsTag SET tagname=? WHERE id=?", [$new, (int)$r['id']]);
                    $hasNew[$bbsid] = true;
                    $moved++;
                }
            }
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            $this->showError('改名失败，请重试', $back);
        }

        kl_cache_clear_site();
        $msg = '标签已改为“' . $new . '”，迁移 ' . $moved . ' 条';
        if ($merged > 0) $msg .= '，合并去重 ' . $merged . ' 条';
        $this->showSuccess($msg, $back);
    }
}

$page = new AdminTagPage();
$page->run();
