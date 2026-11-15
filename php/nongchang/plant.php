<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 农场模块 - 空地播种 (扣5金币，状态 empty -> growing)
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class NongchangPlantPage extends WapPage {
    const RIPE_SECONDS = 1800; // 30分钟成熟
    const CROPS = ['萝卜', '小麦', '玉米', '番茄'];

    public function run() {
        $this->checkLogin();
        $db = $this->db;
        $uid = (int)$this->userSession->getUserId();
        $pid = (int)($_GET['id'] ?? 0);
        if ($pid < 1 || $pid > 6) $this->showError('地块参数错误');

        $farm = $db->getOne("SELECT * FROM WapNongChang WHERE userid=? AND siteid=?", [$uid, $this->siteid]);
        if (!$farm) $this->showError('请先访问农场主页开垦土地');

        $plots = json_decode($farm['plots'] ?? '[]', true);
        if (!is_array($plots)) $plots = [];

        // 定位地块
        $idx = -1;
        foreach ($plots as $i => $p) {
            if ((int)($p['id'] ?? 0) === $pid) { $idx = $i; break; }
        }
        if ($idx < 0) $this->showError('地块不存在');
        if (($plots[$idx]['status'] ?? 'empty') !== 'empty') $this->showError('该地块不是空地，无法播种');

        // 扣除金币
        $gold = (int)$farm['gold'];
        if ($gold < 5) $this->showError('金币不足，播种需要5金币');

        // 作物白名单
        $crop = $_GET['crop'] ?? '萝卜';
        if (!in_array($crop, self::CROPS, true)) $crop = '萝卜';

        $plots[$idx] = ['id' => $pid, 'crop' => $crop, 'status' => 'growing', 'planttime' => time()];
        $db->execute("UPDATE WapNongChang SET plots=?, gold=? WHERE id=?",
            [json_encode($plots, JSON_UNESCAPED_UNICODE), $gold - 5, (int)$farm['id']]);

        header('Location: index.php?siteid=' . $this->siteid . '&classid=' . $this->classid);
        exit;
    }
}

$page = new NongchangPlantPage();
$page->run();
