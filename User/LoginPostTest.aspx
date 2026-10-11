<%@ Page Language="C#" AutoEventWireup="true" CodeBehind="LoginPostTest.aspx.cs" Inherits="KeLin.WebSite.User.LoginPOST" %><%@ Import namespace="KeLin.ClassManager.Tool" %>
<%
    wmlVo.KL_PAGE_DOWN = "";
    wmlVo.KL_PAGE_TOP = "";
    
    Response.Write(WapTool.showTop("第三方柯林程序登录验证说明", wmlVo));//显示头 %>
<div class="content">
柯林第三方网站登录验证地址：<br />
http://对方域名/user/loginPost.aspx?siteid=对方网站ID&sitename=我的网站名称说明&backurl=我的接收用户信息地址&checkcode=我的校验码<br />
<br />
例：登录A网站：http://xk-portal.kelink.com网站，需验证第三方网站(B网站)柯林官方kelink.com会员。<br /><br />
那么A网站的接收示例链接：<br />
[url=<a href="http://kelink.com/user/loginPost.aspx?siteid=2108&sitename=门户演示站&backurl=http://xk-portal.kelink.com/user/loginPostTest.aspx&checkcode=123456" target="_blank">http://kelink.com/user/loginPost.aspx?siteid=2108&sitename=门户演示站&backurl=http://xk-portal.kelink.com/user/loginPostTest.aspx&checkcode=123456</a>]柯林官方验证登录[/url]<br />

<br />当验证成功后，kelink.com网站会将会员信息通过post跳转到返回地址http://xk-portal.kelink.com/user/loginPostTest.aspx。
<br />
<br />以下参数Post后值：
<br />checkcode:<%=Request.Form.Get("checkcode")%>
<br />userid:<%=Request.Form.Get("userid")%>
<br />username:<%=Request.Form.Get("username")%>
<br />nickname:<%=Request.Form.Get("nickname")%>
<br />sex:<%=Request.Form.Get("sex")%>
<br />age:<%=Request.Form.Get("age")%>
<br />headimg:<%=Request.Form.Get("headimg")%>
<br />remark:<%=Request.Form.Get("remark")%>
<br />mobile:<%=Request.Form.Get("mobile")%>
<br />email:<%=Request.Form.Get("email")%>
</div>
</body>
</html>