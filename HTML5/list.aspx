<%@ Page Language="C#" ContentType="text/html" ResponseEncoding="utf-8" %><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="keywords" content="html5建站_html5自助建站_wap手机自助建站_微网站_3G手机_kelink.com_wap开发_手机wap网站_手机自助建站">
<title>HTML5源码</title>
<link rel="SHORTCUT ICON" href="favicon.ico" />
<link href="../css/index.css" type="text/css" rel="stylesheet">
</head>

<body>
<div id="header">
<div class="top" style="height: 70px;"><div id="rq" style="position:absolute;top:0;right:40px;text-align:center;"> </div>
    <div class="topCon clearfix">
        <h1 style="margin-top: 0;"><font color="white">源码示例</font></h1>
        <ul class="nav">
            <li><a href="#dh">常规导航</a></li>
            <li><a href="#zk">右边展开导航</a></li>
            <li ><a href="#tb">Tab切换</a></li>
            <li><a href="#lb">轮播幻灯片</a></li>
            <li><a href="#sc">搜索等</a></li>          
          
            <li class="hover"><a href="#">所有</a></li>
        </ul>
        <!--<div class="nom">官方手机站：<a href="http://kelink.com">Kelink.Com</a><p>137-9898-1178</p></div>-->
    </div>
</div>
</div>
<div class="zhuti">
<div class="main">
<div class="box p3">
	
	
<a name="dh"></a>
<p>排版方式一：【电脑/HTML5页面模板管理】+ 直接写HTML5代码 + 万能UBB，适合前端设计师开发。</p>
<p>
排版方式二：【手机/HTML5页面综合排版】+ HTML5自写代码插件 + 万能UBB。请确保页面中有：&lt;link rel="stylesheet" href="<a href="/Template/default/default.css" target="_blank">/Template/default/default.css</a>" type="text/css" /&gt;
(系统默认自带，可自行更改或编辑default.css ，页面强制自带js) 下面代码复制到“HTML5自写代码”模块中，然后用UBB:[getwml=ID]插入到【手机/HTML5页面综合排版】的【全顶】【顶】【插】【低】【全低】中显示。
</p>
<b>一、首页菜单导航</b>
<br/>
<textarea name="content"  rows="10" class="KL_textarea" style="width:99%; color:#2B2B2B;">
<div class="xk_nav">
	<a href="/shoplist-6517-1.html">产品</a> 
	<a href="/wapindex-2108-99169.html">联系</a> 
	<a href="/wapindex-2108-6516.html">关于</a> 
	<a href="/wapindex-2108-99173.html">案例</a> 
	<a href="/wapindex-2108-6518.html">支持</a> 
	<a href="/wapindex-2108-204570.html">论坛</a> 
	<a href="http://sms.kelink.com">授权</a> 
	<a href="/wapindex.aspx?siteid=2108&amp;classid=204663">功能</a> 
	<a href="/wapindex.aspx?siteid=2108&amp;classid=204664">帮助</a> 
	<a href="http://kelink.com/shop-1522.html">APP</a>
	
</div>
	
</textarea>	
<br/><a name="tb"></a>
<b>二、首页tab切换</b>
<br/>
<textarea name="content"  rows="10" class="KL_textarea" style="width:99%; color:#2B2B2B;">
	<div id="tabBox1" class="tabBox">
		<div class="hd">
			<h3>公告新闻<span>News</span></h3>
			<ul>
				<li><a href="javascript:void(0)">官方</a></li>
				<li><a href="javascript:void(0)">方案</a></li>
				<li><a href="javascript:void(0)">科技</a></li>
			</ul>
		</div>
		<div class="bd" id="tabBox1-bd">
			<div class="con"><!-- 高度自适应需添加外层 -->
    <div class="mxbg">
    <div class="mx">
    [bbs=204571_3_1_20_0]<br>  
    <a href="http://kelink.com/bbs-182158.html">微信插件已发布，免费使用</a><br>
    <a href="http://app.kelink.com">手机网站免费生成APP(请电脑访问)</a>
    </div>
    <div class="zix_more">
    <a href="/bbslist-204571-1.html">进入频道</a>
    </div></div>
			</div>
			<div class="con"><!-- 高度自适应需添加外层 -->
				<div class="mxbg">
				<div class="mx">[article=204593_6_5_15_0]</div>
    <div class="zix_more">
    <a href="/articlelist-204593-1.html">进入频道</a>
    </div>
			</div>
			</div>
			<div class="con"><!-- 高度自适应需添加外层 -->
				<div class="mxbg">
				[article=204665_1_0_0_12*100*100*1]
				<div class="mx">[article=204665_4_5_15_0]</div>
    <div class="zix_more">
    <a href="/articlelist-204665-1.html">进入频道</a>
    </div>
			</div>
		</div>
		</div>
	</div>
	<script type="text/javascript">
	TouchSlide( { slideCell:"#tabBox1",
		endFun:function(i){ //高度自适应
			var bd = document.getElementById("tabBox1-bd");
			bd.parentNode.style.height = bd.children[i].children[0].offsetHeight+"px";
			if(i>0)bd.parentNode.style.transition="200ms";//添加动画效果
		}
	} );</script>
	
</textarea>	
<br/>
<b>三、论坛猜你喜欢</b>
<br/>
<textarea name="content"  rows="10" class="KL_textarea" style="width:99%; color:#2B2B2B;">
<div class="dh_bg">
<div class="dh_h4"><p>猜你喜欢</p><span class="new"><div id="click-2"><img src="/Template/default/img/xk_new.png" alt="刷新"> 换一批</div></span></div>
<div class="mx">
<div id="ubbBox-2">
[bbs=0_5_5_16_0]
</div>
</div>

<script>
readUbb({
	mode : "click",//触发ajax方式，click为点击,setInterval为自动请求ajax
	thisEvent : "click-2",//事件触发元素
	ubbBox : "ubbBox-2",//ubb内容放置的元素
	readCount : -1,//加载次数，-1为无限
	ubb : "{bbs=0_5_5_16_0}",//ubb必须使用{},而不是使用[]
	siteid : "[siteid]"
})
</script>
</div>
</textarea>	
<br/>
<b>四、论坛TAB滑动效果</b>
<br/>
<textarea name="content"  rows="10" class="KL_textarea" style="width:99%; color:#2B2B2B;">
<div class="mod_box" id="mod_box">
  <div class="tit_tab">
    <ul class="clearfix">
      <li class="cur"><span>最新</span><em></em></li>
      <li><span>热点</span><em></em></li>
      <li><span>精华</span><em></em></li>
      <li><span>回复</span><em></em></li>
    </ul>
  </div>
    <div class="tb tit_tab_con mid01_box swipe-wrap">
      <div class="touch_roll">
<ul class="mx2">
          [bbs=0_7_1_15_2]
        </ul>
        <div class="zix_more">[new=bbs_0]查看更多最新帖子[/new]</div>
      </div>
      <div class="touch_roll">
        <ul class="mx2">
          [bbs=0_7_2_15_15]
        </ul>
        <div class="zix_more">[hot=bbs_0]查看更多热门帖子[/hot]</div>
      </div>
      <div class="touch_roll">
        <ul class="son xk2">
          [bbs=0_7_3_15_1]
        </ul>
        <div class="zix_more">[good=bbs_0]查看更多精华帖子[/good]</div>
      </div>
      <div class="touch_roll">
        <ul class="mx2"> [bbs=0_7_6_15_14] </ul>
        <div class="zix_more">[re=bbs_0]查看更多最火回帖[/re]</div>
      </div>
  </div>
</div>
<script type="text/javascript">
TouchSlide({ 
    slideCell:"#mod_box",
	titCell : ".clearfix li",
	mainCell : ".swipe-wrap",
	titOnClassName : "cur"
 });
</script>
</textarea>	
<br/>
<b>五、论坛Ajax换一批效果</b>
<br/>
<textarea name="content"  rows="10" class="KL_textarea" style="width:99%; color:#2B2B2B;">
<div class="dh_bg">
<div class="dh_h4"><p>随机推荐</p><span class="new"><div id="click-2"><img src="/Template/default/img/xk_new.png" alt="刷新"> 换一批</div></span></div>
<div class="mx">
<div id="ubbBox-2">
[bbs=0_5_5_16_0]
</div>
</div>

<script>
readUbb({
	mode : "click",//触发ajax方式，click为点击,setInterval为自动请求ajax
	thisEvent : "click-2",//事件触发元素
	ubbBox : "ubbBox-2",//ubb内容放置的元素
	readCount : -1,//加载次数，-1为无限
	ubb : "{bbs=0_5_5_16_0}",//ubb必须使用{},而不是使用[]
})
</script>
</div>
</textarea>	
<br/><a name="lb"></a>
<b>六、论坛会员Ajax动态显示</b>
<br/>
<textarea name="content"  rows="10" class="KL_textarea" style="width:99%; color:#2B2B2B;">
<div class="bbsdt">
<b>当前在线活动人数:[online=0]</b>
<div id="ubbBox-3">
[users=1_6_7_0_Z]
</div>

<script>
readUbb({
	mode : "setInterval",//触发ajax方式，click为点击,setInterval为自动请求ajax
	modeTime :5000,//自动请求ajax时间间隔
	ubbBox : "ubbBox-3",//ubb内容放置的元素
	readCount : -1,//加载次数，-1为无限
	ubb : "{users=1_5_7_0_Z}",//ubb必须使用{},而不是使用[]
})
</script>
</div>
</textarea>	
<br/>
<b>七、首页轮播幻灯片</b>
<br/>
<textarea name="content"  rows="10" class="KL_textarea" style="width:99%; color:#2B2B2B;">
<div id="slideBox" class="slideBox">

				<div class="bd">
					<ul>
							<li>
								<a class="pic" href="#"><img src="/Template/default/img/starry-v1.jpg" /></a>
							</li>
							<li>
								<a class="pic" href="#"><img src="/Template/default/img/starry-v2.jpg"/></a>
							</li>
					</ul>
				</div>

				<div class="hd">
					<ul></ul>
				</div>
			</div>
			<script type="text/javascript">
				TouchSlide({ 
					slideCell:"#slideBox",
					titCell:".hd ul", //开启自动分页 autoPage:true ，此时设置 titCell 为导航元素包裹层
					mainCell:".bd ul", 
					effect:"leftLoop", 
					autoPage:true,//自动分页
					autoPlay:true //自动播放
				});
			</script>
</textarea>	
<br/>
<b>八、论坛Ajax调用</b>
<br/>
<textarea name="content"  rows="10" class="KL_textarea" style="width:99%; color:#2B2B2B;">
<div class="dh_bg">
<div class="dh_h3"><p>最新发表</p></div>
<div class="mx2">
<div id="ubbBox-1">
[bbs=0_5_1_12_2]
</div>
</div>
<div class="p_newsmore">
<div id="click-1">
点击加载更多数据<img src="http://kelink.com/Template/default/img/xk_more.png" alt="加载更多">
</div>
</div>
<script>
readUbb({
	mode : "click",//触发ajax方式，click为点击,setInterval为自动请求ajax
	thisEvent : "click-1",//点击事件触发元素
	ubbBox : "ubbBox-1",//ubb内容放置的元素
	readCount : 3,//加载次数，-1为无限
	ubb : "{bbs=0_3_1_12_2}",//ubb必须使用{},而不是使用[]
	ubbTag : "a", //列表的标签名称，没有可以留空
	loadHref : "/bbs/book_list.aspx?action=new&siteid=1000&classid=0",//加载次数完成后，跳跳转到本地址，留空不跳转
	loadFn : function(){//加载次数完成后会执行此函数
		document.getElementById("click-1").innerHTML = "进入此频道";
	},
	failure : function(){
		alert("网络错误");
	}
})
</script>
</div>
</textarea>	
<br/><a name="sc"></a>
<b>九、论坛分类排版</b>
<br/>
<textarea name="content"  rows="10" class="KL_textarea" style="width:99%; color:#2B2B2B;">
<div class="forum" id="forum">
  <div class="ban-kuai">
    <div class="tit">
      <h2><a href="#">交流论坛</a><i></i></h2>
    </div>
    <div class="bbslist">
      <div class="forum-style"> <a href="/bbslist-204571.html"><span class="img"><img src="/Template/default/img/bbs-gg.jpg"></span><span class="name">官方公告</span><span class="bbsre">主题：<em>[bbs=204571_100]</em><i></i>互动：<em>[bbsre=204571_100]</em></span><span class="today">[bbs=204571_101]</span></a></div>
      <div class="forum-style"> <a href="/bbslist-204576.html"><span class="img"><img src="/Template/default/img/bbs-sp.jpg"></span><span class="name">视频教程</span><span class="bbsre">主题：<em>[bbs=204576_100]</em><i></i>互动：<em>[bbsre=204576_100]</em></span><span class="today">[bbs=204576_101]</span></a></div>
      <div class="forum-style"> <a href="/bbslist-99524.html"><span class="img"><img src="/Template/default/img/bbs-jl.jpg"></span><span class="name">使用交流</span><span class="bbsre">主题：<em>[bbs=99524_100]</em><i></i>互动：<em>[bbsre=99524_100]</em></span><span class="today">[bbs=99524_101]</span></a></div>
      <div class="forum-style"> <a href="/bbslist-204572.html"><span class="img"><img src="/Template/default/img/bbs-az.jpg"></span><span class="name">程序安装</span><span class="bbsre">主题：<em>[bbs=204572_100]</em><i></i>互动：<em>[bbsre=204572_100]</em></span><span class="today">[bbs=204572_101]</span></a></div>
      <div class="forum-style"> <a href="/bbslist-204573.html"><span class="img"><img src="/Template/default/img/bbs-rm.jpg"></span><span class="name">菜鸟入门</span><span class="bbsre">主题：<em>[bbs=204573_100]</em><i></i>互动：<em>[bbsre=204573_100]</em></span><span class="today">[bbs=204573_101]</span></a></div>
      <div class="forum-style"> <a href="/bbslist-204609.html"><span class="img"><img src="/Template/default/img/bbs-pb.jpg"></span><span class="name">排版设计</span><span class="bbsre">主题：<em>[bbs=204609_100]</em><i></i>互动：<em>[bbsre=204609_100]</em></span><span class="today">[bbs=204609_101]</span></a></div>
      <div class="forum-style" style="border-bottom:0px;"> <a href="/bbslist-204607.html"><span class="img"><img src="/Template/default/img/bbs-ad.jpg"></span><span class="name">广告交易</span><span class="bbsre">主题：<em>[bbs=204607_100]</em><i></i>互动：<em>[bbsre=204607_100]</em></span><span class="today">[bbs=204607_101]</span></a></div>
    </div>
  </div>
</div>
<script type="text/javascript">
function forum(jo){
	var oForum = document.getElementById(jo.id);
	var oTit = oForum.getElementsByClassName(jo.tit);
	var oToday = oForum.getElementsByClassName(jo.today);
	
	for(var i=0;i<oTit.length;i++){
		oTit[i].onclick = function(){
			var oList = this.parentNode.getElementsByClassName(jo.list)[0];
			if(this.className == "tit"){
				this.className = "tit on";
				oList.className = "bbslist on";
			}else{
				this.className = "tit";
				oList.className = "bbslist";
			}
		}
	}
	for(var i=0;i<oToday.length;i++){
		if(parseInt(oToday[i].innerHTML) > 0){
			oToday[i].style.display = "block";
		}
	}
}

forum({
	id : "forum",
	tit : "tit",
	list : "bbslist",
	today : "today",
});
</script>	
</textarea>	
<br/>
<b>十、HTML搜索</b>
<br/>
<textarea name="content"  rows="10" class="KL_textarea" style="width:99%; color:#2B2B2B;">
<style type="text/css">
#search-word {font-size: 13px!important;border: none!important;-webkit-appearance: none!important;}
.search {margin:10px 18px 10px 18px;}
.search-form {background:#FFF;width:100%;height:30px;line-height:30px;display:-webkit-box;text-align:center;position:relative;border:solid 1px #cdcdcd;}
.search-opt {padding:0 0 0 5px;margin:0 5px 0 0;border:none;display:block;background:#FFF;}
.search-input {border: 0 none;-webkit-box-flex: 1;display: block;padding: 0 3px;height: 30px;font-size: 15px;color: #333333;}
.search-reset {background:url(/Template/default/img/search.png) no-repeat;background-size:17px 17px;position:absolute;right:50px;height:17px;width:17px;border:0 none;top:7px;}
.search-btn {width:40px;height:20px;margin:6px 2px;border:0 none;border-left:1px solid #dbdbdb;background:url(/Template/default/img/search.png) no-repeat center 1px;background-size:16px 16px;}
</style>
<div class="search">
	<form name="fs" action="/search.aspx" method="get" class="search-form">
		<div id="search-hidden-params">
			<input type="hidden" name="siteid" value="1000">
			<input type="hidden" name="classid" value="0">
		 </div>
       <select name="function" class="search-opt">
			<option value="download_0">软件</option>
			<option value="bbs_0">帖子</option>
			<option value="article_0">文章</option>
		</select>
		<input id="search-word" type="text" name="key" size="15" id="keyword" placeholder="输入搜索关键字" value="" autocomplete="off" autocorrect="off" class="search-input">
		<button type="submit" class="search-btn"></button>
	</form>
</div>
</textarea>	
<br/>
<b>十一、低部广告位</b>
<br/>
<textarea name="content"  rows="10" class="KL_textarea" style="width:99%; color:#2B2B2B;">
<div class="xk_live">
<li style="background: #de6761;">推广</li>
<p><a href="http://www.kelink.com/shoplist-204661-1.html">正品进口葡萄酒威士忌</a></p>
</div>
</textarea>
<br/>
<b>十二、首页功能模块展示</b>
<br/>
<textarea name="content"  rows="10" class="KL_textarea" style="width:99%; color:#2B2B2B;">
<ul class="list-pic" style="height: 180px;">
  <li> <a href="/articlelist-204665-1.html">
    <div class="icon"> <img src="/Template/default/img/ico-wz.gif" alt=""> </div>
    <div class="name">
      <h4>图文文章</h4>
      <p>多功能图文功能，可用于新闻栏目</p>
    </div>
    </a> </li>
    <li> <a href="/wapindex-2108-204570.html">
    <div class="icon"> <img src="/Template/default/img/ico-bbs.gif" alt=""> </div>
    <div class="name">
      <h4>互动论坛</h4>
      <p>经典bbs论坛，会员发帖回帖</p>
    </div>
    </a> </li>
    <li> <a href="/shoplist-6517-1.html">
    <div class="icon"> <img src="/Template/default/img/ico-sc.gif" alt=""> </div>
    <div class="name">
      <h4>购物商城</h4>
      <p>在线购物商城功能</p>
    </div>
    </a> </li>
    <li> <a href="/downloadlist-204622-1.html">
    <div class="icon"> <img src="/Template/default/img/ico-xz.gif" alt=""> </div>
    <div class="name">
      <h4>下载模块</h4>
      <p>文件下载功能，资源站必备</p>
    </div>
    </a> </li>
    <li style="border-bottom:none;"> <a href="/picturelist-204621-1.html">
    <div class="icon"> <img src="/Template/default/img/ico-xc.gif" alt=""> </div>
    <div class="name">
      <h4>图片相册</h4>
      <p>图片栏目，可用于展示美图风景</p>
    </div>
    </a> </li>
    <li style="border-bottom:none;"> <a href="/novellist-204626-1.html">
    <div class="icon"> <img src="/Template/default/img/ico-xs.gif" alt=""> </div>
    <div class="name">
      <h4>小说书城</h4>
      <p>在线小说阅读功能，支持下载和分享</p>
    </div>
    </a> </li>
</ul>
<div class="xk_more"><a href="/wapindex.aspx?siteid=2108&classid=204663">查看全部功能模块</a></div>

</textarea>
<br/>
<b>十三、首页活动位置</b>
<br/>
<textarea name="content"  rows="10" class="KL_textarea" style="width:99%; color:#2B2B2B;">
<div class="xk_live">
<li style="background: #de6761;">活动</li>
<p><a href="#">第四届手机排版比赛</a></p>
</div>
</textarea>
<br/>
<b>十四、首页特色功能展示</b>
<br/>
<textarea name="content"  rows="10" class="KL_textarea" style="width:99%; color:#2B2B2B;">
<div class="xk_h1">
<p>特色功能</p><y><a href="#">More</a></y>
</div>

      <div id="focus" class="focus">
        <div class="focus-o">
          <ul>
          </ul>
        </div>
        <div class="bd">
          <div class="pic-box">
            <ul>
              <li><a href="#"><img src="/Template/default/img/ico_pc.jpg">
                <p>PC同步</p>
                </a></li>
              <li><a href="#"><img src="/Template/default/img/ico_qq.jpg">
                <p>QQ登录</p>
                </a></li>
              <li><a href="#"><img src="/Template/default/img/ico_weix.jpg">
                <p>微信接入</p>
                </a></li>
              <li><a href="#"><img src="/Template/default/img/ico_ubb.jpg">
                <p>UBB调用</p>
                </a></li>
              <li><a href="#"><img src="/Template/default/img/ico_wjt.jpg">
                <p>伪静态技术</p>
                </a></li>
            </ul>
              <ul>
                <li><a href="#"><img src="/Template/default/img/ico-jz.gif">
                <p>多用户建站</p>
                </a></li>
                <li><a href="#"><img src="/Template/default/img/ico-zx.gif">
                <p>自写代码</p>
                </a></li>
                <li><a href="#"><img src="/Template/default/img/ico-ksh.gif">
                <p>可视化排版</p>
                </a></li>
                <li><a href="#"><img src="/Template/default/img/ico-dgn.gif">
                <p>开放拓展</p>
                </a></li>
                <li><a href="#"><img src="/Template/default/img/ico-fw.gif">
                <p>永久更新</p>
                </a></li>
            </ul>
          </div>
        </div>
      </div>
      <script type="text/javascript">
				TouchSlide({ 
					slideCell:"#focus",
					titCell:".focus-o ul", //开启自动分页 autoPage:true ，此时设置 titCell 为导航元素包裹层
					mainCell:".bd .box", 
					effect:"left", 
					autoPage:true, //自动分页c" 
				});
			</script> 
</textarea>
<br/>
<b>十五、首页程序特性滚动</b>
<br/>
<textarea name="content"  rows="10" class="KL_textarea" style="width:99%; color:#2B2B2B;">
<div class="xk_box">
  <p>程序特性</p>
  <div class="t_news">
    <ul class="news_li"id="news_li">
      <li>.net版高效安全，1.0/2.0/电脑三合一！</li>
      <li>打造柯林开放平台，插件轻松开发</li>
      <li>wap支付/支付宝wap支付/手机网银，商店</li>
      <li>开放平台接口，谁都可以开发插件应用</li>
      <li>提供微信接入微信营销!</li>
      <li>提供ubb解析引擎扩展接口及开发插件示例源码</li>
      <li>全国首款WAP+HTML5多用户自助建站系统。</li>
      <li>WAP界最受欢迎，最具人气，最专业平台。</li>
      <li>所见即所得页面综合排版，自定CSS样式。</li>
      <li>模块化设计，提供源码及二次开发</li>
      <li>微信接入，生成苹果APP+安卓APP+二维码</li>
      <li>提供微信接入微信营销！</li>
      <li>运行环境：NET2.5/3.0 + Mssql数据库</li>
      <li>机型适配/皮肤/多语言选择，双后台管理</li>
      <li>所见即所得页面综合排版，想哪显就往哪里插</li>
      <li>自定CSS样式，无所不能UBB调用功能</li>
      <li>在线生成jar、umd、chm、pdf、epub等格式</li>
      <li>傻瓜式操作，会打字就会建站</li>
      <li>模板各性定义，插件模块人性化配置</li>
      <li>Web.Config中几十项超强功能配置，高手最爱</li>
      <li>用户身份级别可在线购买</li>
      <li>提供UBB解析引擎扩展接口及开发插件示例源码</li>
      <li>打造柯林开放平台，插件轻松开发</li>
    </ul>
    <ul class="swap">
    </ul>
  </div>
</div>
<script type="text/javascript">
tervalTop();
</script>
</textarea>
<br/>
<b>十六、首页官方公告</b>
<br/>
<textarea name="content"  rows="10" class="KL_textarea" style="width:99%; color:#2B2B2B;">
   <div class="xk_gfnew">
    <li>[month]月[day]日 周[weekday]</li>
    <p>[b][url=http://kelink.com/bbs-184476.html]第四届排版设计大赛结果公布！[/url][/b]</p>
<p>[b][url=http://kelink.com/wapindex.aspx?siteid=2108&classid=204650]第四届(2014)“柯林杯”手机排版设计大赛[/url][/b]</p>

    <p>2015年1月将发布最新触屏版模板！所有正版客户免费升级。</p>
    </div>
    <div class="xk_t1">
    <p><a href="/bbslist-204571-1.html">官方公告动态</a></p>
    <y>
    <iframe allowtransparency="true" frameborder="0" width="105" height="29" scrolling="no" src="http://tianqi.2345.com/plugin/widget/index.htm?s=3&z=3&t=1&v=0&d=3&bd=0&k=&f=&q=1&e=1&a=1&c=59287&w=105&h=29&align=center"></iframe>
    </y>
    </div>
    <div class="mx">
    [bbs=204571_2_1_20_0]<br>
    <a href="http://kelink.com/bbs-182302.html">第三届排版大赛获奖名单公布！</a><br>
    <a href="http://kelink.com/bbs-182158.html">微信插件已发布，免费使用</a><br>
    <a href="http://app.kelink.com">手机网站免费生成APP(请电脑访问)</a>
    </div>
</textarea>
<br/>
<b>十七、全局顶部，右上角展开菜单导航</b>
<br/>
<textarea name="content"  rows="10" class="KL_textarea" style="width:99%; color:#2B2B2B;">
<header class="aside-left header" id="header">
  <div class="logo"><a href="/"><img src="/Template/default/img/logo.gif" alt="logo"></a></div>
  <div class="menu-btn"> <a id="menu-js" href="javascript:;"><img src="/Template/default/img/menu-btn.png" alt="菜单按钮"></a> <a id="search-js" href="javascript:;">
    <div class="search-box" id="search-box">
      <form id="search-form" name="g" method="get" action="/bbs/book_list.aspx">
        <input type="hidden" name="siteid" value="1000">
        <input type="hidden" name="classid" value="0">
        <input type="hidden" name="action" value="search">
        <input type="hidden" name="type" value="title">
        <div class="search-box-text">
          <input type="text" name="key" placeholder="请输入关键字">
        </div>
      </form>
      <img src="/Template/default/img/search-btn.png" alt="菜单按钮"> </div>
    </a> [nologins]<a id="user-js" href="/waplogin.aspx?siteid=1000"><img src="/Template/default/img/user-btn.png" alt="菜单按钮"></a> [/nologins]</div>
</header>
<div class="aside-left main" id="main">
  <div class="mains">
[nv]
</textarea>
<br/><a name="zk"></a>
<b>十八、全局底部（配合全局顶部使用，实现菜单动态导航）</b>
<br/>
<textarea name="content"  rows="10" class="KL_textarea" style="width:99%; color:#2B2B2B;">
  </div>
<footet>
<div class="xk_quandibg">
<div class="xk_quandi">
<h1>
<a href="/wapindex-2108-99169.html">联系</a>|<a href="http://sms.kelink.com">服务</a>|<a href="/guessbooklist-99170-1.html">反馈</a>|<a href="/wapstyle/skin.aspx?siteid=[siteid]&backurl=wapindex.aspx%3fsiteid%3d[siteid]%26classid%3d[classid]">切换</a>
</h1>
<p><a href="/wapindex-2108-6516.html">联速科技</a>  微信:<a href="http://www.kelink.com/help/weixin.html">KelinkSoft</a>[br]
?2015 [url=http://www.kelink.com/images/kelinkwap.jpg]Kelink CMS[/url] 粤ICP-13087322</p>
</div>
</div>
</footet>
  </div>
<!--=========================-->
<aside class="aside" id="aside">
[nologins]
  <div class="xk_svers">
    <div class="xk_sver">
      <li><a href="/waplogin.aspx?siteid=1000&amp;classid=0&amp;backurl=wapindex.aspx%3fsiteid%3d1000%26classid%3d0"><img 

src="/Template/default/img/dl.png" alt="."><br>
        登 录</a></li>
      <y><a href="/wapreg.aspx?siteid=1000&amp;classid=0"><img src="/Template/default/img/zc.png" alt="."><br>
        注 册</a></y>
    </div>
    <div class="xk_sverqq">
      <li> <a href="/oauth/qq/qqlogin.aspx?siteid=1000&amp;classid=0"><img src="/Template/default/img/qq.png" alt="."> 使用QQ号登录</a> 

</li>
      <y> <a href="/oauth/qq/qqlogin.aspx?siteid=1000&amp;classid=0"><img src="/Template/default/img/wx.png" alt="."> 使用微信登录</a> 

</y>
    </div>
  </div>[/nologins]
[logins]
<div class="xk_userbg">
<div class="xk_user">
<li><a href="/myfile.aspx?siteid=[siteid]">
<div><span class="tx">[ui]18[/ui]</span><span class="name"><p1>[nickname]</p1><p2>[ui]0[/ui] [ui]2[/ui]</p2></span></div></a></li>
<y>[url=/waplogout.aspx?siteid=[siteid]] [/url]</y>
</div>
<div class="xk_usercd">
<li>[url=/bbs/userinfo.aspx?siteid=[siteid]&touserid=[userid]&backurl=wapindex.aspx%3fsiteid%3d[siteid]%26classid%3d[classid]]空间[/url]</li>
<li>[url=/bbs/book_list.aspx?action=search&siteid=[siteid]&classid=0&key=[userid]&type=pub]帖子[/url]</li>
<li>[url=/rizhi/myrizhi.aspx?siteid=[siteid]&touserid=[userid]&backurl=wapindex.aspx%3fsiteid%3d[siteid]%26classid%3d[classid]]微博[/url]</li>
<li>[url=/album/myalbum.aspx?siteid=[siteid]&touserid=[userid]&backurl=wapindex.aspx%3fsiteid%3d[siteid]%26classid%3d[classid]]相册[/url]</li>
<li>[url=/bbs/messagelist.aspx?siteid=[siteid]&classid=[classid]&types=0&backurl=wapindex.aspx%3fsiteid%3d[siteid]%26classid%3d[classid]]信箱[/url]</li>
</div>
</div>
[/logins]
  <div class="nv">
    <ul>
      <li><a href="http://kelink.com/wapindex-2108-0.html">网站首页</a></li>
      <li><a href="http://kelink.com/wapindex-2108-6516.html">公司介绍</a></li>
      <li><a href="http://kelink.com/shoplist-6517-1.html">产品展示</a></li>
      <li><a href="http://kelink.com/wapindex-2108-99169.html">联系我们</a></li>
      <li><a href="http://kelink.com/wapindex-2108-99173.html">成功案例</a></li>
      <li><a href="http://kelink.com/wapindex-2108-6518.html">技术支持</a></li>
      <li><a href="http://kelink.com/wapindex-2108-99168.html">购买指南</a></li>
      <li><a href="http://kelink.com/wapindex-2108-204577.html">汇款帐号</a></li>
      <li><a href="http://sms.kelink.com/index.asp?action=go">客服中心</a></li>
      <li><a href="http://kelink.com/wapindex-2108-175544.html">盗版转正</a></li>
      <li><a href="http://sms.kelink.com/wap.asp">正版查询</a></li>
      <li><a href="http://kelink.com/wapindex-2108-204570.html">官方论坛</a></li>
    </ul>
  </div>
</aside>
<div class="quick_nav" id="quick_nav" style="left: 12px; bottom: 50px;">
<div class="quick_btn"></div>
<div class="quick_con hide">
  <ul class="quick_list">
    <li class="qnav_home"><a href="/wapindex.aspx?siteid=[siteid]&classid=0">首页</a></li>
    <li class="qnav_one"><a href="/shoplist-6517-1.html">购买</a></li>
    <li class="qnav_two"><a href="tencent://message/?uin=800007757&Site=http://qbar.qq.com/application/&Menu=yes">客服</a></li>
    <li class="qnav_three"><a href="/wapindex-2108-204570.html">论坛</a></li>
    <li class="qnav_four"><a href="/wapindex.aspx?siteid=2108&classid=204663">功能</a></li>
    <li class="qnav_five"><a href="/wapindex.aspx?siteid=2108&classid=204664">帮助</a></li>
    <li class="qnav_six"><a href="#">客户端</a></li>
  </ul>
</div>
</div>
</textarea>
<br/>
<img src="1.png" alt="效果图"/> <img src="2.png" alt="效果图"/> <img src="3.png" alt="效果图"/>
</div>
</div>
</div>

      
                                
                                
                                <div id="outerfooter">
        	<div id="footercontainer">
            	<footer id="footer">Copyright © 2015  本系统版权所有 </footer>
            </div>
        </div>

</body>
</html>