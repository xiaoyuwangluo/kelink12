<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 农场模块 - 收获成熟作物 (exp+10 gold+15，按经验重算等级，状态 ready -> empty)
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class NongchangHarvestPage extends WapPage {
    const RIPE_SECONDS = 1800; // 30分钟成熟

    public function run() {
        $this->checkLogin();
        $db = $this->db;
        $uid = (int)$this->userSession->getUserId();
        $pid = (int)($_GET['id'] ?? 0);
        if ($pid < 1 || $pid > 6) $this->showError('地块参数错误');

        $farm = $db->getOne("SELECT * FROM WapNongChang WHERE userid=?",[$uid]);
        if (!$farm) $this->showError('请先访问农场主页开垦土地');

        $plots = json_decode($farm['plots'] ?? '[]', true);
        if (!is_array($plots)) $plots = [];

        $idx = -1;
        foreach ($plots as $i => $p) {
            if ((int)($p['id'] ?? 0) === $pid) { $idx = $i; break; }
        }
        if ($idx < 0) $this->showError('地块不存在');

        $status = $plots[$idx]['status'] ?? 'empty';
        if ($status === 'empty') $this->showError('空地无可收获作物');
        if ($status === 'growing') {
            $left = self::RIPE_SECONDS - (time() - (int)($plots[$idx]['planttime'] ?? 0));
            if ($left > 0) $this->showError('作物尚未成熟，还需' . (int)ceil($left / 60) . '分钟');
            $status = 'ready'; // 已到时间，视为成熟
        }
        if ($status !== 'ready') $this->showError('该地块当前不可收获');

        // 结算: 经验+10 金币+15 等级=经验/100+1
        $exp = (int)$farm['exp'] + 10;
        $gold = (int)$farm['gold'] + 15;
        $level = intdiv($exp, 100) + 1;

        $plots[$idx] = ['id' => $pid, 'crop' => '', 'status' => 'empty', 'planttime' => 0];
        $db->execute("UPDATE WapNongChang SET plots=?, exp=?, gold=?, level=? WHERE id=?",
            [json_encode($plots, JSON_UNESCAPED_UNICODE), $exp, $gold, $level, (int)$farm['id']]);

        header('Location: index.php?classid=' . $this->classid);
        exit;
    }
}

$page = new NongchangHarvestPage();
$page->run();
