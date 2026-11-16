<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 农场模块 - 农场主页 (6块地状态机: empty空地 -> growing种植中 -> ready可收获)
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class NongchangIndexPage extends WapPage {
    const PLOT_COUNT = 6;          // 地块数量
    const RIPE_SECONDS = 1800;     // 30分钟成熟

    public function run() {
        $this->checkLogin();
        $db = $this->db;
        $uid = (int)$this->userSession->getUserId();

        // 读取或初始化农场数据
        $farm = $db->getOne("SELECT * FROM WapNongChang WHERE userid=?",[$uid]);
        if (!$farm) {
            $plots = $this->defaultPlots();
            $db->insert("INSERT INTO WapNongChang (userid,level,exp,gold,plots,addtime) VALUES (?,1,0,100,?,NOW())",[$uid,json_encode($plots, JSON_UNESCAPED_UNICODE)]);
            $farm = $db->getOne("SELECT * FROM WapNongChang WHERE userid=?",[$uid]);
        }

        $plots = json_decode($farm['plots'] ?? '[]', true);
        if (!is_array($plots)) $plots = [];
        // 补齐至6块地
        for ($i = count($plots) + 1; $i <= self::PLOT_COUNT; $i++) {
            $plots[] = ['id' => $i, 'crop' => '', 'status' => 'empty', 'planttime' => 0];
        }

        // 成熟检测: growing 超过30分钟自动转 ready
        $changed = false;
        $now = time();
        foreach ($plots as $i => $p) {
            $status = $p['status'] ?? 'empty';
            if ($status === 'growing' && $now - (int)($p['planttime'] ?? 0) >= self::RIPE_SECONDS) {
                $plots[$i]['status'] = 'ready';
                $changed = true;
            }
        }
        if ($changed) {
            $db->execute("UPDATE WapNongChang SET plots=? WHERE id=?",
                [json_encode($plots, JSON_UNESCAPED_UNICODE), (int)$farm['id']]);
        }

        $this->strTitle = $this->classVo->classname ?? '农场';
        $this->showTop($this->strTitle);
        echo '<div class="subtitle">' . $this->ToHtm($this->strTitle) . '</div>';

        echo '<div class="content">';
        echo '<div>等级：' . (int)$farm['level'] . ' 级 (' . $this->expNext((int)$farm['exp'], (int)$farm['level']) . ')</div>';
        echo '<div>经验：' . (int)$farm['exp'] . '</div>';
        echo '<div>金币：' . (int)$farm['gold'] . '</div>';
        echo '</div>';

        // 渲染6块地
        echo '<div class="content"><ul class="list">';
        $crops = ['萝卜', '小麦', '玉米', '番茄'];
        foreach ($plots as $p) {
            $pid = (int)($p['id'] ?? 0);
            $status = $p['status'] ?? 'empty';
            echo '<li>【' . $pid . '号地】';
            if ($status === 'empty') {
                echo '空地 ';
                echo '<form method="get" action="plant.php" style="display:inline">';
                echo '<input type="hidden" name="classid" value="' . $this->classid . '"/>';
                echo '<input type="hidden" name="id" value="' . $pid . '"/>';
                echo '<select name="crop">';
                foreach ($crops as $c) echo '<option value="' . $this->ToHtm($c) . '">' . $this->ToHtm($c) . '</option>';
                echo '</select>';
                echo '<input type="submit" value="播种(5金币)"/>';
                echo '</form>';
            } elseif ($status === 'growing') {
                $left = max(0, self::RIPE_SECONDS - ($now - (int)($p['planttime'] ?? 0)));
                echo $this->ToHtm($p['crop'] ?? '') . ' 种植中，还需 ' . (int)ceil($left / 60) . ' 分钟';
            } elseif ($status === 'ready') {
                echo $this->ToHtm($p['crop'] ?? '') . ' 已成熟 ';
                echo '<a href="harvest.php?classid=' . $this->classid . '&id=' . $pid . '">收获</a>';
            } else {
                echo '空地';
            }
            echo '</li>';
        }
        echo '</ul></div>';

        echo '<div class="actionbar"><a href="../wapindex.php">返回首页</a></div>';
        $this->showDown();
    }

    /** 默认6块空地 */
    private function defaultPlots() {
        $plots = [];
        for ($i = 1; $i <= self::PLOT_COUNT; $i++) {
            $plots[] = ['id' => $i, 'crop' => '', 'status' => 'empty', 'planttime' => 0];
        }
        return $plots;
    }

    /** 升级进度提示 */
    private function expNext($exp, $level) {
        $need = $level * 100;
        return '距下一级还需' . max(0, $need - $exp) . '经验';
    }
}

$page = new NongchangIndexPage();
$page->run();
