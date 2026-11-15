<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * WapTool 工具类 (对应原 KeLin.ClassManager.Tool.WapTool)
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
     * UBB 标签解析
     * 支持 [b][i][u][url][img][color][size][font] 等
     * 以及万能调用 [功能模块标识=N_M_X_Y] (此处仅解析基础UBB，万能调用由业务层处理)
     */
    private function parseUBB($str, $wmlVo) {
        $ver = $wmlVo->ver ?? '2';
        $isWML = ($ver == '1');

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
