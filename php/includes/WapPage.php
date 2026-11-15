<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * WapPage 基类 (对应原 ASP.NET 版各页面继承的基类)
 * 所有模块页面继承此类，提供站点初始化、版本识别、公共属性方法
 */
if (!defined('KL_ROOT')) exit;
require_once KL_ROOT . '/includes/Database.php';
require_once KL_ROOT . '/includes/WapTool.php';
require_once KL_ROOT . '/includes/UserSession.php';

class WapPage {
    // 公共属性 (对应原 base.* 属性)
    public $siteid = 0;
    public $classid = 0;
    public $ver = '2';              // 1=WAP1.0 2=WAP2.0 3=电脑版 4=触屏版
    public $siteVo = null;          // 站点信息
    public $classVo = null;         // 栏目信息
    public $wmlVo = null;           // 版本信息
    public $userVo = null;          // 当前用户信息
    public $strTitle = '';          // 页面标题
    public $CopyRight = '';         // 版权信息
    public $http_start = '';        // URL前缀
    public $strShowHtml = '';       // 完整页面HTML (部分页面直接输出)
    public $KL_Site_INFO = '';      // 站点信息UBB
    public $updateTime = '';        // 当前版本号
    public $latest = '0';           // 最新版本号
    public $needUpdate = false;     // 是否需要升级
    public $info = '';              // 版本说明

    protected $db;
    protected $wapTool;
    protected $userSession;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->wapTool = new WapTool();
        $this->userSession = new UserSession();
        $this->updateTime = KL_Version;
        $this->CopyRight = KL_COPYRIGHT;
        $this->http_start = $this->getHttpStart();

        $this->initParams();
        $this->detectVersion();
        $this->loadSiteInfo();
        $this->loadClassInfo();
        $this->buildWmlVo();

        // 绑定到 wapTool
        $this->wapTool->siteVo = $this->siteVo;
        $this->wapTool->classVo = $this->classVo;
        $this->wapTool->wmlVo = $this->wmlVo;

        // 在线人数与流量统计
        $this->trackOnline();
    }

    /**
     * 在线人数统计 + 流量统计 (对应原 WapOnline / visiteCount)
     * 受 KL_VisiteCount_Close 开关控制流量记录
     */
    protected function trackOnline() {
        try {
            $uid = isset($_SESSION['kl_userid']) ? (int)$_SESSION['kl_userid'] : 0;
            $uname = isset($_SESSION['kl_username']) ? $_SESSION['kl_username'] : '游客';
            $ip = UserSession::getClientIp();
            $page = basename($_SERVER['SCRIPT_NAME'] ?? '');
            $now = date('Y-m-d H:i:s');
            $row = $this->db->getOne("SELECT id FROM WapOnline WHERE ip=? AND userid=?", [$ip, $uid]);
            if ($row) {
                $this->db->execute("UPDATE WapOnline SET lasttime=?, page=? WHERE id=?", [$now, $page, $row['id']]);
            } else {
                $this->db->insert("INSERT INTO WapOnline (siteid,userid,username,ip,page,lasttime) VALUES (?,?,?,?,?,?)",
                    [$this->siteid, $uid, $uname, $ip, $page, $now]);
            }
            // 清理超时在线 (KL_DelOnlineTime 分钟)
            $limit = date('Y-m-d H:i:s', time() - KL_DelOnlineTime * 60);
            $this->db->execute("DELETE FROM WapOnline WHERE lasttime < ?", [$limit]);

            // 流量统计 (KL_VisiteCount_Close: 0开 1关)
            if (KL_VisiteCount_Close == 0 && $this->siteid > 0) {
                $today = date('Y-m-d');
                $vis = $this->db->getOne("SELECT id FROM WapVisite WHERE siteid=? AND visitdate=?", [$this->siteid, $today]);
                $isNewIp = !$this->db->getScalar("SELECT COUNT(*) FROM WapVisiteLog WHERE siteid=? AND ip=? AND date(addtime)=?",
                    [$this->siteid, $ip, $today]);
                if ($vis) {
                    $sql = "UPDATE WapVisite SET pv=pv+1" . ($isNewIp ? ",uv=uv+1" : "") . " WHERE id=?";
                    $this->db->execute($sql, [$vis['id']]);
                } else {
                    $this->db->insert("INSERT INTO WapVisite (siteid,visitdate,pv,uv) VALUES (?,?,1,1)", [$this->siteid, $today]);
                }
                if ($isNewIp) {
                    $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
                    $this->db->insert("INSERT INTO WapVisiteLog (siteid,ip,url,ua,addtime) VALUES (?,?,?,?,?)",
                        [$this->siteid, $ip, $page, substr($ua, 0, 200), $now]);
                }
            }
        } catch (Exception $e) {
            // 统计失败不影响页面
        }
    }

    /** 初始化请求参数 */
    protected function initParams() {
        $this->siteid = isset($_GET['siteid']) ? (int)$_GET['siteid'] : 0;
        $this->classid = isset($_GET['classid']) ? (int)$_GET['classid'] : 0;
        if (isset($_GET['ver'])) {
            $this->ver = $_GET['ver'];
        }
    }

    /** 识别版本 (对应原 UA 识别逻辑) */
    protected function detectVersion() {
        if ($this->ver != '') return; // 已指定
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $checks = [
            '0' => KL_GoVersion0,
            '1' => KL_GoVersion1,
            '4' => KL_GoVersion4,
            '3' => KL_GoVersion3,
            '2' => KL_GoVersion2,
        ];
        foreach ($checks as $v => $keywords) {
            if ($keywords === '') continue;
            foreach (explode('|', $keywords) as $kw) {
                if ($kw !== '' && stripos($ua, $kw) !== false) {
                    if ($v == '0') $this->ver = '1';
                    else $this->ver = $v;
                    return;
                }
            }
        }
        $this->ver = '2'; // 默认 WAP2.0
    }

    /** 加载站点信息 */
    protected function loadSiteInfo() {
        if ($this->siteid > 0) {
            $this->siteVo = $this->db->getOne("SELECT * FROM WapSite WHERE id=?", [$this->siteid]);
        }
        if (!$this->siteVo) {
            // 默认站点信息
            $this->siteVo = (object)[
                'id' => 0,
                'sitename' => KL_SITE_NAME,
                'copyright' => KL_COPYRIGHT,
                'siteinfo' => '',
            ];
        }
        $this->strTitle = $this->siteVo->sitename ?? KL_SITE_NAME;
        $this->CopyRight = $this->siteVo->copyright ?? KL_COPYRIGHT;
        $this->KL_Site_INFO = $this->siteVo->siteinfo ?? '';
    }

    /** 加载栏目信息 */
    protected function loadClassInfo() {
        if ($this->classid > 0) {
            $this->classVo = $this->db->getOne("SELECT * FROM WapClass WHERE id=?", [$this->classid]);
        }
        if (!$this->classVo) {
            $this->classVo = (object)['id' => 0, 'childid' => 0, 'classname' => '首页', 'siteid' => $this->siteid];
        }
    }

    /** 构建版本信息对象 */
    protected function buildWmlVo() {
        $this->wmlVo = (object)[
            'ver' => $this->ver,
            'siteid' => $this->siteid,
            'classid' => $this->classid,
            'http_start' => $this->http_start,
        ];
    }

    /** 获取 http(s) 前缀 */
    protected function getHttpStart() {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
        return $scheme . ($_SERVER['HTTP_HOST'] ?? 'localhost') . '/';
    }

    /**
     * 多语言/版本文本
     * 对应 this.GetLang("WAP1文本|WAP2文本|PC文本")
     */
    public function GetLang($text) {
        $parts = explode('|', $text);
        $ver = $this->ver;
        if ($ver == '1' && isset($parts[0])) return $parts[0];
        if (in_array($ver, ['2', '4']) && isset($parts[1])) return $parts[1];
        if ($ver == '3' && isset($parts[2])) return $parts[2];
        return $parts[0] ?? $text;
    }

    /** HTML 编码 */
    public function ToHtm($str) {
        return $this->wapTool->ToHtm($str);
    }

    /** 检查登录，未登录跳转 */
    public function checkLogin($redirect = true) {
        if (!$this->userSession->isLogin()) {
            if ($redirect) {
                $backurl = urlencode($_SERVER['REQUEST_URI'] ?? '');
                header('Location: ' . $this->http_start . 'waplogin.php?siteid=' . $this->siteid . '&backurl=' . $backurl);
                exit;
            }
            return false;
        }
        $this->userVo = $this->userSession->getUserVo();
        return true;
    }

    /** 检查管理员登录 */
    public function checkAdminLogin() {
        if (!$this->userSession->isAdminLogin()) {
            header('Location: ' . $this->http_start . 'admin/login.php');
            exit;
        }
    }

    /** 输出头部 */
    public function showTop($title = '') {
        echo $this->wapTool->showTop($title ?: $this->strTitle, $this->wmlVo);
    }

    /** 输出底部 */
    public function showDown() {
        echo $this->wapTool->showDown($this->wmlVo);
    }

    /** 快捷输出 */
    public function write($str) {
        echo $str;
    }

    /** 显示错误信息 */
    public function showError($msg, $backUrl = '') {
        $this->showTop('错误提示');
        echo '<div class="error">' . $this->ToHtm($msg) . '</div>';
        if ($backUrl) {
            echo '<div><a href="' . $backUrl . '">返回</a></div>';
        }
        $this->showDown();
        exit;
    }

    /** 成功提示 */
    public function showSuccess($msg, $backUrl = '') {
        $this->showTop('操作成功');
        echo '<div class="success">' . $this->ToHtm($msg) . '</div>';
        if ($backUrl) {
            echo '<div><a href="' . $backUrl . '">返回</a></div>';
        }
        $this->showDown();
        exit;
    }

    /** 运行页面 (子类实现) */
    public function run() {
        // 子类重写
    }
}
