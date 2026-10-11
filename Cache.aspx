<%@ Page Language="C#" AutoEventWireup="true" CodeBehind="Cache.aspx.cs" Inherits="KeLin.WebSite.Cache" %><%@ Import namespace="KeLin.ClassManager.Tool" %><%
Response.Write(WapTool.showTop(this.GetLang("更新缓存|更新缓存|Clear Log"), wmlVo));//显示头     

        
        strhtml.Append("<div class=\"subtitle\">更新缓存</div>");
        if (this.INFO == "REMOVEOK")
        {
            strhtml.Append("<div class=\"tip\"><b>");
            strhtml.Append(this.GetLang("操作成功！|操作成功！|Successfully Update"));
            strhtml.Append("</b></div>");

        }
        strhtml.Append("<div class=\"content\"><b>");
        strhtml.Append("网站ID:" + this.siteid +" 缓存状态:" + cacheinfo);       
        strhtml.Append("</b> ");
        strhtml.Append("<form id=\"form1\" action=\"" + http_start + "cache.aspx\" method=\"get\">");
        strhtml.Append("<input name=\"action\"  type=\"hidden\" value=\"remove\" />");
        strhtml.Append("<input type=\"hidden\" name=\"siteid\" value=\"" + siteid + "\" />");      
        strhtml.Append("<input type=\"submit\" class=\"btn\" name=\"\" value=\"更新\" />");
        strhtml.Append("</form>");
        strhtml.Append("</div>");
      

        strhtml.Append("<div class=\"tip\">");
        strhtml.Append("说明：缓存功能，在web.config文件中配置KL_OpenCache参数。");
        strhtml.Append("</div>");

        //导航按钮
        strhtml.Append("<div class=\"btBox\"><div class=\"bt1\">");
        strhtml.Append("<a href=\"" + this.http_start + "/wapindex.aspx?siteid=" + this.siteid + "\">返回首页</a> ");
        strhtml.Append("</div></div>");
        Response.Write(strhtml);
  
Response.Write(WapTool.showDown(wmlVo));   //显示底部
%>