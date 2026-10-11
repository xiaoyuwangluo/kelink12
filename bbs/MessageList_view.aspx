<%@ Page Language="C#" AutoEventWireup="true" CodeBehind="MessageList_view.aspx.cs" Inherits="KeLin.WebSite.bbs.MessageList_view" %><%@ Import namespace="KeLin.ClassManager.Tool" %>
<%Response.Write(WapTool.showTop("查看内信", wmlVo));//显示头   %>
<link rel="stylesheet" href="/NetCSS/message/css/message.css?v=2" type="text/css" />
<div class="main">
<div class="message-top">
    <div class="back"><% Response.Write("<a  href=\"" + http_start + "bbs/messagelist.aspx?siteid=" + siteid + "&amp;classid=" + classid + "&amp;types=" + this.types + "&amp;issystem=" + this.issystem + "&amp;backurl=" + HttpUtility.UrlEncode(backurl) + "&amp;page=" + this.page +   "\"></a>"); %></div>
    <%=toUserVo.nickname %>
    <p class="data">ID:<%=toUserVo.userid %> <%if(toUserVo.isonline=="1"){Response.Write ("在线");}else{Response.Write("离线");} %></p>
    <div class="home"><a href="<%=( http_start + "bbs/messagelist_view.aspx?siteid=" + siteid + "&amp;classid=" + classid + "&amp;types=" + this.types + "&amp;issystem=" + this.issystem + "&amp;id=" + this.id + "&amp;backurl=" + HttpUtility.UrlEncode(backurl) + "&amp;key=" + HttpUtility.UrlEncode(key) + "&amp;page=" + this.page + "&amp;r=" + r ) %>"></a></div>
  </div>
  <div class="message-search">
  <form name="f" action="/bbs/messagelist_add.aspx" method="post" class="message-search-form">
  <textarea name="content" rows="3" class="message-search-input" placeholder="输入回复内容"></textarea>
  <%
      if (this.needpwFlag == "1")
      {

          //Response.Write("<br/>我的密码*:<input type=\"text\" name=\"needpw\" value=\"" + needpw + "\" size=\"10\" /><br/>");

      }
       %>
  <div id="search-hidden-params">
 
  <%
      
      
      
      Response.Write("<input type=\"hidden\" name=\"action\" value=\"gomod\"/>");
      Response.Write("<input type=\"hidden\" name=\"classid\" value=\"" + classid + "\"/>");
      Response.Write("<input type=\"hidden\" name=\"siteid\" value=\"" + siteid + "\"/>");
      Response.Write("<input type=\"hidden\" name=\"types\" value=\"" + types + "\"/>");
      Response.Write("<input type=\"hidden\" name=\"issystem\" value=\"" + issystem + "\"/>");
      Response.Write("<input type=\"hidden\" name=\"toid\" value=\"" + id + "\"/>");
      Response.Write("<input type=\"hidden\" name=\"backurl\" value=\"" + backurl + "\"/>");
      Response.Write("<input type=\"hidden\" name=\"page\" value=\"" + page + "\"/>");
      Response.Write("<input type=\"hidden\" name=\"title\" value=\"\"/>");
      Response.Write("<input type=\"hidden\" name=\"touseridlist\" value=\"" + bookVo.userid + "\"/>");
      Response.Write("<input type=\"hidden\" name=\"sid\" value=\"" + sid + "\"/>");
      //Response.Write("<input type=\"submit\" name=\"g\" class=\"btn\" value=\" 发 送 \"/></form><br/>");

      
       %>
  
 </div>
  <input type="submit" name="g" class="message-search-huifu" value=" "></form>
  </div>
  
<div class="bfff">
<%
  StringBuilder html = new StringBuilder();
        for (int i = 0; (listVo != null && i < listVo.Count); i++)
        {
             
            //html.Append("标题:" + listVo[i].title + "<br/>");
            if (listVo[i].touserid.ToString() != this.userid)
            {
               

                html.Append("<div class=\"message-chat\">");
                html.Append("<span class=\"usertx userrm\"><a href=\"" + this.http_start + "bbs/userinfo.aspx?siteid=" + this.siteid + "&amp;touserid=" + this.userid + "&amp;backurl=" + HttpUtility.UrlEncode(this.GetUrlQueryString()) + "\"><img src=\"" + WapTool.GetHeadImgURL(http_start, userVo.headimg) + "\" alt=\"我的头像\" /></a></span>");

               
                html.Append("<div class=\"r\">");

                
                html.Append(listVo[i].content + "<span class=\"date\">" + listVo[i].addtime + "</span></div>");
                html.Append("</div>");


             
            }
            else
            {
                 html.Append("<div class=\"message-chat\">");
                html.Append("<p>");
                html.Append("<span class=\"usertx userlm\"><a href=\"" + this.http_start + "bbs/userinfo.aspx?siteid=" + this.siteid + "&amp;touserid=" + listVo[i].userid + "&amp;backurl=" + HttpUtility.UrlEncode(this.GetUrlQueryString()) + "\"><img src=\""+WapTool.GetHeadImgURL(http_start,listVo[i].headimg)+"\" alt=\"对方头像\" /></a></span>");
                if (this.id == listVo[i].id.ToString())
                {
                    html.Append("<div class=\"l2\">");
                }
                else
                {
                    html.Append("<div class=\"l\">");

                }
                html.Append(listVo[i].content+"<span class=\"date dl\">"+listVo[i].addtime+"</span></div>");
                html.Append("</p>");
                html.Append("</div>");
                
                
             }
            

        }
        if (html.ToString() != "")
        {
            Response.Write(WapTool.ToWML(html.ToString(), wmlVo));
        }

    
    
    Response.Write("<div class=\"tip\">显示最近30条聊天记录</div>");
     
    
     %>








</div>


<div class="btBox"><div class="bt2">
<%
     Response.Write("<a  href=\"" + http_start + "bbs/messagelist_del.aspx?action=del&amp;siteid=" + siteid + "&amp;classid=" + classid + "&amp;id=" + this.id + "&amp;types=" + this.types + "&amp;issystem=" + this.issystem + "&amp;backurl=" + HttpUtility.UrlEncode(backurl) + "&amp;page=" + this.page + "" + "\">删除本条会话</a>");
    Response.Write("<a  href=\"" + http_start + "bbs/messagelist_del.aspx?action=delother&amp;siteid=" + siteid + "&amp;classid=" + classid + "&amp;id=" + this.id + "&amp;types=" + this.types + "&amp;issystem=" + this.issystem + "&amp;backurl=" + HttpUtility.UrlEncode(backurl) + "&amp;page=" + this.page + "" + "\">删除本页会话</a>");
    
     %>

</div></div>
<div class="btBox"><div class="bt2">
<%
     Response.Write("<a  href=\"" + http_start + "bbs/messagelist.aspx?action=save&amp;siteid=" + siteid + "&amp;classid=" + classid + "&amp;id=" +this.id + "&amp;types=" + this.types + "&amp;issystem=" + this.issystem + "&amp;backurl=" + HttpUtility.UrlEncode(backurl) + "&amp;page=" + this.page  + "\">转移至收藏夹</a>");
    Response.Write("<a  href=\"" + http_start + "bbs/messagelist.aspx?siteid=" + siteid + "&amp;classid=" + classid + "&amp;types=" + this.types + "&amp;issystem=" + this.issystem + "&amp;backurl=" + HttpUtility.UrlEncode(backurl) + "&amp;page=" + this.page +   "\">返回上级列表</a>");
    
     %>

</div></div>

</div>


    



       <%                                                                                                                                                                        
                                                                                                                                                                             
//显示底部
Response.Write(WapTool.showDown(wmlVo)); %>




