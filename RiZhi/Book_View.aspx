<%@ Page Language="C#" AutoEventWireup="true" CodeBehind="Book_View.aspx.cs" Inherits="KeLin.WebSite.rizhi.Book_View" %><%@ Import namespace="KeLin.ClassManager.Tool" %><% 

string toptitle=bookVo.book_title;
if (toptitle.Length > 20) toptitle = WapTool.left(toptitle, 30) + "...";
Response.Write(WapTool.showTop(toptitle, wmlVo));//显示头      
string temptitle = "查看详情";
//如果是日志，标题需要做处理
if (bookVo.book_type == 1)
{

    temptitle = bookVo.book_title;
}                                                                                                                                                                 

//显示广告
if (adVo.threeShowTop != "")
{
    strhtml.Append(adVo.threeShowTop);
}
    /*
    strhtml.Append("<div id =\"rizhifabiao\" class=\"btBox\"><div class=\"bt3\">");

    strhtml.Append("<a ");
    if (this.myfriends == "1")
    {
        strhtml.Append(" class=\"btSelect\" ");
    }
    strhtml.Append("href=\"" + this.http_start + "rizhi/book_list.aspx?siteid=" + this.siteid + "&amp;classid=0&amp;myfriends=1\">我的朋友圈</a> ");
    strhtml.Append("<a ");
    if (this.myfriends == "0")
    {
        strhtml.Append(" class=\"btSelect\" ");
    }
    strhtml.Append("href=\"" + this.http_start + "rizhi/book_list.aspx?siteid=" + this.siteid + "&amp;classid=0&amp;myfriends=0\">");
    if (touserid != "0")
    {
        strhtml.Append("TA");
    }
    else
    {
        strhtml.Append("朋友圈");
    }
    strhtml.Append("广场</a> ");

    strhtml.Append("<a href=\"" + this.http_start + "rizhi/admin_WAPadd.aspx?siteid=" + this.siteid + "&amp;classid=" + this.classid + "&amp;book_type=0\">我要发表</a>");
    strhtml.Append("</div></div>");

    strhtml.Append("<div class=\"pyq_nav\">");
    if (this.classid == "0")
    {
        strhtml.Append("<span class=\"yes\"><a href=\"" + this.http_start + "rizhi/book_list.aspx?siteid=" + this.siteid + "&amp;classid=0&amp;myfriends=" + this.myfriends + "\">");
        strhtml.Append("最新");
        strhtml.Append("</a></span>");
    }
    else
    {
        strhtml.Append("<a href=\"" + this.http_start + "rizhi/book_list.aspx?siteid=" + this.siteid + "&amp;classid=0&amp;myfriends=" + this.myfriends + "\">");
        strhtml.Append("最新");
        strhtml.Append("</a>");
    }

    for (int i = 0; (classList != null && i < classList.Count); i++)
    {



        if (this.classid == classList[i].classid.ToString())
        {
            strhtml.Append("<span class=\"yes\"><a href=\"" + this.http_start + "rizhi/book_list.aspx?siteid=" + this.siteid + "&amp;classid=" + classList[i].classid + "&amp;myfriends=" + this.myfriends + "\">");
            strhtml.Append(classList[i].classname);
            strhtml.Append("</a></span>");
        }
        else
        {
            strhtml.Append("<a href=\"" + this.http_start + "rizhi/book_list.aspx?siteid=" + this.siteid + "&amp;classid=" + classList[i].classid + "&amp;myfriends=" + this.myfriends + "\">");
            strhtml.Append(classList[i].classname);
            strhtml.Append("</a>");
        }

    }

    strhtml.Append("</div>");

    */

strhtml.Append("   <div class=\"pyq_top\"> <span class=\"back\"><a href=\"javascript:window.history.back();\"> </a></span> " + temptitle + " <span class=\"kanta\"><a href=\"" + this.http_start + "rizhi/book_list.aspx?siteid=" + this.siteid + "&classid=0&myfriends=0&touserid=" + bookVo.makerid + "\"> </a></span> </div>\r\n");
strhtml.Append(" <div class=\"pyq_view\">\r\n");
strhtml.Append("  <div class=\"user\"> <img src=\"" + WapTool.GetHeadImgURL(http_start, bookVo.headimg) + "\"><a href=\"" + this.http_start + "bbs/userinfo.aspx?siteid=" + this.siteid + "&amp;touserid=" + bookVo.makerid + "&amp;backurl=" + HttpUtility.UrlEncode(this.GetUrlQueryString()) + "\">" + bookVo.book_author + "</a><span class=\"right time\">" + bookVo.book_date.ToString("MM-dd HH:MM") + "</span> </div>\r\n");
strhtml.Append("  <div class=\"pyq_view_p\"> ");
//开始显示内容
if (bookVo.book_type == 0)
{
    if (bookVo.book_title.StartsWith("http:"))
    {
        strhtml.Append("TA 分享了一个链接，<a href=\"" + bookVo.book_title + "\">点击此进入</a>。<br/>");
    }

    strhtml.Append(WapTool.ReplaceChinaseToImage(bookVo.book_title.Replace("［br］", "<br/>")));
}
else
{
    if (bookVo.book_content.StartsWith("http:"))
    {
        strhtml.Append("TA 分享了一个链接，<a href=\"" + bookVo.book_content + "\">点击此进入</a>。<br/>");
    }
    strhtml.Append(WapTool.ReplaceChinaseToImage(bookVo.book_content.Replace("［br］", "<br/>")));
}

strhtml.Append("<p align=\"center\" style=\"text-indent:0em;\">");

StringBuilder strDown = new StringBuilder(); //文件下载
string[] filelist = bookVo.book_file.Split('|');
for (int i = 0; (i < filelist.Length); i++)
{
    if (filelist[i].IndexOf(".") > 0)
    {
        String[] temp = filelist[i].Split('.');
        if (".gif|.jpg|.jpeg|.png|.bmp|".IndexOf(temp[1].ToLower()) >= 0)
        {

            if (filelist[i].Substring(0, 5).ToString() == "http:" || filelist[i].Substring(0, 1).ToString() == "/")
            {
                strhtml.Append("<img src=\"" + filelist[i] + "\" alt=\"load...\"><br/>");
            }
            else
            {
                strhtml.Append("<a href=\"" + this.http_start + "rizhi/picDIY.aspx?siteid=" + this.siteid + "&amp;classid=" + this.classid + "&amp;id=" + this.id + "&amp;path=" + HttpUtility.UrlEncode(@"rizhi\" + filelist[i]) + "\"><img src=\"" + this.http_start + "rizhi/" + filelist[i] + "\" alt=\"load...\"/></a><br/>");
            }






        }
        else
        {

            strhtml.Append("<a href=\"" + this.http_start + "rizhi/" + filelist[i] + "\">点击下载(" + temp[1] + ")</a><br/>");

            strhtml.Append("<br/>");
        }
    }
}



strhtml.Append("</p>");
strhtml.Append("</div>\r\n");
strhtml.Append("  <div class=\"pyq_view_fx\" id=\"view_fx\" style=\"display: none;\">\r\n");
strhtml.Append("     <div class=\"bdsharebuttonbox\"> <a href=\"#\" class=\"bds_weixin\" data-cmd=\"weixin\" title=\"分享到微信\">微信</a> <a href=\"#\" class=\"bds_qzone\" data-cmd=\"qzone\" title=\"分享到QQ空间\">QQ空间</a> <a href=\"#\" class=\"bds_sqq\" data-cmd=\"sqq\" title=\"分享到QQ好友\">QQ好友</a> <a href=\"#\" class=\"bds_tsina\" data-cmd=\"tsina\" title=\"分享到新浪微博\">新浪微博</a> <a href=\"#\" class=\"bds_tqq\" data-cmd=\"tqq\" title=\"分享到腾讯微博\">腾讯微博</a> <a href=\"#\" class=\"bds_more\" data-cmd=\"more\">更多</a> </div>\r\n");
strhtml.Append("     <script>\r\n");
strhtml.Append("window._bd_share_config={\"common\":{\"bdSnsKey\":{},\"bdText\":\"\",\"bdMini\":\"2\",\"bdMiniList\":false,\"bdPic\":\"\",\"bdStyle\":\"0\",\"bdSize\":\"16\"},\"share\":{\"bdSize\":16}};\r\n");
strhtml.Append("with(document)0[(getElementsByTagName('head')[0]||body).appendChild(createElement('script')).src='http://bdimg.share.baidu.com/static/api/js/share.js?v=89860593.js?cdnversion='+~(-new Date()/36e5)];\r\n");
strhtml.Append("</script> \r\n");
strhtml.Append("   </div>\r\n");
strhtml.Append("   <div class=\"pyq\"> <span class=\"pyq_fx\" id=\"fxmore\"> </span> <span class=\"pyq_zan\"><a id=\"riz" + bookVo.id + "\" href=\"javascript:getgood('riz" + bookVo.id + "','/rizhi/ajax.aspx?type=good&id=" + bookVo.id + "')\">" + bookVo.book_good + "</a></span> <span class=\"pyq_re\"> <a href=\"" + this.http_start + "rizhi/book_re.aspx?actoin=class&amp;siteid=" + this.siteid + "&amp;classid=" + this.classid + "&amp;id=" + bookVo.id + "&amp;getTotal=" + bookVo.book_re + "&amp;lpage=" + this.CurrentPage + "&amp;myfriends=" + this.myfriends + "&amp;touserid=" + this.touserid + "\">" + bookVo.book_re + "</a></span> <span class=\"pyq_sc\"><a href=\"" + this.http_start + "rizhi/Share.aspx?action=fav&amp;siteid=" + this.siteid + "&amp;classid=" + this.classid + "&amp;id=" + bookVo.id + "&amp;lpage=" + this.CurrentPage + "\"> </a></span> ");

if (this.IsCheckManagerLvl("|00|01|03|04|", "") || this.userid == bookVo.makerid.ToString())
{
    strhtml.Append("<span class=\"pyq_del\"><a href=\"" + http_start + "rizhi/rizhilist_del.aspx?action=del&amp;siteid=" + this.siteid + "&amp;classid=" + this.classid + "&amp;myfriends=" + this.myfriends + "&amp;id=" + bookVo.id + "&amp;page=" + this.CurrentPage + "\">&nbsp;</a></span>");
}
          
strhtml.Append(" \r\n");
strhtml.Append("     <script type=\"text/javascript\">\r\n");
strhtml.Append("document.getElementById(\"fxmore\").onclick = function(){myfileShow(\"view_fx\")};\r\n");
strhtml.Append("function myfileShow(id){\r\n");
strhtml.Append("	  var oRmb = document.getElementById(id);\r\n");
strhtml.Append("	  if(oRmb.style.display == \"block\"){\r\n");
strhtml.Append("		  oRmb.style.display = \"none\";\r\n");
strhtml.Append("	  }else{\r\n");
strhtml.Append("	  oRmb.style.display = \"block\";\r\n");
strhtml.Append(" }\r\n");
strhtml.Append("}\r\n");
strhtml.Append("</script> \r\n");
strhtml.Append(" </div>");
strhtml.Append(" </div>\r\n");
  
  //下面是原来的
      
  

    //显示分享下载
   
        strhtml.Append("<script type=\"text/javascript\" src=\"/Template/default/js/cookies.js\"></script>");
        strhtml.Append("<script type=\"text/javascript\">\n");
        strhtml.Append("function getgood(messageid,gourl){\n");
        strhtml.Append("var ck='KL_'+messageid;\n");
        strhtml.Append("var s=getCookie(ck);\n");
        strhtml.Append("if(s!=null){showBox('已赞');return;}\n");
        strhtml.Append("myajax('GET',gourl,messageid);\n");
        strhtml.Append("}\n");
        strhtml.Append("function fnTrue(num,messageid){\n");
        strhtml.Append("if(num.length > 0){\n");
        strhtml.Append("  setCookie2('KL_'+messageid, 'wxcs', 's3600');  document.getElementById(messageid).innerHTML = num;  showBox('  +1  ');}else{\n");
        strhtml.Append("showBox('网络问题，提交失败！');}\n");
        strhtml.Append("}\n");
        strhtml.Append("function fnFalse(messageid){showBox('网络问题，提交失败！');}\n");
        strhtml.Append("</script>\n");
    

    

    

   

        strhtml.Append("<div class=\"pj\"><div id=\"comments\"><div class=\"comment-post\" id=\"comment-post\">");
        //strhtml.Append("<h3 style=\"margin-bottom:-20px;margin-top:5px\">");
        //strhtml.Append("发表评论");
        //strhtml.Append("</h3>");
        strhtml.Append("<form name=\"re\" action=\"" + http_start + "rizhi/book_re.aspx\" method=\"post\"> ");
        strhtml.Append("<p><textarea name=\"content\" rows=\"10\" class=\"post-area\" placeholder=\"快来发表你的看法和意见吧!\" style=\"width:99%\"></textarea></p>");


        strhtml.Append("<input type=\"hidden\" name=\"action\" value=\"add\"/>");
        strhtml.Append("<input type=\"hidden\" name=\"id\" value=\"" + id + "\"/>");
        strhtml.Append("<input type=\"hidden\" name=\"siteid\" value=\"" + siteid + "\"/>");
        strhtml.Append("<input type=\"hidden\" name=\"lpage\" value=\"" + lpage + "\"/>");
        strhtml.Append("<input type=\"hidden\" name=\"classid\" value=\"" + classid + "\"/>");
        strhtml.Append("<input type=\"hidden\" name=\"myfriends\" value=\"" + myfriends + "\"/>");
        strhtml.Append("<input type=\"hidden\" name=\"touserid\" value=\"" + this.touserid + "\"/>");
        //strhtml.Append("<input type=\"hidden\" name=\"backurl\" value=\"" + HttpUtility.UrlEncode("rizhi/view.aspx?id=" + id) + "\"/>");
        strhtml.Append("<div class=\"gdpl\">");
        strhtml.Append("<div title=\"评论\" class=\"face\">");
        strhtml.Append("<a href=\"" + http_start + "rizhi/book_re.aspx?pageback=&amp;getTotal=" + bookVo.book_re + "&amp;id=" + id + "&amp;classid=" + classid + "&amp;siteid=" + siteid + "&amp;myfriends=" + this.myfriends + "&amp;touserid=" + touserid + "\">评论(" + bookVo.book_re + ")</a>");
        strhtml.Append("</div>");

        strhtml.Append("<input type=\"submit\" name=\"go\" class=\"open2\" id=\"submit\" value=\"发表评论\"/>");
        strhtml.Append("</div></form></div></div></div>");

        strhtml.Append("<div class=\"pyq_viewre-re\"><ul>");
        for (int i = 0; (relistVo != null && i < relistVo.Count); i++)
        {
            strhtml.Append("<li><img class=\"img\" src=\"" + WapTool.GetHeadImgURL(http_start, relistVo[i].headimg) + "\"><div class=\"name\"><a href=\"" + http_start + "bbs/userinfo.aspx?siteid=" + siteid + "&amp;touserid=" + relistVo[i].userid + "&amp;id=" + relistVo[i].id + "&amp;backurl=" + "\">" + relistVo[i].nickname + "</a></div><p>" + relistVo[i].content + " ");


            if (this.IsCheckManagerLvl("|00|01|03|04|", "") || this.userid == relistVo[i].userid.ToString())
            {
                strhtml.Append(" <span class=\"pyq_del\"><a href=\"" + http_start + "rizhi/rizhilist_delre.aspx?action=del&amp;siteid=" + this.siteid + "&amp;classid=" + this.classid + "&amp;myfriends=" + this.myfriends + "&amp;reid=" + relistVo[i].id + "&amp;id=" + relistVo[i].bookid + "&amp;page=" + this.CurrentPage + "\">&nbsp;</a></span>");
            }
            strhtml.Append("</p></li>");



            k = k + 1;
        }

        strhtml.Append("</ul></div>");


        if (relistVo == null)
        {
            strhtml.Append("<div class=\"tip\">暂无评论！</div>");
        }
        else
        {
            strhtml.Append("<div class=\"btBox\"><div class=\"bt1\"><a href=\"" + http_start + "rizhi/book_re.aspx?pageback=&amp;getTotal=" + bookVo.book_re + "&amp;id=" + id + "&amp;classid=" + classid + "&amp;siteid=" + siteid + "&amp;myfriends=" + this.myfriends + "&amp;touserid=" + touserid + "\">更多评论(" + bookVo.book_re + ")</a></div></div>");
        }


    
    
    //显示广告
    if (adVo.threeShowDown != "")
    {
        strhtml.Append(adVo.threeShowDown);
    }
   
    if (this.userid == bookVo.makerid.ToString())
    {
        //strhtml.Append("<div class=\btBox\"><div class=\"bt2\">");
        //strhtml.Append("<a href=\"" + this.http_start + "rizhi/myrizhi.aspx?siteid="+this.siteid+"&amp;classid=0&amp;touserid="+this.userid+"\">我的博客</a> ");
        //strhtml.Append("<a href=\"" + this.http_start + "rizhi/rizhilist.aspx?siteid="+this.siteid+"&amp;classid=0&amp;touserid="+this.userid+"&amp;smalltypeid=" + bookVo.smalltype + "" + "\">列表管理</a>");
        //strhtml.Append("</div></div>");
    }
    strhtml.Append("<div class=\btBox\"><div class=\"bt2\">");
    if (WapTool.ISAPI_Rewrite3_Open == "1")
    {
        strhtml.Append("<a href=\"" + this.http_start + "rizhilist-" + classid + "-" + this.lpage + ".html?myfriends=" + myfriends + "&touserid=" + this.touserid + "\">返回上级</a> ");
        strhtml.Append("<a href=\"" + this.http_start + "wapindex-" + siteid + "-0.html" + "\">返回首页</a>");

    }
    else
    {
        strhtml.Append("<a href=\"" + this.http_start + "rizhi/book_list.aspx?siteid=" + siteid + "&amp;classid=" + classid + "&amp;page=" + this.lpage + "&amp;myfriends=" + myfriends + "&amp;touserid=" + touserid + "\">返回上级</a> ");
        strhtml.Append("<a href=\"" + this.http_start + "wapindex.aspx?siteid=" + siteid + "&amp;classid=0" + "\">返回首页</a>");
    }
    strhtml.Append("</div></div>");
    //输出错误
    strhtml.Append(ERROR);
    //输出
    Response.Write(WapTool.ToWML(strhtml.ToString(), wmlVo));



Response.Write(WapTool.showDown(wmlVo)); %>
