<%@ Page Language="C#" AutoEventWireup="true" Codebehind="Index.aspx.cs" Inherits="KeLin.WebSite.Index" %><%@ Import namespace="KeLin.ClassManager.Tool" %><!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=GB2312" />
<title><%=base.strTitle%></title>
<meta name="author" content="kelink,kelink.com,chinastrong" />
<meta name="keywords" content="微网站,APP开发,wap建站,手机上网,手机建站,手机看电影,手机游戏,手机看小说,手机流媒体,手机门户,3G门户" />
<meta name="description" content="体验神奇的WAP建站经历，走进一个全新的wap网站时代！无需专业知识，无需特殊软件，只要您会打字，十分钟的简单制作，您就可以拥有自己的精彩wap站点，让亿万手机用户分享您的设计成果与乐趣；同时本站有成熟完善的商业合作模式，致力于与各wap站长一起创造互惠互利的双赢局面,给大家提供一个3G时代创造财富的机会。还犹豫什么呢？赶快申请吧。" />
<link rel="stylesheet" type="text/css" href="/netcss/index/css/start.css"/>
<link rel="stylesheet" type="text/css" href="/netcss/index/css/index.css"/>
<script type="text/javascript" src="/netcss/index/js/jquery-1.8.3.min.js"></script>
<script src="/netcss/index/js/jquery.SuperSlide.2.1.1.js"></script>
</head>
<body>
<header>
  <div class="header">
    <div class="top">
      <div class="top_con"> <span class="Belongs">广州市联速计算机科技有限公司</span> <span class="zzdl"><a href="admin/login.aspx">站长登录</a></span> <span class="mail"><img alt="企业QQ" src="/netcss/index/images/qq.png" align="Baseline" /> 企业QQ：<span>800007757</span></span> <span class="tel"><img alt="咨询热线" src="/netcss/index/images/tel.png" align="Baseline" /> 咨询热线：<span>4000-588-605</span></span> <span><a href="http://t.qq.com/kelinkCom" rel="noFollow" target="_blank"><img alt="微博" src="/netcss/index/images/xinlang.png" align="Baseline" /></a></span> </div>
    </div>
    <div class="head_bg">
      <div class="head">
        <div class="logo fl">
          <h1><a href=""><img src="/netcss/index/images/logo.jpg" width="100%" height="100%" /></a></h1>
        </div>
        <div class="menu fr">
          <nav>
            <ul>
              <li><a href="index.aspx" class="a1">首 页</a></li>
              <li><a href="WebAd.aspx">程序介绍</a></li>
              <li><a href="WebReg.aspx">注册站长</a></li>
              <li><a href="admin/login.aspx">登录体验</a></li>
              <li><a href="WebHelp.aspx">成功案例</a></li>
              <li><a href="WebModel.aspx">企业模版</a></li>
              <li><a href="http://kelink.com/help/index.html" target="_black">帮助文档</a></li>             
              <li><a href="WebCallMe.aspx">联系我们</a></li>
            </ul>
            <div class="clear"></div>
          </nav>
        </div>
      </div>
    </div>
  </div>
</header>
<div class="slider">
  <div class="bd">
    <ul>
      <li style=" background:#e1e1e1"><a href="#"><img src="/netcss/index/images/01.jpg" /></a></li>
      <li style=" background:#1bb2e9"><a href="#"><img src="/netcss/index/images/02.png" /></a></li>
      <li style=" background:#1ebb90"><a href="#"><img src="/netcss/index/images/03.png" /></a></li>
    </ul>
  </div>
  <div class="hd">
    <ul>
      <li></li>
      <li></li>
    </ul>
  </div>
  <div class="timer"></div>
</div>
<script type="text/javascript">
jQuery(".slider").slide({
	titCell: ".hd ul",
	mainCell: ".bd ul",
	effect: "fold",
	autoPlay: true,
	autoPage: true,
	trigger: "click",
	mouseOverStop: false,
	startFun: function() { 
	    var timer = jQuery(".slider .timer");
        timer.stop(true, true).animate({"width": "0%"}, 0).animate({"width": "100%"}, 5000);
    }});
</script>
<div id="main" class="main">
  <div class="advantage-bg">
    <div class="advantage">
      <p class="h-font">功能支持</p>
      <div class="advantage-info">
        <ul class="clearfix advantage-list">
          <li class="item user"><i class="iuser ubb"></i>
            <p class="info">UBB</p>
            <p class="text">简单易懂的一个UBB代码即可调用出多样化的功能展示，轻松实现手机排版。万能调用！</p>
          </li>
          <li class="item html5-l"><i class="iuser HTML5"></i>
            <p class="info">HTML</p>
            <p class="text">支持HTML/UBB混合排版，便于前端设计者更快捷的设计精美页面。支持HTML5！</p>
          </li>
          <li class="item css3-l"><i class="iuser CSS3"></i>
            <p class="info">CSS</p>
            <p class="text">排版设计CSS是必不可少的重要元素，多样化的CSS调用将是一个网站页面的核心。</p>
          </li>
          <li class="item js-l"><i class="iuser js"></i>
            <p class="info">JavaScript</p>
            <p class="text">支持JS代码调用。强大的JS将网页效果做得极致绚丽，来吧让我们开启前端设计之路！</p>
          </li>
        </ul>
      </div>
    </div>
  </div>
  <div class="advantage-bg">
    <div class="advantage">
      <p class="h-font">程序特色</p>
      <div class="services-area">
        <div class="clearfix"></div>
        <div class="one_third">
          <figure> <img src="/netcss/index/images/ico-1.png" alt="Image description" title="Image title"> </figure>
          <div class="services-box">
            <p><strong>双后台管理</strong></p>
            <em>支持手机+电脑双后台同步管理网站，电脑后台更支持可视化排版！同时手机后台也更方便站长随时管理网站。</em> </div>
        </div>
        <div class="one_third">
          <figure> <img src="/netcss/index/images/ico-2.png" alt="Image description" title="Image title"> </figure>
          <div class="services-box">
            <p><strong>微信、QQ接入</strong></p>
            <em>支持第三方QQ号直接登录网站，绑定网站帐号一键登录。更支持微信公众平台对接。实现强大功能！</em> </div>
        </div>
        <div class="one_third last">
          <figure> <img src="/netcss/index/images/ico-3.png" alt="Image description" title="Image title"> </figure>
          <div class="services-box">
            <p><strong>多版本浏览</strong></p>
            <em>支持多版本设计，可以根据用户不用设备设计不同页面(手机版、触屏版、电脑版……)。同步内容访问！</em> </div>
        </div>
        <div class="clearfix"></div>
        <div class="one_third">
          <figure> <img src="/netcss/index/images/ico-4.png" alt="Image description" title="Image title"> </figure>
          <div class="services-box">
            <p><strong>多用户建站</strong></p>
            <em>企业版程序，支持无数子网站建立。可以无限建立旗下网站。总站可以管理旗下任何网站和内容！</em> </div>
        </div>
        <div class="one_third">
          <figure> <img src="/netcss/index/images/ico-5.png" alt="Image description" title="Image title"> </figure>
          <div class="services-box">
            <p><strong>伪静态技术</strong></p>
            <em>支持地址后缀伪静态，优化掉一大堆的地址字符串，伪装成HTML文件。便于各大搜索引擎收录！</em> </div>
        </div>
        <div class="one_third last">
          <figure> <img src="/netcss/index/images/ico-6.png" alt="Image description" title="Image title"> </figure>
          <div class="services-box">
            <p><strong>永久更新</strong></p>
            <em>一次授权程序，永久免费更新升级。我们每个月都将升级上线新功能，也修复和优化上个版本的错误和漏洞。</em> </div>
        </div>
      </div>
    </div>
  </div>
  <div class="advantage-bg" style="border:0">
    <div class="advantage">
      <p class="h-font">成功案例</p>
      <div class="group">
        <ul>
          <li><a target="_blank" href="http://kelink.com"><img class="logo_kelink" src="/netcss/index/images/ffffff-0.gif"></a>
            <div class="boxewm">
              <div class="box"><img src="/netcss/index/images/ewm_kelink.png">
                <p class="box_i">手机扫描二维码访问该网站</p>
                <b class="bubble_trig ui_trigbox ui_trigbox_t" style="left: 75px;"><b class="ui_trig"></b><b class="ui_trig ui_trig_up"></b></b></div>
            </div>
          </li>
          <li><a target="_blank" href="http://gzzxwl.kelink.com"><img class="logo_wl" src="/netcss/index/images/ffffff-0.gif"></a>
            <div class="boxewm">
              <div class="box"><img src="/netcss/index/images/ewm_wl.png">
                <p class="box_i">手机扫描二维码访问该网站</p>
                <b class="bubble_trig ui_trigbox ui_trigbox_t" style="left: 75px;"><b class="ui_trig"></b><b class="ui_trig ui_trig_up"></b></b></div>
            </div>
          </li>
          <li><a target="_blank" href="http://100du.mobi"><img class="logo_100du" src="/netcss/index/images/ffffff-0.gif"></a>
            <div class="boxewm">
              <div class="box"><img src="/netcss/index/images/ewm_100du.png">
                <p class="box_i">手机扫描二维码访问该网站</p>
                <b class="bubble_trig ui_trigbox ui_trigbox_t" style="left: 75px;"><b class="ui_trig"></b><b class="ui_trig ui_trig_up"></b></b></div>
            </div>
          </li>
          <li><a target="_blank" href="http://wapzz.cn"><img class="logo_wapzz" src="/netcss/index/images/ffffff-0.gif"></a>
            <div class="boxewm">
              <div class="box"><img src="/netcss/index/images/ewm_wapzz.png">
                <p class="box_i">手机扫描二维码访问该网站</p>
                <b class="bubble_trig ui_trigbox ui_trigbox_t" style="left: 75px;"><b class="ui_trig"></b><b class="ui_trig ui_trig_up"></b></b></div>
            </div>
          </li>
          <li style="margin-right:0"><a target="_blank" href="http://mrpyx.cn"><img class="logo_mrpyx" src="/netcss/index/images/ffffff-0.gif"></a>
            <div class="boxewm">
              <div class="box"><img src="/netcss/index/images/ewm_mrpyx.png">
                <p class="box_i">手机扫描二维码访问该网站</p>
                <b class="bubble_trig ui_trigbox ui_trigbox_t" style="left: 75px;"><b class="ui_trig"></b><b class="ui_trig ui_trig_up"></b></b></div>
            </div>
          </li>
          <li><a target="_blank" href="http://mpao.cn"><img class="logo_mpao" src="/netcss/index/images/ffffff-0.gif"></a>
            <div class="boxewm">
              <div class="box"><img src="/netcss/index/images/ewm_mpao.png">
                <p class="box_i">手机扫描二维码访问该网站</p>
                <b class="bubble_trig ui_trigbox ui_trigbox_t" style="left: 75px;"><b class="ui_trig"></b><b class="ui_trig ui_trig_up"></b></b></div>
            </div>
          </li>
          <li><a target="_blank" href="http://jlwz.cn"><img class="logo_jlwz" src="/netcss/index/images/ffffff-0.gif"></a>
            <div class="boxewm">
              <div class="box"><img src="/netcss/index/images/ewm_jlwz.png">
                <p class="box_i">手机扫描二维码访问该网站</p>
                <b class="bubble_trig ui_trigbox ui_trigbox_t" style="left: 75px;"><b class="ui_trig"></b><b class="ui_trig ui_trig_up"></b></b></div>
            </div>
          </li>
          <li><a target="_blank" href="http://yaohuo.me"><img class="logo_yaohuo" src="/netcss/index/images/ffffff-0.gif"></a>
            <div class="boxewm">
              <div class="box"><img src="/netcss/index/images/ewm_yaohuo.png">
                <p class="box_i">手机扫描二维码访问该网站</p>
                <b class="bubble_trig ui_trigbox ui_trigbox_t" style="left: 75px;"><b class="ui_trig"></b><b class="ui_trig ui_trig_up"></b></b></div>
            </div>
          </li>
          <li><a target="_blank" href="http://chinalae.cn"><img class="logo_yunyou" src="/netcss/index/images/ffffff-0.gif"></a>
            <div class="boxewm">
              <div class="box"><img src="/netcss/index/images/ewm_yunyou.png">
                <p class="box_i">手机扫描二维码访问该网站</p>
                <b class="bubble_trig ui_trigbox ui_trigbox_t" style="left: 75px;"><b class="ui_trig"></b><b class="ui_trig ui_trig_up"></b></b></div>
            </div>
          </li>
          <li style="margin-right:0"><a target="_blank" href="http://sjdxw.com"><img class="logo_sjdxw" src="/netcss/index/images/ffffff-0.gif"></a>
            <div class="boxewm">
              <div class="box"><img src="/netcss/index/images/ewm_dx.png">
                <p class="box_i">手机扫描二维码访问该网站</p>
                <b class="bubble_trig ui_trigbox ui_trigbox_t" style="left: 75px;"><b class="ui_trig"></b><b class="ui_trig ui_trig_up"></b></b></div>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </div>
  <div class="ExperienceBG">
    <div class="Experience">
      <h3 class="tit-kelink">柯林WAP手机自助建站系统</h3>
      <p class="tit-Content">体验神奇的WAP建站经历，走进一个全新的wap网站时代！无需专业知识，无需特殊软件，只要您会打字，十分钟的简单制作，您就可以拥有自己的精彩wap站点，让亿万手机用户分享您的设计成果与乐趣；同时本站有成熟完善的商业合作模式，致力于与各wap站长一起创造互惠互利的双赢局面,给大家提供一个3G时代创造财富的机会。还犹豫什么呢？赶快申请吧。</p>
      <div class="GO"><a href="webreg.aspx">立即体验</a></div>
    </div>
  </div>
</div>
<footer>
  <div class="bottom">
    <div class="bottom_con"><%=WapTool.ToWML(base.CopyRight, this.wmlVo)%></div>
  </div>
</footer>
</body>
</html>