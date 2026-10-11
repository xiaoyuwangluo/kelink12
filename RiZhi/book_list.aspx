<%@ Page Language="C#" AutoEventWireup="true" CodeBehind="book_list.aspx.cs" Inherits="KeLin.WebSite.rizhi.book_list" %><%@ Import namespace="KeLin.ClassManager.Tool" %><%
Response.Write(WapTool.showTop(classVo.classname, wmlVo));//显示头                                                                                                                                                                       
Response.Write("<link rel=\"stylesheet\" href=\"/rizhi/js/slideView.css\" type=\"text/css\">");

   //默认是微薄显示
    

    if (classVo.siteimg != "UploadFiles/no.gif" && classVo.siteimg != "NetImages/no.gif")
    {
        strhtml.Append("<div class=\"logo\"><img src=\"" + http_start + classVo.siteimg + "\" alt=\"LOGO\"/></div>");
    }
    if (classVo.introduce != "")
    {
        strhtml.Append(classVo.introduce);
    }

    //显示广告
    if (adVo.secondShowTop != "")
    {
        strhtml.Append(adVo.secondShowTop);
    }



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

    
    
    
   
    
    
   
    
    
    //显示列表
    string lpagetemp = "";
    if (this.CurrentPage > 1)
    {
        if (WapTool.ISAPI_Rewrite3_Open == "1")
        {
            lpagetemp = "?lpage=" + CurrentPage;
        }
        else
        {
            lpagetemp = "&amp;lpage=" + CurrentPage;
        }
    }
    strhtml.Append("<!--listS--><div class=\"pyqline\">");

    kk = kk + ((CurrentPage - 1) * pageSize) - 1;
    for (int i = 0; (listVo != null && i < listVo.Count); i++)
    {
        if (i % 2 == 0)
        {
            strhtml.Append("<div class=\"line4\">");
        }
        else
        {
            strhtml.Append("<div class=\"line5\">");
        }

        strhtml.Append("<table><tbody><tr><td valign=\"top\" align=\"center\" style=\"width:50px;\"><a href=\"" + this.http_start + "bbs/userinfo.aspx?siteid=" + this.siteid + "&amp;touserid=" + listVo[i].makerid + "&amp;backurl=\"><div style=\"width:50px;height:50px;border-width: 0px;-moz-border-radius: 25px;-khtml-border-radius: 25px;-webkit-border-radius: 25px;border-radius: 25px;padding:0px; margin-top:5px; background:url(" + WapTool.GetHeadImgURL(http_start, listVo[i].headimg) + "); background-size:100% 100%\"></div></a><p align=\"center\" style=\"font-size:14px\">");
       

        strhtml.Append("</p></td><td style=\"width:100%;padding-left:5px\"><a href=\"" + this.http_start + "bbs/userinfo.aspx?siteid=" + this.siteid + "&amp;touserid=" + listVo[i].makerid + "&amp;backurl=\">" + ShowNickName_color(listVo[i].makerid, listVo[i].book_author) + "</a>");
        if (listVo[i].sex == "1" && listVo[i].isonline == "1")
        {
            strhtml.Append("<img class=\"Sex\" src=\"/Template/default/useinfo/mr.png\" alt=\".\" />");
        }
        else if (listVo[i].sex == "1" && listVo[i].isonline == "0")
        {

            strhtml.Append("<img class=\"Sex\" src=\"/Template/default/useinfo/mr-off.png\" alt=\".\" />");
        }
        else if (listVo[i].sex == "0" && listVo[i].isonline == "1")
        {

            strhtml.Append("<img class=\"Sex\" src=\"/Template/default/useinfo/miss.png\" alt=\".\" />");


        }
        else
        {

            strhtml.Append("<img class=\"Sex\" src=\"/Template/default/useinfo/miss-off.png\" alt=\".\" />");

        }

        strhtml.Append("<span class=\"right\">&nbsp;&nbsp;");
        //时间
        strhtml.Append( WapTool.ShowTime(listVo[i].book_date));

        
        strhtml.Append("</span><br/>");

        string[] arry = listVo[i].book_img.Split('|');
        string[] arryfile = listVo[i].book_file.Split('|');

        if (listVo[i].book_type == 1)
        {
            if (listVo[i].book_content.StartsWith("http:"))
            {
                strhtml.Append("<span style=\"font-size: 12px; color: #999999;\">分享了一个链接</span><br/>");
            }
            else
            {
                strhtml.Append("<span style=\"font-size: 12px; color: #999999;\">分享了一个日志</span><br/>");
            }
            string smallimg = "/Template/default/img/pyq_urlimg.gif";
            if (arry[0] != "")
            {
                smallimg ="/rizhi/"+ arry[0];
            }
            strhtml.Append("<span class=\"pyq_url\"><a href=\"/rizhi/view.aspx?id=" + listVo[i].id + "&amp;classid=" + this.classid + "&amp;lpage=" + this.CurrentPage + "&amp;myfriends=" + this.myfriends + "\"><img src=\"" + smallimg + "\">" + listVo[i].book_title.Replace("［br］", "") + "</a></span>");

        }
        else
        {




            strhtml.Append("<span onclick=\"window.location.href='/rizhi/view.aspx?id=" + listVo[i].id + "&amp;classid=" + this.classid + "&amp;lpage=" + this.CurrentPage + "&amp;myfriends=" + this.myfriends + "';\">" + WapTool.ReplaceChinaseToImage(listVo[i].book_title.Replace("［br］", "")) + "</span><br/>");
            if (listVo[i].book_img != "")
            {
                if (arry.Length == 1 || (arry.Length == 2 && arry[1] == ""))
                {
                    if (arry[0].StartsWith("upload")) arry[0] = "/rizhi/" + arry[0];
                    if (arryfile[0].StartsWith("upload")) arryfile[0] = "/rizhi/" + arryfile[0];

                    strhtml.Append("<div id=\"slideViewId-" + listVo[i].id + "\"><ul>");
                    strhtml.Append("<li><a href=\"javascript:;\"><img onclick=\"slideView(this)\" _src=\"" + arryfile[0] + "\" src=\"" + arry[0] + "\" alt=\"图\" max-width=\"300px\" max-height=\"500px\"/>");

                    strhtml.Append("</a></li></ul></div>");
               
                }
                else
                {
                    strhtml.Append("<div class=\"pyq_img\" id=\"slideViewId-"+listVo[i].id+"\"><ul>");


                    for (int y = 0; y < arry.Length; y++)
                    {

                        if (arry[y] != "")
                        {
                            if (arry[y].StartsWith("upload")) arry[y] = "/rizhi/" + arry[y];
                            if (arryfile[y].StartsWith("upload")) arryfile[y] = "/rizhi/" + arryfile[y];
                            strhtml.Append("<li><a href=\"javascript:;\"><img onclick=\"slideView(this)\" _src=\"" + arryfile[y] + "\" src=\"" + arry[y] + "\" alt=\"图\" /></a></li>");
                        }
                    }
                    strhtml.Append("</ul></div>");

                }
            }
        }

        strhtml.Append("<br/>");
        
      
        
        
        strhtml.Append("<div class=\"pyq\">");

        
        
        strhtml.Append("<span class=\"pyq_zan\">");
        strhtml.Append("<a id=\"riz" + listVo[i].id + "\" href=\"javascript:getgood('riz" + listVo[i].id + "','/rizhi/ajax.aspx?type=good&id=" + listVo[i].id + "')\">" + listVo[i].book_good + "</a>");
        strhtml.Append("</span>");
        
      
       
        
        strhtml.Append("<span class=\"pyq_re\">");
        strhtml.Append(" <a href=\"" + this.http_start + "rizhi/book_re.aspx?actoin=class&amp;siteid=" + this.siteid + "&amp;classid=" + listVo[i].book_classid + "&amp;id=" + listVo[i].id + "&amp;getTotal=" + listVo[i].book_re + "&amp;lpage=" + this.CurrentPage + "&amp;myfriends="+this.myfriends+"&amp;touserid="+this.touserid+"\">" + listVo[i].book_re + "</a>");

        strhtml.Append("</span>");


        strhtml.Append("<span class=\"pyq_sc\"><a href=\"" + this.http_start + "rizhi/Share.aspx?action=fav&amp;siteid=" + this.siteid + "&amp;classid=" + this.classid + "&amp;id=" + listVo[i].id + "&amp;lpage=" + this.CurrentPage + "\"> </a></span>");
        if (this.IsCheckManagerLvl("|00|01|03|04|", "") || this.userid == listVo[i].makerid.ToString())
        {
            strhtml.Append("<span class=\"pyq_del\"><a href=\"" + http_start + "rizhi/rizhilist_del.aspx?action=del&amp;siteid=" + this.siteid + "&amp;classid="+this.classid+"&amp;myfriends=" + this.myfriends + "&amp;id=" + listVo[i].id + "&amp;page=" + this.CurrentPage +   "\">&nbsp;</a></span>");
        }
           
        strhtml.Append("</div>");
        
        
        

        
        
        
      

        
        strhtml.Append("</div>");


        

           
        strhtml.Append("</p></td></tr></tbody></table>");


        kk = kk + 1;

        strhtml.Append("</div>");
    }



    if (listVo == null)
    {
        strhtml.Append("<div class=\"tip\">");
        if (this.userid == "0")
        {
            strhtml.Append("还没有登录，请先登录哦！");
        }
        else
        {
            strhtml.Append("还没有记录，快去发表吧！");
        }
        strhtml.Append("</div>");
    }

    strhtml.Append("</div><!--listE-->");
    
    //显示导航分页
    strhtml.Append(linkURL);



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

    //显示广告
    if (adVo.secondShowDown != "")
    {
        strhtml.Append(adVo.secondShowDown );
    }
    //导航按钮
    
    if (action == "search")
    {
        //strhtml.Append("<div class=\"btBox\"><div class=\"bt1\">");
        //strhtml.Append("<a href=\"" + this.http_start + "rizhi/book_search.aspx?siteid=" + siteid + "&amp;classid=" + classid + "" + "\">返回搜索</a> ");
        //strhtml.Append("</div></div>");
    }
    strhtml.Append("<div class=\"btBox\"><div class=\"bt2\">");
    if (WapTool.ISAPI_Rewrite3_Open == "1")
    {
        strhtml.Append("<a href=\"" + this.http_start + "wapindex-" + siteid + "-" + classVo.childid + ".html" + "\">返回上级</a> ");
        strhtml.Append("<a href=\"" + this.http_start + "wapindex-" + siteid + "-0.html" + "\">返回首页</a>");

    }
    else
    {
        strhtml.Append("<a href=\"" + this.http_start + "wapindex.aspx?siteid=" + siteid + "&amp;classid=" + classVo.childid + "" + "\">返回上级</a> ");
        strhtml.Append("<a href=\"" + this.http_start + "wapindex.aspx?siteid=" + siteid + "&amp;classid=0" + "\">返回首页</a>");
    }

    strhtml.Append("</div></div>");
    if (classVo.sitedowntip != "")
    {
        strhtml.Append( classVo.sitedowntip);
    }
    Response.Write("<script type=\"text/javascript\" src=\"/rizhi/js/slideView.js\"></script>");

    Response.Write(ERROR);  
    //输出
    Response.Write(WapTool.ToWML(strhtml.ToString(), wmlVo));
                                                                                                                                                                           





                                                                                                                                                                               
                                                                                                                                                                            
//显示底部
Response.Write(WapTool.showDown(wmlVo)); %>
