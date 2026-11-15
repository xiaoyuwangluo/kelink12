<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 管理后台 - 系统设置 (config.php 核心常量只读展示 + WapSite 可编辑项)
 * config.php 常量为只读，不可通过本页面修改；可编辑项(copyright/closetip/siteinfo)存入 WapSite 表 id=1 行
 */
require_once __DIR__ . '/../includes/bootstrap.php';

class AdminSettingPage extends WapPage {
    /** 敏感值脱敏展示 */
    private function mask($val) {
        $val = (string)$val;
        if ($val === '') return '(空)';
        if (strlen($val) <= 2) return '**';
        return substr($val, 0, 2) . str_repeat('*', max(4, strlen($val) - 2));
    }

    /** 核心常量分组定义 (只读展示) */
    private function constGroups() {
        return [
            '数据库' => [
                'KL_DB_TYPE' => '数据库类型',
                'KL_SQL_SERVERIP' => '数据库IP/服务名',
                'KL_SQL_UserName' => '数据库用户名',
                'KL_SQL_PassWord' => '数据库密码(脱敏)',
                'KL_DatabaseName' => '主数据库名',
                'KL_BAK_SQLCONN' => '备份连接串',
            ],
            '开关' => [
                'KL_VisiteCount_Close' => '流量统计(0开1关)',
                'ISAPI_Rewrite3_Open' => '伪静态(0关1开)',
                'KL_search_need_login' => '查询需登录',
                'KL_CLOSE_LOG_INFO' => '关闭活动日志',
                'KL_ShowAllError' => '显示详细错误',
                'KL_OpenCache' => '缓存开关',
                'KL_OpenCacheTime' => '缓存小时数',
                'KL_CheckDomain' => '校验域名绑定',
                'KL_HiddenQuery' => '隐藏URL参数',
                'KL_RegUser_CheckCode' => '注册验证码(0关1开)',
                'KL_Weixin_Reg_Open' => '微信注册开关',
                'KL_AliPay_Open' => '支付宝开关',
                'KL_WxPay_Open' => '微信支付开关',
                'KL_QQ_Login_Open' => 'QQ登录开关',
            ],
            '安全' => [
                'KL_In' => '防注入关键词',
                'KL_Filter_All' => '全局过滤词',
                'KL_In_Open' => '防注入模式(0全局1局部)',
                'KL_NotDownAndUpload' => '禁止上传/下载扩展名',
                'KL_SessionTimeOut' => '会话超时(分钟)',
                'KL_CheckIPTime' => '防刷间隔(秒)',
                'KL_LoginTime' => '登录失败限制',
                'KL_CHECKCODE' => '超管伪验证码(脱敏)',
                'KL_GetIP_FromCDN' => 'CDN取真实IP',
                'KL_WAPAdmin_NeedPassWord' => '后台需密码',
                'KL_UpMaxSize' => '上传大小限制(KB)',
                'KL_UpAllowExt' => '允许上传扩展名',
            ],
            '模块限制' => [
                'KL_CheckBBSCount' => '每天发贴数',
                'KL_CheckBBSreCount' => '每天回贴数',
                'KL_SendMSGCount' => '每天站内短信数',
                'KL_CheckZoneReCount' => '每天空间留言数',
                'KL_ADDFriendCount' => '每天加好友数',
            ],
            '下载' => [
                'KL_DownCheck' => '下载防盗链',
                'KL_DownCheckReferrer' => '校验Referrer',
                'KL_DownThread' => '下载线程数',
                'KL_DownSpeed' => '限速(字节/秒)',
            ],
        ];
    }

    public function run() {
        $this->checkAdminLogin();
        $db = $this->db;
        $back = 'setting.php?siteid=' . $this->siteid;

        // POST 保存可编辑项 (仅更新 WapSite id=1，无法修改 config.php 常量)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->userSession->checkSubmitLimit()) {
                $this->showError('提交过于频繁，请稍后再试', $back);
            }
            $action = $_POST['action'] ?? '';
            if ($action === 'save') {
                if (!$db->getOne("SELECT id FROM WapSite WHERE id=1")) {
                    $this->showError('站点表无 id=1 记录，无法保存设置', $back);
                }
                $n = $db->execute("UPDATE WapSite SET copyright=?, closetip=?, siteinfo=? WHERE id=1", [
                    trim($_POST['copyright'] ?? ''),
                    trim($_POST['closetip'] ?? ''),
                    trim($_POST['siteinfo'] ?? ''),
                ]);
                $this->showSuccess($n > 0 ? '系统设置保存成功' : '无变更或保存失败', $back);
            } else {
                $this->showError('非法操作', $back);
            }
        }

        $this->strTitle = '系统设置';
        $this->showTop($this->strTitle);
        echo '<div class="subtitle">系统设置</div><div><a href="index.php">返回后台首页</a></div>';

        // 可编辑项 (存 WapSite 表 id=1)
        $site = $db->getOne("SELECT * FROM WapSite WHERE id=1");
        if ($site) {
            echo '<div class="content"><form method="post" action="' . $back . '">'
                . '<input type="hidden" name="action" value="save"/>'
                . '<div>版权信息<br/><input type="text" name="copyright" value="' . $this->ToHtm($site['copyright'] ?? '') . '" size="40"/></div>'
                . '<div>关闭提示语<br/><input type="text" name="closetip" value="' . $this->ToHtm($site['closetip'] ?? '') . '" size="40"/></div>'
                . '<div>站点信息<br/><textarea name="siteinfo" rows="4" cols="40">' . $this->ToHtm($site['siteinfo'] ?? '') . '</textarea></div>'
                . '<div><input type="submit" value="保存设置"/></div>'
                . '</form><div class="time">注：以上设置存入站点表；config.php 常量只读，不可在此修改。</div></div>';
        } else {
            echo '<div class="error">站点表无 id=1 记录，可编辑项已跳过</div>';
        }

        // 只读常量分组展示
        foreach ($this->constGroups() as $group => $items) {
            echo '<div class="content"><div class="subtitle">' . $this->ToHtm($group) . '</div><ul class="list">';
            foreach ($items as $name => $label) {
                if (!defined($name)) continue;
                $val = (string)constant($name);
                if ($name === 'KL_SQL_PassWord' || $name === 'KL_CHECKCODE') {
                    $val = $this->mask($val);
                }
                echo '<li>' . $this->ToHtm($label) . ' <span class="time">' . $this->ToHtm($name) . '</span>: ' . $this->ToHtm($val) . '</li>';
            }
            echo '</ul></div>';
        }

        $this->showDown();
    }
}

$page = new AdminSettingPage();
$page->run();
