<%@ Page Language="C#" AutoEventWireup="true" CodeBehind="list.aspx.cs" Inherits="KeLin.WebSite.admin.charReplace.list" %><%@ Import namespace="KeLin.ClassManager.Tool" %><%
Response.Write(WapTool.showTop("替换字符管理", wmlVo));//显示头                                                                                                                                                                       



strhtml.Append("<div class=\"subtitle\">替换字符管理</div>");
    //显示搜索

    strhtml.Append("<div class=\"content\">");
        
    strhtml.Append("例文章、论坛内容等字符替换。<br/>");
    strhtml.Append("<a href=\"" + this.http_start + "admin/charReplace/add.aspx?action=go&amp;siteid=" + this.siteid + "&amp;classid=" + this.classid + "&amp;page=" + this.CurrentPage + "\">批量新增</a><br/>");

    strhtml.Append("<form id=\"form1\" action=\"" + http_start + "admin/charReplace/list.aspx\" method=\"get\">");
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
        
        
        
      
        strhtml.Append(index + ".[<a href=\"" + this.http_start + "admin/charReplace/del.aspx?action=go&amp;id=" + listVo[i].id + "&amp;siteid=" + this.siteid + "&amp;classid=" + this.classid + "&amp;page=" + this.CurrentPage + "\">删</a>.<a href=\"" + this.http_start + "admin/charReplace/mod.aspx?action=go&amp;id=" + listVo[i].id + "&amp;siteid=" + this.siteid + "&amp;classid=0&amp;page=" + this.CurrentPage + "\">修</a>]" + HttpUtility.HtmlEncode( listVo[i].secondShowTop) +"→"+ HttpUtility.HtmlEncode( listVo[i].secondShowDown)+ "<br/>");
       
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


