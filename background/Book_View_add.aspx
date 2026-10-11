<%@ Page Language="C#" AutoEventWireup="true" CodeBehind="Book_View_add.aspx.cs" Inherits="KeLin.WebSite.background.Book_View_add" %><%@ Import namespace="KeLin.ClassManager.Tool" %>
<%
    Response.Write(WapTool.showTop("上传空间背景", wmlVo));//显示头  
  
    Response.Write("<div class=\"subtitle\">" + this.GetLang("上传空间背景|上传空间背景|上传空间背景") + "</div>");
    Response.Write("<div class=\"tip\">");
    Response.Write(this.ERROR);
    if (this.INFO == "OK")
    {

        Response.Write("<b>上传成功！</b> ");

       
    }
    else if (this.INFO == "EXTERR")
    {
        Response.Write("<b>上传文件格式错误，只允许上传：PNG|GIF|JPG|JPEG</b><br/>");

    }
    else if (this.INFO == "NOTSPACE")
    {
        Response.Write("<b>网站总空间已经大于系统分配给此网站的最大空间了，网站空间：" + siteVo.sitespace + "M；此网站已使用：" + (siteVo.myspace) + "KB</b><br/>");

    }
    else if (this.INFO == "MAXFILE")
    {
        Response.Write("<b>你上传的单个文件总大小超出了最大限制" + siteVo.MaxFileSize + "KB</b><br/>");

    }
   
    else if (this.INFO == "ERR_FORMAT")
    {
        Response.Write("<b>取到非法值:“$$(”请更换手机浏览器或重新编辑！</b><br/>");
    }
    


    Response.Write("</div>");
    if (this.INFO != "OK")
    {
        Response.Write("<div class=\"content\">");


        //选择栏目
        Response.Write("<form name=\"gt\" action=\"" + http_start + "background/book_view_add.aspx\" enctype=\"multipart/form-data\" method=\"post\">");


        
       
        
        Response.Write("名称:<input type=\"text\" name=\"book_title\" size=\"8\" value=\"" + book_title + "\"/><br/>");
        Response.Write("价格:<input type=\"text\" name=\"book_content\" size=\"5\" value=\"" + book_content + "\"/>"+siteVo.sitemoneyname +"<br/>");
        Response.Write("<input type=\"file\" name=\"book_file\" value=\"\"/><br/>");
        Response.Write("<input type=\"hidden\" name=\"action\" value=\"gomod\"/>");
        Response.Write("<input type=\"hidden\" name=\"classid\" value=\"" + classid + "\"/>");
        Response.Write("<input type=\"hidden\" name=\"siteid\" value=\"" + siteid + "\"/>");
        Response.Write("<input type=\"hidden\" name=\"sid\" value=\"" + sid + "\"/>");
        Response.Write("<input type=\"submit\"  name=\"bt\" value=\"" + this.GetLang("上传|上传|submit") + "\"/></form>");
        Response.Write("<br/>建议.jpg格式，宽高503*185</div>");
    }

    Response.Write("<div class=\"btBox\"><div class=\"bt1\">");
    Response.Write("<a href=\"" + this.http_start + "background/book_list.aspx?siteid=" + this.siteid + "&amp;classid=" + this.classid + "\">" + this.GetLang("返回上级|返回上级|Back to list") + "</a><br/>");
  
 
    Response.Write("</div></div>");
    



//Response.Write(ERROR);                                                                                                                                                                     
//显示底部
Response.Write(WapTool.showDown(wmlVo)); %>



