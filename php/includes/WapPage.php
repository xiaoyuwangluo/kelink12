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
    /** 栏目级权限守卫生效的模块目录 (21 个内容模块，含 admin_WAPadd.php / book_re.php) */
    const POST_GUARD_MODULES = [
        'article', 'bbs', 'shop', 'novel', 'video', 'picture', 'download', 'ring', 'link',
        'guessbook', 'gongqiu', 'paimai', 'yuehui', 'wabao', 'guangbo', 'adlink', 'airplane',
        'album', 'hotel', 'rizhi', 'xinzhang',
    ];

    // 公共属性 (对应原 base.* 属性)
    public $classid = 0;
    public $ver = '';               // 1=WAP1.0 2=WAP2.0 3=电脑版 4=触屏版 (空=待UA识别)
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

        // 栏目级投稿/评论权限自动守卫 (仅对模块目录下 admin_WAPadd.php / book_re.php 生效)
        $this->guardPost();
    }

    /**
     * 读取权限字段 (兼容 classVo/userVo 为数组或对象两种形态)
     */
    protected function getPermField($vo, $field) {
        if (is_array($vo)) return trim((string)($vo[$field] ?? ''));
        if (is_object($vo)) return trim((string)($vo->$field ?? ''));
        return '';
    }

    /**
     * 解析逗号分隔的分组串 (如 "1,2,9") 为整型数组
     */
    protected function parseGroups($str) {
        $out = [];
        foreach (explode(',', $str) as $g) {
            $g = trim($g);
            if ($g !== '' && ctype_digit($g)) $out[] = (int)$g;
        }
        return $out;
    }

    /**
     * 当前登录用户的会员分组 (WapUser.usergroup，默认 1=普通会员)
     */
    protected function getUserGroup() {
        if (!$this->userVo) $this->userVo = $this->userSession->getUserVo();
        return (int)($this->getPermField($this->userVo, 'usergroup') ?: 1);
    }

    /**
     * 栏目投稿权限校验：当前用户 usergroup 是否在栏目 postgroup 允许列表内
     * - postgroup 为空 = 不限分组，放行
     * - 未登录会员 = 不拦截，维持各模块 KL_*_Anonymous_Open 游客开关现状
     * - 后台管理员 (kl_adminid) = 放行
     * @return bool true=有权限
     */
    public function checkPostPerm() {
        if (!$this->userSession->isLogin()) return true;
        if ($this->userSession->isAdminLogin()) return true;
        $postgroup = $this->getPermField($this->classVo, 'postgroup');
        if ($postgroup === '') return true; // 空=不限
        return in_array($this->getUserGroup(), $this->parseGroups($postgroup), true);
    }

    /**
     * 栏目版主(管理)权限校验：managegroup 或后台超管 kl_adminid
     * - managegroup 为空 = 仅超级管理员可管理
     * @return bool true=有权限
     */
    public function checkManagePerm() {
        if ($this->userSession->isAdminLogin()) return true; // kl_adminid 超管放行
        $managegroup = $this->getPermField($this->classVo, 'managegroup');
        if ($managegroup === '') return false; // 空=仅超管
        if (!$this->userSession->isLogin()) return false;
        return in_array($this->getUserGroup(), $this->parseGroups($managegroup), true);
    }

    /**
     * 投稿/评论页统一前置守卫：由基类构造末尾自动调用，21 个模块页面无需任何修改。
     * 通过 SCRIPT_NAME 自动识别：仅当脚本位于模块目录且文件名为
     * admin_WAPadd.php / book_re.php 时执行 checkPostPerm，失败输出"无权限"页并终止；
     * wapindex / list / view 等浏览页文件名不匹配，完全不受影响。
     */
    protected function guardPost() {
        $script = (string)($_SERVER['SCRIPT_NAME'] ?? '');
        if ($script === '') return;
        $file = basename($script);
        if ($file !== 'admin_WAPadd.php' && $file !== 'book_re.php') return;
        $dir = basename(dirname($script));
        if (!in_array($dir, self::POST_GUARD_MODULES, true)) return;
        if (!$this->checkPostPerm()) {
            $act = ($file === 'book_re.php') ? '发表评论' : '投稿';
            $this->showError('无权限：您的会员分组不允许在该栏目' . $act . '，请联系管理员调整分组',
                '../wapindex.php');
        }
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
                $this->db->insert("INSERT INTO WapOnline (userid,username,ip,page,lasttime) VALUES (?,?,?,?,?)",
                    [$uid, $uname, $ip, $page, $now]);
            }
            // 清理超时在线 (KL_DelOnlineTime 分钟)
            $limit = date('Y-m-d H:i:s', time() - KL_DelOnlineTime * 60);
            $this->db->execute("DELETE FROM WapOnline WHERE lasttime < ?", [$limit]);

            // 流量统计 (KL_VisiteCount_Close: 0开 1关)
            if (KL_VisiteCount_Close == 0) {
                $today = date('Y-m-d');
                $vis = $this->db->getOne("SELECT id FROM WapVisite WHERE visitdate=?", [$today]);
                $isNewIp = !$this->db->getScalar("SELECT COUNT(*) FROM WapVisiteLog WHERE ip=? AND date(addtime)=?",
                    [$ip, $today]);
                if ($vis) {
                    $sql = "UPDATE WapVisite SET pv=pv+1" . ($isNewIp ? ",uv=uv+1" : "") . " WHERE id=?";
                    $this->db->execute($sql, [$vis['id']]);
                } else {
                    $this->db->insert("INSERT INTO WapVisite (visitdate,pv,uv) VALUES (?,1,1)", [$today]);
                }
                if ($isNewIp) {
                    $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
                    $this->db->insert("INSERT INTO WapVisiteLog (ip,url,ua,addtime) VALUES (?,?,?,?)",
                        [$ip, $page, substr($ua, 0, 200), $now]);
                }
            }
        } catch (Exception $e) {
            // 统计失败不影响页面
        }
    }

    /** 初始化请求参数 */
    protected function initParams() {
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
        try {
            $this->siteVo = $this->db->getOne("SELECT * FROM WapSite ORDER BY id ASC LIMIT 1");
            // getOne 返回关联数组，统一转为对象以匹配 siteVo->field 的读取方式
            if (is_array($this->siteVo)) $this->siteVo = (object)$this->siteVo;
        } catch (Exception $e) {
            $this->siteVo = null;
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
            if (is_array($this->classVo)) $this->classVo = (object)$this->classVo;
        }
        if (!$this->classVo) {
            $this->classVo = (object)['id' => 0, 'childid' => 0, 'classname' => '首页'];
        }
    }

    /** 构建版本信息对象 */
    protected function buildWmlVo() {
        $this->wmlVo = (object)[
            'ver' => $this->ver,
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
                header('Location: ' . $this->http_start . 'waplogin.php?backurl=' . $backurl);
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
