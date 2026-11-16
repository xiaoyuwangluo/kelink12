<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 每日签到 (对应原 signin/index.aspx)
 * 今日已签则提示；未签则写入 WapSignin，按连续天数加积分(+10/天，连续7天翻倍)
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class SigninPage extends WapPage {
    public function run() {
        $db = $this->db;
        $this->checkLogin();
        $uid = (int)$this->userSession->getUserId();
        $today = date('Y-m-d');

        $this->strTitle = '每日签到';
        $this->showTop($this->strTitle);
        echo '<div class="subtitle">每日签到</div>';

        // 今日是否已签
        $signed = $db->getOne("SELECT * FROM WapSignin WHERE userid=? AND signdate=?", [$uid, $today]);
        if ($signed) {
            echo '<div class="content">您今日已签到，已连续签到 <strong>' . (int)$signed['days'] . '</strong> 天。<br/>请勿重复签到。</div>';
            $this->showHistory($uid);
            echo '<div><a href="../myfile.php">返回用户中心</a></div>';
            $this->showDown();
            return;
        }

        // 计算连续天数：昨日有签到记录则延续，否则重新从1开始
        $last = $db->getOne("SELECT * FROM WapSignin WHERE userid=? ORDER BY signdate DESC LIMIT 1", [$uid]);
        $days = 1;
        if ($last && $last['signdate'] === date('Y-m-d', strtotime('-1 day'))) {
            $days = (int)$last['days'] + 1;
        }

        // 积分奖励：每天+10，连续满7天(第7/14/21...天)翻倍
        $point = 10;
        if ($days % 7 === 0) $point *= 2;

        $db->insert("INSERT INTO WapSignin (userid,signdate,days,addtime) VALUES (?,?,?,NOW())",[$uid,$today,$days]);
        $db->execute("UPDATE WapUser SET point=point+? WHERE id=?", [$point, $uid]);

        echo '<div class="success">签到成功！连续 ' . $days . ' 天，获得积分 ' . $point . ($days % 7 === 0 ? '(连续7天翻倍)' : '') . '</div>';
        $this->showHistory($uid);
        echo '<div><a href="../myfile.php">返回用户中心</a></div>';
        $this->showDown();
    }

    /** 签到记录概况 */
    private function showHistory($uid) {
        $db = $this->db;
        $total = (int)$db->getScalar("SELECT COUNT(*) FROM WapSignin WHERE userid=?", [$uid]);
        echo '<div class="content">累计签到 ' . $total . ' 天</div>';
    }
}

$page = new SigninPage();
$page->run();
