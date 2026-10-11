<%@ Page Language="C#" AutoEventWireup="true" Codebehind="WebAD.aspx.cs" Inherits="KeLin.WebSite.WebAD" %><%@ Import namespace="KeLin.ClassManager.Tool" %>
<!DOCTYPE HTML>
<html>
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=GB2312" />
	<title>程序框架 -联速科技出品</title>
	<meta name="author" content="kelink,kelink.com,chinastrong" />
	<meta name="keywords" content="3g,3g手机,3g建站,3g门户,wap,wap建站,手机上网,手机建站,手机看电影,手机游戏,手机看小说,手机流媒体,手机门户,3G门户" />
	<meta name="description" content="体验神奇的WAP建站经历，走进一个全新的wap网站时代！无需专业知识，无需特殊软件，只要您会打字，十分钟的简单制作，您就可以拥有自己的精彩wap站点，让亿万手机用户分享您的设计成果与乐趣；同时本站有成熟完善的商业合作模式，致力于与各wap站长一起创造互惠互利的双赢局面,给大家提供一个3G时代创造财富的机会。还犹豫什么呢？赶快申请吧。" />
	<link rel="stylesheet" type="text/css" href="/netcss/index/css/start.css"/>
	<link rel="stylesheet" type="text/css" href="/netcss/index/css/jiagou.css"/>
	<script type="text/javascript" src="/netcss/index/js/jquery-1.8.3.min.js"></script>
	<script type="text/javascript" src="/netcss/index/js/lightcase.js"></script>
	<script type="text/javascript">
		jQuery(document).ready(function($) {
			$('a[data-rel^=lightcase]').lightcase();//简单例子
			
			$('a[data-rel="lightcase:example_group:slideshow"]').lightcase({showSequenceInfo : false, transition : 'scrollHorizontal'});//相册例子
			
			$('a[data-rel="lightcase:example_group"]').lightcase({showSequenceInfo:false});
			
			$('#example1').lightcase({transition : 'none'});
			
			$('#example2').lightcase({transition : 'fade'});
			
			$('#example3').lightcase({transition : 'elastic'});
			
			$('#example4').lightcase({transition : 'scrollTop'});
			
			//video as html5 with fallback
			$('#various5').lightcase({
				showSequenceInfo:false,
				onStart:function() {
					html5media();
				}
			});
			
			// video as iframe
			$('#various6').lightcase({
				iframe : {
					width : 1280,
					height : 720,
					frameborder : 0
				}
			});
			
			// video as flash
			$('#various7').lightcase({
				type : 'flash',
				flash : {
					width : 1280,
					height : 720
				}
			});
		});
	</script>
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
                  <li><a href="index.aspx" >首 页</a></li>
              <li><a href="WebAd.aspx" class="a1">程序介绍</a></li>
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
<div id="main" class="main">
      <div class="kj_01_banner"></div>
      <div class="kj_02_banner"></div>
      <div class="kj_03_banner">
    <div class="kj_03_banner_ubb"><a href="http://kelink.com/admin/ubb2.aspx" target="_black">UBB帮助文档</a></div>
  </div>
      <div class="kj_04_banner">
    <div class="kj_04_banner_shilibg">
          <div class="kj_04_banner_shili">
        <p> <a id="example5" data-rel="lightcase" href="/netcss/index/images/yiji.jpg" title="一级/首页/普通页面示例"><img src="/netcss/index/images/shili-01.png" alt="更多功能请访问http://kelink.com体验"></a> <a id="example6" data-rel="lightcase" href="/netcss/index/images/erji.jpg" title="二级列表页面示例"><img src="/netcss/index/images/shili-02.png" alt="更多功能请访问http://kelink.com体验"></a> <a id="example7" data-rel="lightcase" href="/netcss/index/images/sanji.jpg" title="三级内容页面示例"><img src="/netcss/index/images/shili-03.png" alt="更多功能请访问http://kelink.com体验"></a> <a id="example8" data-rel="lightcase" href="/netcss/index/images/ggsz.jpg" title="二/三级页面广告设置示例" class="last"><img class="last" src="/netcss/index/images/shili-04.png" alt="更多功能请访问http://kelink.com体验"></a> </p>
      </div>
        </div>
  </div>
  <div class="kj_07_banner">
  <div class="kj_04_banner_shilibg" style="height:600px;">
  <div class="kj_04_banner_shili" style="left:550px; bottom:120px;">
        <p> <a id="example5" data-rel="lightcase" href="/netcss/index/images/shili-lm.png" title="电脑后台栏目管理示例"><img src="/netcss/index/images/shili-05.png" alt="更多功能请访问http://kelink.com体验"></a></p>
      </div>
      </div>
      </div>
      <div class="kj_08_banner">
      <div class="kj_04_banner_shilibg" style="height:600px;">
  <div class="kj_04_banner_shili" style="left:55px; bottom:100px;">
        <p> <a id="example5" data-rel="lightcase" href="/netcss/index/images/shili-nr.png" title="电脑后台栏目内容管理示例"><img src="/netcss/index/images/shili-06.png" alt="更多功能请访问http://kelink.com体验"></a> <a id="example5" data-rel="lightcase" href="/netcss/index/images/shili-nr.jpg" title="手机后台栏目内容管理示例"><img src="/netcss/index/images/shili-07.png" alt="更多功能请访问http://kelink.com体验"></a></p>
      </div>
      </div>
      </div>
      <div class="kj_09_banner">
      <div class="kj_10_banner_shilibg" style="height:600px;">
  <div class="kj_10_banner_shili" style="right: 500px;bottom: 150px;">
        <p>
        <a id="example5" data-rel="lightcase" href="/netcss/index/images/houtai_zhpb.png" title="电脑后台页面综合排版展示"><img src="/netcss/index/images/shili-10.png" alt="更多功能请访问http://kelink.com体验"></a></a>
        </p>
      </div>
      </div>
      </div>
      <div class="kj_06_banner"></div>
      <div class="kj_10_banner">
      <div class="kj_10_banner_shilibg">
  <div class="kj_10_banner_shili">
        <p>
        <a id="example5" data-rel="lightcase" href="/netcss/index/images/shili-ym.jpg" title="程序核心框架源码"><img src="/netcss/index/images/shili-08.png" alt="更多功能请访问http://kelink.com体验"></a>
        <a id="example5" data-rel="lightcase" href="/netcss/index/images/shili-ymsl.jpg" title="程序二次开发实战"><img src="/netcss/index/images/shili-09.png" alt="更多功能请访问http://kelink.com体验"></a>
        </p>
      </div>
      </div>
      </div>
      <div class="kj_11_banner"><div class="kj_11_banner_app"><a href="http://app.kelink.com" target="_black">免费生成APP客户端</a></div></div>
    </div>
<footer>
      <div class="bottom">
    <div class="bottom_con">CopyRight (C) Kelink Soft.All Rights Reserved. 粤ICP-13087322</div>
  </div>
    </footer>
</body>
</html>