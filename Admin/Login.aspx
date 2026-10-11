<%@ Page Language="C#" AutoEventWireup="true" CodeBehind="Login.aspx.cs" Inherits="KeLin.WebSite.Login" %><%
Response.Buffer = true; 
Response.Expires = -1;
Response.ExpiresAbsolute = DateTime.Now.AddDays(-1); 
Response.Expires = 0 ;
Response.CacheControl = "no-cache";  
%><html xmlns="http://www.w3.org/1999/xhtml">
<head><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<HTML xmlns="http://www.w3.org/1999/xhtml">
<HEAD><META content="IE=11.0000" http-equiv="X-UA-Compatible">
<META http-equiv="Content-Type" content="text/html; charset=GB2312"> 
<TITLE>站长登录 -后台管理</TITLE> 
<SCRIPT src="../netcss/admin/js/jquery-1.9.1.min.js" type="text/javascript"></SCRIPT>
<link rel="stylesheet" href="../netcss/admin/css/starry.css" type="text/css">

     
<SCRIPT type="text/javascript">
$(function(){
	//得到焦点
	$("#password").focus(function(){
		$("#left_hand").animate({
			left: "150",
			top: " -38"
		},{step: function(){
			if(parseInt($("#left_hand").css("left"))>140){
				$("#left_hand").attr("class","left_hand");
			}
		}}, 2000);
		$("#right_hand").animate({
			right: "-64",
			top: "-38px"
		},{step: function(){
			if(parseInt($("#right_hand").css("right"))> -70){
				$("#right_hand").attr("class","right_hand");
			}
		}}, 2000);
	});
	//失去焦点
	$("#password").blur(function(){
		$("#left_hand").attr("class","initial_left_hand");
		$("#left_hand").attr("style","left:100px;top:-12px;");
		$("#right_hand").attr("class","initial_right_hand");
		$("#right_hand").attr("style","right:-112px;top:-12px");
	});
});
</SCRIPT>
 
<META name="GENERATOR" content="MSHTML 11.00.9600.17496"></HEAD> 
<BODY scroll="no" style="overflow-x:hidden;overflow-y:hidden">
<DIV class="top_div"></DIV>
<DIV class="login">
<DIV style="width: 165px; height: 96px; position: absolute;z-index: 5;">
<DIV class="tou"></DIV>
<DIV class="initial_left_hand" id="left_hand"></DIV>
<DIV class="initial_right_hand" id="right_hand"></DIV></DIV>
<form name="Login" action="Login.aspx" method="post" >  
<P style="padding: 30px 0px 10px; position: relative;z-index:9"><SPAN class="u_logo"></SPAN>
                <input type="hidden"name="ActionStatus" value="login" />
                <input onFocus="this.select()" name="UserName" class="ipt" type="text" placeholder="请输入管理员用户名" value=""/>
    </P>
<P style="position: relative;z-index:10"><SPAN class="p_logo"></SPAN>
<input onFocus="this.select()" class="ipt" id="password" type="password" name="Password" placeholder="请输入管理员密码" value="" />
  </P>
  <P style="position: relative; margin-top:10px;"><SPAN class="y_logo"></SPAN>
  <input onFocus="this.select()" size="4" name="CheckCode" class="ipt" type="text" placeholder="请输入验证码" value=""/>
<SPAN class="yzm"><img src="CreatImage.aspx" alt="验证码" /> </SPAN> 
  </P>
<DIV style="height: 50px; line-height: 50px; margin-top: 20px; ">
<P><input type="submit" name="bt" value="立即登录" class="btn btn-dl"></P>
<%if (base.pd == false){%>
<div class="morebg">
<ul class="more">
<li style="top:0; left:0"><a href="#"><input class="btn btn-zc" type="button" onclick="window.location='/webreg.aspx';" value="注册站长"></a></li>
<li style="top:0; right:0"><a href="#"><input class="btn btn-mm" type="button" onclick="window.open('sendMail.aspx', 'getmail', 'height=200, width=430, toolbar= no, menubar=no, scrollbars=no, resizable=no, location=no, status=no');" value="忘记密码"></a></li>
</ul>
</div>
<%}%>
</form>
</DIV></DIV>
           
</BODY></HTML>
