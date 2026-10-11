<%@ Page Language="C#" AutoEventWireup="true" CodeBehind="ClassConfigAll.aspx.cs" Inherits="KeLin.WebSite.link.ClassConfigAll" %><%@ Import namespace="KeLin.ClassManager.Tool" %><%
Response.Write(WapTool.showTop(this.GetLang("本栏目配置|本栏目配置|Manage Ads"), wmlVo));//显示头                                                                                                                                                                       

    Response.Write("<div class=\"subtitle\">" + this.GetLang("本栏目配置|本栏目配置|Manage Ads") + "</div>");
    Response.Write("<div class=\"tip\">");
    Response.Write(this.ERROR);
    if (this.INFO == "OK")
    {
        Response.Write("<b>");
        Response.Write(this.GetLang("保存成功！|保存成功！|Successfully saved"));
        Response.Write("</b><br/>");
        Response.Write("<a href=\"" + this.http_start + "link/admin_userlistWAP.aspx?siteid=" + this.siteid + "&amp;classid=" + this.classid + "&amp;page=" + this.page + "\">" + this.GetLang("返回列表|返回列表|Back to list") + "</a><br/>");
    }
    Response.Write("</div>");
    Response.Write("<div class=\"content\">");
    Response.Write("<form name=\"go\" action=\""+this.http_start+"link/classconfigall.aspx\" method=\"post\">");
    Response.Write("[0]" + this.GetLang("点击标题后|点击标题后|点击标题后") + "*:<br/>");
    Response.Write("<select name=\"top0\">");
    Response.Write("<option value=\"" + top0 + "\">" + top0 + "</option>");
    Response.Write("<option value=\"0\">0_直接进入对方网站(有提示)</option>");
    Response.Write("<option value=\"1\">1_显示网站介绍</option>");
    Response.Write("<option value=\"2\">2_直接进入对方网站(无提示无统计)</option>");
    Response.Write("</select><br/>");
    Response.Write("[1]"+this.GetLang("自动审核|自动审核|自动审核") + "*:<br/>");
    Response.Write("<select name=\"top1\">");
    Response.Write("<option value=\"" + top1 + "\">" + top1 + "</option>");
    Response.Write("<option value=\"0\">0_是</option>");
    Response.Write("<option value=\"1\">1_否</option>");
    Response.Write("</select><br/>");
    Response.Write("[2]"+this.GetLang("显示链入链出量|显示链入链出量|显示链入链出量") + "*:<br/>");
    Response.Write("<select name=\"top2\">");
    Response.Write("<option value=\""+top2+"\">"+top2+"</option>");
    Response.Write("<option value=\"0\">0_不显示</option>");
    Response.Write("<option value=\"1\">1_显示</option>");
    Response.Write("</select><br/>");
    Response.Write("[3]显示投票功能*:<br/>");
    Response.Write("<select name=\"top3\">");
    Response.Write("<option value=\"" + top3 + "\">" + top3 + "</option>");
    Response.Write("<option value=\"0\">0_显示</option>");
    Response.Write("<option value=\"1\">1_不显示</option>");
    Response.Write("</select><br/>");
    Response.Write("[4]默认显示排序方式*:<br/>");
    Response.Write("<select name=\"top4\">");
    Response.Write("<option value=\"" + top4 + "\">" + top4 + "</option>");
    Response.Write("<option value=\"\">默认_动态排行</option>");
    Response.Write("<option value=\"hot\">hot_人气排行</option>");
    Response.Write("<option value=\"new\">new_最新排行</option>");
    Response.Write("<option value=\"back\">back_链入排行</option>");
    Response.Write("<option value=\"smalltype\">smalltype_投票排行</option>");
    Response.Write("<option value=\"good\">good_推荐排行</option>");
    Response.Write("<option value=\"checking\">checking_审核中网站</option>");
    Response.Write("</select><br/>");
    Response.Write("[5]外站链入后默认进入*:<br/>");
    Response.Write("<select name=\"top5\">");
    Response.Write("<option value=\"" + top5 + "\">" + top5 + "</option>");
    Response.Write("<option value=\"0\">0_进入首页</option>");
    Response.Write("<option value=\"1\">1_进入友链栏目</option>");
    Response.Write("</select><br/>(修改记录可自定义跳转页面)<br/>");

    Response.Write("[6]二级页面返回上级/首页替换:<br/>");
    Response.Write("<input type=\"text\" name=\"top6\" value=\"" + this.top6 + "\"/><br/>");
    Response.Write("例:<input type=\"text\" name=\"infos" + r + "\" value=\"[url=/wapindex.aspx?siteid=[siteid]&amp;classid=[parentid]]返回上级[/url]-[url=/wapindex.aspx?siteid=[siteid]]返回首页[/url]\"/><br/>");

    Response.Write("[7]三级页面返回上级/首页替换:<br/>");
    Response.Write("<input type=\"text\" name=\"top7\" value=\"" + this.top7 + "\"/><br/>");
    Response.Write("例:<input type=\"text\" name=\"inos" + r + "\" value=\"[url=/link/book_list.aspx?siteid=[siteid]&amp;classid=[classid]]返回[classname][/url]-[url=/wapindex.aspx?siteid=[siteid]]返回首页[/url]\"/><br/>");


    Response.Write("[8]二级页面显示上一页下一页，默认有加载更多:<br/>");
    Response.Write("<select name=\"top8\">");
    Response.Write("<option value=\"" + top8 + "\">" + top8 + "</option>");
    Response.Write("<option value=\"0\">0_关闭</option>");
    Response.Write("<option value=\"1\">1_开启</option>");
    Response.Write("</select><br/>");


    Response.Write("[9]二级页面列表显示图片宽*高:<br/>");
    Response.Write("<input type=\"text\" name=\"top9\" size=\"7\"  value=\"" + this.top9 + "\"/> 格式120*120<br/>");

    Response.Write("[10]二级页面布局效果，建议0,6,12,13:");
    Response.Write("<select name=\"top10\">");
    Response.Write("<option value=\"" + this.top10 + "\">" + this.top10 + "</option>");
    Response.Write("<option value=\"0\">布局0默认</option>");
    for (int i = 0; i < 30; i++)
    {
          if (i == 12)
        {
            Response.Write("<option value=\"12\">布局12(左图正方)</option>");

        }
        else if (i == 13)
        {
            Response.Write("<option value=\"13\">布局13(图长方)</option>");

        }
        else
        {
            Response.Write("<option value=\"" + (i + 1) + "\">布局" + (i + 1) + "</option>");
        }
          
    }
    Response.Write("<option value=\"100\">布局100 自定义需配合【13】使用</option>");
    Response.Write("</select><br/>");
    Response.Write("[13]用自定义万能UBB编辑好代码(填写html5/WML模块中的标题ID):<br/>");
    Response.Write("[getwml=<input type=\"text\" name=\"top13\"   value=\"" + this.top13 + "\" size=\"5\"/>] <a href=\"/UBB_API/templace/class/ubbDoc/functionData.htm\" target=\"_blank\">参考应用二</a><br/>");
    Response.Write("----------<br/>");

    Response.Write("[11]微信链接模式:");
    Response.Write("<select name=\"top11\">");
    Response.Write("<option value=\"" + top11 + "\">" + top11 + "</option>");
    Response.Write("<option value=\"0\">0_关闭</option>");
    Response.Write("<option value=\"1\">1_开启</option>");
    Response.Write("</select><br/>");



    Response.Write("[12]显示关闭顶部排序方式:");
    Response.Write("<select name=\"top12\">");
    Response.Write("<option value=\"" + top12 + "\">" + top12 + "</option>");
    Response.Write("<option value=\"0\">0_显示</option>");
    Response.Write("<option value=\"1\">1_关闭</option>");
    Response.Write("</select><br/>");
    
    if (userVo.managerlvl == "00" || userVo.managerlvl == "01")
    {
        Response.Write("<hr/>");
        Response.Write("是否更新所有友链栏目？");
        Response.Write("<br/><input type=\"text\"  name=\"chkall\" size=\"3\" value=\"no\"/>如果是输入yes");
        Response.Write("<hr/>");
    }
    Response.Write("<input type=\"hidden\" name=\"action\" value=\"gomod\"/>");
    Response.Write("<input type=\"hidden\" name=\"classid\" value=\"" + classid + "\"/>");
    Response.Write("<input type=\"hidden\" name=\"siteid\" value=\"" + siteid + "\"/>");
    Response.Write("<input type=\"hidden\" name=\"page\" value=\"" + page + "\"/>");
    Response.Write("<input type=\"hidden\" name=\"sid\" value=\"" + sid + "\"/>");
    Response.Write("<input type=\"submit\" name=\"bt\" value=\"" + this.GetLang("保 存|保 存|Save") + "\"/><br/>");
    Response.Write("说明:更多栏目属性请在页面综合排版修改栏目！<br/>");
    Response.Write("</form></div>");
    Response.Write("<div class=\"btBox\"><div class=\"bt1\">");
    Response.Write("<a href=\"" + this.http_start + "link/admin_userlistWAP.aspx?siteid=" + this.siteid + "&amp;classid=" + this.classid + "&amp;page=" + this.page + "\">" + this.GetLang("返回列表|返回列表|Back to list") + "</a>");

    Response.Write("</div></div>");
 


                                                                                                                                                                               
                                                                                                                                                                             
//显示底部
Response.Write(WapTool.showDown(wmlVo)); %>


