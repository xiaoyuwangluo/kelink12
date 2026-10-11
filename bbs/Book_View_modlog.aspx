<%@ Page Language="C#" AutoEventWireup="true" CodeBehind="Book_View_modlog.aspx.cs" Inherits="KeLin.WebSite.bbs.Book_View_modlog" %><%@ Import namespace="KeLin.ClassManager.Tool" %><%
if(this.INFO == "OK"){
    
        wmlVo.timer = "3";
        wmlVo.strUrl = "bbs/book_view.aspx?siteid=" + siteid + "&amp;classid=" + classid + "&amp;id=" + id + "&amp;lpage=" + lpage;
    
   
}
StringBuilder strhtml=new StringBuilder ();
Response.Write(WapTool.showTop("清空日志", wmlVo));//显示头                                                                                                                                                                       


strhtml.Append("<div class=\"subtitle\">" + this.GetLang("清空日志操作|清空日志操作|Modify operation") + "</div>");
    strhtml.Append("<div class=\"tip\">");
    strhtml.Append(this.ERROR);
    if (this.INFO == "OK")
    {
        strhtml.Append("<b>");
        strhtml.Append("清空成功！");
        strhtml.Append("</b> 自动跳转至 <a href=\"" + this.http_start + "bbs/book_view.aspx?siteid=" + this.siteid + "&amp;classid=" + this.classid + "&amp;lpage=" + this.lpage + "&amp;id=" + this.id + "\">" + this.GetLang("帖子主题|帖子主题|Back to subject") + "</a><br/>");
        
    }
     else if(this.INFO=="NULL")
    {
        strhtml.Append("<b>");
        strhtml.Append("<b>标题最少" + this.titlemax + "字，内容最少" + this.contentmax + "字！</b><br/>");
        strhtml.Append("</b><br/>");

    }
    strhtml.Append("</div>");
    if (this.INFO != "OK")
    {
        //显示表情
        strhtml.Append("<form name=\"go\" action=\"" + this.http_start + "bbs/book_view_modlog.aspx\" method=\"post\">");



        strhtml.Append("<div class=\"content\">");
      
        strhtml.Append("确定要清空本帖日志吗？<br/>");
        

       
        
        strhtml.Append("<input type=\"hidden\" name=\"action\" value=\"gomod\"/>");
        strhtml.Append("<input type=\"hidden\" name=\"id\" value=\"" + id + "\"/>");
        strhtml.Append("<input type=\"hidden\" name=\"classid\" value=\"" + classid + "\"/>");
        strhtml.Append("<input type=\"hidden\" name=\"siteid\" value=\"" + siteid + "\"/>");
        strhtml.Append("<input type=\"hidden\" name=\"lpage\" value=\"" + lpage + "\"/>");
        strhtml.Append("<input type=\"hidden\" name=\"sid\" value=\"" + sid + "\"/>");
        strhtml.Append("<input type=\"submit\" name=\"bt\" class=\"btn\" value=\"确定\"/>");

        strhtml.Append("</div></form>");
    }

   
    
    strhtml.Append("<div class=\"btBox\"><div class=\"bt3\">");   
    strhtml.Append("<a href=\"" + this.http_start + "bbs/book_view.aspx?siteid=" + this.siteid + "&amp;classid=" + this.classid + "&amp;lpage=" + this.lpage + "&amp;id=" + this.id + "\">" + this.GetLang("返回主题|返回主题|Back to subject") + "</a> ");
    strhtml.Append("<a href=\"" + this.http_start + "bbs/book_list.aspx?action=class&amp;siteid=" + this.siteid + "&amp;classid=" + this.classid + "&amp;page=" + this.lpage + "\">" + this.GetLang("返回列表|返回列表|Back to list") + "</a> ");
    strhtml.Append("<a href=\"" + this.http_start + "bbs/book_view_admin.aspx?siteid=" + this.siteid + "&amp;classid=" + this.classid + "&amp;lpage=" + this.lpage + "&amp;id=" + this.id + "\">" + this.GetLang("返回管理|返回上級|Back to admin") + "</a> ");
    strhtml.Append("</div></div>");
    Response.Write(strhtml);

                                                                                                                                                                               
                                                                                                                                                                             
//显示底部
Response.Write(WapTool.showDown(wmlVo)); %>


