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
    public $wmlObStarted = false; // WAP1.0 输出缓冲是否已启动 (防止重复 ob_start)

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
            // WML 1.0 头部：WML 响应类型 + XML 声明 + DOCTYPE + card 起始标签
            if (!headers_sent()) {
                header('Content-Type: text/vnd.wap.wml; charset=UTF-8');
            }
            $html = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
            $html .= '<!DOCTYPE wml PUBLIC "-//WAPFORUM//DTD WML 1.1//EN" "http://www.wapforum.org/DTD/wml_1.1.xml">' . "\n";
            $html .= '<wml><head><meta forua="true" http-equiv="Cache-Control" content="max-age=0"/></head>' . "\n";
            $html .= '<card id="main" title="' . $this->ToHtm($title) . '">' . "\n";
            // 全局顶部广告 (转为 WML 段落输出)
            $topAd = $this->getGlobalAd('top');
            if ($topAd) $html .= $this->ToWML($topAd, $wmlVo);
            // 启动输出缓冲：页面直接 echo 的 HTML 片段在输出前统一降级为 WML
            if (!$this->wmlObStarted) {
                $this->wmlObStarted = true;
                ob_start(array($this, 'wmlize'));
            }
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

        if ($ver == '1') {
            // 版权段落 (<p>) + 闭合 card/wml
            $html .= '<p>' . $this->ToWML($copyright, $wmlVo) . '</p>' . "\n";
            $html .= '</card></wml>';
        } else {
            // 版权
            $html .= '<div class="copyright">' . $this->ToWML($copyright, $wmlVo) . '</div>' . "\n";
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
        $classid = $wmlVo->classid ?? 0;
        $curVer = $wmlVo->ver ?? '2';
        $http = $wmlVo->http_start ?? '';

        $versions = [
            '1' => 'WAP1.0',
            '2' => 'WAP2.0',
            '3' => '电脑版',
            '4' => '触屏版',
        ];
        if ($curVer == '1') {
            // WML 1.0: 版本切换输出 WML 链接 (<p> 内 <a> 序列，URL 中 & 转 &amp;)
            $html = '<p>';
            foreach ($versions as $v => $name) {
                if ($v == $curVer) {
                    $html .= $name . '<br/>';
                } else {
                    $html .= '<a href="' . $http . 'wapindex.php?classid=' . $classid . '&amp;ver=' . $v . '">' . $name . '</a><br/>';
                }
            }
            $html .= '</p>';
            return $html;
        }
        $html = '<div class="versionswitch">';
        foreach ($versions as $v => $name) {
            if ($v == $curVer) {
                $html .= '<span class="curver">' . $name . '</span> ';
            } else {
                $html .= '<a href="' . $http . 'wapindex.php?classid=' . $classid . '&ver=' . $v . '">' . $name . '</a> ';
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
        if ($ver == '1') {
            // WML 1.0: 先转义原文中的裸 & 与 < > (UBB 方括号不受影响；
            // [url=...] 链接里的 & 随本步一并转成 &amp;，满足 WML 对 URL 的转义要求)
            $str = preg_replace('/&(?!(?:#[0-9]+|#[xX][0-9a-fA-F]{1,8}|[a-zA-Z][a-zA-Z0-9]{1,10});)/', '&amp;', $str);
            $str = str_replace(array('<', '>'), array('&lt;', '&gt;'), $str);
        }
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
     * WML 降级输出层 (仅 WAP1.0 输出缓冲回调)
     * 将页面直接 echo 的 HTML 片段统一降级为 WML 1.1 合法子集：
     * div/ul/li/h1-h6/blockquote → <p>，span/strong/em/pre/code/marquee → 降级或去除，
     * form/input/select 等表单控件整块移除，裸 & 转 &amp;，并折叠嵌套 <p>
     */
    public function wmlize($html) {
        // 块级标签降级
        $html = preg_replace('/<div\b[^>]*>/i', '<p>', $html);
        $html = preg_replace('/<\/div\s*>/i', '</p>', $html);
        $html = preg_replace('/<ul\b[^>]*>/i', '', $html);
        $html = preg_replace('/<\/ul\s*>/i', '', $html);
        $html = preg_replace('/<li\b[^>]*>/i', '<p>', $html);
        $html = preg_replace('/<\/li\s*>/i', '</p>', $html);
        $html = preg_replace('/<h[1-6]\b[^>]*>/i', '<b>', $html);
        $html = preg_replace('/<\/h[1-6]\s*>/i', '</b>', $html);
        $html = preg_replace('/<blockquote\b[^>]*>/i', '<p>', $html);
        $html = preg_replace('/<\/blockquote\s*>/i', '</p>', $html);
        $html = preg_replace('/<hr\b[^>]*\/?>/i', '<br/>', $html);
        // 行内标签降级
        $html = preg_replace('/<span\b[^>]*>/i', '', $html);
        $html = preg_replace('/<\/span\s*>/i', '', $html);
        $html = preg_replace('/<strong\b[^>]*>/i', '<b>', $html);
        $html = preg_replace('/<\/strong\s*>/i', '</b>', $html);
        $html = preg_replace('/<em\b[^>]*>/i', '<i>', $html);
        $html = preg_replace('/<\/em\s*>/i', '</i>', $html);
        $html = preg_replace('/<pre\b[^>]*>/i', '', $html);
        $html = preg_replace('/<\/pre\s*>/i', '', $html);
        $html = preg_replace('/<code\b[^>]*>/i', '', $html);
        $html = preg_replace('/<\/code\s*>/i', '', $html);
        $html = preg_replace('/<marquee\b[^>]*>/i', '', $html);
        $html = preg_replace('/<\/marquee\s*>/i', '', $html);
        // WAP1.0 不支持的表单控件整块移除
        $html = preg_replace('/<form\b[^>]*>.*?<\/form\s*>/is', '', $html);
        $html = preg_replace('/<select\b[^>]*>.*?<\/select\s*>/is', '', $html);
        $html = preg_replace('/<textarea\b[^>]*>.*?<\/textarea\s*>/is', '', $html);
        $html = preg_replace('/<input\b[^>]*\/?>/i', '', $html);
        $html = preg_replace('/<button\b[^>]*>.*?<\/button\s*>/is', '', $html);
        // 裸 & 转实体 (合法实体保持不变)
        $html = preg_replace('/&(?!(?:#[0-9]+|#[xX][0-9a-fA-F]{1,8}|[a-zA-Z][a-zA-Z0-9]{1,10});)/', '&amp;', $html);
        // 合并降级产生的嵌套 <p> (如 content div 包裹 ToWML 段落)：
        // 丢弃内层 p 的开/闭标签，保留外层配对，保证标签平衡
        $depth = 0; $skip = 0;
        $html = preg_replace_callback('/<p>|<\/p>/', function ($m) use (&$depth, &$skip) {
            if ($m[0] === '<p>') {
                if ($depth > 0) { $skip++; return ''; }
                $depth++;
                return '<p>';
            }
            if ($skip > 0) { $skip--; return ''; }
            if ($depth > 0) { $depth--; return '</p>'; }
            return '';
        }, $html);
        return $html;
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
    public function callModule($n, $moduleId, $siteParam = 0, $classid = 0, $wmlVo = null) {
        $wmlVo = $wmlVo ?: $this->wmlVo;
        $module = self::$MODULE_MAP[$moduleId] ?? '';
        $table = self::$MODULE_TABLE[$module] ?? '';
        if ($module === '' || $n < 1) return '';
        $n = min(50, $n);
        try {
            $db = Database::getInstance();
            $where = 'isshow=1';
            $params = [];
            if ($classid > 0) { $where .= ' AND classid=?'; $params[] = $classid; }
            $order = 'id DESC';
            $result = $db->getPage($table, 'id,title', $where, $order, 1, $n, $params);
        } catch (Exception $e) {
            return '';
        }
        if (empty($result['list'])) return '';
        $http = $wmlVo->http_start ?? '/';
        if (($wmlVo->ver ?? '2') == '1') {
            // WML 1.0: 调用结果输出为 <p> 内的 <a> 链接序列，URL 中 & 转 &amp;
            $html = '<p>';
            foreach ($result['list'] as $row) {
                $url = str_replace('&', '&amp;', $http . $module . '/view.php?classid=' . $classid . '&id=' . $row['id']);
                $html .= '<a href="' . $url . '">' . $this->ToHtm($row['title']) . '</a><br/>';
            }
            $html .= '</p>';
            return $html;
        }
        $html = '<div class="callmodule">';
        foreach ($result['list'] as $row) {
            $url = $http . $module . '/view.php?classid=' . $classid . '&id=' . $row['id'];
            $html .= '<a href="' . $url . '">' . $this->ToHtm($row['title']) . '</a><br/>';
        }
        $html .= '</div>';
        return $html;
    }

    /** 当前在线人数 */
    public function onlineCount() {
        try {
            $db = Database::getInstance();
            $limit = date('Y-m-d H:i:s', time() - KL_DelOnlineTime * 60);
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
        $str = preg_replace('/\[i\](.*?)\[\/i\]/is', $isWML ? '<i>$1</i>' : '<em>$1</em>', $str);
        // [u]下划线[/u]
        $str = preg_replace('/\[u\](.*?)\[\/u\]/is', '<u>$1</u>', $str);
        // [url=链接]文字[/url] 或 [url]链接[/url]
        $str = preg_replace('/\[url=([^\]]+)\](.*?)\[\/url\]/is', '<a href="$1">$2</a>', $str);
        $str = preg_replace('/\[url\](.*?)\[\/url\]/is', '<a href="$1">$1</a>', $str);
        // [img=地址] 或 [img]地址[/img]
        $str = preg_replace('/\[img=([^\]]+)\]/is', '<img src="$1" alt="" />', $str);
        $str = preg_replace('/\[img\](.*?)\[\/img\]/is', '<img src="$1" alt="" />', $str);
        // [color=颜色]文字[/color]
        $str = preg_replace('/\[color=([^\]]+)\](.*?)\[\/color\]/is', $isWML ? '$2' : '<span style="color:$1">$2</span>', $str);
        // [size=大小]文字[/size]
        $str = preg_replace('/\[size=([^\]]+)\](.*?)\[\/size\]/is', $isWML ? '$2' : '<span style="font-size:$1px">$2</span>', $str);
        // [font=字体]文字[/font]
        $str = preg_replace('/\[font=([^\]]+)\](.*?)\[\/font\]/is', $isWML ? '$2' : '<span style="font-family:$1">$2</span>', $str);
        // [align=center]...[/align]
        $str = preg_replace('/\[align=([^\]]+)\](.*?)\[\/align\]/is', $isWML ? '<p>$2</p>' : '<div style="text-align:$1">$2</div>', $str);
        // [quote]引用[/quote]
        $str = preg_replace('/\[quote\](.*?)\[\/quote\]/is', $isWML ? '<p>　　$1</p>' : '<blockquote>$1</blockquote>', $str);
        // [code]代码[/code]
        $str = preg_replace('/\[code\](.*?)\[\/code\]/is', $isWML ? '<p>$1</p>' : '<pre><code>$1</code></pre>', $str);
        // [hr]
        $str = preg_replace('/\[hr\]/i', $isWML ? '<p>----------------</p>' : '<hr/>', $str);
        // [br]
        $str = preg_replace('/\[br\]/i', '<br/>', $str);
        // [email]邮箱[/email]
        $str = preg_replace('/\[email\](.*?)\[\/email\]/is', '<a href="mailto:$1">$1</a>', $str);
        // [fly]文字[/fly] 跑马灯
        $str = preg_replace('/\[fly\](.*?)\[\/fly\]/is', $isWML ? '$1' : '<marquee>$1</marquee>', $str);
        // [move]...[/move]
        $str = preg_replace('/\[move\](.*?)\[\/move\]/is', $isWML ? '$1' : '<marquee>$1</marquee>', $str);
        // [list][*]项[/list]
        $str = preg_replace_callback('/\[list\](.*?)\[\/list\]/is', function($m) use ($isWML) {
            return $this->parseList($m[1], $isWML);
        }, $str);

        return $str;
    }

    private function parseList($content, $isWML = false) {
        $items = preg_split('/\[\*\]/', $content);
        if ($isWML) {
            // WML 不支持 ul/li，降级为 <p> 段落列表
            $html = '';
            foreach ($items as $item) {
                $item = trim($item);
                if ($item !== '') $html .= '<p>· ' . $item . '</p>';
            }
            return $html;
        }
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
