<%@ Page Language="C#" AutoEventWireup="true" CodeBehind="MessageList.aspx.cs" Inherits="KeLin.WebSite.bbs.MessageList" %><%@ Import namespace="KeLin.ClassManager.Tool" %><%
string msgbox = "";
if (issystem == "1")
{
    msgbox = "系统";
}
else if (issystem == "2")
{
    msgbox = "收藏夹";
}else if (types == "0")
{
    msgbox = "收件箱";
}
else
{
    msgbox = "发件箱"; 
}
Response.Write(WapTool.showTop(msgbox, wmlVo));//显示头  
%>

<link rel="stylesheet" href="/NetCSS/message/css/message.css?v=2" type="text/css" />
<div class="main">
<div class="message-nav"><ul>
<li<%if(types=="0" && issystem=="0"){Response.Write(" class=\"on\"");} %>><a href="/bbs/messagelist.aspx?siteid=<%=siteid %>&classid=<%=classid %>&types=0&issystem=0&backurl=<%=HttpUtility.UrlEncode(backurl)%>">收信箱</a></li>
<li<%if(types=="2" && issystem=="0"){Response.Write(" class=\"on\"");} %>><a href="/bbs/messagelist.aspx?siteid=<%=siteid %>&classid=<%=classid %>&types=2&issystem=0&backurl=<%=HttpUtility.UrlEncode(backurl)%>">发信箱</a></li>
<li<%if(types=="0" && issystem=="2"){Response.Write(" class=\"on\"");} %>><a href="/bbs/messagelist.aspx?siteid=<%=siteid %>&classid=<%=classid %>&types=0&issystem=2&backurl=<%=HttpUtility.UrlEncode(backurl)%>">收藏夹</a></li>
</ul></div>
<div class="message-search">
	  <form id="message-search-form" name="g" method="get" action="/bbs/messagelist.aspx" class="message-search-form">
		<div id="search-hidden-params">
            <% Response.Write("<input type=\"hidden\" name=\"action\" value=\"class\" />");
               Response.Write("<input type=\"hidden\" name=\"siteid\" value=\"" + siteid + "\" />");
               Response.Write("<input type=\"hidden\" name=\"classid\" value=\"" + classid + "\" />");               
               Response.Write("<input type=\"hidden\" name=\"types\" value=\"" + (types) + "\" />");
               Response.Write("<input type=\"hidden\" name=\"issystem\" value=\"" + (issystem) + "\" />");
               Response.Write("<input type=\"hidden\" name=\"backurl\" value=\"" + (backurl) + "\" />");
               Response.Write("<input type=\"hidden\" name=\"sid\" value=\"" + sid + "\" />"); %>
		</div>
		<input id="message-search-word" type="text" name="key" class="message-search-input" placeholder="搜索<%=total %>条内信">
		<input id="message-search-reset" type="reset" value=" " class="message-search-reset" style="display:none">
		<button type="submit" class="message-search-btn"></button>
	  </form>
</div>
<ul class="message-list">
<li>
<%if (issystemcount > 0)
  { %>
<div class="Unread"></div>
<%} %>
<a class="xinlist" href="/bbs/messagelist.aspx?siteid=<%=siteid %>&classid=<%=classid %>&types=0&issystem=1&backurl=<%=HttpUtility.UrlEncode(backurl)%>">
<div class="icon-w"><img src="/NetCSS/message/css/xttz.png"></div>
<div class="m-Contents">
<h2>系统通知<span class="right"><%= WapTool.ShowTime(systemtime)%></span></h2>
<p><%=systemTitle %></p>
</div>
</a>
</li>
<%for (int i = 0; (listVo != null && i < listVo.Count); i++)
  {


      Response.Write("<li>");
      
      if (listVo[i].isnew == 1)
      {
          Response.Write("<div class=\"Unread\"></div>");
      }
      Response.Write("<a class=\"xinlist\" href=\"" + http_start + "bbs/messagelist_view.aspx?siteid=" + siteid + "&amp;classid=" + classid + "&amp;types=" + this.types + "&amp;issystem=" + this.issystem + "&amp;id=" + listVo[i].id + "&amp;backurl=" + HttpUtility.UrlEncode(backurl) + "&amp;key=" + HttpUtility.UrlEncode(key) + "&amp;page=" + this.CurrentPage + "\">");
      if (types == "2") { listVo[i].headimg = userVo.headimg; }
      Response.Write("<div class=\"icon-w\"><img src=\"" + WapTool.GetHeadImgURL(http_start,listVo[i].headimg) + "\"></div>");
      Response.Write("<div class=\"m-Contents\">");
      Response.Write("<h2>" + listVo[i].nickname + "<span class=\"right\">" + WapTool.ShowTime(listVo[i].addtime) + "</span></h2>");
      Response.Write("<p>" + listVo[i].title + "</p>");
      Response.Write("</div>");
      Response.Write("</a>");
      Response.Write("</li>");



 
      

      //strhtml.Append(" [<a class=\"urlbtn\" href=\"" + http_start + "bbs/messagelist_del.aspx?action=del&amp;siteid=" + siteid + "&amp;classid=" + classid + "&amp;id=" + listVo[i].id + "&amp;types=" + this.types + "&amp;issystem=" + this.issystem + "&amp;backurl=" + HttpUtility.UrlEncode(backurl) + "&amp;page=" + this.CurrentPage + "" + "\" >删除</a>");
      if (types != "2" && issystem != "2")
      {
          //strhtml.Append(".<a class=\"urlbtn\" href=\"" + http_start + "bbs/messagelist.aspx?action=save&amp;siteid=" + siteid + "&amp;classid=" + classid + "&amp;id=" + listVo[i].id + "&amp;types=" + this.types + "&amp;issystem=" + this.issystem + "&amp;backurl=" + HttpUtility.UrlEncode(backurl) + "&amp;page=" + this.CurrentPage + "" + "\">收藏</a>");

      }
      
  }
  if (listVo == null)
  {
      Response.Write("<div class=\"tip\">暂无" + msgbox + "消息！</div>");
  }
  //显示导航分页
  Response.Write(linkURL);
    
     %>



</ul>
<div class="btBox"><div class="bt1"><a href="/bbs/messagelist_add.aspx?siteid=<%=siteid %>&amp;classid=<%=classid %>&amp;types=<%=types %>&amp;backurl=<%= HttpUtility.UrlEncode(backurl) %>">发送消息</a></div></div>
<div class="btBox"><div class="bt1">
<%
    if (types == "0" && issystem == "0")
    {
        Response.Write("<a href=\"" + http_start + "bbs/messagelist_del.aspx?action=delall&amp;siteid=" + siteid + "&amp;classid=" + classid + "&amp;types=0&amp;issystem=0&amp;backurl=" + HttpUtility.UrlEncode(backurl) + "&amp;page=" + this.CurrentPage + "" + "\">清空收件箱</a> ");
       
    }
    else if (types == "0" && issystem == "1")
    {
        Response.Write("<a href=\"" + http_start + "bbs/messagelist_del.aspx?action=delall&amp;siteid=" + siteid + "&amp;classid=" + classid + "&amp;types=0&amp;issystem=1&amp;backurl=" + HttpUtility.UrlEncode(backurl) + "&amp;page=" + this.CurrentPage + "" + "\">清空系统消息</a> ");
    }
    else if (types == "0" && issystem == "2")
    {
        
        Response.Write("<a href=\"" + http_start + "bbs/messagelist_del.aspx?action=delall&amp;siteid=" + siteid + "&amp;classid=" + classid + "&amp;types=0&amp;issystem=2&amp;backurl=" + HttpUtility.UrlEncode(backurl) + "&amp;page=" + this.CurrentPage + "" + "\">清空收藏夹</a> ");

    }
    else if (types == "2" && issystem == "0")
    {
        Response.Write("<a href=\"" + http_start + "bbs/messagelist_del.aspx?action=delall&amp;siteid=" + siteid + "&amp;classid=" + classid + "&amp;types=2&amp;issystem=0&amp;backurl=" + HttpUtility.UrlEncode(backurl) + "&amp;page=" + this.CurrentPage + "" + "\">清空发件箱</a> ");

    }
%></div></div>
<div class="btBox"><div class="bt2"><a href="<%= this.http_start %><%=backurl %>">返回源来页</a> <a href="/wapindex.aspx?siteid=<%=siteid %>">返回首页</a></div></div>
</div>

<%

    //strhtml.Append(" [<a class=\"urlbtn\" href=\"" + http_start + "bbs/messagelist_del.aspx?action=del&amp;siteid=" + siteid + "&amp;classid=" + classid + "&amp;id=" + listVo[i].id + "&amp;types=" + this.types + "&amp;issystem=" + this.issystem + "&amp;backurl=" + HttpUtility.UrlEncode(backurl) + "&amp;page=" + this.CurrentPage + "" + "\" >删除</a>");
    if (types != "2" && issystem != "2")
    {
       // strhtml.Append(".<a class=\"urlbtn\" href=\"" + http_start + "bbs/messagelist.aspx?action=save&amp;siteid=" + siteid + "&amp;classid=" + classid + "&amp;id=" + listVo[i].id + "&amp;types=" + this.types + "&amp;issystem=" + this.issystem + "&amp;backurl=" + HttpUtility.UrlEncode(backurl) + "&amp;page=" + this.CurrentPage + "" + "\">收藏</a>");

    }
    //strhtml.Append("]");
                                                                                                                                                                               
Response.Write(ERROR);                                                                                                                                                                              
//显示底部
Response.Write(WapTool.showDown(wmlVo)); %>



