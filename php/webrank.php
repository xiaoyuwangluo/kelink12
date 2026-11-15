<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 站点排行 (对应原 WebRank.aspx)
 * WapSite 列表关联 WapVisite 按浏览量(pv)排序
 */
require_once __DIR__ . '/includes/bootstrap.php';

class WebRankPage extends WapPage {
    public function run() {
        $this->showTop($this->GetLang('站点排行|站点排行|站点排行'));

        $db = $this->db;
        // 站点 + 流量汇总，按 pv 降序排行
        $rows = $db->getAll(
            "SELECT s.id, s.sitename, COALESCE(SUM(v.pv),0) AS pv, COALESCE(SUM(v.uv),0) AS uv "
            . "FROM WapSite s LEFT JOIN WapVisite v ON v.siteid = s.id "
            . "GROUP BY s.id, s.sitename ORDER BY pv DESC, s.id ASC LIMIT 50"
        );

        echo '<div class="subtitle">' . $this->GetLang('站点流量排行榜|站点流量排行榜|站点流量排行榜') . '</div>';
        echo '<div class="content"><ul class="list">';
        if (empty($rows)) {
            echo '<li>暂无站点数据</li>';
        }
        $i = 0;
        foreach ($rows as $row) {
            $i++;
            echo '<li>' . $i . '. <a href="wapindex.php?siteid=' . (int)$row['id'] . '">' . $this->ToHtm($row['sitename']) . '</a>';
            echo ' <span class="time">(pv:' . (int)$row['pv'] . ' uv:' . (int)$row['uv'] . ')</span></li>';
        }
        echo '</ul></div>';

        echo '<div class="content"><a href="wapindex.php?siteid=' . $this->siteid . '">返回首页</a> | ';
        echo '<a href="wapregsite.php?siteid=' . $this->siteid . '">我也要建站</a></div>';
        $this->showDown();
    }
}

$page = new WebRankPage();
$page->run();
