<%@ Page Language="C#" AutoEventWireup="true" CodeBehind="myrizhi_admin.aspx.cs" Inherits="KeLin.WebSite.rizhi.myrizhi_admin" %><%@ Import namespace="KeLin.ClassManager.Tool" %><%
StringBuilder strhtml=new StringBuilder ();                                                                                                                                                              
Response.Write(WapTool.showTop(this.GetLang("分类管理|分类管理|my fav admin"), wmlVo));//显示头                                                                                                                                                                       



    strhtml.Append("<div class=\"subtitle\">" + this.GetLang("分类管理|分类管理|my fav admin") + "</div>");
    strhtml.Append("<div class=\"tip\">");
    if (this.INFO == "ADDOK")
    {
        strhtml.Append("<b>" + this.GetLang("添加成功！|添加成功！|add successfully!") + "</b><br/>");

    }
    else if (this.INFO == "NULL")
    {
        strhtml.Append("<b>" + this.GetLang("不能为空！|不能为空！|not null!") + "</b><br/>");

    }
    strhtml.Append("</div>");
  
    strhtml.Append("<div class=\"content\">");
    strhtml.Append("<form name=\"f\" action=\"" + http_start + "rizhi/myrizhi_admin.aspx\" method=\"post\">");
    strhtml.Append("分类名称:<br/>");
    strhtml.Append("<input type=\"text\" name=\"subjectname\" value=\"" + subjectname + "\"/><br/>");
    strhtml.Append("排序号:<br/>");
    strhtml.Append("<input type=\"text\" name=\"ordernum\" value=\"" + ordernum + "\" size=\"5\"/>");
   
    strhtml.Append("<input type=\"hidden\" name=\"action\" value=\"goadd\" />");
    strhtml.Append("<input type=\"hidden\" name=\"siteid\" value=\"" + siteid + "\" />");
    strhtml.Append("<input type=\"hidden\" name=\"classid\" value=\"" + classid + "\" />");
    strhtml.Append("<input type=\"hidden\" name=\"backurl\" value=\"" + (backurl) + "\" />");
    strhtml.Append("<input type=\"hidden\" name=\"sid\" value=\"" + sid + "\" />");
    strhtml.Append("<input type=\"submit\" name=\"g\" value=\"" + this.GetLang("添加|添加|submit") + "\"/>");
    strhtml.Append("</form>");
    
    strhtml.Append("<hr/>");
    strhtml.Append("排序号|分类名<br/>");
    for (int i = 0; (sublistVo != null && i < sublistVo.Count); i++)
    {

        strhtml.Append(sublistVo[i].ordernum + "|" + sublistVo[i].subjectname);
        strhtml.Append("[<a href=\"" + http_start + "rizhi/myrizhi_admin_mod.aspx?action=mod&amp;siteid=" + this.siteid + "&amp;classid="+this.classid+"&amp;id=" + sublistVo[i].id +  "\">修</a>.<a href=\"" + http_start + "rizhi/myrizhi_admin_del.aspx?action=del&amp;siteid=" + this.siteid + "&amp;classid="+this.classid+"&amp;id=" + sublistVo[i].id + "\">删</a>]<br/>");

    }
    if (sublistVo == null)
    {
        strhtml.Append("没有建分类，请先添加<br/>");
    }
    strhtml.Append("</div>");
    
    strhtml.Append("<div class=\"btBox\"><div class=\"bt2\">");
    strhtml.Append("<a href=\"" + http_start + "rizhi/myrizhi_admin_del.aspx?action=delall&amp;siteid=" + this.siteid + "&amp;classid="+this.classid+"\">清空我的分类</a><br/>");
    strhtml.Append("<a href=\"" + this.http_start + "rizhi/admin_WAPadd.aspx?siteid=" + this.siteid + "&amp;classid=" + this.classid + "\">返回上级</a> ");
  
    strhtml.Append("</div></div>");

    Response.Write(strhtml);




                                                                                                                                                                               
Response.Write(ERROR);                                                                                                                                                                              
//显示底部
Response.Write(WapTool.showDown(wmlVo)); %>


