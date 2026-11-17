<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 管理后台 - 充值卡管理 (对应原 WapCard 点卡管理)
 * 批量生成(随机卡号/密码md5存储) / 搜索 / 状态筛选 / 列表 / 作废二次确认
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class AdminCardPage extends WapPage {
    /** 生成随机卡号：12位数字 */
    private function makeCardNo() {
        $no = '';
        for ($i = 0; $i < 12; $i++) $no .= (string)random_int(0, 9);
        return $no;
    }

    /** 生成随机卡密明文：10位字母数字 */
    private function makeCardPwd() {
        $chars = 'abcdefghjkmnpqrstuvwxyz23456789ABCDEFGHJKLMNPQRSTUVWXYZ';
        $pwd = '';
        $max = strlen($chars) - 1;
        for ($i = 0; $i < 10; $i++) $pwd .= $chars[random_int(0, $max)];
        return $pwd;
    }

    /** 列表回跳地址（保留状态/关键词） */
    private function backUrl($status, $q) {
        $url = 'card.php?status=' . urlencode((string)$status);
        if ($q !== '') $url .= '&q=' . urlencode($q);
        return $url;
    }

    public function run() {
        $this->checkAdminLogin();
        $db = $this->db;

        $q = trim($_GET['q'] ?? '');
        $status = $_GET['status'] ?? 'all';
        if (!in_array($status, ['all', 'unused', 'used'], true)) $status = 'all';
        $back = $this->backUrl($status, $q);
        $generated = [];

        // 作废二次确认页
        if (($_GET['act'] ?? '') === 'void') {
            $this->showVoidConfirm((int)($_GET['id'] ?? 0), $back);
            return;
        }

        // POST 操作：批量生成 / 作废（防重复提交）
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->userSession->checkSubmitLimit()) {
                $this->showError('提交过于频繁，请稍后再试', $back);
            }
            $action = $_POST['action'] ?? '';
            if ($action === 'gen') {
                $num = min(100, max(1, (int)($_POST['num'] ?? 1)));
                $cardtype = trim($_POST['cardtype'] ?? '') ?: 'money';
                $money = max(0, (int)($_POST['money'] ?? 0));
                for ($i = 0; $i < $num; $i++) {
                    // 卡号查重
                    $cardno = $this->makeCardNo();
                    $try = 0;
                    while ($db->getScalar("SELECT COUNT(*) FROM WapCard WHERE cardno=?", [$cardno]) > 0 && $try < 10) {
                        $cardno = $this->makeCardNo();
                        $try++;
                    }
                    $plain = $this->makeCardPwd();
                    $db->insert("INSERT INTO WapCard (cardno,cardpwd,cardtype,money,isused,addtime) VALUES (?,?,?,?,0,?)",
                        [$cardno, md5($plain), $cardtype, $money, date('Y-m-d H:i:s')]);
                    $generated[] = [$cardno, $plain];
                }
            } elseif ($action === 'voiddone' || $action === 'void') {
                $id = (int)($_POST['id'] ?? 0);
                $row = $id > 0 ? $db->getOne("SELECT * FROM WapCard WHERE id=?",[$id]) : null;
                if (!$row) {
                    $this->showError('充值卡不存在', $back);
                }
                if (!empty($row['isused'])) {
                    $this->showError('该卡已被使用，不能作废', $back);
                }
                // 作废：删除未使用的卡密记录
                $db->execute("DELETE FROM WapCard WHERE id=?",[$id]);
                $this->showSuccess('充值卡已作废', $back);
            } else {
                $this->showError('非法操作', $back);
            }
        }

        $this->strTitle = '充值卡管理';
        $adminTitle = '充值卡管理';
        $activeNav = 'card';
        require __DIR__ . '/inc/header.php';
        echo '<div class="subtitle">充值卡管理</div><div><a href="index.php">返回后台首页</a></div>';

        // 批量生成表单
        echo '<div class="content"><form method="post" action="' . $back . '">'
            . '<input type="hidden" name="action" value="gen"/>'
            . '数量 <input type="text" name="num" size="4" value="5"/> '
            . '类型 <input type="text" name="cardtype" size="8" value="money"/> '
            . '面值 <input type="text" name="money" size="5" value="100"/> '
            . '<input type="submit" value="批量生成"/></form></div>';

        // 生成结果（明文卡密仅本次展示，数据库存md5）
        if ($generated) {
            echo '<div class="success">已生成 ' . count($generated) . ' 张充值卡，请保存卡密：<br/>';
            foreach ($generated as $g) {
                echo $this->ToHtm($g[0]) . ' - ' . $this->ToHtm($g[1]) . '<br/>';
            }
            echo '</div>';
        }

        // 统计（随搜索条件联动）
        $baseWhere = '1=1';
        $params = [];
        if ($q !== '') {
            $baseWhere .= ' AND (id=? OR cardno LIKE ? OR cardtype LIKE ?)';
            $params[] = (int)$q;
            $params[] = '%' . $q . '%';
            $params[] = '%' . $q . '%';
        }
        $total = (int)$db->getScalar("SELECT COUNT(*) FROM WapCard WHERE " . $baseWhere, $params);
        $unusedCount = (int)$db->getScalar("SELECT COUNT(*) FROM WapCard WHERE " . $baseWhere . " AND isused=0", $params);
        $usedCount = (int)$db->getScalar("SELECT COUNT(*) FROM WapCard WHERE " . $baseWhere . " AND isused<>0", $params);

        // 状态筛选
        echo '<div class="content">筛选：';
        $filters = [
            'all' => '全部(' . $total . ')',
            'unused' => '未使用(' . $unusedCount . ')',
            'used' => '已使用(' . $usedCount . ')',
        ];
        foreach ($filters as $k => $label) {
            echo ($status === $k)
                ? '<strong>[' . $label . ']</strong> '
                : '<a href="' . $this->backUrl($k, $q) . '">' . $label . '</a> ';
        }
        echo '</div>';

        // 搜索
        echo '<div class="content"><form method="get" action="card.php">'
            . '搜索：<input type="text" name="q" value="' . $this->ToHtm($q) . '" placeholder="ID/卡号/类型"/>'
            . '<input type="hidden" name="status" value="' . $this->ToHtm($status) . '"/>'
            . '<input type="submit" value="查询"/></form>';
        if ($q !== '') echo ' <a href="card.php">清除搜索</a>';
        echo '</div>';

        // 列表条件
        $where = $baseWhere;
        if ($status === 'unused') {
            $where .= ' AND isused=0';
        } elseif ($status === 'used') {
            $where .= ' AND isused<>0';
        }
        $page = max(1, (int)($_GET['page'] ?? 1));
        $result = $db->getPage('WapCard', '*', $where, 'id DESC', $page, 20, $params);

        // 使用人名称映射
        $users = [];
        foreach ($db->getAll("SELECT id,username FROM WapUser") as $u) {
            $users[(int)$u['id']] = $u['username'];
        }

        echo '<div class="content">共' . $result['total'] . '张<br/><ul class="list">';
        foreach ($result['list'] as $c) {
            $cid = (int)$c['id'];
            $statusTxt = empty($c['isused']) ? '<span style="color:green">未使用</span>' : '<span style="color:gray">已使用</span>';
            $time = !empty($c['addtime']) ? date('m-d H:i', strtotime($c['addtime'])) : '';
            echo '<li>ID:' . $cid . ' ' . $this->ToHtm($c['cardno']) . ' 类型:' . $this->ToHtm($c['cardtype'] ?? '')
                . ' 面值:' . (int)$c['money'] . ' ' . $statusTxt . ' <span class="time">' . $time . '</span>';
            if (!empty($c['isused'])) {
                $uid = (int)($c['userid'] ?? 0);
                $userTxt = $uid > 0 ? $this->ToHtm($users[$uid] ?? ('#' . $uid)) : '未知会员';
                $usedTime = !empty($c['usetime']) ? date('m-d H:i', strtotime($c['usetime'])) : '未记录';
                echo ' 使用人:' . $userTxt . ' 时间:' . $usedTime;
            } else {
                echo ' <a href="' . $back . ($q !== '' ? '&' : '?') . 'act=void&id=' . $cid . '">作废</a>';
            }
            echo '</li>';
        }
        if (empty($result['list'])) echo '<li>暂无充值卡</li>';
        echo '</ul></div>';

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

    /** 作废二次确认页 */
    private function showVoidConfirm($id, $back) {
        $row = $id > 0 ? $this->db->getOne("SELECT * FROM WapCard WHERE id=?", [$id]) : null;
        if (!$row) $this->showError('充值卡不存在', $back);
        if (!empty($row['isused'])) $this->showError('该卡已被使用，不能作废', $back);

        $this->strTitle = '作废充值卡确认';
        $adminTitle = '作废充值卡确认';
        $activeNav = 'card';
        require __DIR__ . '/inc/header.php';
        echo '<div class="subtitle">作废充值卡确认</div>';
        echo '<div><a href="' . $back . '">返回充值卡列表</a></div>';
        echo '<div class="content">卡号：' . $this->ToHtm($row['cardno']) . '<br/>'
            . '类型：' . $this->ToHtm($row['cardtype'] ?? '') . '　面值：' . (int)$row['money'] . '<br/>'
            . '作废后该卡将无法充值，请谨慎操作。';
        echo '<div class="error">确认作废该充值卡？</div>';
        echo '<form method="post" action="' . $back . '">'
            . '<input type="hidden" name="action" value="voiddone"/>'
            . '<input type="hidden" name="id" value="' . (int)$row['id'] . '"/>'
            . '<input type="submit" value="确认作废"/></form></div>';
        require __DIR__ . '/inc/footer.php';
        exit;
    }
}

$page = new AdminCardPage();
$page->run();
