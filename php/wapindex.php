<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * WAP站点首页 (对应原 WapIndex.aspx)
 * 显示站点栏目列表
 */
require_once __DIR__ . '/includes/bootstrap.php';

class WapIndexPage extends WapPage {
    public function run() {
        $this->buildIndexHtml();
        echo $this->strShowHtml;
    }

    private function buildIndexHtml() {
        $db = $this->db;

        // 构建页面
        $html = $this->wapTool->showTop($this->strTitle, $this->wmlVo);

        // 站点标题/Logo
        $html .= '<div class="sitetitle">';
        if (!empty($this->siteVo->logourl)) {
            $html .= '<img src="' . $this->ToHtm($this->siteVo->logourl) . '" alt="' . $this->ToHtm($this->strTitle) . '"/>';
        }
        $html .= '<h1>' . $this->ToHtm($this->strTitle) . '</h1>';
        $html .= '</div>';

        // 用户登录状态条
        $html .= '<div class="userbar">';
        if ($this->userSession->isLogin()) {
            $nick = $this->userSession->getUserName();
            $html .= '您好,<a href="' . $this->http_start . 'myfile.php">' . $this->ToHtm($nick) . '</a> ';
            $html .= '<a href="' . $this->http_start . 'waplogout.php">退出</a>';
        } else {
            $html .= '<a href="' . $this->http_start . 'waplogin.php">登录</a> ';
            $html .= '<a href="' . $this->http_start . 'wapreg.php">注册</a>';
        }
        $html .= '</div>';

        // 搜索框
        $html .= '<div class="searchbar">';
        $html .= '<form method="get" action="' . $this->http_start . 'search.php">';
        $html .= '<input type="text" name="keyword" placeholder="搜索..."/>';
        $html .= '<input type="submit" value="搜索"/>';
        $html .= '</form></div>';

        // 栏目列表 (顶级栏目)
        $classes = $db->getAll("SELECT * FROM WapClass WHERE parentid=0 AND isshow=1 ORDER BY orderid ASC, id ASC",[]);
        $html .= '<div class="classlist"><ul>';
        foreach ($classes as $cls) {
            $module = $cls['module'] ?? 'article';
            $url = $this->http_start . $module . '/list.php?classid=' . $cls['id'];
            $html .= '<li><a href="' . $url . '">';
            if (!empty($cls['classpic'])) {
                $html .= '<img src="' . $this->ToHtm($cls['classpic']) . '" alt=""/>';
            }
            $html .= $this->ToHtm($cls['classname']);
            $html .= '</a></li>';
        }
        $html .= '</ul></div>';

        // 底部版权
        $html .= $this->wapTool->showDown($this->wmlVo);
        $this->strShowHtml = $html;
    }
}

$page = new WapIndexPage();
$page->run();
