<%@ Page Language="C#" AutoEventWireup="true" CodeBehind="add.aspx.cs" Inherits="KeLin.WebSite.admin.charRepalce.add" %><%@ Import namespace="KeLin.ClassManager.Tool" %><%
Response.Write(WapTool.showTop(this.GetLang("新增内容|新增內容|content add"), wmlVo));//显示头                                                                                                                                                                       


    Response.Write("<div class=\"subtitle\">" + this.GetLang("新增操作|新增操作|Add operation") + "</div>");
    Response.Write("<div class=\"tip\">");
    Response.Write(this.ERROR);
    if (this.INFO == "OK")
    {
        Response.Write("<b>");
        Response.Write(this.whickOK);
        Response.Write("</b><br/>");
        Response.Write("<a href=\"" + this.http_start + "admin/charReplace/list.aspx?siteid=" + this.siteid + "&amp;classid=" + this.toclassid + "&amp;page=" + this.page + "\">" + this.GetLang("返回列表|返回列表|Back to list") + "</a>");
    }

    Response.Write("</div>");
    Response.Write("<div class=\"content\">");
    //选多少个
    Response.Write("<form name=\"g1\" action=\"" + http_start + "admin/charReplace/add.aspx\" method=\"get\">");
    Response.Write(this.GetLang("选择|選擇|Select") + " <input type=\"text\" name=\"num\" value=\"" + this.num + "\" size=\"2\"/>");   
    Response.Write("<input type=\"hidden\" name=\"action\" value=\"go\"/>");
    Response.Write("<input type=\"hidden\"  name=\"classid\" value=\"" + classid + "\"/>");
    Response.Write("<input type=\"hidden\"  name=\"siteid\" value=\"" + siteid + "\"/>");
    Response.Write("<input type=\"hidden\"  name=\"page\" value=\"" + page + "\"/>");
    Response.Write("<input type=\"hidden\"  name=\"sid\" value=\"" + sid + "\"/>");
    Response.Write(" <input type=\"submit\"  name=\"bt\" value=\"" + this.GetLang("确定|确定|GO") + "\"/></form>");
    Response.Write("<br/><br/>");
 
    
    //选择栏目
    Response.Write("<form name=\"gt\" action=\"" + http_start + "admin/charReplace/add.aspx\" method=\"post\">");
    

    
    
    for (int i = 0; i < this.num; i++)
    {
        Response.Write("----- " + (i + 1) + " -------<br/>");
       
        Response.Write( "原字符*:<br/>");
        Response.Write("<textarea name=\"oldchar\" rows=\"5\" style=\"width:100%\"></textarea><br/>");
        Response.Write("替换<br/>");
        Response.Write( "新字符*:<br/>");
        Response.Write("<textarea name=\"newchar\" rows=\"5\" style=\"width:100%\"></textarea><br/>");
        

    }



    
    Response.Write("<input type=\"hidden\" name=\"action\" value=\"gomod\"/>");
    Response.Write("<input type=\"hidden\" name=\"classid\" value=\"" + classid + "\"/>");
    Response.Write("<input type=\"hidden\" name=\"siteid\" value=\"" + siteid + "\"/>");
    Response.Write("<input type=\"hidden\" name=\"page\" value=\"" + page + "\"/>");
    Response.Write("<input type=\"hidden\" name=\"sid\" value=\"" + sid + "\"/>");
    Response.Write("<input type=\"submit\"  name=\"bt\" value=\"" + this.GetLang("新 增|新 增|Add") + "\"/></form>");
    Response.Write("</div>");

   
    Response.Write("<div class=\"btBox\"><div class=\"bt1\">");
    Response.Write("<a href=\"" + this.http_start + "admin/charReplace/list.aspx?siteid=" + this.siteid + "&amp;classid=" + this.classid + "&amp;page=" + this.page + "\">" + this.GetLang("返回列表|返回列表|Back to list") + "</a>");

    Response.Write("</div></div>");
 


                                                                                                                                                                               
                                                                                                                                                                             
//显示底部
Response.Write(WapTool.showDown(wmlVo)); %>


