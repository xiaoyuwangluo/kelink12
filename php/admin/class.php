<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 管理后台 - 栏目管理 (对应原 admin/classlist.aspx)
 * 支持: 模块筛选 / 关键词搜索 / 新增编辑 / 父栏目与循环校验
 *       删除二次确认(子栏目与栏目内容拦截) / 保存删除清缓存
 */
require_once __DIR__ . '/../includes/bootstrap.php';
require_once KL_ROOT . '/includes/Community.php';

class AdminClassPage extends WapPage {
    /** 可选栏目模块（与 wapindex.php/各模块目录对应） */
    private $modules = ['article','bbs','shop','novel','video','picture','download','ring','link',
        'guessbook','gongqiu','paimai','yuehui','wabao','guangbo','adlink','airplane','album','hotel',
        'rizhi','xinzhang','chat','clan','games','zhdao','nongchang','signin','form'];

    /** 有 classid 关联的内容表（用于删除前统计栏目内容数） */
    private $contentTable = [
        'article' => 'WapArticle', 'bbs' => 'WapBbs', 'shop' => 'WapShop',
        'novel' => 'WapNovel', 'video' => 'WapVideo', 'picture' => 'WapPicture',
        'download' => 'WapDownload', 'ring' => 'WapRing', 'link' => 'WapLink',
        'guessbook' => 'WapGuestbook', 'gongqiu' => 'WapGongqiu', 'paimai' => 'WapPaimai',
        'yuehui' => 'WapYuehui', 'wabao' => 'WapWabao', 'guangbo' => 'WapGuangbo',
        'adlink' => 'WapAdlink', 'airplane' => 'WapAirplane', 'album' => 'WapAlbum',
        'hotel' => 'WapHotel', 'rizhi' => 'WapRizhi', 'xinzhang' => 'WapXinzhang',
        'zhdao' => 'WapZhiDao',
    ];

    /** 列表回跳地址（保留模块筛选与关键词） */
    private function backUrl($module, $q) {
        $url = 'class.php?module=' . urlencode((string)$module);
        if ($q !== '') $url .= '&q=' . urlencode($q);
        return $url;
    }

    public function run() {
        $this->checkAdminLogin();
        $db = $this->db;

        $module = $_GET['module'] ?? '';
        if ($module !== '' && !in_array($module, $this->modules, true)) $module = '';
        $q = trim($_GET['q'] ?? '');
        $back = $this->backUrl($module, $q);
        $page = max(1, (int)($_GET['page'] ?? 1));

        // 删除二次确认页
        if (($_GET['act'] ?? '') === 'del') {
            $this->showDeleteConfirm((int)($_GET['id'] ?? 0), $module, $q, $back);
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
                $classname = trim($_POST['classname'] ?? '');
                $modulePost = in_array($_POST['module'] ?? '', $this->modules, true) ? $_POST['module'] : 'article';
                if ($classname === '') {
                    $this->showError('栏目名称不能为空', $back);
                }
                $orderid = (int)($_POST['orderid'] ?? 0);
                $parentid = (int)($_POST['parentid'] ?? 0);
                $isshow = isset($_POST['isshow']) ? 1 : 0;
                // 栏目权限分组：postgroup=允许投稿的会员分组(逗号分隔，空=不限)；managegroup=版主分组(空=仅超管)
                $postgroup = preg_replace('/[^0-9,]/', '', trim($_POST['postgroup'] ?? ''));
                $managegroup = preg_replace('/[^0-9,]/', '', trim($_POST['managegroup'] ?? ''));
                $this->checkParent($parentid, $id, $modulePost, $back);
                if ($action === 'save' && $id > 0) {
                    $exists = $db->getOne("SELECT id FROM WapClass WHERE id=?", [$id]);
                    if (!$exists) $this->showError('栏目不存在', $back);
                    $db->execute("UPDATE WapClass SET classname=?, module=?, orderid=?, parentid=?, isshow=?, postgroup=?, managegroup=? WHERE id=?",[$classname,$modulePost,$orderid,$parentid,$isshow,$postgroup,$managegroup,$id]);
                    kl_cache_clear_site();
                    $this->showSuccess('栏目保存成功', $back);
                } else {
                    $newId = $db->insert("INSERT INTO WapClass (parentid,classname,module,orderid,isshow,postgroup,managegroup) VALUES (?,?,?,?,?,?,?)",[$parentid,$classname,$modulePost,$orderid,$isshow,$postgroup,$managegroup]);
                    kl_cache_clear_site();
                    $this->showSuccess('栏目添加成功，ID=' . $newId, $back);
                }
            } elseif ($action === 'deldone' || $action === 'delete') {
                $this->doDelete($id, $module, $q, $back);
                $this->showSuccess('栏目已删除', $back);
            } else {
                $this->showError('非法操作', $back);
            }
        }

        // 统计
        $total = (int)$db->getScalar("SELECT COUNT(*) FROM WapClass");

        $this->strTitle = '栏目管理';
        $this->showTop($this->strTitle);
        echo '<div class="subtitle">栏目管理</div><div><a href="index.php">返回后台首页</a></div>';

        // 模块筛选
        echo '<div class="content"><form method="get" action="class.php">模块：<select name="module" onchange="this.form.submit()">'
            . '<option value="">全部(' . $total . ')</option>';
        foreach ($this->modules as $m) {
            $sel = ($module === $m) ? ' selected="selected"' : '';
            echo '<option value="' . $m . '"' . $sel . '>' . $m . '</option>';
        }
        echo '</select> <input type="hidden" name="q" value="' . $this->ToHtm($q) . '"/>'
            . '<input type="submit" value="筛选"/></form></div>';

        // 搜索
        echo '<div class="content"><form method="get" action="class.php">'
            . '搜索：<input type="text" name="q" value="' . $this->ToHtm($q) . '" placeholder="栏目ID/名称"/>'
            . '<input type="hidden" name="module" value="' . $this->ToHtm($module) . '"/>'
            . '<input type="submit" value="查询"/></form></div>';

        // 新增/编辑表单
        $editId = (int)($_GET['edit'] ?? 0);
        $row = null;
        $isEdit = true;
        if ($editId > 0) {
            $row = $db->getOne("SELECT * FROM WapClass WHERE id=?",[$editId]);
            if (!$row) { echo '<div class="error">栏目不存在</div>'; $row = []; }
        } else {
            $isEdit = false;
            $row = ['id' => 0, 'classname' => '', 'module' => ($module !== '' ? $module : 'article'),
                'orderid' => 0, 'parentid' => 0, 'isshow' => 1, 'postgroup' => '', 'managegroup' => ''];
        }
        $act = $isEdit ? 'save' : 'add';
        echo '<div class="content"><form method="post" action="' . $back . '">'
            . '<input type="hidden" name="action" value="' . $act . '"/>'
            . '<input type="hidden" name="id" value="' . (int)$row['id'] . '"/>'
            . '<div>栏目名称<br/><input type="text" name="classname" value="' . $this->ToHtm($row['classname'] ?? '') . '" size="20"/></div>'
            . '<div>所属模块 <select name="module">';
        foreach ($this->modules as $m) {
            $sel = (($row['module'] ?? '') === $m) ? ' selected="selected"' : '';
            echo '<option value="' . $m . '"' . $sel . '>' . $m . '</option>';
        }
        echo '</select></div>'
            . '<div>排序 <input type="text" name="orderid" value="' . (int)($row['orderid'] ?? 0) . '" size="5"/></div>'
            . '<div>父栏目ID <input type="text" name="parentid" value="' . (int)($row['parentid'] ?? 0) . '" size="5"/>'
            . ' <span class="info">0=顶级；父栏目须同模块，不能是自身或下级</span></div>'
            . '<div>投稿分组 <input type="text" name="postgroup" value="' . $this->ToHtm($row['postgroup'] ?? '') . '" size="10"/>'
            . ' <span class="info">逗号分隔如 1,2,9 (1普通/2VIP/9超管)，空=不限</span></div>'
            . '<div>版主分组 <input type="text" name="managegroup" value="' . $this->ToHtm($row['managegroup'] ?? '') . '" size="10"/>'
            . ' <span class="info">逗号分隔分组ID，空=仅超管</span></div>'
            . '<div><label><input type="checkbox" name="isshow" value="1"' . (empty($row['isshow']) ? '' : ' checked="checked"') . '/>前台显示</label></div>'
            . '<div><input type="submit" value="' . ($isEdit ? '保存栏目' : '添加栏目') . '"/></div>'
            . '</form></div>';

        // 列表（模块筛选 + 关键词搜索 + 分页）
        $where = '1=1';
        $params = [];
        if ($module !== '') {
            $where .= ' AND module=?';
            $params[] = $module;
        }
        if ($q !== '') {
            $where .= ' AND (id=? OR classname LIKE ?)';
            $params[] = (int)$q;
            $params[] = '%' . $q . '%';
        }
        $result = $db->getPage('WapClass', '*', $where, 'orderid ASC, id ASC', $page, 20, $params);

        // 父栏目名称映射
        $nameMap = [];
        foreach ($db->getAll("SELECT id,classname FROM WapClass") as $c) {
            $nameMap[(int)$c['id']] = $c['classname'];
        }

        echo '<div class="content"><ul class="list">';
        foreach ($result['list'] as $c) {
            $cid = (int)$c['id'];
            $hide = empty($c['isshow']) ? '<span style="color:red">[隐藏]</span>' : '';
            $parentTxt = (int)$c['parentid'] > 0
                ? ' 上级:' . $this->ToHtm($nameMap[(int)$c['parentid']] ?? ('#' . (int)$c['parentid']))
                : ' 顶级';
            $contentCount = 0;
            if (isset($this->contentTable[$c['module']])) {
                $contentCount = (int)$db->getScalar("SELECT COUNT(*) FROM " . $this->contentTable[$c['module']] . " WHERE classid=?", [$cid]);
            }
            echo '<li>ID:' . $cid . ' <a href="' . $back . '&edit=' . $cid . '">' . $this->ToHtm($c['classname']) . '</a>' . $hide
                . ' <span class="info">[' . $this->ToHtm($c['module'] ?? '') . ']</span>'
                . ' 排序:' . (int)$c['orderid'] . $parentTxt . ' 内容:' . $contentCount
                . ' <a href="content.php?module=' . urlencode((string)$c['module']) . '">内容管理</a>'
                . ' <a href="' . $back . '&act=del&id=' . $cid . '">删除</a></li>';
        }
        if (empty($result['list'])) echo '<li>暂无栏目</li>';
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

    /** 父栏目校验：存在、同模块、非自身、非下级（防循环） */
    private function checkParent($parentid, $id, $module, $back) {
        if ($parentid <= 0) return;
        if ($id > 0 && $parentid === $id) {
            $this->showError('父栏目不能是自身', $back);
        }
        $parent = $this->db->getOne("SELECT id,module,parentid FROM WapClass WHERE id=?", [$parentid]);
        if (!$parent) {
            $this->showError('父栏目不存在', $back);
        }
        if ((string)$parent['module'] !== (string)$module) {
            $this->showError('父栏目与当前栏目模块不一致', $back);
        }
        // 沿父链上溯，若回到当前栏目则形成循环
        $cur = $parent;
        $guard = 0;
        while ($cur && (int)$cur['parentid'] > 0) {
            if ($id > 0 && (int)$cur['parentid'] === $id) {
                $this->showError('父栏目不能是当前栏目的下级', $back);
            }
            $cur = $this->db->getOne("SELECT id,parentid FROM WapClass WHERE id=?", [(int)$cur['parentid']]);
            if (++$guard > 200) break;
        }
    }

    /** 删除二次确认页 */
    private function showDeleteConfirm($id, $module, $q, $back) {
        $db = $this->db;
        $row = $id > 0 ? $db->getOne("SELECT * FROM WapClass WHERE id=?", [$id]) : null;
        if (!$row) $this->showError('栏目不存在', $back);
        $children = (int)$db->getScalar("SELECT COUNT(*) FROM WapClass WHERE parentid=?", [$id]);
        $contents = isset($this->contentTable[$row['module']])
            ? (int)$db->getScalar("SELECT COUNT(*) FROM " . $this->contentTable[$row['module']] . " WHERE classid=?", [$id])
            : 0;

        $this->strTitle = '删除栏目确认';
        $this->showTop($this->strTitle);
        echo '<div class="subtitle">删除栏目确认</div>';
        echo '<div><a href="' . $back . '">返回栏目列表</a></div>';
        echo '<div class="content">栏目：' . $this->ToHtm($row['classname']) . '（ID:' . (int)$row['id'] . '）'
            . ' 模块：' . $this->ToHtm($row['module']) . '<br/>'
            . '子栏目：' . $children . ' 个　栏目内容：' . $contents . ' 条<br/>'
            . '删除后不可恢复，请谨慎操作。';
        if ($children > 0 || $contents > 0) {
            $reason = $children > 0 ? '该栏目下存在 ' . $children . ' 个子栏目' : '该栏目下存在 ' . $contents . ' 条内容';
            echo '<div class="error">' . $reason . '，无法删除。请先清理后再操作。</div></div>';
        } else {
            echo '<div class="error">确认删除该栏目？</div>';
            echo '<form method="post" action="' . $back . '">'
                . '<input type="hidden" name="action" value="deldone"/>'
                . '<input type="hidden" name="id" value="' . (int)$row['id'] . '"/>'
                . '<input type="submit" value="确认删除"/></form></div>';
        }
        $this->showDown();
        exit;
    }

    /** 执行删除（子栏目与栏目内容拦截） */
    private function doDelete($id, $module, $q, $back) {
        $db = $this->db;
        $row = $id > 0 ? $db->getOne("SELECT * FROM WapClass WHERE id=?", [$id]) : null;
        if (!$row) $this->showError('栏目不存在', $back);
        $children = (int)$db->getScalar("SELECT COUNT(*) FROM WapClass WHERE parentid=?", [$id]);
        if ($children > 0) {
            $this->showError('该栏目下存在 ' . $children . ' 个子栏目，无法删除', $back);
        }
        $contents = isset($this->contentTable[$row['module']])
            ? (int)$db->getScalar("SELECT COUNT(*) FROM " . $this->contentTable[$row['module']] . " WHERE classid=?", [$id])
            : 0;
        if ($contents > 0) {
            $this->showError('该栏目下存在 ' . $contents . ' 条内容，无法删除', $back);
        }
        $db->execute("DELETE FROM WapClass WHERE id=?", [$id]);
        kl_cache_clear_site();
    }
}

$page = new AdminClassPage();
$page->run();
