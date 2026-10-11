<%@ Page Language="C#" AutoEventWireup="true" CodeBehind="Book_Re.aspx.cs" Inherits="KeLin.WebSite.rizhi.Book_Re" %><%@ Import namespace="KeLin.ClassManager.Tool" %><%
Response.Write(WapTool.showTop(this.GetLang("查看评论|查看回複|View Reply"), wmlVo));//显示头                                                                                                                                                                       


    strhtml.Append(ERROR);
    //strhtml.Append("<div class=\"subtitle\">" + this.GetLang("查看评论|查看回複|View Reply") + "</div>");
    if (this.INFO == "OK")
    {
        strhtml.Append("<div class=\"tip\"><b>");
        strhtml.Append(this.GetLang("发表成功！|发表成功！|Successfully add"));
        if (siteVo.isCheck == 1)
        {
            strhtml.Append("审核后显示！");
        }


        strhtml.Append("</b>  ");
        strhtml.Append("<a class=\"urlbtn\" href=\"" + this.http_start + "rizhi/book_list.aspx?siteid=" + siteid + "&amp;classid=" + classid + "&amp;lpage=" + lpage + "&amp;myfriends=" + this.myfriends + "&amp;touserid="+this.touserid+"\">返回上级</a>&nbsp;&nbsp;&nbsp;&nbsp;");
       
        strhtml.Append("<a class=\"urlbtn\" href=\"" + this.http_start + "rizhi/book_view.aspx?siteid=" + siteid + "&amp;classid=" + classid + "&amp;id=" + id + "&amp;lpage=" + lpage + "&amp;myfriends=" + this.myfriends + "&amp;touserid="+this.touserid+"\">返回内容</a><br/>");
        strhtml.Append("</div>");
    }
    else if (this.INFO == "NULL")
    {
        strhtml.Append("<div class=\"tip\"><b>");
        strhtml.Append(this.GetLang("内容不能为空！|內容不能爲空！|The content can not be empty!"));
        strhtml.Append("</b><br/>");
        strhtml.Append("</div>");

    }
    else if (this.INFO == "REPEAT")
    {
        strhtml.Append("<div class=\"tip\">");
        strhtml.Append("<b>请不要发重复内容！</b>");
        strhtml.Append("</div>");
    }
    else if (this.INFO == "WAITING")
    {
        strhtml.Append("<div class=\"tip\">");
        strhtml.Append("<b>请再过" + this.KL_CheckIPTime + "秒后操作！</b>");
        strhtml.Append("</div>");

    }
    else
    {
        strhtml.Append("<div class=\"tip\">查看评论</div>");
    }
    //显示输入框
    strhtml.Append("<div class=\"pj\"><div id=\"comments\"><div class=\"comment-post\" id=\"comment-post\">");

    strhtml.Append("<form name=\"re\" action=\"" + http_start + "rizhi/book_re.aspx\" method=\"post\"> ");
    strhtml.Append("<p><textarea name=\"content\" rows=\"10\" class=\"post-area\" placeholder=\"快来发表你的看法和意见吧!\" style=\"width:99%\"></textarea></p>");


    strhtml.Append("<input type=\"hidden\" name=\"action\" value=\"add\"/>");
    strhtml.Append("<input type=\"hidden\" name=\"id\" value=\"" + id + "\"/>");
    strhtml.Append("<input type=\"hidden\" name=\"siteid\" value=\"" + siteid + "\"/>");
    strhtml.Append("<input type=\"hidden\" name=\"lpage\" value=\"" + lpage + "\"/>");
    strhtml.Append("<input type=\"hidden\" name=\"classid\" value=\"" + classid + "\"/>");
    strhtml.Append("<input type=\"hidden\" name=\"myfriends\" value=\"" + myfriends + "\"/>");
    strhtml.Append("<input type=\"hidden\" name=\"touserid\" value=\"" + this.touserid + "\"/>");
    strhtml.Append("<div class=\"gdpl\">");
    //strhtml.Append("<div title=\"评论\" class=\"face\">");
    //strhtml.Append("<a href=\"" + http_start + "rizhi/book_re.aspx?pageback=&amp;getTotal=" + bookVo.book_re + "&amp;id=" + id + "&amp;classid=" + classid + "&amp;siteid=" + siteid + "&amp;myfriends=" + this.myfriends + "&amp;touserid=" + touserid + "\">评论(" + bookVo.book_re + ")</a>");
    //strhtml.Append("</div>");

    strhtml.Append("<input type=\"submit\" name=\"go\" class=\"open2\" id=\"submit\" value=\"发表评论\"/>");
    strhtml.Append("</div></form></div></div></div>");

    strhtml.Append("<div id=\"bbsrelist\">");
    strhtml.Append("<!--listS-->");
    strhtml.Append("<div class=\"pyq_viewre-re\"><ul>");
   
    kk = kk + ((CurrentPage - 1) * pageSize) - 1;
    for (int i = 0; (listVo != null && i < listVo.Count); i++)
    {
        strhtml.Append("<li><img class=\"img\" src=\"" + WapTool.GetHeadImgURL(http_start, listVo[i].headimg) + "\"><div class=\"name\"><a href=\"" + http_start + "bbs/userinfo.aspx?siteid=" + siteid + "&amp;touserid=" + listVo[i].userid + "&amp;id=" + listVo[i].id + "&amp;backurl=" + "\">" + listVo[i].nickname + "</a></div><p>" + listVo[i].content + "");
        if (this.IsCheckManagerLvl("|00|01|03|04|", "") || this.userid == listVo[i].userid.ToString())
        {
            strhtml.Append(" <span class=\"pyq_del\"><a href=\"" + http_start + "rizhi/rizhilist_delre.aspx?action=del&amp;siteid=" + this.siteid + "&amp;classid=" + this.classid + "&amp;myfriends=" + this.myfriends + "&amp;reid=" + listVo[i].id + "&amp;id=" + listVo[i].bookid + "&amp;page=" + this.CurrentPage + "\">&nbsp;</a></span>");
        }
        strhtml.Append("</p></li>");




        kk = kk + 1;
    }

    strhtml.Append("</ul></div>");

    //显示列表

   
    if (listVo == null)
    {
        strhtml.Append("<div class=\"tip\">暂无评论记录！</div>");
    }
    strhtml.Append("<!--listE-->");
    strhtml.Append("</div>");
    //显示导航分页
    strhtml.Append(linkURL);
   
    strhtml.Append("<div class=\"btBox\"><div class=\"bt2\">");
    strhtml.Append("<a href=\"" + this.http_start + "rizhi/book_view.aspx?siteid=" + siteid + "&amp;classid=" + classid + "&amp;id=" + id + "&amp;lpage=" + lpage + "&amp;myfriends=" + this.myfriends + "&touserid=" + touserid + "\">返回上级</a> ");
    strhtml.Append("<a href=\"" + this.http_start + "rizhi/book_list.aspx?action=class&amp;siteid=" + siteid + "&amp;classid=" + classid + "&amp;page=" + lpage + "&amp;myfriends=" + myfriends + "&touserid=" + touserid + "\">返回列表</a> ");

    strhtml.Append("</div></div>");

    //输出
    Response.Write(WapTool.ToWML(strhtml.ToString(), wmlVo));

    
    





                                                                                                                                                                               
                                                                                                                                                                            
//显示底部
Response.Write(WapTool.showDown(wmlVo)); %>
