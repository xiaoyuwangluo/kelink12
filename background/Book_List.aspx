<%@ Page Language="C#" AutoEventWireup="true" CodeBehind="Book_List.aspx.cs" Inherits="KeLin.WebSite.background.Book_List" %><%@ Import namespace="KeLin.ClassManager.Tool" %><%
Response.Write(WapTool.showTop("购买空间背景", wmlVo));//显示头                                                                                                                                                                       
    

    if (classVo.siteimg != "UploadFiles/no.gif" && classVo.siteimg != "NetImages/no.gif")
    {
        strhtml.Append("<div class=\"logo\"><img src=\"" + http_start + classVo.siteimg + "\" alt=\"LOGO\"/></div>");
    }
    if (classVo.introduce != "")
    {
        strhtml.Append(classVo.introduce);
    }

    
    strhtml.Append("<!--web-->");
    /*如果是管理员显示管理按钮*/
    if (this.IsCheckManagerLvl("|00|01|", "") == true)
    {
        strhtml.Append("<div class=\"subtitle\">站长管理</div>");
        strhtml.Append("<div class=\"content\">[<a href=\"" + http_start + "background/book_view_add.aspx?siteid=" + siteid + "&amp;classid=" + this.classid + "" + "\">上传背景</a>/<a href=\"" + http_start + "background/book_view_daoru.aspx?siteid=" + siteid + "&amp;classid=" + this.classid + "" + "\">导入系统背景</a>]</div>");
   
    }                                                                                                                                                                        
                                                                                                                                                                                  
    strhtml.Append("<div class=\"subtitle\">我的当前空间背景</div>");
    strhtml.Append("<a href=\"/bbs/userinfo.aspx?siteid=" + this.siteid + "&classid=" + this.classid + "&touserid=" + this.userid + "\"><div class=\"userinfobg\"  style =\"background-image:url("+ WapTool.GetBGimage(siteid,userid)+");\"></div></a>");

    strhtml.Append("<div class=\"btBox\"><div class=\"bt4\">");
    strhtml.Append("<a");
    if (this.ordertype == "1")
    {
        strhtml.Append(" class=\"btSelect\"");
    }
    strhtml.Append(" href=\"" + this.http_start + "background/book_list.aspx?action=class&amp;siteid=" + this.siteid + "&amp;classid=" + this.classid + "&amp;ordertype=1\">按最新</a>");


    strhtml.Append("<a");
    if (this.ordertype == "2")
    {
        strhtml.Append(" class=\"btSelect\"");
    }
    strhtml.Append(" href=\"" + this.http_start + "background/book_list.aspx?action=class&amp;siteid=" + this.siteid + "&amp;classid=" + this.classid + "&amp;ordertype=2\">按时间</a>");


    strhtml.Append("<a");

    if (this.ordertype == "3")
    {
        strhtml.Append(" class=\"btSelect\"");
    }
    strhtml.Append(" href=\"" + this.http_start + "background/book_list.aspx?action=class&amp;siteid=" + this.siteid + "&amp;classid=" + this.classid + "&amp;ordertype=3\">按价高</a>");

    strhtml.Append("<a");
    if (this.ordertype == "4")
    {
        strhtml.Append(" class=\"btSelect\"");

    }
    strhtml.Append(" href=\"" + this.http_start + "background/book_list.aspx?action=class&amp;siteid=" + this.siteid + "&amp;classid=" + this.classid + "&amp;ordertype=4\">按价低</a>");
 
    strhtml.Append("</div></div>");
    //显示列表
    //strhtml.Append(linkTOP);

    strhtml.Append("<!--listS-->");
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
        index = index + kk;
        strhtml.Append(index + "." + listVo[i].bgname + " ");
        if (this.IsCheckManagerLvl("|00|01|", "") == true)
        {
            strhtml.Append("[<a href=\"" + http_start + "background/book_view_mod.aspx?siteid=" + siteid + "&amp;classid=" + this.classid + "&amp;id=" + listVo[i].id + "&amp;lpage=" + CurrentPage + "&amp;ordertype=" + this.ordertype + "" + "\">修改</a>/<a href=\"" + http_start + "background/book_view_del.aspx?siteid=" + siteid + "&amp;classid=" + this.classid + "&amp;id=" + listVo[i].id + "&amp;lpage=" + CurrentPage + "&amp;ordertype=" + this.ordertype + "" + "\">删除</a>] ");
        }
        strhtml.Append("<span class=\"right\">价格:<font color=\"red\"><b>" + listVo[i].needmoney + "</b></font>" + siteVo.sitemoneyname);

        strhtml.Append(" <a class=\"urlbtn\" href=\"" + http_start + "background/book_view_buy.aspx?siteid=" + siteid + "&amp;classid=" + this.classid + "&amp;id=" + listVo[i].id + "&amp;lpage=" + CurrentPage + "&amp;ordertype=" + this.ordertype + "" + "\">购买</a> </span>");

        strhtml.Append("<a class=\"urlbtn\" href=\"" + http_start + "background/book_view_buy.aspx?siteid=" + siteid + "&amp;classid=" + this.classid + "&amp;id=" + listVo[i].id + "&amp;lpage=" + CurrentPage + "&amp;ordertype=" + this.ordertype + "" + "\"><div class=\"userinfobg\"  style =\"background-image:url(/" + listVo[i].bgimage + ");\">");
      
        
        strhtml.Append("</div></a>");
        strhtml.Append("</div>");
    }

    if (listVo==null)
    {
        strhtml.Append("<div class=\"tip\">暂无记录！</div>");
    }
    strhtml.Append("<!--listE-->");
    //显示导航分页
    strhtml.Append(linkURL);

    //显示电脑版
    string isWebHtml = this.ShowWEB_list(this.classid); //看是存在html代码    
    if (isWebHtml != "")
    {
        string strhtml_list = strhtml.ToString();
        int s = strhtml_list.IndexOf("<!--web-->");
        strhtml_list = strhtml_list.Substring(s, strhtml_list.Length - s);

        Response.Clear();
        Response.Write(WapTool.ToWML(isWebHtml.Replace("[view]", strhtml_list), wmlVo));
        Response.End();
    }

    
    //导航按钮
    strhtml.Append("<div class=\"btBox\"><div class=\"bt3\">");     
    strhtml.Append("<a href=\"" + this.http_start + "myfile.aspx?siteid=" + siteid + "&amp;classid=" + classVo.childid + "" + "\">返回地盘</a> ");
    strhtml.Append("<a href=\"" + this.http_start + "bbs/userinfo.aspx?siteid=" + siteid + "&amp;touserid=" + this.userid + "" + "\">返回空间</a> ");
    strhtml.Append("<a href=\"" + this.http_start + "wapindex.aspx?siteid=" + siteid + "&amp;classid=0" + "\">返回首页</a>");
    strhtml.Append("</div></div>");
                                                                                                                                                                                  
    if (classVo.sitedowntip != "")
    {
        strhtml.Append(classVo.sitedowntip);
    }
    //输出
    Response.Write(WapTool.ToWML(strhtml.ToString(), wmlVo));
                                                                                                                                                                           





                                                                                                                                                                               
Response.Write(ERROR);                                                                                                                                                                              
//显示底部
Response.Write(WapTool.showDown(wmlVo)); %>
