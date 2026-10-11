<%@ Page Language="C#" AutoEventWireup="true" CodeBehind="LoginPOST.aspx.cs" Inherits="KeLin.WebSite.User.LoginPOST" %><%@ Import namespace="KeLin.ClassManager.Tool" %><%

StringBuilder strhtml = new StringBuilder();
//显示头
wmlVo.KL_PAGE_TOP = "";
wmlVo.KL_PAGE_DOWN = "";
Response.Write(WapTool.showTop(this.GetLang("登录网站|登錄網站|LogIn"), wmlVo));//显示头

    strhtml.Append("<div class=\"subtitle\">" + this.GetLang("登录网站|登录网站|Login") + "</div>");
    strhtml.Append("<div class=\"tip\">" + this.siteVo.sitename + " 授权网站: " + sitename + "(" + backurl.Split('/')[2] + ")进行身份验证登录。</div>");
       
    if (this.INFO == "OK")
    {
         
        strhtml.Append("<form name=\"f2\" action=\"" + backurl + "\" method=\"post\">");

        strhtml.Append("<div class=\"tip\">");

        strhtml.Append("验证成功，正在进入"+sitename+"("+backurl.Split('/')[2]+")。");
        strhtml.Append("<input type=\"hidden\" name=\"checkcode\" value=\"" + checkcode + "\"/>");
        strhtml.Append("<input type=\"hidden\" name=\"userid\" value=\"" + userVo.userid + "\"/>");
        strhtml.Append("<input type=\"hidden\" name=\"username\" value=\"" + userVo.username + "\"/>");
        strhtml.Append("<input type=\"hidden\" name=\"nickname\" value=\"" + userVo.nickname + "\"/>");
        strhtml.Append("<input type=\"hidden\" name=\"sex\" value=\"" + userVo.sex + "\"/>");
        strhtml.Append("<input type=\"hidden\" name=\"age\" value=\"" + userVo.age + "\"/>");
        strhtml.Append("<input type=\"hidden\" name=\"headimg\" value=\"" + userVo.headimg + "\"/>");
        strhtml.Append("<input type=\"hidden\" name=\"remark\" value=\"" + userVo.remark + "\"/>");
        strhtml.Append("<input type=\"hidden\" name=\"mobile\" value=\"" + userVo.mobile + "\"/>");
        strhtml.Append("<input type=\"hidden\" name=\"email\" value=\"" + userVo.email + "\"/>");
        
        
        strhtml.Append("</div>");
        strhtml.Append("</form>");
        
        strhtml.Append("<script type='text/javascript'> var go=1; setInterval(function(){ if(go==1){ f2.submit(); go=0;} },2000); </script>");

    }
    if (this.INFO == "IDNULL")
    {
        strhtml.Append("<div class=\"tip\">");
        strhtml.Append("<b>" + this.GetLang("*用户ID/用户名/手机号码不能为空！|*用户ID/用户名/手机号码不能为空！|username not null!") + "</b></div>");
    }
    else if (this.INFO == "PASSNULL")
    {
        strhtml.Append("<div class=\"tip\">");
        strhtml.Append("<b>" + this.GetLang("*密码不能为空！|*密码不能为空！|password not null!") + "</b></div>");
    }
    else if (this.INFO == "NOTEXIST")
    {
        strhtml.Append("<div class=\"tip\">");
        strhtml.Append("<b>" + this.GetLang("*用户ID/用户名/手机号码不存！|*用户ID/用户名/手机号码不存！|username not exist ") + "</b></div>");
    }
    else if (this.INFO == "PASSERR")
    {
        strhtml.Append("<div class=\"tip\">");
        strhtml.Append("<b>" + this.GetLang("*密码错误！|*密码错误！|Password error!") + "</b></div>");
    }
    else if (this.INFO == "USERLOCK")
    {
        strhtml.Append("<div class=\"tip\">");
        strhtml.Append("<b>" + this.GetLang("*用户被锁定！|*用户被锁定！|user locked!") + "</b></div>");
    }
    else if (this.INFO == "MAXLOGIN")
    {
        strhtml.Append("<div class=\"tip\">");
        strhtml.Append("<b>" + this.GetLang("*登录失败次数超过" + this.KL_LoginTime + "次了，请明天再来!") + "</b></div>");
    }
     
 

    if (this.INFO != "OK")
    {
        strhtml.Append("<form name=\"f\" action=\"" + http_start + "user/LoginPOST.aspx\" method=\"post\">");
        strhtml.Append("<div class=\"content\">");
        strhtml.Append(this.GetLang("用户ID/用户名/手机号码|用户ID/用户名/手机号码|userid/username/mobile") + "*:<br/>");

        strhtml.Append("<input type=\"text\" name=\"logname\" class=\"txt\" value=\"" + this.logname + "\"/><br/>");
       

        
        strhtml.Append(this.GetLang("密码|密码|password") + "*:<br/>");
        strhtml.Append("<input type=\"password\" name=\"logpass\" class=\"txt\" value=\"" + this.logpass + "\"/><br/>");     
     

       
 
        strhtml.Append("<input type=\"hidden\" name=\"action\" value=\"login\"/>");       
        strhtml.Append("<input type=\"hidden\" name=\"siteid\" value=\"" + siteid + "\"/>");      
        strhtml.Append("<input type=\"hidden\" name=\"backurl\" value=\"" + backurl + "\"/>");
        strhtml.Append("<input type=\"hidden\" name=\"sitename\" value=\"" + sitename + "\"/>");
        strhtml.Append("<input type=\"hidden\" name=\"checkcode\" value=\"" + checkcode + "\"/>");
        strhtml.Append("<input type=\"submit\" name=\"g\" class=\"btn\" value=\"" + this.GetLang("登 录|登 录|Login") + "\"/><br/>");

        strhtml.Append("</div>");
        strhtml.Append("</form>");

       
        
       
        
       

       
    }
   

    
 

    Response.Write(strhtml);

//显示底部
Response.Write(WapTool.showDown(wmlVo)); 
%>