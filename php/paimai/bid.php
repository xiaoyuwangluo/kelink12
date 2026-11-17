<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 拍卖模块 - 出价 (对应原 paimai 出价页)
 * 规则：出价须高于当前最高价，且加价幅度不低于最小加价10元；写入 WapPaimaiBid
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class PaimaiBidPage extends WapPage {
    const MIN_STEP = 10; // 最小加价幅度(元)

    public function run() {
        $this->checkLogin();
        $db = $this->db;
        $uid = $this->userSession->getUserId();
        $paimaiid = (int)($_GET['paimaiid'] ?? $_POST['paimaiid'] ?? 0);
        if ($paimaiid <= 0) $this->showError('参数错误');

        $item = $db->getOne("SELECT * FROM WapPaimai WHERE id=? AND isshow=1",[$paimaiid]);
        if (!$item) $this->showError('拍卖品不存在');

        $top = $db->getOne("SELECT MAX(price) AS top FROM WapPaimaiBid WHERE paimaiid=?", [$paimaiid]);
        $curTop = (int)($top['top'] ?? 0); // 当前最高价(无人出价则为0)
        $minNext = $curTop + self::MIN_STEP;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $price = (int)($_POST['price'] ?? 0);
            if ($price < $minNext) {
                $this->showError('出价无效：须为不低于 ￥' . $minNext . ' 的整数（当前最高价￥' . $curTop . '，最小加价' . self::MIN_STEP . '元）');
            }
            $nickname = $this->userVo['nickname'] ?: $this->userVo['username'];
            $db->insert("INSERT INTO WapPaimaiBid (paimaiid,userid,nickname,price,addtime) VALUES (?,?,?,?,NOW())",[$paimaiid,$uid,$nickname,$price]);
            $this->showSuccess('出价成功！您的出价 ￥' . $price,
                'view.php?classid=' . $this->classid . '&id=' . $paimaiid);
            return;
        }

        // 出价页
        $this->strTitle = '我要出价';
        $this->showTop($this->strTitle);
        echo '<div class="ui-breadcrumb"><a href="' . $this->http_start . 'wapindex.php">首页</a> / <a href="list.php?classid=' . $this->classid . '">拍卖</a> / <a href="view.php?classid=' . $this->classid . '&id=' . $paimaiid . '">拍品</a> / 出价</div>';
        echo '<div class="subtitle">出价 - ' . $this->ToHtm($item['title']) . '</div>';
        echo '<div class="ui-card">';
        echo '<div class="ui-flex ui-between ui-center"><span class="ui-tag ui-tag-muted">当前最高价</span><span class="price">￥' . $curTop . '</span></div>';
        echo '<div class="ui-flex ui-between ui-center ui-mt-2"><span class="ui-tag ui-tag-muted">最低可出价</span><span class="ui-tag">￥' . $minNext . '</span></div>';
        echo '<div class="ui-alert ui-alert-info">加价幅度 ' . self::MIN_STEP . ' 元，请填写不低于最低可出价的整数。</div>';
        echo '<form method="post" action="bid.php?classid=' . $this->classid . '&paimaiid=' . $paimaiid . '">';
        echo '<div class="ui-field"><label for="price">我的出价(元)</label>';
        echo '<input type="number" id="price" name="price" min="' . $minNext . '" step="1" required/></div>';
        echo '<input class="ui-btn ui-btn-primary ui-btn-block" type="submit" value="确认出价"/></form>';
        echo '</div>';
        echo '<div class="ui-flex ui-gap-2 ui-mt-2"><a class="ui-btn ui-btn-ghost" href="view.php?classid=' . $this->classid . '&id=' . $paimaiid . '">返回拍品</a></div>';
        $this->showDown();
    }
}
$page = new PaimaiBidPage();
$page->run();
