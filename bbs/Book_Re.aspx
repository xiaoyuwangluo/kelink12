<%@ Page Language="C#" AutoEventWireup="true" CodeBehind="Book_Re.aspx.cs" Inherits="KeLin.WebSite.bbs.Book_Re" %><%@ Import namespace="KeLin.ClassManager.Tool" %><%
if(this.INFO == "OK"){
    if ("1".Equals(WapTool.getArryString(classVo.smallimg, '|', 1)))
    {
        wmlVo.timer = "3";
        wmlVo.strUrl = "bbs/book_view.aspx?siteid=" + siteid + "&amp;classid=" + classid + "&amp;id=" + id + "&amp;lpage=" + lpage;
    }
    else
    {
        wmlVo.timer = "3";
        wmlVo.strUrl = "bbs/book_re.aspx?siteid=" + siteid + "&amp;classid=" + classid + "&amp;id=" + id + "&amp;lpage=" + lpage;

    }
}
Response.Write(WapTool.showTop(this.GetLang("查看回复|查看回複|View Reply"), wmlVo));//显示头                                                                                                                                                                       
if (ver == "1")
{
    strhtml.Append("<p align=\"" + classVo.position + "\">");
    strhtml.Append(ERROR);
    if (this.INFO == "OK")
    {
        
        strhtml.Append("<b>回复成功！</b> ");
        if (siteVo.isCheck == 1)
        {
            strhtml.Append("<b>审核后显示！</b> ");
        }
        strhtml.Append("获得" + WapTool.GetSiteMoneyName(siteVo.sitemoneyname, this.lang) + ":" + allMoney + ",经验:" + getexpr + "");

        if (this.getmoney_percent != 0)
        {
            strhtml.Append("，" + WapTool.GetMyID(userVo.idname, lang, userVo.endTime) + "身份，额外获得" + WapTool.GetSiteMoneyName(siteVo.sitemoneyname, this.lang) + ":" + getmoney_percent + "");
        }
        if (this.getexpr_percent != 0)
        {
            strhtml.Append("，");
            if (this.getmoney_percent == 0) strhtml.Append(WapTool.GetMyID(userVo.idname, lang, userVo.endTime) + "身份，额外");
            strhtml.Append("获得经验:" + getexpr_percent);

        }
        strhtml.Append("。<br/>跳转中...<a href=\"" + this.http_start + wmlVo.strUrl + "" + "\">返回</a><br/>");
    }
    else if (this.INFO == "NULL")
    {
        strhtml.Append("<b>回复内容最少" + contentmax + "字！</b><br/>");

    }
    else if (this.INFO == "TITLEMAX")
    {       
       
            strhtml.Append("<b>内容最大" + this.content_max + "字。</b><br/>");
       
    }
    else if (this.INFO == "ERR_FORMAT")
    {
        strhtml.Append("<b>取到非法值:“$$(”请更换手机浏览器或重新编辑！</b><br/>");
    }
    else if (this.INFO == "ERROR_Secret")
    {

        strhtml.Append("<b>暗号错误，如果忘记联系站长索取！</b><br/>");
    }
    else if (this.INFO == "REPEAT")
    {

        strhtml.Append("<b>请不要发重复内容！</b><br/>");
    }
    else if (this.INFO == "WAITING")
    {
        strhtml.Append("<b>请再过" + this.KL_CheckIPTime + "秒后操作！</b><br/>");

    }
    else if (this.INFO == "MAX")
    {

        strhtml.Append("<b>今天你已超过回帖限制：" + this.KL_CheckBBSreCount + " 个回帖子，请明天再来！</b><br/>");
    }
    else if (this.INFO == "LOCK")
    {

        strhtml.Append("<b>抱歉，您已经被加入黑名单，请注意发帖规则！</b><br/>");
    }
    else if (this.INFO == "NOMONEY")
    {

        strhtml.Append("<b>你当前的只有:" + userVo.money + "个，发帖需要扣掉：" + getmoney2 + "个</b><br/>");
    }
    if (this.INFO == "")
    {
        //顶部链接
        strhtml.Append("<a href=\"" + this.http_start + "bbs/book_re.aspx?action=class&amp;id=" + this.id + "&amp;siteid=" + this.siteid + "&amp;classid=" + this.classid + "&amp;page=" + this.CurrentPage + "&amp;lpage=" + this.lpage + "&amp;ot=" + this.ot + "&amp;go=" + this.r + "\">刷新</a>.");
        if (this.ot == "1")
        {
            strhtml.Append("<a href=\"" + this.http_start + "bbs/book_re.aspx?action=class&amp;id=" + this.id + "&amp;siteid=" + this.siteid + "&amp;classid=" + this.classid + "&amp;page=" + this.CurrentPage + "&amp;lpage=" + this.lpage + "&amp;ot=0&amp;go=" + this.r + "\">按最新回复</a>.");
        }
        else
        {
            strhtml.Append("<a href=\"" + this.http_start + "bbs/book_re.aspx?action=class&amp;id=" + this.id + "&amp;siteid=" + this.siteid + "&amp;classid=" + this.classid + "&amp;page=" + this.CurrentPage + "&amp;lpage=" + this.lpage + "&amp;ot=1&amp;go=" + this.r + "\">按最早回复</a>.");
            
        }
        strhtml.Append("<a href=\"" + this.http_start + "bbs/book_re.aspx?action=class&amp;id=" + this.id + "&amp;siteid=" + this.siteid + "&amp;classid=" + this.classid + "&amp;page=" + this.CurrentPage + "&amp;lpage=" + this.lpage + "&amp;ot=" + this.ot + "&amp;mainuserid=" + this.mainuserid + "&amp;go=" + this.r + "\">楼主回复</a> ");
       
        strhtml.Append("<br/>");
        strhtml.Append("<a href=\"" + this.http_start + "bbs/book_view.aspx?siteid=" + siteid + "&amp;classid=" + classid + "&amp;id=" + id + "&amp;lpage=" + lpage + "" + "\">返回主题</a>.");
        strhtml.Append("<a href=\"" + this.http_start + "bbs/book_list.aspx?action=class&amp;siteid=" + siteid + "&amp;classid=" + classid + "&amp;page=" + lpage + "" + "\">返回列表</a>");
        strhtml.Append("<br/>");
        if (bookVo.islock == 0)
        {
            if (this.reply != "")
            {
                strhtml.Append("<b>回复" + this.reply + "楼:</b><br/>");
                strhtml.Append("<select name=\"sendmsg2"+r+"\">");
                strhtml.Append("<option value=\"\">通知" + this.reply + "楼？</option>");
                strhtml.Append("<option value=\"0\">否</option>");
                strhtml.Append("<option value=\"1\">是</option>");
                strhtml.Append("</select><br/>");
            }
            //显示输入框
            strhtml.Append("<select name=\"face\">");
            strhtml.Append("<option value=\"\">表情</option>");
            for (int i = 0; (facelistImg != null && i < this.facelistImg.Length); i++)
            {
                strhtml.Append("<option value=\"" + this.facelistImg[i] + "\">" + this.facelist[i] + "</option>");
            }
            strhtml.Append("</select>");
            strhtml.Append("<select name=\"sendmsg" + r + "\" value=\"\">");
            strhtml.Append("<option value=\"0\">通知楼主？</option>");
            strhtml.Append("<option value=\"0\">否</option>");
            strhtml.Append("<option value=\"1\">是</option>");
            strhtml.Append("</select><br/>");
            strhtml.Append("<input type=\"text\" name=\"con" + r + "tent\" value=\"" + reShowInfo + "\" maxlength=\"500\"/><br/>");
            if (this.isNeedSecret == true)
            {
                strhtml.Append("本版暗号*:<input type=\"text\" name=\"secret\" value=\"\" size=\"10\" /><br/>");

            }
            strhtml.Append("<anchor><go href=\"" + http_start + "bbs/book_re.aspx\" method=\"post\" accept-charset=\"utf-8\">");
            strhtml.Append("<postfield name=\"action\" value=\"add\"/>");
            strhtml.Append("<postfield name=\"id\" value=\"" + id + "\"/>");
            strhtml.Append("<postfield name=\"siteid\" value=\"" + siteid + "\"/>");
            strhtml.Append("<postfield name=\"lpage\" value=\"" + lpage + "\"/>");
            strhtml.Append("<postfield name=\"content\" value=\"$(con" + r + "tent)\"/>");
            strhtml.Append("<postfield name=\"sendmsg\" value=\"$(sendmsg" + r + ")\"/>");
            strhtml.Append("<postfield name=\"sendmsg2\" value=\"$(sendmsg2" + r + ")\"/>");
            strhtml.Append("<postfield name=\"face\" value=\"$(face)\"/>");
            strhtml.Append("<postfield name=\"classid\" value=\"" + classid + "\"/>");
            strhtml.Append("<postfield name=\"reply\" value=\"" + reply + "\"/>");
            strhtml.Append("<postfield name=\"replyid\" value=\"" + replyid + "\"/>");
            if (this.isNeedSecret == true)
            {
                strhtml.Append("<postfield name=\"secret\" value=\"$(secret)\"/>");
            }
            strhtml.Append("<postfield name=\"touserid\" value=\"" + this.GetRequestValue("touserid") + "\"/>");
            strhtml.Append("<postfield name=\"sid\" value=\"" + sid + "\"/>");
            //strhtml.Append("<postfield name=\"backurl\" value=\"" + HttpUtility.UrlEncode("bbs/book_re.aspx?action=class&siteid=" + siteid + "&classid=" + classid + "&id=" + id) + "\"/>");
            strhtml.Append("</go>发表回复</anchor> <a href=\"" + this.http_start + "bbs/book_re_addfile.aspx?action=class&amp;siteid=" + this.siteid + "&amp;classid=" + this.classid + "&amp;id=" + this.id + "&amp;lpage=" + this.lpage + "\">" + this.GetLang("文件回帖|文件回帖|upload file") + "</a>");
            strhtml.Append("<br/>");
        }

        

        //显示列表

        kk = kk + ((CurrentPage - 1) * pageSize) - 1;
        for (int i = 0; (listVo != null && i < listVo.Count); i++)
        {
            if (ot == "1")
            {
                index = (kk + 1);
            }
            else
            {
                index = (total - kk);
            }

            if (listVo[i].book_top == 1 && this.CurrentPage == 1)
            {
                strhtml.Append("[顶楼]");
            }
            else
            {
                if (index == 1)
                {
                    strhtml.Append("[沙发]");
                }
                else if (index == 2)
                {
                    strhtml.Append("[椅子]");
                }
                else if (index == 3)
                {
                    strhtml.Append("[板凳]");
                }
                else
                {
                    strhtml.Append("[" + index + "楼]");
                }
            }

            if (this.userid == bookVo.book_pub && bookVo.sendMoney > 0 && listVo[i].myGetMoney == 0)
            {
                strhtml.Append("[<a href=\"" + this.http_start + "bbs/SendMoney.aspx?action=sendmoney&amp;classid=" + classid + "&amp;id=" + id + "&amp;reid=" + listVo[i].id + "&amp;siteid=" + this.siteid + "\">赏分</a>]");
            }

            if (listVo[i].myGetMoney > 0)
            {
                strhtml.Append("[<b>得金:" + listVo[i].myGetMoney + "</b>]");
            }

            //--------------------管理员
            if (this.IsCheckManagerLvl("|00|01|03|04|", classVo.adminusername))
            {
                strhtml.Append("[<a href=\"" + this.http_start + "bbs/SendMoney_free.aspx?action=sendmoney&amp;classid=" + classid + "&amp;id=" + id + "&amp;reid=" + listVo[i].id + "&amp;touserid=" + listVo[i].userid + "&amp;siteid=" + this.siteid + "\">送</a>]");
                strhtml.Append("[<a href=\"" + this.http_start + "bbs/Book_re_del.aspx?action=go&amp;siteid=" + this.siteid + "&amp;classid=" + this.classid + "&amp;lpage=" + this.lpage + "&amp;page=" + this.CurrentPage + "&amp;reid=" + listVo[i].id + "&amp;id=" + this.id + "&amp;ot=" + this.ot + "\">删</a>]");
                strhtml.Append("[<a href=\"" + this.http_start + "bbs/Book_re_mod.aspx?action=go&amp;siteid=" + this.siteid + "&amp;classid=" + this.classid + "&amp;lpage=" + this.lpage + "&amp;page=" + this.CurrentPage + "&amp;reid=" + listVo[i].id + "&amp;id=" + this.id + "&amp;ot=" + this.ot + "\">审</a>]");

                if (listVo[i].book_top == 1)
                {
                    strhtml.Append("[<a href=\"" + this.http_start + "bbs/Book_re_top.aspx?action=go&amp;tops=0&amp;siteid=" + this.siteid + "&amp;classid=" + this.classid + "&amp;lpage=" + this.lpage + "&amp;page=" + this.CurrentPage + "&amp;reid=" + listVo[i].id + "&amp;id=" + this.id + "&amp;ot=" + this.ot + "\">消顶</a>]");
                }
                else
                {
                    strhtml.Append("[<a href=\"" + this.http_start + "bbs/Book_re_top.aspx?action=go&amp;tops=1&amp;siteid=" + this.siteid + "&amp;classid=" + this.classid + "&amp;lpage=" + this.lpage + "&amp;page=" + this.CurrentPage + "&amp;reid=" + listVo[i].id + "&amp;id=" + this.id + "&amp;ot=" + this.ot + "\">顶</a>]");
                }
            }
            else if (this.userid == listVo[i].userid.ToString())  //自己删除自己的帖子
            {
                strhtml.Append("[<a href=\"" + this.http_start + "bbs/Book_re_del.aspx?action=go&amp;siteid=" + this.siteid + "&amp;classid=" + this.classid + "&amp;lpage=" + this.lpage + "&amp;page=" + this.CurrentPage + "&amp;reid=" + listVo[i].id + "&amp;id=" + this.id + "&amp;ot=" + this.ot + "\">删</a>]");
            }
            strhtml.Append("[<a href=\"" + this.http_start + "bbs/Book_re.aspx?siteid=" + this.siteid + "&amp;classid=" + this.classid + "&amp;lpage=" + this.lpage + "&amp;page=" + this.CurrentPage + "&amp;reply=" + index + "&amp;id=" + this.id + "&amp;touserid=" + listVo[i].userid + "&amp;ot=" + this.ot + "\">回</a>]");
            //----------------------
            if (listVo[i].reply != 0)
            {
                strhtml.Append("回复" + listVo[i].reply + "楼:");
            }
            strhtml.Append(listVo[i].content);
            if (listVo[i].isdown > 0)
            {
                strhtml.Append("{<a href=\"" + this.http_start + "bbs/book_re_addfileshow.aspx?siteid=" + this.siteid + "&amp;classid=" + this.classid + "&amp;id=" + this.id + "&amp;reid=" + listVo[i].id + "&amp;lpage=" + this.lpage + "\">查看" + listVo[i].isdown + "个附件</a>}");
            }

            strhtml.Append("<br/><a href=\"" + this.http_start + "bbs/userinfo.aspx?siteid=" + siteid + "&amp;touserid=" + listVo[i].userid + "&amp;backurl=" + "\">" + ShowNickName_color(listVo[i].userid, listVo[i].nickname) + "(" + listVo[i].userid + ")</a> " + string.Format("{0:MM-dd HH:mm}", listVo[i].redate) + "<br/>");
            kk = kk + 1;
        }
        if (listVo == null)
        {
            strhtml.Append("暂无回复记录！<br/>");
        }
        //显示导航分页
        strhtml.Append(linkURL);
    }
    strhtml.Append("<a href=\"" + this.http_start + "bbs/book_view.aspx?siteid=" + siteid + "&amp;classid=" + classid + "&amp;id="+id+"&amp;lpage="+lpage+"" + "\">返回主题</a> ");
    strhtml.Append("<a href=\"" + this.http_start + "bbs/book_list.aspx?action=class&amp;siteid=" + siteid + "&amp;classid=" + classid + "&amp;page="+lpage+"" + "\">返回列表</a> ");
    strhtml.Append(WapTool.GetVS(wmlVo));
    strhtml.Append("</p>");

    //输出
    Response.Write(WapTool.ToWML(strhtml.ToString(), wmlVo));
    
}
else //2.0界面
{
    string isWebHtml = this.ShowWEB_view(this.classid); //看是存在html代码
    strhtml.Append("<!--web-->");
    if (ERROR != "")
    {
        strhtml.Append("<!--listX-->" + ERROR + "<!--listY-->");
    }
    if (this.INFO == "OK")
    {
        strhtml.Append("<div class=\"tip\">");
        strhtml.Append("<!--listX--><b>回复成功！</b> ");
        if (siteVo.isCheck == 1)
        {
            strhtml.Append("<b>审核后显示！</b> ");
        }
        strhtml.Append("获得" + WapTool.GetSiteMoneyName(siteVo.sitemoneyname, this.lang) + ":" + allMoney + ",经验:" + getexpr + "");
       if (this.getmoney_percent != 0)
        {
            strhtml.Append("，" + WapTool.GetMyID(userVo.idname, lang, userVo.endTime) + "身份，额外获得" + WapTool.GetSiteMoneyName(siteVo.sitemoneyname, this.lang) + ":" + getmoney_percent + "");
        }
        if (this.getexpr_percent != 0)
        {
            strhtml.Append("，");
            if (this.getmoney_percent == 0) strhtml.Append(WapTool.GetMyID(userVo.idname, lang, userVo.endTime) + "身份，额外");
            strhtml.Append("获得经验:" + getexpr_percent);

        }
        strhtml.Append("。");
        strhtml.Append("<!--listY--><br/>跳转中...<a href=\"" + this.http_start + wmlVo.strUrl + "" + "\">返回</a><br/>");
        strhtml.Append("</div>");
        strhtml.Append("<script type='text/javascript'> setInterval(function(){ window.location.href='" + this.http_start + wmlVo.strUrl.Replace("&amp;","&") + "';},4000); </script>");

    }
    else if (this.INFO == "NULL")
    {
        strhtml.Append("<div class=\"tip\">");
        strhtml.Append("<!--listX--><b>回复内容最少" + contentmax + "字！</b><!--listY--><br/>");
        strhtml.Append("</div>");

    }
    else if (this.INFO == "TITLEMAX")
    {
        strhtml.Append("<div class=\"tip\">");
        strhtml.Append("<!--listX--><b>内容最大" + this.content_max + "字。</b><!--listY--></div>");

    }
    else if (this.INFO == "ERR_FORMAT")
    {
        strhtml.Append("<div class=\"tip\">");
        strhtml.Append("<!--listX--><b>取到非法值:“$$(”请更换手机浏览器或重新编辑！</b><!--listY--><br/>");
        strhtml.Append("</div>");
    }
    else if (this.INFO == "REPEAT")
    {
        strhtml.Append("<div class=\"tip\">");
        strhtml.Append("<!--listX--><b>请不要发重复内容！</b><!--listY--><br/>");
        strhtml.Append("</div>");
    }
    else if (this.INFO == "ERROR_Secret")
    {

        strhtml.Append("<div class=\"tip\"><!--listX--><b>暗号错误，如果忘记联系站长索取！</b><!--listY--><br/></div>");
    }
    else if (this.INFO == "WAITING")
    {
        strhtml.Append("<div class=\"tip\">");
        strhtml.Append("<!--listX--><b>请再过" + this.KL_CheckIPTime + "秒后操作！</b><!--listY--><br/>");
        strhtml.Append("</div>");
    }
    else if (this.INFO == "MAX")
    {
        strhtml.Append("<div class=\"tip\">");
        strhtml.Append("<!--listX--><b>今天你已超过回帖限制：" + this.KL_CheckBBSreCount + " 个回帖了，请明天再来！</b><!--listY--><br/>");
        strhtml.Append("</div>");
    }
    else if (this.INFO == "LOCK")
    {
        strhtml.Append("<div class=\"tip\">");
        strhtml.Append("<!--listX--><b>抱歉，您已经被加入黑名单，请注意发帖规则！</b><!--listY--><br/>");
        strhtml.Append("</div>");
    }
    else if (this.INFO == "NOMONEY")
    {

        strhtml.Append("<div class=\"tip\"><!--listX--><b>你当前的只有:" + userVo.money + "个，发帖需要扣掉：" + getmoney2 + "个</b><!--listY--></div>");
    }

    if ( this.INFO == ""  ||  this.showAjax == "1" )
    {
        //顶部链接
        strhtml.Append("<div class=\"btBox\"><div class=\"bt4\">");
        if (this.ot == "1")
        {
            strhtml.Append("<a href=\"" + this.http_start + "bbs/book_re.aspx?action=class&amp;id=" + this.id + "&amp;siteid=" + this.siteid + "&amp;classid=" + this.classid + "&amp;page=" + this.CurrentPage + "&amp;lpage=" + this.lpage + "&amp;ot=0&amp;go=" + this.r + "\">按最新回复</a> ");
        }
        else
        {
            strhtml.Append("<a href=\"" + this.http_start + "bbs/book_re.aspx?action=class&amp;id=" + this.id + "&amp;siteid=" + this.siteid + "&amp;classid=" + this.classid + "&amp;page=" + this.CurrentPage + "&amp;lpage=" + this.lpage + "&amp;ot=1&amp;go=" + this.r + "\">按最早回复</a> ");
        }
        strhtml.Append("<a href=\"" + this.http_start + "bbs/book_re.aspx?action=class&amp;id=" + this.id + "&amp;siteid=" + this.siteid + "&amp;classid=" + this.classid + "&amp;page=" + this.CurrentPage + "&amp;lpage=" + this.lpage + "&amp;ot=" + this.ot + "&amp;mainuserid=" + this.mainuserid + "&amp;go=" + this.r + "\">楼主回复</a> ");


        strhtml.Append("<a href=\"" + this.http_start + "bbs/book_view.aspx?siteid=" + siteid + "&amp;classid=" + classid + "&amp;id=" + id + "&amp;lpage=" + lpage + "" + "\">返回主题</a> ");
        strhtml.Append("<a href=\"" + this.http_start + "bbs/book_re.aspx?action=class&amp;id=" + this.id + "&amp;siteid=" + this.siteid + "&amp;classid=" + this.classid + "&amp;page=" + this.CurrentPage + "&amp;lpage=" + this.lpage + "&amp;ot=" + this.ot + "&amp;go=" + this.r + "\">刷新</a> ");

        strhtml.Append("</div>");
        strhtml.Append("</div>");
        if (bookVo.islock == 0)
        {
            //显示最新回复
            strhtml.Append("<div class=\"pj\">");
            strhtml.Append("<div id=\"comments\">");
            strhtml.Append("<div class=\"comment-post\" id=\"comment-post\">");
            strhtml.Append("<h3 style=\"margin-bottom:-20px; padding-top:5px;\">发表回复</h3>");

            strhtml.Append("<form name=\"f\" action=\"" + http_start + "bbs/book_re.aspx" + "\" method=\"post\">");
          
            strhtml.Append("<p class=\"num\">&nbsp; &nbsp;");
            
          
            strhtml.Append("</p>");
            strhtml.Append("<textarea name=\"content\" id=\"content\"  onselect=\"storeCaret();\" onclick=\"storeCaret();\" onkeyup=\"storeCaret();\" rows=\"5\" class=\"KL_textarea\" style=\"width:99%; color:#999;\">" + this.reShowInfo + "</textarea><br/>");
            if (this.isNeedSecret == true)
            {
                strhtml.Append("本版暗号*:<input type=\"text\" name=\"secret\" value=\"\" size=\"10\" /><br/>");

            }
            strhtml.Append("<input type=\"hidden\" name=\"action\" value=\"add\"/>");
            strhtml.Append("<input type=\"hidden\" name=\"id\" value=\"" + id + "\"/>");
            strhtml.Append("<input type=\"hidden\" name=\"siteid\" value=\"" + siteid + "\"/>");
            strhtml.Append("<input type=\"hidden\" name=\"lpage\" value=\"" + lpage + "\"/>");
            strhtml.Append("<input type=\"hidden\" name=\"classid\" value=\"" + classid + "\"/>");
            strhtml.Append("<input type=\"hidden\" name=\"reply\" value=\"" + reply + "\"/>");
            strhtml.Append("<input type=\"hidden\" name=\"replyid\" value=\"" + replyid + "\"/>");
            strhtml.Append("<input type=\"hidden\" name=\"touserid\" value=\"" + this.GetRequestValue("touserid") + "\"/>");
            strhtml.Append("<input type=\"hidden\" name=\"sid\" value=\"" + sid + "\"/>");

            strhtml.Append("<div class=\"gdpl\">");
            strhtml.Append("<span id=\"showfacelist\"></span><a href=\"javascript:showface();\" class=\"bbsface\" title=\"表情\"></a>&nbsp;&nbsp;<input type=\"checkbox\" value=\"0\" onclick=\"this.value=(this.value==0)?1:0\" style=\"top:0px;width:14px; height: 14px;\" name=\"sendmsg\"><y style=\"font-size: 12px;color:#777;\">通知楼主</y>");
            if (this.reply != "0")
            {
                strhtml.Append("&nbsp;&nbsp;<input type=\"checkbox\" value=\"0\" onclick=\"this.value=(this.value==0)?1:0\" style=\"top:0px;width:14px; height: 14px;\" name=\"sendmsg2\"><y style=\"font-size: 12px;color:#777;\">通知" + this.reply + "楼</y>");

            }
            strhtml.Append("\n<script type=\"text/javascript\"> \n function showface() \n{ \n var strhtml =\"<div id='SmohanFaceBox'><span class='Corner'></span><div class='Content'><h3><span>常用表情</span><a class='close' href='javascript:hiddenface(1);' title='关闭'></a></h3><ul><img src='/Template/default/img/loads.gif' alt='.'/>正在加载中...</ul></div></div>\" ;\n  document.getElementById('showfacelist').innerHTML = strhtml; myajax('GET','/bbs/ajax.aspx?type=face&siteid=" + this.siteid + "&classid=" + this.classid + "','bbsface'); \n}  \n");
            strhtml.Append("\n function hiddenface(a) { if(a==1){ document.getElementById('SmohanFaceBox').style.display = 'none'; }else {document.getElementById('SmohanFaceBox').style.display = 'block';} }");
            strhtml.Append("\n function storeCaret() { \n var textEl = f.content; \n if (textEl.createTextRange) \n textEl.caretPos = document.selection.createRange().duplicate(); \n }");
            strhtml.Append("\n function addubb(ubb) {");
            strhtml.Append("\nvar textEl = f.content;");
            strhtml.Append("\nvar text = ubb;");
            strhtml.Append("\nif (textEl.createTextRange && textEl.caretPos) {");
            strhtml.Append("\n   var caretPos = textEl.caretPos;");
            strhtml.Append("\n   caretPos.text = caretPos.text.charAt(caretPos.text.length - 1) == ' ' ? text + ' ' : text;");
            strhtml.Append("\n }");
            strhtml.Append("\n else {");
            strhtml.Append("\n   textEl.value = textEl.value + text; }");
            strhtml.Append("\n} ");
            strhtml.Append("\n</script>\n");

            if (this.showAjax == "0")
            {
                strhtml.Append("<input type=\"submit\" name=\"g\" class=\"open2\" id=\"submit\"   value=\"快速回复\"/>");
            
            }
            else
            {
                strhtml.Append("<input type=\"button\" name=\"g\" class=\"open2\" id=\"submit\"  onClick=\"gosubmit();\"  value=\"快速回复\"/>");
            }
            strhtml.Append("<div class=\"fujian\" id=\"reset\"><a href=\"" + this.http_start + "bbs/book_re_addfile.aspx?action=class&amp;siteid=" + this.siteid + "&amp;classid=" + this.classid + "&amp;id=" + this.id + "&amp;lpage=" + this.lpage + "\">附件</a></div>");
            strhtml.Append("</div></form>");
            strhtml.Append("</div></div></div>");
            strhtml.Append("\n<script type=\"text/javascript\"> \n function gosubmit() \n{ \n  if( '" + this.reb + "'=='0' && '" + this.userid + "'=='0'){showBox('请先登录网站！');return;}; if(document.getElementById('content').value==''){showBox('回复不能为空！');return;}; document.getElementById('submit').disabled = 'disabled'; document.getElementById('submit').value='提交中...'; \n myajax('POST',getFormUrl(f),'bbsre'); \n}  \n");
            strhtml.Append("\n</script>\n");
        }
        //显示列表
        strhtml.Append("<div id=\"bbsrelist\">");
    
        strhtml.Append("<!--listS-->");
        
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

            strhtml.Append("<table><tbody><tr><td valign=\"top\" align=\"center\" style=\"width:50px;\"><a href=\"" + this.http_start + "bbs/userinfo.aspx?siteid=" + this.siteid + "&amp;touserid=" + listVo[i].userid + "&amp;backurl=" + HttpUtility.UrlEncode(this.GetUrlQueryString()) + "\"><div style=\"width:50px;height:50px;border-width: 0px;-moz-border-radius: 25px;-khtml-border-radius: 25px;-webkit-border-radius: 25px;border-radius: 25px;padding:0px; margin-top:5px; background:url(" + WapTool.GetHeadImgURL(http_start, listVo[i].headimg) + "); background-size:100% 100%\"></div></a><p align=\"center\" style=\"font-size:14px\">");
            if (ot == "1")
            {
                index = (kk + 1);
            }
            else
            {
                index = (total - kk);
            }

            if (listVo[i].book_top == 1 && this.CurrentPage == 1)
            {
                strhtml.Append("[顶楼]");
            }
            else
            {
                if (index == 1)
                {
                    strhtml.Append("沙发");
                }
                else if (index == 2)
                {
                    strhtml.Append("椅子");
                }
                else if (index == 3)
                {
                    strhtml.Append("地板");
                }
                else
                {
                    strhtml.Append("" + index + "楼");
                }
            }

            strhtml.Append("</p></td><td style=\"width:100%;padding-left:5px\"><a href=\"" + this.http_start + "bbs/userinfo.aspx?siteid=" + this.siteid + "&amp;touserid=" + listVo[i].userid + "&amp;backurl=" + HttpUtility.UrlEncode(this.GetUrlQueryString()) + "\">" + ShowNickName_color(listVo[i].userid, listVo[i].nickname) + "</a>");
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

            strhtml.Append("<span class=\"right\">" + string.Format("{0:MM-dd HH:mm}", listVo[i].redate) + "</span><br>");
            //if (listVo[i].reply != 0)
            //{
            //    strhtml.Append("回复" + listVo[i].reply + "楼:");
            //}
            strhtml.Append(listVo[i].content);
            if (listVo[i].isdown > 0)
            {
                strhtml.Append("{<a href=\"" + this.http_start + "bbs/book_re_addfileshow.aspx?siteid=" + this.siteid + "&amp;classid=" + this.classid + "&amp;id=" + this.id + "&amp;reid=" + listVo[i].id + "&amp;lpage=" + this.lpage + "\">查看" + listVo[i].isdown + "个附件</a>}");
            }

            strhtml.Append("<p align=\"right\">");
            if (listVo[i].myGetMoney > 0)
            {
                strhtml.Append("<b>得金:" + listVo[i].myGetMoney + "</b>");
            }

            if (this.userid == bookVo.book_pub && bookVo.sendMoney > 0 && listVo[i].myGetMoney == 0)
            {
                strhtml.Append("&nbsp;<a class=\"urlbtn\" href=\"" + this.http_start + "bbs/SendMoney.aspx?action=sendmoney&amp;classid=" + classid + "&amp;id=" + id + "&amp;reid=" + listVo[i].id + "&amp;siteid=" + this.siteid + "\">赏分</a>");
            }


            //--------------------管理员
            if (this.IsCheckManagerLvl("|00|01|03|04|", classVo.adminusername))
            {
                strhtml.Append("<a class=\"urlbtn\" href=\"" + this.http_start + "bbs/SendMoney_free.aspx?action=sendmoney&amp;classid=" + classid + "&amp;id=" + id + "&amp;reid=" + listVo[i].id + "&amp;touserid=" + listVo[i].userid + "&amp;siteid=" + this.siteid + "\">送币</a>");

                strhtml.Append("<a class=\"urlbtn\" href=\"" + this.http_start + "bbs/Book_re_del.aspx?action=go&amp;siteid=" + this.siteid + "&amp;classid=" + this.classid + "&amp;lpage=" + this.lpage + "&amp;page=" + this.CurrentPage + "&amp;reid=" + listVo[i].id + "&amp;id=" + this.id + "&amp;ot=0\">删除</a>");
                strhtml.Append("<a class=\"urlbtn\" href=\"" + this.http_start + "bbs/Book_re_mod.aspx?action=go&amp;siteid=" + this.siteid + "&amp;classid=" + this.classid + "&amp;lpage=" + this.lpage + "&amp;page=" + this.CurrentPage + "&amp;reid=" + listVo[i].id + "&amp;id=" + this.id + "&amp;ot=0\">审核</a>");

                if (listVo[i].book_top == 1)
                {
                    strhtml.Append("<a class=\"urlbtn\" href=\"" + this.http_start + "bbs/Book_re_top.aspx?action=go&amp;tops=0&amp;siteid=" + this.siteid + "&amp;classid=" + this.classid + "&amp;lpage=" + this.lpage + "&amp;page=" + this.CurrentPage + "&amp;reid=" + listVo[i].id + "&amp;id=" + this.id + "&amp;ot=0\">消顶</a>");
                }
                else
                {
                    strhtml.Append("<a class=\"urlbtn\" href=\"" + this.http_start + "bbs/Book_re_top.aspx?action=go&amp;tops=1&amp;siteid=" + this.siteid + "&amp;classid=" + this.classid + "&amp;lpage=" + this.lpage + "&amp;page=" + this.CurrentPage + "&amp;reid=" + listVo[i].id + "&amp;id=" + this.id + "&amp;ot=0\">置顶</a>");
                }
            }
            else if (this.userid == listVo[i].userid.ToString())  //自己删除自己的帖子
            {
                strhtml.Append("<a class=\"urlbtn\" href=\"" + this.http_start + "bbs/Book_re_del.aspx?action=go&amp;siteid=" + this.siteid + "&amp;classid=" + this.classid + "&amp;lpage=" + this.lpage + "&amp;page=" + this.CurrentPage + "&amp;reid=" + listVo[i].id + "&amp;id=" + this.id + "&amp;ot=0\">删我</a>");
            }



            strhtml.Append("<a class=\"urlbtn\" href=\"" + this.http_start + "bbs/Book_re.aspx?siteid=" + this.siteid + "&amp;classid=" + this.classid + "&amp;lpage=" + this.lpage + "&amp;page=" + this.CurrentPage + "&amp;reply=" + index + "&amp;replyid="+listVo[i].id+"&amp;id=" + this.id + "&amp;touserid=" + listVo[i].userid + "&amp;ot=0\">回复</a><a class=\"urlbtn\" href=\"" + this.http_start + "bbs/book_re.aspx?action=class&amp;id=" + this.id + "&amp;siteid=" + this.siteid + "&amp;classid=" + this.classid + "&amp;mainuserid=" + listVo[i].userid + "&go=" + r + "\">只看TA</a></p></td></tr></tbody></table>");


            kk = kk + 1;

            strhtml.Append("</div>");
        }





        if (listVo == null)
        {
            strhtml.Append("<div class=\"tip\">");
            strhtml.Append("暂无回复记录！");
            strhtml.Append("</div>");
        }
       
        strhtml.Append("<!--listE-->");
        strhtml.Append("</div>");

        //显示导航分页

        strhtml.Append(linkURL);

    }
       
        strhtml.Append("<script type=\"text/javascript\">\n");
        strhtml.Append("function fnTrue(num,messageid){\n");
        
        strhtml.Append("if(messageid=='bbsface') {\n");
        strhtml.Append("if(num.indexOf(\"SmohanFaceBox\") > 0){ document.getElementById('showfacelist').innerHTML = num; }\n");
        strhtml.Append("}else if(messageid=='bbsre'){\n");
        strhtml.Append(" document.getElementById('submit').disabled = ''; \n document.getElementById('submit').value='快速回复';");

        strhtml.Append("\n var st1 = num.indexOf('<!--list' + 'X-->');\n var et1 = num.indexOf('<!--list' + 'Y-->');");
        strhtml.Append("\n var st = num.indexOf('<!--list' + 'S-->');\n var et = num.indexOf('<!--list' + 'E-->');");
        strhtml.Append("\n if(st1 < 0 || et1 < 0){}else{showBox(num.substring(st1 + 12,et1)); } ");
        strhtml.Append("\n if(st < 0 || et < 0){}else{document.getElementById('bbsrelist').innerHTML = num.substring(st + 12,et); } ");
        strhtml.Append("\n}");
        strhtml.Append("\n}");
        strhtml.Append("\n function fnFalse(messageid){showBox('网络不给力，提交失败！');}\n");
        strhtml.Append("</script>\n");

    //显示电脑版
    if (isWebHtml != "")
    {
        string strhtml_list = strhtml.ToString();
        int s = strhtml_list.IndexOf("<!--web-->");
        strhtml_list = strhtml_list.Substring(s, strhtml_list.Length - s);

        Response.Clear();
        Response.Write(WapTool.ToWML(isWebHtml.Replace("[view]", strhtml_list), wmlVo));
        Response.End();
    }
    strhtml.Append("<div class=\"btBox\"><div class=\"bt2\">");
    strhtml.Append("<a href=\"" + this.http_start + "bbs/book_view.aspx?siteid=" + siteid + "&amp;classid=" + classid + "&amp;id=" + id + "&amp;lpage=" + lpage + "" + "\">返回主题</a> ");
    strhtml.Append("<a href=\"" + this.http_start + "bbs/book_list.aspx?action=class&amp;siteid=" + siteid + "&amp;classid=" + classid + "&amp;page=" + lpage + "" + "\">返回列表</a> ");
   
    strhtml.Append("</div></div>");

    //输出
    Response.Write(WapTool.ToWML(strhtml.ToString(), wmlVo));
    
    
    




}
                                                                                                                                                                               
                                                                                                                                                                            
//显示底部
Response.Write(WapTool.showDown(wmlVo)); %>
