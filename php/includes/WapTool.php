<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * WapTool 工具类 (对应原 ASP.NET 版 WapTool 工具类)
 * 负责 UBB 解析、WML/HTML 转换、头部底部输出、版本切换等
 */
if (!defined('KL_ROOT')) exit;

class WapTool {
    public $wmlVo = null;   // 当前版本信息对象
    public $siteVo = null;  // 站点信息对象
    public $classVo = null; // 栏目信息对象

    public function __construct($wmlVo = null, $siteVo = null, $classVo = null) {
        $this->wmlVo = $wmlVo;
        $this->siteVo = $siteVo;
        $this->classVo = $classVo;
    }

    /**
     * 显示页面头部
     * 对应 WapTool.showTop(title, wmlVo)
     */
    public function showTop($title = '', $wmlVo = null) {
        $wmlVo = $wmlVo ?: $this->wmlVo;
        $ver = $wmlVo->ver ?? '2';
        $siteVo = $this->siteVo;
        $siteName = $siteVo->sitename ?? '小宇网络';

        if ($ver == '1') {
            // WML 1.0 头部
            $html = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
            $html .= '<!DOCTYPE wml PUBLIC "-//WAPFORUM//DTD WML 1.1//EN" "http://www.wapforum.org/DTD/wml_1.1.xml">' . "\n";
            $html .= '<wml><head><meta http-equiv="Cache-Control" content="no-cache"/></head>' . "\n";
            $html .= '<card id="main" title="' . $this->ToHtm($title) . '">' . "\n";
            $html .= '<p>' . "\n";
        } else {
            // WAP 2.0 / HTML 头部
            $charset = 'UTF-8';
            $html = '<!DOCTYPE html>' . "\n";
            $html .= '<html><head>' . "\n";
            $html .= '<meta http-equiv="Content-Type" content="text/html; charset=' . $charset . '" />' . "\n";
            $html .= '<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />' . "\n";
            // 模板样式 (templates/{dir}/style.css，站点 template 字段选择)
            $tplDir = preg_replace('/[^a-z0-9_\-]/i', '', (string)($this->siteVo->template ?? 'default'));
            if ($tplDir === '') $tplDir = 'default';
            $cssFile = KL_ROOT . '/templates/' . $tplDir . '/style.css';
            if (is_file($cssFile)) {
                $html .= '<link rel="stylesheet" href="' . $wmlVo->http_start . 'templates/' . $tplDir . '/style.css" />' . "\n";
            }
            $html .= '<title>' . $this->ToHtm($title ?: $siteName) . '</title>' . "\n";
            // 全局顶部广告
            $topAd = $this->getGlobalAd('top');
            if ($topAd) $html .= $this->ToWML($topAd, $wmlVo);
            $html .= '</head><body>' . "\n";
        }
        return $html;
    }

    /**
     * 显示页面底部
     * 对应 WapTool.showDown(wmlVo)
     */
    public function showDown($wmlVo = null) {
        $wmlVo = $wmlVo ?: $this->wmlVo;
        $ver = $wmlVo->ver ?? '2';
        $siteVo = $this->siteVo;
        $copyright = $siteVo->copyright ?? KL_COPYRIGHT;

        $html = '';
        // 全局底部广告
        $downAd = $this->getGlobalAd('down');
        if ($downAd) $html .= $this->ToWML($downAd, $wmlVo);
        // 版权
        $html .= '<div class="copyright">' . $this->ToWML($copyright, $wmlVo) . '</div>' . "\n";

        if ($ver == '1') {
            $html .= '</p></card></wml>';
        } else {
            $html .= '</body></html>';
        }
        return $html;
    }

    /**
     * 获取版本切换链接
     * 对应 WapTool.GetVS(wmlVo)
     */
    public function GetVS($wmlVo = null) {
        $wmlVo = $wmlVo ?: $this->wmlVo;
        $siteid = $wmlVo->siteid ?? 0;
        $classid = $wmlVo->classid ?? 0;
        $curVer = $wmlVo->ver ?? '2';
        $http = $wmlVo->http_start ?? '';

        $versions = [
            '1' => 'WAP1.0',
            '2' => 'WAP2.0',
            '3' => '电脑版',
            '4' => '触屏版',
        ];
        $html = '<div class="versionswitch">';
        foreach ($versions as $v => $name) {
            if ($v == $curVer) {
                $html .= '<span class="curver">' . $name . '</span> ';
            } else {
                $html .= '<a href="' . $http . 'wapindex.php?siteid=' . $siteid . '&classid=' . $classid . '&ver=' . $v . '">' . $name . '</a> ';
            }
        }
        $html .= '</div>';
        return $html;
    }

    /**
     * UBB/WML 转换
     * 对应 WapTool.ToWML(str, wmlVo)
     * 将 UBB 代码转换为对应版本的 WML 或 HTML
     */
    public function ToWML($str, $wmlVo = null) {
        if ($str === '' || $str === null) return '';
        $wmlVo = $wmlVo ?: $this->wmlVo;
        $ver = $wmlVo->ver ?? '2';

        // 先处理换行
        $str = str_replace("\r\n", "\n", $str);
        $str = str_replace("\r", "\n", $str);

        // UBB 标签解析 (核心)
        $str = $this->parseUBB($str, $wmlVo);

        // 普通文本HTML处理
        if ($ver == '1') {
            // WML 1.0: 换行转 <br/>
            $str = str_replace("\n", "<br/>\n", $str);
        } else {
            $str = str_replace("\n", "<br/>\n", $str);
        }

        return $str;
    }

    /**
     * 万能调用模块映射 (对应原版 [功能模块=N_M_X_Y] 中的 M)
     * N=调用条数 M=模块ID X=站点ID Y=栏目ID(0为不限)
     */
    public static $MODULE_MAP = [
        1=>'article', 2=>'bbs', 3=>'shop', 4=>'novel', 5=>'video',
        6=>'picture', 7=>'download', 8=>'ring', 9=>'link', 10=>'guessbook',
        11=>'gongqiu', 12=>'paimai', 13=>'yuehui', 14=>'wabao', 15=>'guangbo',
        16=>'adlink', 17=>'airplane', 18=>'album', 19=>'hotel', 20=>'rizhi', 21=>'xinzhang',
    ];
    public static $MODULE_TABLE = [
        'article'=>'WapArticle', 'bbs'=>'WapBbs', 'shop'=>'WapShop', 'novel'=>'WapNovel',
        'video'=>'WapVideo', 'picture'=>'WapPicture', 'download'=>'WapDownload', 'ring'=>'WapRing',
        'link'=>'WapLink', 'guessbook'=>'WapGuestbook', 'gongqiu'=>'WapGongqiu', 'paimai'=>'WapPaimai',
        'yuehui'=>'WapYuehui', 'wabao'=>'WapWabao', 'guangbo'=>'WapGuangbo', 'adlink'=>'WapAdlink',
        'airplane'=>'WapAirplane', 'album'=>'WapAlbum', 'hotel'=>'WapHotel', 'rizhi'=>'WapRizhi',
        'xinzhang'=>'WapXinzhang',
    ];

    /**
     * 万能调用 [功能模块=N_M_X_Y]
     * 动态调用指定站点/栏目下某模块最新N条内容
     */
    public function callModule($n, $moduleId, $siteid, $classid, $wmlVo = null) {
        $wmlVo = $wmlVo ?: $this->wmlVo;
        $module = self::$MODULE_MAP[$moduleId] ?? '';
        $table = self::$MODULE_TABLE[$module] ?? '';
        if ($module === '' || $n < 1) return '';
        $n = min(50, $n);
        try {
            $db = Database::getInstance();
            $where = 'siteid=? AND isshow=1';
            $params = [$siteid];
            if ($classid > 0) { $where .= ' AND classid=?'; $params[] = $classid; }
            $order = 'id DESC';
            $result = $db->getPage($table, 'id,title', $where, $order, 1, $n, $params);
        } catch (Exception $e) {
            return '';
        }
        if (empty($result['list'])) return '';
        $http = $wmlVo->http_start ?? '/';
        $html = '<div class="callmodule">';
        foreach ($result['list'] as $row) {
            $url = $http . $module . '/view.php?siteid=' . $siteid . '&classid=' . $classid . '&id=' . $row['id'];
            $html .= '<a href="' . $url . '">' . $this->ToHtm($row['title']) . '</a><br/>';
        }
        $html .= '</div>';
        return $html;
    }

    /** 当前在线人数 */
    public function onlineCount($siteid = 0) {
        try {
            $db = Database::getInstance();
            $limit = date('Y-m-d H:i:s', time() - KL_DelOnlineTime * 60);
            if ($siteid > 0) {
                return (int)$db->getScalar("SELECT COUNT(*) FROM WapOnline WHERE siteid=? AND lasttime>?", [$siteid, $limit]);
            }
            return (int)$db->getScalar("SELECT COUNT(*) FROM WapOnline WHERE lasttime>?", [$limit]);
        } catch (Exception $e) { return 0; }
    }

    /**
     * UBB 标签解析
     * 支持 [b][i][u][url][img][color][size][font] 等
     * 以及万能调用 [功能模块=N_M_X_Y]
     */
    private function parseUBB($str, $wmlVo) {
        $ver = $wmlVo->ver ?? '2';
        $isWML = ($ver == '1');

        // 万能调用 [功能模块=N_M_X_Y]
        $str = preg_replace_callback('/\[功能模块=(\d+)_(\d+)_(\d+)_(\d+)\]/', function($m) use ($wmlVo) {
            return $this->callModule((int)$m[1], (int)$m[2], (int)$m[3], (int)$m[4], $wmlVo);
        }, $str);

        // [b]粗体[/b]
        $str = preg_replace('/\[b\](.*?)\[\/b\]/is', $isWML ? '<b>$1</b>' : '<strong>$1</strong>', $str);
        // [i]斜体[/i]
        $str = preg_replace('/\[i\](.*?)\[\/i\]/is', '<em>$1</em>', $str);
        // [u]下划线[/u]
        $str = preg_replace('/\[u\](.*?)\[\/u\]/is', '<u>$1</u>', $str);
        // [url=链接]文字[/url] 或 [url]链接[/url]
        $str = preg_replace('/\[url=([^\]]+)\](.*?)\[\/url\]/is', '<a href="$1">$2</a>', $str);
        $str = preg_replace('/\[url\](.*?)\[\/url\]/is', '<a href="$1">$1</a>', $str);
        // [img=地址] 或 [img]地址[/img]
        $str = preg_replace('/\[img=([^\]]+)\]/is', '<img src="$1" alt="" />', $str);
        $str = preg_replace('/\[img\](.*?)\[\/img\]/is', '<img src="$1" alt="" />', $str);
        // [color=颜色]文字[/color]
        $str = preg_replace('/\[color=([^\]]+)\](.*?)\[\/color\]/is', '<span style="color:$1">$2</span>', $str);
        // [size=大小]文字[/size]
        $str = preg_replace('/\[size=([^\]]+)\](.*?)\[\/size\]/is', '<span style="font-size:$1px">$2</span>', $str);
        // [font=字体]文字[/font]
        $str = preg_replace('/\[font=([^\]]+)\](.*?)\[\/font\]/is', '<span style="font-family:$1">$2</span>', $str);
        // [align=center]...[/align]
        $str = preg_replace('/\[align=([^\]]+)\](.*?)\[\/align\]/is', '<div style="text-align:$1">$2</div>', $str);
        // [quote]引用[/quote]
        $str = preg_replace('/\[quote\](.*?)\[\/quote\]/is', '<blockquote>$1</blockquote>', $str);
        // [code]代码[/code]
        $str = preg_replace('/\[code\](.*?)\[\/code\]/is', '<pre><code>$1</code></pre>', $str);
        // [hr]
        $str = preg_replace('/\[hr\]/i', '<hr/>', $str);
        // [br]
        $str = preg_replace('/\[br\]/i', '<br/>', $str);
        // [email]邮箱[/email]
        $str = preg_replace('/\[email\](.*?)\[\/email\]/is', '<a href="mailto:$1">$1</a>', $str);
        // [fly]文字[/fly] 跑马灯
        $str = preg_replace('/\[fly\](.*?)\[\/fly\]/is', '<marquee>$1</marquee>', $str);
        // [move]...[/move]
        $str = preg_replace('/\[move\](.*?)\[\/move\]/is', '<marquee>$1</marquee>', $str);
        // [list][*]项[/list]
        $str = preg_replace_callback('/\[list\](.*?)\[\/list\]/is', function($m) {
            return $this->parseList($m[1]);
        }, $str);

        return $str;
    }

    private function parseList($content) {
        $items = preg_split('/\[\*\]/', $content);
        $html = '<ul>';
        foreach ($items as $item) {
            $item = trim($item);
            if ($item !== '') $html .= '<li>' . $item . '</li>';
        }
        $html .= '</ul>';
        return $html;
    }

    /**
     * HTML 编码
     * 对应 this.ToHtm()
     */
    public function ToHtm($str) {
        if ($str === null) return '';
        return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
    }

    /**
     * 获取全局顶部/底部广告
     */
    private function getGlobalAd($pos) {
        // 可从数据库 site 表读取 KL_PAGE_TOP / KL_PAGE_DOWN
        // 此处返回配置默认值
        if ($pos == 'top') return defined('KL_PAGE_TOP') ? KL_PAGE_TOP : '';
        return defined('KL_PAGE_DOWN') ? KL_PAGE_DOWN : '';
    }

    /**
     * 生成验证码
     */
    public static function makeCheckCode($len = 4) {
        $code = '';
        for ($i = 0; $i < $len; $i++) {
            $code .= mt_rand(0, 9);
        }
        return $code;
    }

    /**
     * 检查防SQL注入
     */
    public static function checkSqlInject($str) {
        $filter = explode('|', KL_In);
        foreach ($filter as $f) {
            if ($f !== '' && stripos($str, $f) !== false) {
                return true;
            }
        }
        return false;
    }
}
