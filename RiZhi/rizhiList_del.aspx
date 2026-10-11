<%@ Page Language="C#" AutoEventWireup="true" CodeBehind="rizhiList_del.aspx.cs" Inherits="KeLin.WebSite.rizhi.rizhiList_del" %><%@ Import namespace="KeLin.ClassManager.Tool" %><%

StringBuilder strhtml=new StringBuilder ();
Response.Write(WapTool.showTop(this.GetLang("删除|删除|delete"), wmlVo));//显示头                                                                                                                                                                       

    strhtml.Append("<div class=\"subtitle\">" + this.GetLang("删除|删除|delete") + "</div>");
    strhtml.Append(this.ERROR);
    strhtml.Append("<div class=\"tip\">");
    if (this.INFO == "")
    {
        strhtml.Append("<a href=\"" + http_start + "rizhi/rizhilist_del.aspx?action=go" + action + "&amp;siteid=" + siteid + "&amp;classid=" + classid + "&amp;id=" + this.id + "&amp;page=" + this.page + "&amp;myfriends=" + this.myfriends + "\">" + this.GetLang("确定要删除吗？是！|確定要刪除嗎？是！|Are you sure? YES") + "</a><br/>");


    }
    else if (this.INFO == "OK")
    {
        strhtml.Append("<b>" + this.GetLang("删除成功！|刪除成功！|Deleted successfully!") + "</b><br/>");


    }else if (this.INFO == "ERROR")
    {
        strhtml.Append("<b>删除失败！原因可能权限不足。</b><br/>");


    }

    strhtml.Append("</div>");
    strhtml.Append("<div class=\"btBox\"><div class=\"bt1\">");
    strhtml.Append("<a href=\"" + http_start + "rizhi/book_list.aspx?siteid=" + siteid + "&amp;classid=" + classid + "&amp;myfriends=" + this.myfriends + "&amp;page=" + this.page + "" + "\">返回上级</a>");

    strhtml.Append("</div></div>");

    
    Response.Write(strhtml);


                                                                                                                                                                               
                                                                                                                                                                             
//显示底部
Response.Write(WapTool.showDown(wmlVo)); %>


