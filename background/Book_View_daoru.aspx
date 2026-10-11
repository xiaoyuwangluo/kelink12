<%@ Page Language="C#" AutoEventWireup="true" CodeBehind="Book_View_daoru.aspx.cs" Inherits="KeLin.WebSite.background.Book_View_daoru" %><%@ Import namespace="KeLin.ClassManager.Tool" %><%
Response.Write(WapTool.showTop("导入系统空间背景", wmlVo));//显示头  
                                                                                                                                                                    

    //显示广告
    //if (adVo.threeShowTop != "")
    //{
    //    strhtml.Append(adVo.threeShowTop );
    //}
    strhtml.Append("<div class=\"subtitle\">导入系统空间背景</div>");
    
 
    
    if (this.INFO == "OK")
   {
       strhtml.Append("<div class=\"tip\"><b>*导入 " + max + " 个成功！</b></div>");
   }
    else if (this.INFO == "NOFORMAT")
   {
       strhtml.Append("<div class=\"tip\"><b>*名称不能为空间，或价格要数值！</b></div>");
   }
   else if (this.INFO == "NOPASS")
   {
       strhtml.Append("<div class=\"tip\"><b>密码错误，请重新录入。</b></div>");
  
   }

    if (this.INFO != "OK")
    {
        strhtml.Append("<div class=\"content\">");
        strhtml.Append("<form name=\"f\" action=\"" + http_start + "background/book_view_daoru.aspx\" method=\"post\">");
        
        strhtml.Append("我的密码:<input type=\"text\" name=\"pw\" value=\"" + pw + "\" size=\"8\"/><br/>");
        strhtml.Append("<input type=\"hidden\" name=\"siteid\" value=\"" + siteid + "\" />");
        strhtml.Append("<input type=\"hidden\" name=\"classid\" value=\"" + classid + "\" />");
       
        strhtml.Append("<input type=\"hidden\" name=\"lpage\" value=\"" + (this.lpage) + "\" />");
        strhtml.Append("<input type=\"hidden\" name=\"ordertype\" value=\"" + ordertype + "\" />");
        strhtml.Append("<input type=\"hidden\" name=\"sid\" value=\"" + sid + "\" />");
        strhtml.Append("<input type=\"hidden\" name=\"action\" value=\"godel\" />");
        strhtml.Append("<input type=\"submit\" name=\"\" value=\"确定导入\"/><br/>");
        strhtml.Append("</form>");
        strhtml.Append("</div>");
    }

    strhtml.Append("<div class=\"btBox\"><div class=\"bt2\">");
    strhtml.Append("<a href=\"" + this.http_start + "background/book_list.aspx?siteid=" + siteid + "&amp;classid=" + classid + "&amp;page=" + this.lpage + "&amp;ordertype="+this.ordertype +"" + "\">返回上级</a> ");
    strhtml.Append("<a href=\"" + this.http_start + "wapindex.aspx?siteid=" + siteid + "&amp;classid=0" + "\">返回首页</a>");
 
    strhtml.Append("</div></div>");
    //输出错误
    strhtml.Append(ERROR);
    //输出
    Response.Write(WapTool.ToWML(strhtml.ToString(), wmlVo));



Response.Write(WapTool.showDown(wmlVo)); %>
