<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * paimai模块 - 内容页
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class PaimaiViewPage extends WapPage {
    public function run() {
        $db = $this->db;
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) $this->showError('参数错误');
        $row = $db->getOne("SELECT * FROM WapPaimai WHERE id=?",[$id]);
        if (!$row) $this->showError('内容不存在');
        $db->execute("UPDATE WapPaimai SET hits=hits+1 WHERE id=?", [$id]);

        $class = $db->getOne("SELECT classname FROM WapClass WHERE id=?", [(int)$row['classid']]);
        $classname = $class['classname'] ?? '拍卖';

        $this->strTitle = $row['title'];
        $this->showTop($this->strTitle);
        echo '<div class="ui-breadcrumb"><a href="' . $this->http_start . 'wapindex.php">首页</a> / <a href="list.php?classid=' . $this->classid . '">' . $this->ToHtm($classname) . '</a> / 详情</div>';
        echo '<div class="subtitle">' . $this->ToHtm($row['title']) . '</div>';

        // 拍卖出价区：当前最高价、出价次数
        $top = $db->getOne("SELECT MAX(price) AS top, COUNT(*) AS cnt FROM WapPaimaiBid WHERE paimaiid=?", [$id]);
        $curTop = (int)($top['top'] ?? 0);
        $bidCount = (int)($top['cnt'] ?? 0);

        echo '<div class="ui-card">';
        echo '<div class="ui-flex ui-flex-wrap ui-gap-2">';
        echo '<span class="ui-tag ui-tag-muted">' . date('Y-m-d H:i', strtotime($row['addtime'])) . '</span>';
        echo '<span class="ui-tag ui-tag-muted">点击 ' . ((int)$row['hits'] + 1) . '</span>';
        echo '<span class="ui-tag">' . $this->ToHtm($classname) . '</span>';
        echo '</div>';
        if (!empty($row['picurl'])) echo '<div class="pic ui-mt-2"><img src="' . $this->ToHtm($row['picurl']) . '"/></div>';
        echo '<div class="content ui-mt-2">' . $this->wapTool->ToWML($row['content'], $this->wmlVo) . '</div>';
        echo '</div>';

        echo '<div class="ui-card bidbar">';
        echo '<div class="ui-card-head"><span class="ui-card-title">竞拍信息</span><span class="ui-card-extra">共 ' . $bidCount . ' 次出价</span></div>';
        echo '<div class="ui-flex ui-between ui-center"><span class="ui-tag ui-tag-muted">当前最高价</span><span class="price">￥' . $curTop . '</span></div>';
        echo '<div class="ui-alert ui-alert-info">每次加价不低于 10 元，出价须高于当前最高价。</div>';
        echo '<a class="ui-btn ui-btn-primary ui-btn-block" href="bid.php?classid=' . $this->classid . '&paimaiid=' . $id . '">我要出价</a>';
        echo '</div>';

        // 最近出价
        echo '<div class="ui-card">';
        echo '<div class="ui-card-head"><span class="ui-card-title">最近出价</span><span class="ui-card-extra">最高价优先</span></div>';
        if ($bidCount > 0) {
            $bids = $db->getAll("SELECT nickname,price,addtime FROM WapPaimaiBid WHERE paimaiid=? ORDER BY price DESC,id DESC LIMIT 5", [$id]);
            echo '<ul class="ui-list bidlist">';
            foreach ($bids as $b) {
                echo '<li><span class="ui-flex ui-between ui-center"><span>' . $this->ToHtm($b['nickname']) . '</span>'
                   . '<span class="ui-flex ui-gap-2 ui-center"><span class="price">￥' . (int)$b['price'] . '</span>'
                   . '<span class="ui-tag ui-tag-muted">' . date('m-d H:i', strtotime($b['addtime'])) . '</span></span></span></li>';
            }
            echo '</ul>';
        } else {
            echo '<div class="ui-empty"><div class="icon">🔨</div>暂无出价，快来抢占先机</div>';
        }
        echo '</div>';

        echo '<div class="replybar ui-flex ui-flex-wrap ui-gap-2 ui-mt-2">';
        if (!empty($row['downurl'])) echo '<a class="ui-btn" href="' . $this->http_start . 'down.php?id=' . $id . '">下载</a>';
        echo '<a class="ui-btn" href="book_re.php?classid=' . $this->classid . '&id=' . $id . '">评论</a>';
        echo '<a class="ui-btn ui-btn-ghost" href="list.php?classid=' . $this->classid . '">返回列表</a></div>';
        $this->showDown();
    }
}
$page = new PaimaiViewPage();
$page->run();
