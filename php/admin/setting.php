<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 管理后台 - 系统设置 (config.php 核心常量只读展示 + WapSite 可编辑项)
 * config.php 常量为只读，不可通过本页面修改；可编辑项(sitename/copyright/siteinfo/logourl/domain/isclose/closetip)存入 WapSite 表 id=1 行
 */
require_once __DIR__ . '/../includes/bootstrap.php';
require_once KL_ROOT . '/includes/Community.php';

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
        $back = 'setting.php';

        // POST 保存可编辑项 (仅更新 WapSite id=1，无法修改 config.php 常量)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->userSession->checkSubmitLimit()) {
                $this->showError('提交过于频繁，请稍后再试', $back);
            }
            $action = $_POST['action'] ?? '';
            if ($action === 'save') {
                $site = $db->getOne("SELECT id FROM WapSite WHERE id=1");
                if (!$site) {
                    $this->showError('站点表无 id=1 记录，无法保存设置', $back);
                }
                $sitename = trim($_POST['sitename'] ?? '');
                $copyright = trim($_POST['copyright'] ?? '');
                $siteinfo = trim($_POST['siteinfo'] ?? '');
                $logourl = trim($_POST['logourl'] ?? '');
                $domain = trim($_POST['domain'] ?? '');
                $isclose = (int)($_POST['isclose'] ?? 0) === 1 ? 1 : 0;
                $closetip = trim($_POST['closetip'] ?? '');

                if ($sitename === '') {
                    $this->showError('站点名称不能为空', $back);
                }
                if (mb_strlen($sitename) > 50) {
                    $this->showError('站点名称不能超过 50 个字符', $back);
                }
                if (mb_strlen($copyright) > 255) {
                    $this->showError('版权信息不能超过 255 个字符', $back);
                }
                if (mb_strlen($siteinfo) > 2000) {
                    $this->showError('站点信息不能超过 2000 个字符', $back);
                }
                if (mb_strlen($closetip) > 255) {
                    $this->showError('关闭提示语不能超过 255 个字符', $back);
                }
                if ($logourl !== '' && !preg_match('~^(https?://|/)~i', $logourl)) {
                    $this->showError('Logo 地址需以 http://、https:// 或 / 开头', $back);
                }
                if ($domain !== '' && !preg_match('/^[a-zA-Z0-9.\-]+$/', $domain)) {
                    $this->showError('绑定域名只能包含字母、数字、点与短横线', $back);
                }
                $db->execute("UPDATE WapSite SET sitename=?, copyright=?, siteinfo=?, logourl=?, domain=?, isclose=?, closetip=? WHERE id=1", [
                    $sitename, $copyright, $siteinfo, $logourl, $domain, $isclose, $closetip,
                ]);
                kl_cache_clear_site();
                $this->showSuccess('系统设置保存成功，缓存已清理', $back);
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
            $isclose = (int)($site['isclose'] ?? 0);
            echo '<div class="content"><form method="post" action="' . $back . '">'
                . '<input type="hidden" name="action" value="save"/>'
                . '<div>站点名称<br/><input type="text" name="sitename" maxlength="50" value="' . $this->ToHtm($site['sitename'] ?? '') . '" size="40"/></div>'
                . '<div>关闭站点<br/><select name="isclose">'
                . '<option value="0"' . ($isclose === 0 ? ' selected="selected"' : '') . '>正常开放</option>'
                . '<option value="1"' . ($isclose === 1 ? ' selected="selected"' : '') . '>关闭访问</option>'
                . '</select></div>'
                . '<div>绑定域名<br/><input type="text" name="domain" maxlength="100" value="' . $this->ToHtm($site['domain'] ?? '') . '" size="40" placeholder="example.com"/></div>'
                . '<div>Logo 地址<br/><input type="text" name="logourl" maxlength="255" value="' . $this->ToHtm($site['logourl'] ?? '') . '" size="40" placeholder="http:// 或 / 开头"/></div>'
                . '<div>版权信息<br/><input type="text" name="copyright" maxlength="255" value="' . $this->ToHtm($site['copyright'] ?? '') . '" size="40"/></div>'
                . '<div>关闭提示语<br/><input type="text" name="closetip" maxlength="255" value="' . $this->ToHtm($site['closetip'] ?? '') . '" size="40"/></div>'
                . '<div>站点信息<br/><textarea name="siteinfo" rows="4" cols="40">' . $this->ToHtm($site['siteinfo'] ?? '') . '</textarea></div>'
                . '<div><input type="submit" value="保存设置"/></div>'
                . '</form><div class="time">当前模板：' . $this->ToHtm($site['template'] ?? '') . '（模板切换请在 config.php 配置）</div>'
                . '<div class="time">注：以上设置存入站点表；config.php 常量只读，不可在此修改。</div></div>';
        } else {
            echo '<div class="error">站点表无 id=1 记录，可编辑项已跳过</div>';
        }

        // 只读常量：分组筛选 + 关键词搜索
        $groups = $this->constGroups();
        $curGroup = $_GET['group'] ?? 'all';
        if ($curGroup !== 'all' && !isset($groups[$curGroup])) $curGroup = 'all';
        $q = trim($_GET['q'] ?? '');

        // 按关键词预筛，统计各分组命中数
        $filteredGroups = [];
        foreach ($groups as $name => $items) {
            $hit = [];
            foreach ($items as $const => $label) {
                if (!defined($const)) continue;
                if ($q !== '' && stripos($const, $q) === false && stripos($label, $q) === false) continue;
                $hit[$const] = $label;
            }
            $filteredGroups[$name] = $hit;
        }
        $totalHits = 0;
        foreach ($filteredGroups as $hit) $totalHits += count($hit);

        echo '<div class="content"><div class="subtitle">配置常量（只读）</div>分组：';
        $groupUrl = function ($g) use ($q) {
            return 'setting.php?group=' . urlencode($g) . ($q !== '' ? '&q=' . urlencode($q) : '');
        };
        echo ($curGroup === 'all')
            ? '<strong>[全部(' . $totalHits . ')]</strong> '
            : '<a href="' . $groupUrl('all') . '">全部(' . $totalHits . ')</a> ';
        foreach ($filteredGroups as $name => $hit) {
            echo ($curGroup === $name)
                ? '<strong>[' . $this->ToHtm($name) . '(' . count($hit) . ')]</strong> '
                : '<a href="' . $groupUrl($name) . '">' . $this->ToHtm($name) . '(' . count($hit) . ')</a> ';
        }
        echo '<br/><form method="get" action="setting.php">搜索常量：'
            . '<input type="text" name="q" value="' . $this->ToHtm($q) . '" placeholder="常量名/说明"/>'
            . '<input type="hidden" name="group" value="' . $this->ToHtm($curGroup) . '"/>'
            . '<input type="submit" value="查询"/></form>';
        if ($q !== '') echo '<a href="setting.php?group=' . urlencode($curGroup) . '">清除搜索</a>';
        echo '</div>';

        $shown = 0;
        foreach ($filteredGroups as $group => $items) {
            if ($curGroup !== 'all' && $curGroup !== $group) continue;
            if (empty($items)) continue;
            $shown++;
            echo '<div class="content"><div class="subtitle">' . $this->ToHtm($group) . '</div><ul class="list">';
            foreach ($items as $name => $label) {
                $val = (string)constant($name);
                if ($name === 'KL_SQL_PassWord' || $name === 'KL_CHECKCODE') {
                    $val = $this->mask($val);
                }
                echo '<li>' . $this->ToHtm($label) . ' <span class="time">' . $this->ToHtm($name) . '</span>: ' . $this->ToHtm($val) . '</li>';
            }
            echo '</ul></div>';
        }
        if ($shown === 0) echo '<div class="content">没有匹配的配置常量</div>';

        $this->showDown();
    }
}

$page = new AdminSettingPage();
$page->run();
