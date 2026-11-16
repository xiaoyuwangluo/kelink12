<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 短信发送 (对应原 SMS/systemlist.aspx)
 * 表单(手机号+内容) → 写入 WapSms(status=1 模拟发送成功)，按会话每日限量 KL_SendMSGCount
 */
require_once __DIR__ . '/includes/bootstrap.php';

class SmsPage extends WapPage {
    public function run() {
        $this->showTop($this->GetLang('短信发送|短信发送|短信发送'));
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->doSend();
        } else {
            $this->showForm();
        }
        $this->showDown();
    }

    /** 发送表单 */
    private function showForm() {
        $used = (int)($_SESSION['kl_sms_count'] ?? 0);
        echo '<div class="subtitle">' . $this->GetLang('短信发送(模拟)|短信发送(模拟)|短信发送(模拟)') . '</div>';
        echo '<div class="content"><form method="post" action="sms.php">';
        echo '手机号:<input type="text" name="mobile" maxlength="11" required/><br/>';
        echo '内容:<textarea name="content" rows="3" cols="20" required></textarea><br/>';
        echo '<input type="submit" value="发送短信"/></form></div>';
        echo '<div class="tip">今日已发送 ' . $used . ' 条，每日限额 ' . (int)KL_SendMSGCount . ' 条。</div>';
    }

    /** 处理发送 */
    private function doSend() {
        $mobile = trim($_POST['mobile'] ?? '');
        $content = trim($_POST['content'] ?? '');

        if ($mobile === '' || $content === '') {
            echo '<div class="error">请填写手机号和短信内容</div>';
            $this->showForm();
            return;
        }
        if (!preg_match('/^1[3-9]\d{9}$/', $mobile)) {
            echo '<div class="error">手机号格式不正确</div>';
            $this->showForm();
            return;
        }

        // 每日限量 (按会话计数，跨天自动重置)
        $today = date('Y-m-d');
        if (($_SESSION['kl_sms_date'] ?? '') !== $today) {
            $_SESSION['kl_sms_date'] = $today;
            $_SESSION['kl_sms_count'] = 0;
        }
        if ((int)$_SESSION['kl_sms_count'] >= KL_SendMSGCount) {
            echo '<div class="error">已达今日发送限额(' . (int)KL_SendMSGCount . '条)，请明日再试</div>';
            $this->showForm();
            return;
        }

        // 写入短信表，status=1 模拟发送成功
        $this->db->insert("INSERT INTO WapSms (mobile,content,status,addtime) VALUES (?,?,?,NOW())",[$mobile,$content,1]);
        $_SESSION['kl_sms_count'] = (int)$_SESSION['kl_sms_count'] + 1;

        echo '<div class="success">短信发送成功(模拟)！收件号码：' . $this->ToHtm($mobile) . '</div>';
        $this->showForm();
    }
}

$page = new SmsPage();
$page->run();
