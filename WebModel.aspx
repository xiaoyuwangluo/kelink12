<%@ Page Language="C#" AutoEventWireup="true" Codebehind="WebModel.aspx.cs" Inherits="KeLin.WebSite.models.WebModel" %>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=GB2312" />
<title>企业网站模板</title>
<meta name="author" content="kelink,kelink.com,chinastrong" />
<meta name="keywords" content="微网站,APP开发,wap建站,手机上网,手机建站,手机看电影,手机游戏,手机看小说,手机流媒体,手机门户,3G门户" />
<meta name="description" content="体验神奇的WAP建站经历，走进一个全新的wap网站时代！无需专业知识，无需特殊软件，只要您会打字，十分钟的简单制作，您就可以拥有自己的精彩wap站点，让亿万手机用户分享您的设计成果与乐趣；同时本站有成熟完善的商业合作模式，致力于与各wap站长一起创造互惠互利的双赢局面,给大家提供一个3G时代创造财富的机会。还犹豫什么呢？赶快申请吧。" />
<link rel="stylesheet" type="text/css" href="/netcss/index/css/start.css"/>
<link rel="stylesheet" type="text/css" href="/netcss/index/css/index.css"/>
<script type="text/javascript" src="/netcss/index/js/jquery-1.8.3.min.js"></script>
<script src="/netcss/index/js/jquery.SuperSlide.2.1.1.js"></script>
<style type="text/css">

.style7 { width: 422px; }
* { padding: 0; margin: 0; list-style: none; text-decoration: none; }
*:focus { outline: none; }
.main { font-family: "Microsoft Yahei", "微软雅黑", "宋体", sans-serif !important; font-size: 14px; }
.left{ float:left;}
/*搜索*/
.search { position: relative; padding: 20px 10px; border-bottom: 1px solid #cac8c8; }
.search h2 { position: absolute; top: 20px; left: 10px; z-index: 10; line-height: 24px; color: #000; font-size: 14px; }
.search .find { padding-left: 120px; }
.search .find input[type=text] { border: 1px solid #CCC; height: 24px; line-height: 24px; padding: 0 5px; width: 135px; }
.search .find input[type=submit] { margin-left: 5px; width: 60px; height: 26px; border: 1px solid #CCCCCC; background: #FFF; cursor: pointer; }
/*导航菜单*/
.nav-type { position: relative; padding: 20px 10px; background: #fff; }
.nav-type h2 { position: absolute; top: 20px; left: 10px; z-index: 10; line-height: 24px; color: #000; font-size: 14px; }
.nav-type ul { overflow: hidden; max-width: 960px; padding-left: 120px; }
.nav-type ul li { overflow: hidden; float: left; width: 140px; }
.nav-type ul li a { padding: 3px 5px; line-height: 28px; color: #000; font-size: 14px; }
.nav-type ul li.cur a, .nav-type ul li:hover a { background: #e1472f;}
.nav-type ul li.cur a, .nav-type ul li:hover a { color: #FDFDFD; }
/*列表*/
.test-list { overflow:hidden; border: 1px solid #cac8c8; }
.test-list ul { overflow: hidden; background: #fff; }
.test-list ul li { overflow:hidden; float: left; width:379px; padding: 0 10px 0 10px; }
.test-list ul li iframe { width: 320px; padding: 105px 30px 96px 29px; background: #fff; background: url("/NetCSS/admin/images/iPhone.png") no-repeat center center; border: none; }
.test-list ul li .box { position: relative; padding: 0 0 0 90px; margin:0 55px; }
.test-list ul li .box .coderq { position: absolute; top: 0; left: 0; z-index:10; width: 80px; height: 80px; border: 1px solid #fff; }
.test-list ul li .box h3 { margin-bottom: 5px; font-size: 14px; color: #000; line-height: 28px; }
.test-list ul li .box .find-list{width:170px; height:28px;}
.test-list ul li .box .find-list a{ display:block; width:55px; padding: 2px 5px; margin:0 10px 1px 0; text-align:center; background-color: #ebeef1; border: 1px solid #dedfdf; color: #e1472f;  line-height:24px; font-size:13px;}
.test-list ul li .box .find-list a:hover { background-color: #e1472f; color: #FFFFFF; border: 1px solid #e1472f;}
.test-list ul li .box .find-list span{ overflow:hidden;}
.test-list ul li .box .find-list span a.hide{ visibility:hidden;}
.test-list ul li .box .find-list span:hover a.hide{ visibility:visible;}

</style>
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
              <li><a href="index.aspx">首 页</a></li>
              <li><a href="WebAd.aspx">程序介绍</a></li>
              <li><a href="WebReg.aspx">注册站长</a></li>
              <li><a href="admin/login.aspx">登录体验</a></li>
              <li><a href="WebHelp.aspx">成功案例</a></li>
              <li><a href="WebModel.aspx" class="a1">企业模版</a></li>
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
  <div class="banner-moban"></div>
  <div class="moban-bg">
    <div class="moban">
      <div class="moban-so">
            
            
            <div class="search">
            <h2> 名称或编号： </h2>
            <div class="find">
            <form name="f" action ="webmodel.aspx" method="post">
              <input name="typename" type="text" id="typename" value="<%= typename%>" style="width:200px;">
              
              <input type="submit" name="sb" value="查询" id="bt_find" class="bt" style="width:60px;"> </form>
            </div>
          </div>
           
            
          <div class="nav-type">
            <h2>所属的行业：</h2>
            <ul>
              <%=strJavascript%>
            </ul>
          </div> 
            
            
            
            
            
            
            
          </div>
          <!--list-->
          <div class="test-list">
            <ul id="test-list-ul">
            </ul>
          </div>
        <div class="pgs cl"><div class="pg"><%=linkURL %></div></div>
      </div>
    </div>
  </div>
</div>
        
  <script type="text/javascript" language="javascript">

(function (){
	
	var aList = <%=json %>;
	var iConut = 0;
	 
	$(function (){
		
		try{
		append();
		codeRQ();
		}catch(e){
			
		}
	});
	
	function append(){
		var oUl = $('#test-list-ul');
		var url = '/wapindex.aspx?siteid=' + aList[iConut].siteid + '&sid=-' + aList[iConut].ver;
		
		for(var i=0;i<aList.length;i++){

			var oLi = $('<li>\
			  <iframe id="templae-' + aList[i].siteid + '" width="320" height="480"></iframe>\
			  <div class="box">\
			  <h3>编号:' + aList[i].siteid + ' ' + aList[i].title +'</h3>\
			  <img class="coderq" src="' + aList[i].codeRQ + '" width="80" height="80" />\
			  <div class="find-list" id="find-list-' + aList[i].siteid + '">\
			    <a class="left" href="/wapindex.aspx?siteid=' + aList[i].siteid + '&sid=-2" target="_blank">手机版</a> \
				<span class="left">\
			    <a href="/wapindex.aspx?siteid=' + aList[i].siteid + '&sid=-3" target="_blank">电脑版 </a>\
				</span>\
			  </div>\
			  </div>\
			  </li>');
			oUl.append(oLi);
			var subdoc = $(document.getElementById('templae-' + aList[i].siteid).contentWindow.document);

			subdoc.find('body').eq(0).html('<style type="text/css">\
			*{ padding:0; margin:0; font-style:normal;}\
			body{ width:100%; height:100%; line-height:180px; color:#555; text-align:center;}\
			.loadingTip{ width:100%; height:100%; background:#fff; text-align:center; font-size:20px; font-weight:bold; color:#ccc;}\
			.loadingTip i:nth-child(1){ animation:loading .6s infinite;-webkit-animation:loading .8s infinite;}\
			.loadingTip i:nth-child(2){ animation:loading .6s .15s infinite;-webkit-animation:loading .6s .15s infinite}\
			.loadingTip i:nth-child(3){ animation:loading .6s .3s infinite;-webkit-animation:loading .6s .3s infinite}\
			.loadingTip i:nth-child(4){ animation:loading .6s .45s infinite;-webkit-animation:loading .6s .45s infinite}\
			.loadingTip i:nth-child(5){ animation:loading .6s .6s infinite;-webkit-animation:loading .6s .6s infinite}\
			@keyframes loading{\
				0%{color:#21D0FD}  \
				100%{ color:#ccc;}\
			}\
			@-webkit-keyframes loading{\
				0%{color:#21D0FD}  \
				100%{ color:#ccc;}\
			}\
			</style><div class="loadingTip"><i>页</i><i>面</i><i>加</i><i>载</i><i>中</i></div>');
		}
		
	}
	
	function codeRQ(){
		var url = '/wapindex.aspx?siteid=' + aList[iConut].siteid + '&sid=-2';

		var oIframe = $('#templae-' + aList[iConut].siteid);
		oIframe.attr({
			src : url
		});
		oIframe.load(function (){
			iConut++;
			if(iConut >= aList.length){
				return;
			}
			codeRQ();
		});
		
	}
	
})();	

 
</script>
 

<footer>
  <div class="bottom">
    <div class="bottom_con">CopyRight ? Kelink Soft.All Rights Reserved. 粤ICP-13087322</div>
  </div>
</footer>
</body>
</html>