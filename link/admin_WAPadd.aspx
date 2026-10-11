<%@ Page Language="C#" AutoEventWireup="true" CodeBehind="admin_WAPadd.aspx.cs" Inherits="KeLin.WebSite.link.admin_WAPadd" %><%@ Import namespace="KeLin.ClassManager.Tool" %><%
Response.Write(WapTool.showTop(this.GetLang("加入网站|加入网站|content add"), wmlVo));//显示头                                                                                                                                                                       

    Response.Write("<div class=\"subtitle\">" + this.GetLang("加入网站|加入网站|Add operation") + "</div>");
    
    Response.Write(this.ERROR);
    if (this.INFO == "NULL")
    {
        Response.Write("<div class=\"tip\">");
        Response.Write("标题和内容不能小于2个字符！  ");
        Response.Write("</div>");
    }     else if (this.INFO == "REPEAT")
    {
        Response.Write("<div class=\"tip\">");
        Response.Write("<b>请不要发重复内容！</b><br/>");
        Response.Write("</div>");
    }
    else if (this.INFO == "WAITING")
    {
        Response.Write("<div class=\"tip\">");  
        Response.Write("<b>请再过" + this.KL_CheckIPTime + "秒后操作！</b><br/>");
        Response.Write("</div>");

    }
    else if (this.INFO == "LOCK")
    {
        Response.Write("<div class=\"tip\">");  
        Response.Write("<b>抱歉，您已经被加入黑名单，请注意发帖规则！</b><br/>");
        Response.Write("</div>");
    
    }else if (this.INFO == "OK")
    {
        Response.Write("<div class=\"tip\">");  
        Response.Write("加入成功！  ");
        if (this.isAutoCheck == "1")
        {
            Response.Write(" 审核后显示！  ");
        }
        Response.Write("请将以下地址加入你的网站：<br/>" + toMylink);
        Response.Write("</div>");

    }

    if (this.INFO != "OK")
    {
        
        Response.Write("<div class=\"content\">");
        if (buju2 != "1")
        {
            Response.Write("<div class=\"tip\">第一张图作为LOGO图,建议120*120,第二张图可上传二维码图，建议300*300。</div>");
      

            Response.Write("<form name=\"g1\" action=\"" + http_start + "link/admin_WAPadd.aspx\"   method=\"get\">");
            Response.Write("选择图片:");
            Response.Write("<select name=\"num\">");

            Response.Write("<option value=\"" + this.num + "\">" + this.num + "</option>");
            for (int i = 1; i < 7; i++)
            {
                Response.Write("<option value=\"" + i + "\">" + i + "</option>");
            }

            Response.Write("</select>");

            Response.Write("<input type=\"hidden\" name=\"action\" value=\"go\"/>");
            Response.Write("<input type=\"hidden\"  name=\"classid\" value=\"" + classid + "\"/>");
            Response.Write("<input type=\"hidden\"  name=\"siteid\" value=\"" + siteid + "\"/>");
            Response.Write("<input type=\"hidden\"  name=\"sid\" value=\"" + sid + "\"/>");
            Response.Write("<input type=\"submit\"  name=\"bt\" value=\"" + this.GetLang("确定|确定|GO") + "\"/></form><br/>");

            Response.Write("缩放:" + this.GetLang("宽|宽|Width") + ":<input type=\"text\" name=\"swidth\" size=\"3\" value=\"" + swidth + "\"/>");
            Response.Write(this.GetLang("高|高|Height") + ":<input type=\"text\" name=\"sheight\" size=\"3\" value=\"" + sheight + "\"/> ");
            Response.Write("<br/>");

        }
       
        Response.Write("<form name=\"gt\" action=\"" + http_start + "link/admin_WAPadd.aspx\" enctype=\"multipart/form-data\" method=\"post\">");
        

        for (int i = 0; i < this.num; i++)
        {

            if (i == 0)
            {
                Response.Write("第" + (i + 1) + "张选LOGO图标：");
            }
            else if (i == 1)
            {
                Response.Write("第" + (i + 1) + "张选二维码图：");
            }
            else
            {
                Response.Write("第" + (i + 1) + "张图：");
            }

            Response.Write("<input type=\"file\" name=\"book_file\" value=\"\"/> ");
            Response.Write("<br/>");
        }
        
       
       
       
        //选择栏目

        if (classList != null && classList.Count == 1)
        {
            Response.Write("<input type=\"hidden\" name=\"toclassid\" value=\"" + classList[0].classid + "\"/>");
        }
        else
        {
            Response.Write("选择分类:<br/>");
            Response.Write("<select name=\"toclassid\">");
            Response.Write("<option value=\"" + toclassid + "\">" + toclassid + "</option>");
            for (int i = 0; (classList != null && i < classList.Count); i++)
            {

                Response.Write("<option value=\"" + classList[i].classid + "\">" + classList[i].classid + "_" + classList[i].classname + "</option>");

            }
            Response.Write("</select><br/>");
        }
        if (buju2 == "1")
        {
            Response.Write("微信名称*:<br/>");
        }
        else
        {
            Response.Write(this.GetLang("网站名称|网站名称|Title") + "*:<br/>");
        }
        
        Response.Write("<input type=\"text\" name=\"book_title\" class=\"txt\" value=\"" + book_title + "\"/><br/>");
        if (buju2 == "1")
        {
            Response.Write("微信号*:<br/>");
            Response.Write("<input type=\"text\" name=\"book_weixin\" class=\"txt\" value=\""+book_weixin+"\"/><br/>");
        
            Response.Write("微信地址(关注地址或微信文章地址)*:<br/>");
        }
        else
        {
            Response.Write(this.GetLang("网站地址|网站地址|address") + "*:<br/>");
        }
        Response.Write("<input type=\"text\" name=\"book_pub\" class=\"txt\" value=\"" + book_pub + "\"/><br/>");
        if (buju2 == "1")
        {
            Response.Write("微信简介*:<br/>");
        }
        else
        {
            Response.Write(this.GetLang("简介|简介|Content") + "*:<br/>");
        }
        Response.Write("<textarea name=\"book_content\" rows=\"5\" style=\"width:100%\">" + book_content + "</textarea><br/>");

        Response.Write("<input type=\"hidden\" name=\"action\" value=\"gomod\"/>");
        Response.Write("<input type=\"hidden\" name=\"classid\" value=\"" + classid + "\"/>");
        Response.Write("<input type=\"hidden\" name=\"siteid\" value=\"" + siteid + "\"/>");
        Response.Write("<input type=\"hidden\" name=\"page\" value=\"" + page + "\"/>");
        Response.Write("<input type=\"hidden\" name=\"sid\" value=\"" + sid + "\"/>");
        Response.Write("<input type=\"submit\"  name=\"bt\" class=\"btn\" value=\"" + this.GetLang("提 交|提 交|submit") + "\"/></form>");
        Response.Write("</div>");
    }

    Response.Write("<div class=\"btBox\"><div class=\"bt2\">");
    Response.Write("<a href=\"" + this.http_start + "link/book_list.aspx?siteid=" + this.siteid + "&amp;classid=" + this.classid + "&amp;page=" + this.page + "\">" + this.GetLang("返回上级|返回上级|Back to list") + "</a> ");
    Response.Write("<a href=\"" + this.http_start + "wapindex.aspx?siteid=" + this.siteid + "\">" + this.GetLang("返回首页|返回首页|Back to list") + "</a>");    
    Response.Write("</div></div>");
 


                                                                                                                                                                               
                                                                                                                                                                             
//显示底部
Response.Write(WapTool.showDown(wmlVo)); %>


