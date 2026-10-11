<%@ Page Language="C#" AutoEventWireup="true" CodeBehind="admin_WAPModifyadd.aspx.cs" Inherits="KeLin.WebSite.link.admin_WAPModifyadd" %>

<%@ Import namespace="KeLin.ClassManager.Tool" %>
<%
    string titles = "更改列表显示图";
    if (types == "1") titles = "续传顶部划动图片";
    Response.Write(WapTool.showTop(titles, wmlVo));//显示头                                                                                                                                                                       
    Response.Write("<div style=\"max-width:900px;margin:0px auto;\">");
    Response.Write("<div class=\"subtitle\">" + this.GetLang("续传图片|续传图片|Add operation") + "</div>");
    Response.Write("<div class=\"tip\">");
    Response.Write(this.ERROR);
    if (this.INFO == "OK")
    {

        Response.Write("<b>续传成功！</b> ");

        Response.Write("<a href=\"" + this.http_start + "link/admin_wapmodify.aspx?siteid=" + this.siteid + "&amp;classid=" + this.classid + "&amp;smalltypeid=" + this.smalltypeid + "&amp;id=" + this.id + "\">返回查看效果</a>");
    }
    else if (this.INFO == "EXTERR")
    {
        Response.Write("<b>续传文件格式错误，只允许续传：" + siteVo.UpFileType + "</b><br/>");

    }
    else if (this.INFO == "NOTSPACE")
    {
        Response.Write("<b>网站总空间已经大于系统分配给此网站的最大空间了，网站空间：" + siteVo.sitespace + "M；此网站已使用：" + (siteVo.myspace) + "KB</b><br/>");

    }
    else if (this.INFO == "MAXFILE")
    {
        Response.Write("<b>你续传的单个文件总大小超出了最大限制" + siteVo.MaxFileSize + "KB</b><br/>");

    }
    else if (this.INFO == "NULL")
    {
        Response.Write("<b>标题不能小于2个字符！</b><br/>");

    }
    else if (this.INFO == "LOCK")
    {

        Response.Write("<b>抱歉，您已经被加入黑名单，请注意发帖规则！</b><br/>");
    }


    Response.Write("</div>");
    if (types == "2")
    {
        Response.Write("<div class=\"title\">");
        //选多少个

        Response.Write("<form name=\"g1\" action=\"" + http_start + "link/admin_WAPModifyadd.aspx\" method=\"get\">");
        Response.Write(this.GetLang("续传数量|续传数量|Upload Number") + " <input type=\"text\" name=\"num\" value=\"" + this.num + "\" size=\"2\"/>");
        Response.Write("<input type=\"hidden\" name=\"action\" value=\"go\"/>");
        Response.Write("<input type=\"hidden\"  name=\"classid\" value=\"" + classid + "\"/>");
        Response.Write("<input type=\"hidden\"  name=\"siteid\" value=\"" + siteid + "\"/>");
        Response.Write("<input type=\"hidden\"  name=\"id\" value=\"" + id + "\"/>");
        Response.Write("<input type=\"hidden\"  name=\"types\" value=\"" + types + "\"/>");
        Response.Write("<input type=\"hidden\"  name=\"sid\" value=\"" + sid + "\"/>");
        Response.Write(" <input type=\"submit\"  name=\"bt\" value=\"" + this.GetLang("确定|确定|GO") + "\"/></form>");

        Response.Write("</div>");
    }
    Response.Write("<div class=\"content\">");
    
    //选择栏目
    Response.Write("<form name=\"gt\" action=\"" + http_start + "link/admin_WAPModifyadd.aspx\" enctype=\"multipart/form-data\" method=\"post\">");
   
  
    Response.Write(this.GetLang("图宽|图宽|Width") + ":<input type=\"text\" name=\"swidth\" size=\"5\" value=\"" + swidth + "\"/>");
    Response.Write(this.GetLang("图高|图高|Height") + ":<input type=\"text\" name=\"sheight\" size=\"5\" value=\"" + sheight + "\"/><br/>");
    Response.Write("(不缩放,请留空,只输入宽或高,不变型)<br/>");

     
    
    for (int i = 0; i < this.num; i++)
    {
        Response.Write("---"+this.GetLang("图片|图片|Source") + (i + 1) + "---<br/>");
       
        Response.Write("<input type=\"file\" name=\"book_file\" value=\"\"/><br/>");
        //Response.Write("<textarea name=\"book_file_info\" rows=\"3\" style=\"width:100%\"></textarea><br/>");
    }

       

    Response.Write("<input type=\"hidden\" name=\"action\" value=\"gomod\"/>");
    Response.Write("<input type=\"hidden\" name=\"classid\" value=\"" + classid + "\"/>");
    Response.Write("<input type=\"hidden\" name=\"siteid\" value=\"" + siteid + "\"/>");
    Response.Write("<input type=\"hidden\" name=\"num\" value=\"" + num + "\"/>");
    Response.Write("<input type=\"hidden\" name=\"types\" value=\"" + types + "\"/>");
    Response.Write("<input type=\"hidden\"  name=\"id\" value=\"" + id + "\"/>");
    Response.Write("<input type=\"hidden\" name=\"sid\" value=\"" + sid + "\"/>");
    Response.Write("<input type=\"submit\"  name=\"bt\" value=\"上传\"/></form>");
    Response.Write("</div>");


    Response.Write("<hr>");
    if (types == "2")
    {
        string[] arrys = bookVo.book_file.Split('|');
        for (int y = 0; y < arrys.Length; y++)
        {
            if (arrys[y].Trim() != "")
            {
                Response.Write("<img src=\"/link/" + arrys[y] + "\" style=\"width:150px;height:150px\" alt=\"划动图\"/> 【<a href=\"/link/admin_wapmodifyadd.aspx?action=del&path=" + arrys[y] + "&types=" + types + "&siteid=" + this.siteid + "&classid=" + this.classid + "&id=" + this.id + "&sid=" + this.sid + "\">删除</a>】<br/>");
            }
        }
    }
    else
    {
        Response.Write("<img src=\"/link/" + bookVo.book_img + "\" style=\"width:150px;height:150px\" alt=\"列表图\"/> <br/>");
         
    }
    Response.Write("<hr>");
    Response.Write("<div class=\"btBox\"><div class=\"bt1\">");
    Response.Write("<a href=\"" + this.http_start + "link/admin_wapmodify.aspx?siteid=" + this.siteid + "&amp;classid=" + this.classid + "&amp;smalltypeid=" + this.smalltypeid + "&amp;id="+this.id+"\">返回上级</a>");
  
    Response.Write("</div></div>");


    Response.Write("</div>");
                                                                                                                                                                               
                                                                                                                                                                             
//显示底部
Response.Write(WapTool.showDown(wmlVo)); %>



