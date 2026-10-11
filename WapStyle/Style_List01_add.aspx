<%@ Page Language="C#" AutoEventWireup="true" validateRequest=false CodeBehind="Style_List01_add.aspx.cs" Inherits="KeLin.WebSite.WapStyle.Style_List01_add" %><%@ Import namespace="KeLin.ClassManager.Tool" %><%
Response.Write(WapTool.showTop(this.GetLang("添加CSS样式|添加CSS样式|add CSS"), wmlVo));//显示头                                                                                                                                                                       
if (ver == "1")
{
    Response.Write("<p align=\"left\">");

    Response.Write("点击此进入<a href=\""+this.http_start+"wapstyle/style_list01_add.aspx?siteid="+this.siteid+"&amp;classid="+this.classid+"&amp;sid="+this.sid1+"-2-"+this.cs+"-"+this.lang+"-"+this.myua+"-"+this.width+"\">WAP2.0编辑管理</a><br/>");
    
    Response.Write("<a href=\"" + this.http_start + "wapstyle/style_list01.aspx?siteid=" + siteid + "&amp;classid="+this.classid+"" + "\">返回上级</a>");
    Response.Write("</p>");

    //输出
   
    
}
else //2.0界面
{



    Response.Write("<div class=\"subtitle\">" + this.GetLang("添加CSS样式|添加CSS样式|add CSS") + "</div>");
    Response.Write("<div class=\"tip\">");
    if (this.INFO == "NULL")
    {
        Response.Write("<b>提交数据错误，所有项不能为空！</b><br/>");

    }
    else if (this.INFO == "OK")
    {
        Response.Write("<b>添加成功！</b><br/>");
    }

    Response.Write("</div>");
    if (this.INFO != "OK")
    {
        Response.Write("<div class=\"content\">");
        Response.Write("<form name=\"f\" action=\"" + http_start + "wapstyle/style_list01_add.aspx\" method=\"post\">");
        Response.Write("排序号:<br/><input type=\"text\" name=\"rank\" value=\"" + this.rank + "\"/><br/>");
        Response.Write("风格颜色:<br/>#<input type=\"text\" name=\"style_color\" value=\"" + this.style_color + "\" size=\"6\"/><br/>");
        Response.Write("名称:<br/><input type=\"text\" name=\"style_name\" value=\"" + this.style_name + "\"/><br/>");
        Response.Write("(如果下面样式有HTML5效果，请在名称上加上\"[HTML5]\"，方便选皮肤时显示)<br/>");
        Response.Write("CSS样式:<br/><textarea name=\"style\" rows=\"12\" style=\"width:100%\">" + this.style + "</textarea><br/>");
        
        
        Response.Write("<input type=\"hidden\" name=\"action\" value=\"gomod\"/>");
        Response.Write("<input type=\"hidden\" name=\"id\" value=\"" + id + "\"/>");
        Response.Write("<input type=\"hidden\" name=\"classid\" value=\"" + classid + "\"/>");
        Response.Write("<input type=\"hidden\" name=\"siteid\" value=\"" + siteid + "\"/>");
 
        Response.Write("<input type=\"hidden\" name=\"sid\" value=\"" + sid + "\"/>");
        Response.Write("<input type=\"submit\" name=\"g\" value=\"" + this.GetLang("提 交|提 交|submit") + "\"/><br/><br/>");
        Response.Write("CSS参考(复制到上面直修改):<br/><textarea name=\"styletemp\" rows=\"5\" style=\"width:100%\">" + wmlVo.mycss + " </textarea><br/>");

        Response.Write("说明：CSS样式=系统定义 + 自己定义！<br/>");
        Response.Write("系统定义样式名UBB调用：<br/>");
        Response.Write("[div=logo]显示LOGO位置[/div]<br/>");
        Response.Write("[div=welcome]显示欢迎位置[/div]<br/>");
        Response.Write("[div=tip]提示位置[/div]<br/>");
        Response.Write("[div=title]显示标题位置[/div]<br/>");
        Response.Write("[div=subtitle]显示副标题位置[/div]<br/>");
        Response.Write("[div=content]显示内容位置[/div]<br/>");
        Response.Write("[div=topre]显示最新回复位置[/div]<br/>");
        Response.Write("[div=showpage]显示分页位置[/div]<br/>");
        Response.Write("[div=line1]显示文本行一[/div]<br/>");
        Response.Write("[div=line2]显示文本行二[/div]<br/>");
        Response.Write("[div=mylink]显示导航位置[/div] <br/>");
        Response.Write("[div=nexttitle]上一条下一条位置[/div] <br/>");
        Response.Write("[div=sysad]全局顶部或底部[/div] <br/>");
        Response.Write("自定义样式名UBB调用：<br/>");
        Response.Write("[div=自定义名]显示内容位置[/div] <br/>");
        Response.Write("</form></div>");

    }
%>
<hr />
一、当上面录入框内容中没有关键字 default.css 时系统在最后一行自动添加以下内容：
<p>
<font  COLOR="#0000ff">&lt;</font><font  COLOR="#a31515">link</font><font >
</font><font  COLOR="#ff0000">rel</font><font  COLOR="#0000ff">=&quot;stylesheet&quot;</font><font >
</font><font  COLOR="#ff0000">href</font><font  COLOR="#0000ff">=&quot;/Template/default/default.css&quot;</font><font >
</font><font  COLOR="#ff0000">type</font><font  COLOR="#0000ff">=&quot;text/css&quot;</font><font >
</font><font  COLOR="#0000ff">/&gt;</font></p>
此文件是标准皮肤效果，【<a href="/Template/default/default.css">请点击此下载</a>】，可以用记事本打开复制代码并粘贴到上面录入框自行编辑CSS样式，不调用此文件在上面录入&lt;!--default.css--&gt;。
								      
								      
								      <hr />
								      二、当上面录入框内容没有关键字：author,keywords,description 时系统自动添加以下内容。请自行复制下面容放到第一行中，并将内容改成你想要的SEO效果。

<p><font  COLOR="#0000ff">
&lt;</font><font SIZE="2" COLOR="#a31515">meta</font><font SIZE="2"> </font>
<font SIZE="2" COLOR="#ff0000">name</font><font SIZE="2" COLOR="#0000ff">=&quot;keywords&quot;</font><font SIZE="2">
</font><font SIZE="2" COLOR="#ff0000">content</font><font SIZE="2" COLOR="#0000ff">=&quot;[title] [classname]&quot;</font><font SIZE="2">
</font><font SIZE="2" COLOR="#0000ff">/&gt;<br>
&lt;</font><font SIZE="2" COLOR="#a31515">meta</font><font SIZE="2"> </font>
<font SIZE="2" COLOR="#ff0000">name</font><font SIZE="2" COLOR="#0000ff">=&quot;description&quot;</font><font SIZE="2">
</font><font SIZE="2" COLOR="#ff0000">content</font><font SIZE="2" COLOR="#0000ff">=&quot;[title] [sitename] [sitename] [domain] &quot;</font><font SIZE="2">
</font><font SIZE="2" COLOR="#0000ff">/&gt;</font><br />
<font  COLOR="#0000ff">&lt;</font><font SIZE="2" COLOR="#a31515">meta</font><font SIZE="2"> </font>
<font SIZE="2" COLOR="#ff0000">name</font><font SIZE="2" COLOR="#0000ff">=&quot;author&quot;</font><font SIZE="2">
</font><font SIZE="2" COLOR="#ff0000">content</font><font SIZE="2" COLOR="#0000ff">=&quot;[sitename] [domain]&quot;</font><font SIZE="2">
</font><font SIZE="2" COLOR="#0000ff">/&gt;<br></font>
<br />[title]是动态取标题；[classname]栏目名称；[sitename]网站名称；[domain]域名; 
<br />除了此处SEO外，页面缩合排版时，栏目链接请改成短地址，如[url=/bbs/list.aspx?classid=栏目ID]论坛[/url]，其它模块一样，请举一反三。

</p>
								      <hr />
三、当上面录入框内容没有关键字：viewport 时系统自动添加以下内容：你可以将maximum-scale=1.0改成maximum-scale=2.0并复制到上面第一行支持页面缩放<br />

<p><font  COLOR="#0000ff">&lt;</font><font SIZE="2" COLOR="#a31515">meta</font><font SIZE="2"> </font>
<font SIZE="2" COLOR="#ff0000">name</font><font SIZE="2" COLOR="#0000ff">=&quot;viewport&quot;</font><font SIZE="2">
</font><font SIZE="2" COLOR="#ff0000">content</font><font SIZE="2" COLOR="#0000ff">=&quot;width=device-width; 
initial-scale=1.0; minimum-scale=1.0; maximum-scale=1.0&quot;&gt;</font></p>

	<hr />							      
四、上面录入框中的层宽width参数用[width]代替动态取当前配置宽，建议写死宽度，如320px, 最大宽可用max-width:720px
<br />
<hr />	
五、其它应用：
<p>*调用脚本文件.js： 
<font  COLOR="#0000ff">&lt;</font><font  COLOR="#a31515">script</font><font >
</font><font  COLOR="#ff0000">type</font><font  COLOR="#0000ff">=&quot;text/javascript&quot;</font><font >
</font><font  COLOR="#ff0000">src</font><font  COLOR="#0000ff">=&quot;js文件地址&quot;&gt;&lt;/</font><font  COLOR="#a31515">script</font><font  COLOR="#0000ff">&gt;</p>
</font>
<p>*调用样式文件.css： 
<font  COLOR="#0000ff">&lt;</font><font  COLOR="#a31515">link</font><font >
</font><font  COLOR="#ff0000">rel</font><font  COLOR="#0000ff">=&quot;stylesheet&quot;</font><font >
</font><font  COLOR="#ff0000">href</font><font  COLOR="#0000ff">=&quot;css文件地址&quot;</font><font >
</font><font  COLOR="#ff0000">type</font><font  COLOR="#0000ff">=&quot;text/css&quot;</font><font >
</font><font  COLOR="#0000ff">/&gt;</p>
</font>
*显示地址栏小图标：
<font SIZE="2" COLOR="#0000ff">
&lt;</font><font SIZE="2" COLOR="#a31515">link</font><font SIZE="2"> </font>
<font SIZE="2" COLOR="#ff0000">rel</font><font SIZE="2" COLOR="#0000ff">=&quot;SHORTCUT 
ICON&quot;</font><font SIZE="2"> </font><font SIZE="2" COLOR="#ff0000">href</font><font SIZE="2" COLOR="#0000ff">=&quot;favicon.ico地址&quot;</font><font SIZE="2">
</font><font SIZE="2" COLOR="#0000ff">/&gt;</font>
<br />

<%
    Response.Write("<div class=\"btBox\"><div class=\"bt1\">");

    Response.Write("<a href=\"" + this.http_start + "wapstyle/style_list01.aspx?siteid=" + siteid + "&amp;classid="+this.classid+"" + "\">返回上级</a>");
    Response.Write("</div></div>");
    

}
                                                                                                                                                                               
Response.Write(ERROR);                                                                                                                                                                              
//显示底部
Response.Write(WapTool.showDown(wmlVo)); %>


