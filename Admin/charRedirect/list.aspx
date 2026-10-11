<%@ Page Language="C#" AutoEventWireup="true" CodeBehind="list.aspx.cs" Inherits="KeLin.WebSite.admin.charRedirect.list" %><%@ Import namespace="KeLin.ClassManager.Tool" %><%
Response.Write(WapTool.showTop("来源+IP+UA关键字跳转地址", wmlVo));//显示头                                                                                                                                                                       



strhtml.Append("<div class=\"subtitle\">来源+城市+UA关键字跳转地址</div>");
    //显示搜索

    strhtml.Append("<div class=\"content\">");

    strhtml.Append("说明：启用此功能需要在<font color='red'>web.config中的“KL_CharRedirect”设为1。当前状态：" + this.KL_CharRedirect + "</font>，如果IP老是一样，可能主机开启了CDN功能，需要将参数：KL_GetIP_FromCDN 设为1。 当域名访问时，判段　IP+IP对应城市+浏览器UA+地址来源　中存在设置的关键字，进行跳转地址。<br/>");
    strhtml.Append("当前页字符参考：<br/><textarea name=\"newchar\" rows=\"3\" style=\"width:100%\">" + contentkey + "</textarea><br/>");
    strhtml.Append("<a href=\"" + this.http_start + "admin/charRedirect/add.aspx?action=go&amp;siteid=" + this.siteid + "&amp;classid=" + this.classid + "&amp;page=" + this.CurrentPage + "\">批量新增</a><br/>");

    strhtml.Append("<form id=\"form1\" action=\"" + http_start + "admin/charRedirect/list.aspx\" method=\"get\">");
    strhtml.Append("关键字：<input type=\"text\" name=\"key\" value=\"" + key + "\" size=\"5\"/>");
   
    strhtml.Append("<input type=\"submit\" value=\"搜索\" />");
    strhtml.Append("<input type=\"hidden\" name=\"action\" value=\"class\" />");
    strhtml.Append("<input type=\"hidden\" name=\"siteid\" value=\"" + siteid + "\" />");
    strhtml.Append("<input type=\"hidden\" name=\"sid\" value=\"" + sid + "\" />");
    strhtml.Append("</form>");
    
   
    strhtml.Append("</div>");

    //显示列表
    strhtml.Append(linkTOP);
    string YesOrNo = "";
    string CheckStr = "";
    for (int i = 0; (listVo != null && i < listVo.Count); i++)
    {
        index = index + kk;

        if (i % 2 == 0)
        {
            strhtml.Append("<div class=\"line1\">");
        }
        else
        {
            strhtml.Append("<div class=\"line2\">");
        }
        
        
        
      
        strhtml.Append(index + ".[<a href=\"" + this.http_start + "admin/charRedirect/del.aspx?action=go&amp;id=" + listVo[i].id + "&amp;siteid=" + this.siteid + "&amp;classid=" + this.classid + "&amp;page=" + this.CurrentPage + "\">删</a>.<a href=\"" + this.http_start + "admin/charRedirect/mod.aspx?action=go&amp;id=" + listVo[i].id + "&amp;siteid=" + this.siteid + "&amp;classid=0&amp;page=" + this.CurrentPage + "\">修</a>]" + HttpUtility.HtmlEncode( listVo[i].secondShowTop) +"→"+ HttpUtility.HtmlEncode( listVo[i].secondShowDown)+ "<br/>");
       
        strhtml.Append("</div>");
    }

    if (listVo==null)
    {
        strhtml.Append("<div class=\"tip\">暂无记录！</div>");
    }
    
    //显示导航分页
    strhtml.Append( linkURL );

   
    //导航按钮
    strhtml.Append("<div class=\"btBox\"><div class=\"bt1\">");

    strhtml.Append("<a href=\"" + this.http_start + "admin/basesitemodifywml.aspx?siteid=" + siteid + "&amp;classid=0" + "\">返回上级</a>");

    strhtml.Append("</div></div>");

    //输出
    Response.Write(strhtml);
 
                                                                                                                                                                               
Response.Write(ERROR);                                                                                                                                                                              
//显示底部
Response.Write(WapTool.showDown(wmlVo)); %>


