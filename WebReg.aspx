<%@ Page Language="C#" AutoEventWireup="true" Codebehind="WebReg.aspx.cs" Inherits="KeLin.WebSite.WebReg" %><%@ Import namespace="KeLin.ClassManager.Tool" %>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=GB2312" />
<title>测试体验 -联速科技出品</title>
<meta name="author" content="kelink,kelink.com,chinastrong" />
<meta name="keywords" content="3g,3g手机,3g建站,3g门户,wap,wap建站,手机上网,手机建站,手机看电影,手机游戏,手机看小说,手机流媒体,手机门户,3G门户" />
<meta name="description" content="体验神奇的WAP建站经历，走进一个全新的wap网站时代！无需专业知识，无需特殊软件，只要您会打字，十分钟的简单制作，您就可以拥有自己的精彩wap站点，让亿万手机用户分享您的设计成果与乐趣；同时本站有成熟完善的商业合作模式，致力于与各wap站长一起创造互惠互利的双赢局面,给大家提供一个3G时代创造财富的机会。还犹豫什么呢？赶快申请吧。" />
<link rel="stylesheet" type="text/css" href="/netcss/index/css/start.css"/>
<script src="/netcss/index/js/jquery.min.js"></script>
<script src="/netcss/index/js/jquery.SuperSlide.2.1.1.js"></script>
</head>
<body>
<header>
  <div class="header">
    <div class="top">
      <div class="top_con"> <span class="Belongs">广州市联速计算机科技有限公司</span> <span class="zzdl"><a href="admin/login.html">站长登录</a></span> <span class="mail"><img alt="企业QQ" src="/netcss/index/images/qq.png" align="Baseline" /> 企业QQ：<span>800007757</span></span> <span class="tel"><img alt="咨询热线" src="/netcss/index/images/tel.png" align="Baseline" /> 咨询热线：<span>4000-588-605</span></span> <span><a href="http://t.qq.com/kelinkCom" rel="noFollow" target="_blank"><img alt="微博" src="/netcss/index/images/xinlang.png" align="Baseline" /></a></span> </div>
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
              <li><a href="WebAd.aspx">程序介绍</a></li>
              <li><a href="WebReg.aspx" class="a1">注册站长</a></li>
              <li><a href="admin/login.aspx">登录体验</a></li>
              <li><a href="WebHelp.aspx" >成功案例</a></li>
              <li><a href="WebModel.aspx">企业模版</a></li>
              <li><a href="http://kelink.com/help/index.html" target="_black">帮助文档</a></li>             
              <li><a href="WebCallMe.aspx" >联系我们</a></li>
            </ul>
            <div class="clear"></div>
          </nav>
        </div>
      </div>
    </div>
  </div>
</header>
<div id="main" class="main">
  <div class="kelink_app">
    <div class="kelink_app_ri"> <span class="kelink_app_ri_nav"> <a href="http://app.kelink.com" target="_black">免费生成APP</a> <a href="http://sms.kelink.com" target="_black" style="margin-left:20px;">服务中心</a> </span> </div>
  </div>
  <div class="mains">
  <h2>程序体验</h2>
  <div class="developer">
      <h3>注册站长</h3>
    </div>
    <div class="tips">本系统只供测试7天之用，购买源码程序请联系企业QQ800007757。</div>
  <IFRAME name=preview2 frameBorder=no  height=800  src="admin/reg.aspx" width="500" scrolling="no">您的浏览器不支持嵌入式框架，请更换HTML5浏览器注册。</IFRAME>
  <!--
  
    
    
    <div class="developer">
      <h3>注册站长</h3>
    </div>
    <div class="tips">本系统只供测试7天之用，购买源码程序请联系企业QQ800007757。</div>
    <div class="fill-form-bg">
      <div class="fill-form">
        <form action="/wuliu/modifyinfoA.aspx" id="tempForm" name="tempForm" method="post">
          <p><b>赠送域名<i>*</i></b>
            <input name="tb_domain" type="text" id="tb_domain" onkeydown="siteurltousername();" onkeyup="siteurltousername();" class="gzh-input" style="width:200px;" placeholder="请输入您想要的域名前缀">
            <strong>.kelink.com</strong>
            <input type="submit" name="bt_check" value=" 域名验证 " id="bt_check" class="bt">
          </p>
          <p><b>网站名称<i>*</i></b>
            <input name="mobile" check-required="value" id="appName" type="text" class="gzh-input" placeholder="请输入您的网站名称">
          </p>
          <p><b>网站类别<i>*</i></b>
            <select name="DropDownList1" id="DropDownList1">
              <option selected="selected" value="19">个人网站</option>
              <option value="1">情感绿洲</option>
              <option value="2">娱乐休闲</option>
              <option value="3">电脑网络</option>
              <option value="4">经济金融</option>
              <option value="6">新闻出版</option>
              <option value="7">科学技术</option>
              <option value="8">法学园地</option>
              <option value="9">文学世界</option>
              <option value="10">旅游自然</option>
              <option value="11">少儿乐园</option>
              <option value="12">艺术之窗</option>
              <option value="13">生活资讯</option>
              <option value="14">公司企业</option>
              <option value="15">社会文化</option>
              <option value="16">教育学习</option>
              <option value="17">体育竞技</option>
              <option value="18">医药健康</option>
            </select>
          </p>
          <p><b>用户名<i>*</i></b>
            <input name="tb_username" type="text" readonly id="tb_username" class="gzh-input" >
          </p>
          <p><b>个性昵称<i>*</i></b>
            <input name="remark" check-required="value" id="appName" value="" type="text" class="gzh-input" placeholder="请输入您的昵称">
          </p>
          <p><b>登录密码<i>*</i></b>
            <input name="remark" check-required="value" id="appName" value="" type="text" class="gzh-input" placeholder="请输入您的登录密码，建议英文+数字">
          </p>
          <p><b>密码验证<i>*</i></b>
            <input name="remark" check-required="value" id="appName" value="" type="text" class="gzh-input" placeholder="再次输入密码以验证">
          </p>
          <p><b>手机号码<i>*</i></b>
            <input name="remark" check-required="value" id="appName" value="" type="text" class="gzh-input" placeholder="请输入您的手机号码">
          </p>
          <p><b>性别<i>*</i></b>
            <select name="DropDownList1" id="DropDownList1">
              <option selected="selected" value="19">男生</option>
              <option value="1">女生</option>
            </select>
          </p>
          <p><b>年龄<i>*</i></b>
            <input name="remark" check-required="value" id="appName" value="" type="text" class="gzh-input" placeholder="请输入您的年龄">
          </p>
          <p><b>电子邮箱<i>*</i></b>
            <input name="remark" check-required="value" id="appName" value="" type="text" class="gzh-input" placeholder="请输入您的电子邮箱，用于找回密码">
          </p>
          <p><b>验证码<i>*</i></b>
            <input name="remark" check-required="value" id="appName" value="" type="text" class="gzh-input" placeholder="请输入验证码">
            <span class="yanzm"><img src="/netcss/index/images/CreatImage.gif" /></span> </p>
          <p><b>使用协议<i>*</i></b>
            <input id="CheckBox1" type="checkbox" name="CheckBox1" checked="checked">
            <a href="http://test.kelink.com/admin/Contract.htm" target="_blank">我已仔细阅读并同意本站免费WAP建站服务使用协议。</a> </p>
          <div class="form">
            <input type="submit" class="btn btn-gzh" name="button" id="button" value="确认注册">
          </div>
        </form>
        
        
        
      </div>-->
      
      
      <div class="ctR">
        <div class="rbox"> <span>
          <h3>最新注册</h3>
          </span>
          <ul class="list">
          <%
     int max=23;
     if (listVo != null && listVo.Count < 23)
     {
         max = listVo.Count;
     }
     for (int i = 0; (listVo != null && i < max); i++)
    {
        Response.Write("<LI><A href=\""+ listVo[i].vurl + "\" target=_blank>" + WapTool.left( listVo[i].vtitle,13) + "</A>" + string.Format("{0:MM-dd HH:mm}", listVo[i].vregtime) + "</LI>");

    }

    
    
 %>
            
          </ul>
        </div>
       
      </div>
      <div class="cxtd"> <span>
        <h3>程序特点</h3>
        </span>
        <ul class="list2">
          <li>支持WAP1/WAP2/HTML5/平板/电脑版排版。</li>
          <li>手机和电脑双后台同步管理！</li>
          <li>三层架构，低层封装，展示层开源。</li>
          <li>功能强大、界面简洁、操作简单。</li>
          <li>模块独立、插件管理、资源共享。</li>
          <li>所见即所得，想哪显就往哪里插。</li>
          <li>无限级栏目，树型管理一目了然。</li>
          <li>强大的UBB调用功能，无所不能。</li>
          <li>自写WAP/HTML5代码功能。在线生成二维码图</li>
          <li>3D图型报表，手机地图定位等，在线播放影音</li>
          <li>强大的域名解析系统，自动解析所有二级域名</li>
          <li>建无数个网站动态分配域名、空间、到期时间等</li>
          <li>多达五十多个业务功能插件，16套高级网站模板</li>
          <li>强大的内容审核功能，先审后显或先显后审！</li>
          <li>具有多角色访问，自定义VIP身份等级会员等。</li>
          <li>双币系统，WAP在线支付，手机网银接口。</li>
          <li>自定CSS样式，所见所得CSS样式编辑。</li>
          <li>机型适配、界面、多语言选择。</li>
          <li>水印，图片DIY，防盗链等。</li>
          <li><font color="red">QQ登录、微信接入，生成苹果APP+安卓APP+二维码</font></li>
          <li>WAP业界最受欢迎的程序！</li>
          <li>老牌子，技术过硬，服务过硬，值得信赖！</li>
          <li>一次购买，永久使用，永久升级！</li>
        </ul>
      </div>
    </div>
  </div>
</div>
<footer>
  <div class="bottom">
    <div class="bottom_con">CopyRight (C) Kelink Soft.All Rights Reserved. 粤ICP-13087322</div>
  </div>
</footer>
</body>
</html>
