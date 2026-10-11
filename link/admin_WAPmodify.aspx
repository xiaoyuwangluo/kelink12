<%@ Page Language="C#" AutoEventWireup="true" CodeBehind="admin_WAPmodify.aspx.cs" Inherits="KeLin.WebSite.link.admin_WAPmodify" %><%@ Import namespace="KeLin.ClassManager.Tool" %><%
Response.Write(WapTool.showTop(this.GetLang("修改简介|修改简介|content modification"), wmlVo));//显示头                                                                                                                                                                       


    Response.Write("<div class=\"subtitle\">" + this.GetLang("修改操作|修改操作|Modify operation") + "</div>");
    Response.Write("<div class=\"tip\">");
    Response.Write(this.ERROR);
    if (this.INFO == "OK")
    {
        Response.Write("<b>");
        Response.Write(this.GetLang("修改成功！|修改成功！|Successfully modified"));
        Response.Write("</b> ");
        Response.Write("<a href=\"" + this.http_start + "link/admin_userlistWAP.aspx?siteid=" + this.siteid + "&amp;classid=" + this.classid + "&amp;page=" + this.page + "\">" + this.GetLang("返回列表|返回列表|Back to list") + "</a><br/>");
    }
    else if (this.INFO == "NULL")
    {
        Response.Write("<b>");
        Response.Write(this.GetLang("网站名称或简介不能为空！|网站名称或简介不能爲空！|The title or content can not be empty!"));
        Response.Write("</b><br/>");

    }
    Response.Write("</div>");
    Response.Write("<div class=\"content\">");
    Response.Write("<form name=\"go\" action=\"" + this.http_start + "link/admin_WAPmodify.aspx\" method=\"post\">");
    Response.Write("列表显示LOGO图:");
    Response.Write("<a href=\"" + this.http_start + "link/admin_wapmodifyadd.aspx?types=1&siteid=" + this.siteid + "&classid=" + this.classid + "&id=" + this.id + "&sid=" + this.sid + "\">【编辑图片】</a><br/>");
    Response.Write("<img src=\"/link/" + bookVo.book_img + "\" style=\"width:120px;height:120px\" alt=\"列表显示图\"/><br/>");

    Response.Write("<input type=\"hidden\" name=\"book_img\" value=\"" + bookVo.book_img + "\" style=\"width:100%\"/>");
    Response.Write("顶部划图,多个用|区分,");
    Response.Write("<a href=\"" + this.http_start + "link/admin_wapmodifyadd.aspx?types=2&siteid=" + this.siteid + "&classid=" + this.classid + "&id=" + this.id + "&sid=" + this.sid + "\">【编辑图片】</a><br/>");

    string[] arrys = bookVo.book_file.Split('|');
    for (int y = 0; y < arrys.Length; y++)
    {
        if (arrys[y].Trim() != "")
        {
            Response.Write("<img src=\"/link/" + arrys[y] + "\" style=\"width:150px;height:150px\" alt=\"划动图\"/>");
        }
    }
    Response.Write("<input type=\"hidden\" name=\"book_file\" value=\"" + bookVo.book_file + "\" style=\"width:100%\" /><br/>");
    
    
    Response.Write(this.GetLang("网站名称|网站名称|Title") + "*:<br/>");
    Response.Write("<input type=\"text\" name=\"book_title\" value=\"" + bookVo.book_title + "\"/><br/>");
    Response.Write(this.GetLang("作者|作者|Author") + ":<br/>");
    Response.Write("<input type=\"text\" name=\"book_author\" value=\"" + bookVo.book_author + "\"/><br/>");
    Response.Write(this.GetLang("网站地址|网站地址|User ID") + ":<br/>");
    Response.Write("<input type=\"text\" name=\"book_pub\" value=\"" + bookVo.book_pub + "\"/><br/>");
    Response.Write(this.GetLang("给对方地址|给对方地址|给对方地址") + ":<br/>");
    Response.Write("<input type=\"text\" name=\"toshare" + r + "\" value=\"http://" + Request.ServerVariables["HTTP_HOST"] + "/link/gourl.aspx?id=" + this.siteid + "_" + this.id + "\"/><br/>");
    
    Response.Write("从对方过来指定跳转地址:<br/>");
    Response.Write("<input type=\"text\" name=\"redirectURL\" value=\""+bookVo.redirectURL+"\"/><br/>");
    Response.Write("(留空转至首页，否则必须为本站地址，去掉http://域名，以/开头，例/shop/book_list.aspx?siteid=1000&amp;classid=8)<br/>");
    
    Response.Write(this.GetLang("简介|简介|Content") + "(注意如是微信模式内容后面带“→微信号”):<br/>");
    Response.Write("<textarea name=\"book_content\" rows=\"6\" style=\"width:100%\">" + bookVo.book_content.Replace("[br]", "\r\n") + "</textarea><br/>");    
    Response.Write("<input type=\"hidden\" name=\"action\" value=\"gomod\"/>");
    Response.Write("<input type=\"hidden\" name=\"id\" value=\"" + id + "\"/>");
    Response.Write("<input type=\"hidden\" name=\"classid\" value=\"" + classid + "\"/>");
    Response.Write("<input type=\"hidden\" name=\"siteid\" value=\"" + siteid + "\"/>");
    Response.Write("<input type=\"hidden\" name=\"page\" value=\"" + page + "\"/>");
    Response.Write("<input type=\"hidden\" name=\"sid\" value=\"" + sid + "\"/>");
    Response.Write("<input type=\"submit\" name=\"bt\" value=\"" + this.GetLang("修 改|修 改|Modify") + "\"/>");

    Response.Write("</form></div>");
    Response.Write("<div class=\"btBox\"><div class=\"bt1\">");
    Response.Write("<a href=\"" + this.http_start + "link/admin_userlistWAP.aspx?siteid=" + this.siteid + "&amp;classid=" + this.classid + "&amp;page=" + this.page + "\">" + this.GetLang("返回列表|返回列表|Back to list") + "</a>");
     
    Response.Write("</div></div>");
 


                                                                                                                                                                               
                                                                                                                                                                             
//显示底部
Response.Write(WapTool.showDown(wmlVo)); %>


