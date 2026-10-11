<%@ Page Language="C#" AutoEventWireup="true" CodeBehind="Book_Re.aspx.cs" Inherits="KeLin.WebSite.shop.Book_Re" %><%@ Import namespace="KeLin.ClassManager.Tool" %><%
Response.Write(WapTool.showTop(this.GetLang("查看回复|查看回複|View Reply"), wmlVo));//显示头                                                                                                                                                                       


    strhtml.Append("<!--web-->");
    strhtml.Append(ERROR);
    
    if (this.INFO == "OK")
    {
        strhtml.Append("<div class=\"tip\"><b>");
        strhtml.Append(this.GetLang("发表成功！|发表成功！|Successfully add"));
        if (siteVo.isCheck == 1)
        {
            strhtml.Append("审核后显示！");
        }


        strhtml.Append("</b>");
        strhtml.Append("<a class=\"urlbtn\" href=\"" + this.http_start + "shop/book_view.aspx?siteid=" + siteid + "&amp;classid=" + classid + "&amp;id=" + id + "&amp;lpage=" + lpage + "" + "\">返回</a><br/>");
        strhtml.Append("</div>");
    }
    else if (this.INFO == "NULL")
    {
        strhtml.Append("<div class=\"tip\"><b>");
        strhtml.Append(this.GetLang("内容不能为空！|內容不能爲空！|The content can not be empty!"));
        strhtml.Append("</b><br/>");
        strhtml.Append("</div>");

    }
    else if (this.INFO == "NULLBUY")
    {
        strhtml.Append("<div class=\"tip\"><b>");
        strhtml.Append("购买此商品并交易成功后才可以评论哦。");
        strhtml.Append("</b><br/>");
        strhtml.Append("</div>");

    }
    else if (this.INFO == "ERR_FORMAT")
    {
        strhtml.Append("<div class=\"tip\">");
        strhtml.Append("<b>取到非法值:“$$(”请更换手机浏览器或重新编辑！</b><br/>");
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
    //显示输入框
    strhtml.Append("<div class=\"pj\"><div id=\"comments\"><div class=\"comment-post\" id=\"comment-post\">");
    strhtml.Append("<h3 style=\"margin-bottom:-20px;margin-top:5px\">");
    strhtml.Append("我要评论");
    strhtml.Append("</h3>");
    strhtml.Append("<form name=\"re\" action=\"" + http_start + "shop/book_re.aspx\" method=\"post\"><p class=\"num\" style=\"visibility:hidden\">文明评论，重在参与</p>");
    strhtml.Append("<p><textarea name=\"content\" rows=\"10\" class=\"post-area\" placeholder=\"有什么疑问快来问问吧!\" style=\"width:99%\"></textarea></p>");

    strhtml.Append("<input type=\"hidden\" name=\"action\" value=\"add\"/>");
    strhtml.Append("<input type=\"hidden\" name=\"id\" value=\"" + id + "\"/>");
    strhtml.Append("<input type=\"hidden\" name=\"siteid\" value=\"" + siteid + "\"/>");
    strhtml.Append("<input type=\"hidden\" name=\"lpage\" value=\"" + lpage + "\"/>");
    strhtml.Append("<input type=\"hidden\" name=\"classid\" value=\"" + classid + "\"/>");
    strhtml.Append("<input type=\"hidden\" name=\"sid\" value=\"" + sid + "\"/>");
    strhtml.Append("<input type=\"hidden\" name=\"backurl\" value=\"" + HttpUtility.UrlEncode("shop/book_re.aspx?actoin=class&siteid=" + siteid + "&classid=" + classid + "&id=" + id) + "\"/>");
    strhtml.Append("<div class=\"gdpl\">");
    strhtml.Append("<div title=\"评论\" class=\"face\">");
    strhtml.Append("<a href=\"" + http_start + "shop/book_re.aspx?pageback=&amp;getTotal=" + this.total + "&amp;id=" + id + "&amp;classid=" + classid + "&amp;siteid=" + siteid + "" + "\">评论(" + this.total + ")</a>");
    strhtml.Append("</div>");

    strhtml.Append("<input type=\"submit\" name=\"go\" class=\"open2\" id=\"submit\" value=\"发表评论\"/>");
    strhtml.Append("</div></form></div></div></div>");

   
    //显示列表
    strhtml.Append("<!--listS-->"); 
    kk = kk + ((CurrentPage - 1) * pageSize) - 1;
    for (int i = 0; (listVo != null && i < listVo.Count); i++)
    {
        if (i % 2 == 0)
        {
            strhtml.Append("<div class=\"line1\">");
        }
        else
        {
            strhtml.Append("<div class=\"line2\">");
        }
        strhtml.Append("<span class=\"right\">");
        strhtml.Append(string.Format("{0:MM-dd HH:mm}", listVo[i].redate));
        strhtml.Append("&nbsp;&nbsp;" + (total - kk) + "楼 <img src=\"/Template/default/img/huifu.png\" style=\"width:16px;height:16px\" onClick=\"window.location.href='" + this.http_start + "shop/book_re_add.aspx?siteid=" + this.siteid + "&amp;classid=" + this.classid + "&amp;page=" + this.CurrentPage + "&amp;id=" + this.id + "&amp;reid=" + listVo[i].id + "&amp;sid=" + this.sid + "';\" alt=\"回复\"/>");
        strhtml.Append("</span>");
        strhtml.Append("<a href=\"" + http_start + "bbs/userinfo.aspx?siteid=" + siteid + "&amp;touserid=" + listVo[i].userid + "&amp;id=" + listVo[i].id + "&amp;backurl=" + "\">" + listVo[i].nickname + "</a>:<br/>");
        strhtml.Append(listVo[i].content  );
              
        strhtml.Append("</div>"); 
        
        //strhtml.Append("" + (total - kk) + ".[<a href=\""+this.http_start +"shop/book_re_add.aspx?siteid="+this.siteid +"&amp;classid="+this.classid +"&amp;page="+this.CurrentPage +"&amp;id="+this.id +"&amp;reid="+listVo[i].id +"&amp;sid="+this.sid+"\">回</a>]<a href=\"" + http_start + "bbs/userinfo.aspx?siteid=" + siteid + "&amp;touserid=" + listVo[i].userid + "&amp;id=" + listVo[i].id + "&amp;backurl=" + "\">" + listVo[i].nickname + "</a>:" + listVo[i].content + "</div>");
        kk = kk + 1;
    }
    if (listVo == null)
    {
        strhtml.Append("<div class=\"tip\">暂无回复记录！</div>");
    }
    strhtml.Append("<!--listE-->");

  
    //显示导航分页
  
        strhtml.Append(linkURL);
    
    
    strhtml.Append("<div class=\"btBox\"><div class=\"bt1\">");
    strhtml.Append("<a href=\"" + this.http_start + "shop/book_view.aspx?siteid=" + siteid + "&amp;classid=" + classid + "&amp;id=" + id + "&amp;lpage=" + lpage + "" + "\">返回当前商品</a> ");
    strhtml.Append("</div></div>");
    strhtml.Append("<div class=\"btBox\"><div class=\"bt1\">");
    strhtml.Append("<a href=\"" + this.http_start + "shop/book_list.aspx?action=class&amp;siteid=" + siteid + "&amp;classid=" + classid + "&amp;page=" + lpage + "" + "\">返回商品列表</a> ");
    strhtml.Append("</div></div>");
    strhtml.Append("<div class=\"btBox\"><div class=\"bt1\">");
    strhtml.Append("<a href=\"" + this.http_start + "wapindex.aspx?siteid=" + siteid + "" + "\">返回网站首页</a> ");
    strhtml.Append("</div></div>");

    //输出
    Response.Write(WapTool.ToWML(strhtml.ToString(), wmlVo));

    
    





                                                                                                                                                                               
                                                                                                                                                                            
//显示底部
Response.Write(WapTool.showDown(wmlVo)); %>
