<%@ Page Language="C#" AutoEventWireup="true" CodeBehind="Book_View_Buy.aspx.cs" Inherits="KeLin.WebSite.background.Book_View_Buy" %><%@ Import namespace="KeLin.ClassManager.Tool" %><%
Response.Write(WapTool.showTop("购买空间背景", wmlVo));//显示头  
   
    //显示广告
    //if (adVo.threeShowTop != "")
    //{
    //    strhtml.Append(adVo.threeShowTop );
    //}
    strhtml.Append("<div class=\"subtitle\">购买空间背景</div>" );
    
 
    
    if (this.INFO == "OK")
   {
       strhtml.Append("<div class=\"tip\"><b>购买成功，请回返回查看。</b></div>");
   }
   else if (this.INFO == "NOMONEY")
   {
       strhtml.Append("<div class=\"tip\"><b>我的币不够啦，当前我的币有" + userVo.money + "个。</b></div>");
   }
   else if (this.INFO == "NOPASS")
   {
       strhtml.Append("<div class=\"tip\"><b>密码错误，请重新录入。</b></div>");
  
   }

    if (this.INFO != "OK")
    {
        strhtml.Append("<div class=\"content\">");
        strhtml.Append("<b>" + bookVo.bgname + "</b><br/>");
        strhtml.Append("<div class=\"userinfobg\"  style =\"background-image:url(/" + bookVo.bgimage + ");\">");      
        strhtml.Append("</div>");
        strhtml.Append("价格：" + bookVo.needmoney + " " + siteVo.sitemoneyname + "<br/>");
        strhtml.Append("我当前拥有：" + userVo.money  + " " + siteVo.sitemoneyname + "<br/>");
        strhtml.Append("<form name=\"f\" action=\"" + http_start + "background/book_view_buy.aspx\" method=\"post\">");
        strhtml.Append("我的密码:<input type=\"text\" name=\"pw\" value=\"" + pw + "\" size=\"8\"/><br/>");
        strhtml.Append("<input type=\"hidden\" name=\"siteid\" value=\"" + siteid + "\" />");
        strhtml.Append("<input type=\"hidden\" name=\"classid\" value=\"" + classid + "\" />");
        strhtml.Append("<input type=\"hidden\" name=\"id\" value=\"" + (bookVo.id) + "\" />");
        strhtml.Append("<input type=\"hidden\" name=\"lpage\" value=\"" + (this.lpage) + "\" />");
        strhtml.Append("<input type=\"hidden\" name=\"ordertype\" value=\"" + ordertype + "\" />");
        strhtml.Append("<input type=\"hidden\" name=\"sid\" value=\"" + sid + "\" />");
        strhtml.Append("<input type=\"hidden\" name=\"action\" value=\"gomod\" />");
        strhtml.Append("<input type=\"submit\" class=\"btn\" name=\"\" value=\"确定购买\"/><br/>");
        strhtml.Append("</form>");
        strhtml.Append("</div>");
    }

    string isWebHtml = this.ShowWEB_view(this.classid); //看是存在html代码   
    strhtml.Append("<div class=\"btBox\"><div class=\"bt2\">");
    strhtml.Append("<a href=\"" + this.http_start + "background/book_list.aspx?siteid=" + siteid + "&amp;classid=" + classid + "&amp;page=" + this.lpage + "&amp;ordertype="+this.ordertype +"" + "\">返回上级</a> ");
  
        strhtml.Append("<a href=\"" + this.http_start + "wapindex.aspx?siteid=" + siteid + "&amp;classid=0" + "\">返回首页</a>");
 
    strhtml.Append("</div></div>");

   
    if (isWebHtml != "")
    {
        //string strhtml_list = strhtml.ToString();
        //int s = strhtml_list.IndexOf("<div class=\"title\">");
        //strhtml_list = strhtml_list.Substring(s, strhtml_list.Length - s);

        Response.Clear();
        Response.Write(WapTool.ToWML(isWebHtml, wmlVo).Replace("[view]", strhtml.ToString()));
        Response.End();
    }
    
    
    //输出错误
    strhtml.Append(ERROR);
    //输出
    Response.Write(WapTool.ToWML(strhtml.ToString(), wmlVo));



Response.Write(WapTool.showDown(wmlVo)); %>
