<%@ Page Language="C#" AutoEventWireup="true" CodeBehind="mod.aspx.cs" Inherits="KeLin.WebSite.admin.charRedirect.mod" %><%@ Import namespace="KeLin.ClassManager.Tool" %><%
Response.Write(WapTool.showTop(this.GetLang("修改内容|修改內容|content modification"), wmlVo));//显示头                                                                                                                                                                       


    Response.Write("<div class=\"subtitle\">" + this.GetLang("修改操作|修改操作|Modify operation") + "</div>");
    Response.Write("<div class=\"tip\">");
    Response.Write(this.ERROR);
    if (this.INFO == "OK")
    {
        Response.Write("<b>");
        Response.Write(this.GetLang("修改成功！|修改成功！|Successfully modified"));
        Response.Write("</b><br/>");
        Response.Write("<a href=\"" + this.http_start + "admin/charRedirect/list.aspx?siteid=" + this.siteid + "&amp;classid=" + this.classid + "&amp;page=" + this.page + "\">" + this.GetLang("返回列表|返回列表|Back to list") + "</a><br/>");
    }
    else if (this.INFO == "NULL")
    {
        Response.Write("<b>");
        Response.Write("不能为空！");
        Response.Write("</b><br/>");

    }
    Response.Write("</div>");
   
    Response.Write("<div class=\"content\">");   
    Response.Write("<form name=\"go\" action=\"" + this.http_start + "admin/charRedirect/mod.aspx\" method=\"post\">");

    Response.Write("地址来源+IP对应城市+浏览器UA　中存在设置的关键字，同时符合多个关键字用,区分,并且关系，例：广州,http *:");
    Response.Write("<br/>");
    Response.Write("<textarea name=\"oldchar\" rows=\"5\" style=\"width:100%\">" + bookVo.secondShowTop + "</textarea><br/>");
    Response.Write("<br/>");
    Response.Write("跳转地址*:（如果/开头地址，要.aspx地址，不能伪静态地址，地址栏中不显示参数。如果http开头，地址栏中显示参数）");
    Response.Write("<br/>");
    Response.Write("<textarea name=\"newchar\" rows=\"5\" style=\"width:100%\">" + bookVo.secondShowDown + "</textarea><br/>");

    Response.Write("<input type=\"hidden\" name=\"action\" value=\"gomod\"/>");
    Response.Write("<input type=\"hidden\" name=\"id\" value=\"" + id + "\"/>");
    Response.Write("<input type=\"hidden\" name=\"classid\" value=\"" + classid + "\"/>");
    Response.Write("<input type=\"hidden\" name=\"siteid\" value=\"" + siteid + "\"/>");
    Response.Write("<input type=\"hidden\" name=\"page\" value=\"" + page + "\"/>");
    Response.Write("<input type=\"hidden\" name=\"sid\" value=\"" + sid + "\"/>");
    Response.Write("<input type=\"submit\" name=\"bt\" value=\"" + this.GetLang("修 改|修 改|Modify") + strInfo+"\"/>");

    Response.Write("</form></div>");
    Response.Write("<div class=\"btBox\"><div class=\"bt1\">");
    Response.Write("<a href=\"" + this.http_start + "admin/charRedirect/list.aspx?siteid=" + this.siteid + "&amp;classid=" + this.classid + "&amp;page=" + this.page + "\">" + this.GetLang("返回列表|返回列表|Back to list") + "</a>");
   
    Response.Write("</div></div>");
 


                                                                                                                                                                               
                                                                                                                                                                             
//显示底部
Response.Write(WapTool.showDown(wmlVo)); %>


