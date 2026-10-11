<%@ Page Language="C#" AutoEventWireup="true" CodeBehind="book_list.aspx.cs" Inherits="KeLin.WebSite.chat.book_list" %><%@ Import namespace="KeLin.ClassManager.Tool" %><%
wmlVo.timer=chattimes; //刷新时间
wmlVo.strUrl = "chat/book_list.aspx?siteid="+this.siteid+"&amp;classid="+this.classid+"&amp;r="+this.r;
Response.Write(WapTool.showTop(classVo.classname, wmlVo));//显示头                                                                                                                                                                       
if (ver == "1")
{
      

    strhtml.Append("<p align=\"" + classVo.position + "\">");
    //strhtml.Append("时间:"+this.chattimes);
    if (classVo.siteimg != "UploadFiles/no.gif" && classVo.siteimg != "NetImages/no.gif")
    {
        strhtml.Append("<img src=\"" + http_start + classVo.siteimg + "\" alt=\"LOGO\"/><br/>");
    }
    if (classVo.introduce != "")
    {
        strhtml.Append(classVo.introduce + "<br/>");
    }
    
    //显示广告
    if (adVo.secondShowTop != "")
    {
        strhtml.Append(adVo.secondShowTop+"<br/>");
    }

    if (this.classid == "0"&& this.action !="search")
    {
        strhtml.Append("<b>栏目ID为0了，请重新选择房间。</b><br/>");

    }else if (this.INFO == "WAITING")
    {
        strhtml.Append("<b>你发言太快了，请再过" + this.KL_CheckIPTime + "秒后操作！</b><br/>");

    }
    else if (this.INFO == "ERR_FORMAT")
    {
        strhtml.Append("<b>取到非法值:“$$(”请更换手机浏览器或重新编辑！</b><br/>");
    }  
    else if (this.INFO == "LOCK")
    {

        strhtml.Append("<b>抱歉，你已经被禁言了！</b><br/>");
    }
    
    if (this.IsCheckManagerLvl("|00|01", classVo.adminusername) == true)
    {
        strhtml.Append("----------<br/>");
        if (par0 == "1")
        {
            strhtml.Append("【<a href=\"" + this.http_start + "chat/speak.aspx?siteid=" + this.siteid + "&amp;classid=" + this.classid + "\">点击这里发言</a>】<br/>");
        }
        strhtml.Append("【<a href=\"" + this.http_start + "chat/classconfigall.aspx?siteid=" + this.siteid + "&amp;classid=" + this.classid + "\">栏目属性设置</a>】<br/>");
        strhtml.Append("----------<br/>");
    }
    
    if (par0 == "0")
    {
        strhtml.Append("<a href=\"" + this.http_start + "chat/speak.aspx?siteid=" + this.siteid + "&amp;classid=" + this.classid + "\">发言</a>|<a href=\"" + this.http_start + "chat/book_list.aspx?siteid=" + this.siteid + "&amp;classid=" + this.classid + "&amp;r=" + r + "\">刷新</a>|<a href=\"" + this.http_start + "chat/config.aspx?siteid=" + this.siteid + "&amp;classid=" + this.classid + "\">设置</a>|<a href=\"" + this.http_start + "chat/showadmin.aspx?siteid=" + this.siteid + "&amp;classid=" + this.classid + "\">聊管</a><br/>");
        strhtml.Append("<a href=\"" + this.http_start + "wapindex.aspx?siteid=" + this.siteid + "&amp;classid=" + classVo.childid + "&amp;r=" + r + "\">换房</a>|<a href=\"" + this.http_start + "bbs/ModifyNick.aspx?siteid=" + this.siteid + "&amp;backurl=" + HttpUtility.UrlEncode(this.GetUrlQueryString()) + "\">改名</a>|<a href=\"" + this.http_start + "chat/Exit.aspx?siteid=" + this.siteid + "&amp;classid=" + this.classid + "\">退出</a>|<a href=\"" + this.http_start + "bbs/onlist.aspx?siteid=" + this.siteid + "&amp;classid=" + this.classid + "\">在线</a><br/>");
        //显示列表
        strhtml.Append("----------<br/>");

        if (chatRob.ToString() != "")
        {
            strhtml.Append(chatRob);
            strhtml.Append("<br/>");
        }
        
    }
    else
    {

        strhtml.Append("<a href=\"" + this.http_start + "chat/book_list.aspx?siteid=" + this.siteid + "&amp;classid=" + this.classid + "&amp;r=" + r + "\">刷新</a>|<a href=\"" + this.http_start + "chat/config.aspx?siteid=" + this.siteid + "&amp;classid=" + this.classid + "\">设置</a>|");
        strhtml.Append("<a href=\"" + this.http_start + "chat/Exit.aspx?siteid=" + this.siteid + "&amp;classid=" + this.classid + "\">退出</a><br/>");
       
      
    }
        
    
    for (int i = 0; (listVo != null && i < listVo.Count); i++)
    {
        index = index + kk;


        strhtml.Append(string.Format("{0:HH:mm}", listVo[i].times) + "<a href=\"" + this.http_start + "chat/speak.aspx?siteid=" + this.siteid + "&amp;classid=" + this.classid + "&amp;touserid=" + listVo[i].userid + "&amp;tonickname=" + HttpUtility.UrlEncode(listVo[i].nickname) + "\">" + ShowNickName_color(listVo[i].userid, listVo[i].nickname) + "</a>对[" + listVo[i].tonickname + "]说:" + WapTool.GetOnlyImg(listVo[i].content.Replace("[br]", " ")) + "<br/>");
    }
    if (listVo == null)
    {
        strhtml.Append("暂时无人发言！<br/>");
    }
    
    //显示导航分页
    strhtml.Append(linkURL);
    if (this.par0 == "0" || (this.par0 == "1" && this.IsCheckManagerLvl("|00|01", classVo.adminusername) == true))
    {

        strhtml.Append("<select name=\"face" + r + "\" value=\"\">");
        strhtml.Append("<option value=\"\">表情</option>");
        strhtml.Append("<option value=\"1.gif\">胜利</option>");
        strhtml.Append("<option value=\"2.gif\">偷笑</option>");
        strhtml.Append("<option value=\"3.gif\">送花</option>");
        strhtml.Append("<option value=\"4.gif\">调皮</option>");
        strhtml.Append("<option value=\"5.gif\">欢迎</option>");
        strhtml.Append("<option value=\"6.gif\">微笑</option>");
        strhtml.Append("<option value=\"7.gif\">暴汗</option>");
        strhtml.Append("<option value=\"8.gif\">大笑</option>");
        strhtml.Append("<option value=\"9.gif\">晕了</option>");
        strhtml.Append("<option value=\"10.gif\">疑惑</option>");
        strhtml.Append("<option value=\"11.gif\">别吵</option>");
        strhtml.Append("<option value=\"12.gif\">流泪</option>");
        strhtml.Append("<option value=\"13.gif\">脸红</option>");
        strhtml.Append("<option value=\"14.gif\">色色</option>");
        strhtml.Append("<option value=\"15.gif\">惊讶</option>");
        strhtml.Append("<option value=\"16.gif\">上火</option>");
        strhtml.Append("<option value=\"17.gif\">飞吻</option>");
        strhtml.Append("<option value=\"18.gif\">摆酷</option>");
        strhtml.Append("<option value=\"19.gif\">睡觉</option>");
        strhtml.Append("<option value=\"20.gif\">闭嘴</option>");
        strhtml.Append("<option value=\"21.gif\">再见</option>");
        strhtml.Append("</select>");
        strhtml.Append("<input type=\"text\"  name=\"content" + r + "\" value=\"\" size=\"10\"/><br/>");
        strhtml.Append("<anchor><go href=\"" + http_start + "chat/book_list.aspx\" method=\"post\" accept-charset=\"utf-8\">");
        strhtml.Append("<postfield name=\"action\" value=\"add\"/>");
        strhtml.Append("<postfield name=\"classid\" value=\"" + classid + "\"/>");
        strhtml.Append("<postfield name=\"siteid\" value=\"" + siteid + "\"/>");
        strhtml.Append("<postfield name=\"tonickname\" value=\"大家\"/>");
        strhtml.Append("<postfield name=\"content\" value=\"$(content" + r + ")\"/>");
        strhtml.Append("<postfield name=\"face\" value=\"$(face" + r + ")\"/>");
        strhtml.Append("<postfield name=\"sid\" value=\"" + sid + "\"/>");
        strhtml.Append("</go>" + this.GetLang("快速发言|快速发言|submit") + "</anchor><br/>");
    }
    //显示广告
    if (adVo.secondShowDown != "")
    {
        strhtml.Append(adVo.secondShowDown+"<br/>");
    }
    //导航按钮   
    strhtml.Append("<a href=\"" + this.http_start + "wapindex.aspx?siteid=" + siteid + "&amp;classid=" + classVo.childid + "" + "\">返回上级</a> ");
    strhtml.Append("<a href=\"" + this.http_start + "wapindex.aspx?siteid=" + siteid + "&amp;classid=0" + "\">返回首页</a>");
    strhtml.Append(WapTool.GetVS(wmlVo));
    strhtml.Append("</p>");

    //输出
    Response.Write(WapTool.ToWML(strhtml.ToString(), wmlVo));
    
}
else //2.0界面
{

    %>
    <style>
* { margin: 0; padding: 0;list-style: none; }
body {line-height: 28px; font: 16px/30px "Microsoft YaHei"; margin-bottom: 0px; BACKGROUND: #e7e8eb; box-shadow: 0 0 15px #ccc;}
input { border-radius: 0px; }
form {/*display: inline;*/}
li, ul, ol { list-style: none; }
img { border: none; }
a { text-decoration: none; color: #006cbf; }
a:hover { color: #2b73df; text-decoration: none; }
	.main { max-width: 800px; margin: 0px auto; box-shadow: 0 7px 17px #ccc; background: rgb(238, 238, 238);}
	.subtitle { line-height: 40px; color: #000;letter-spacing: 0px; background: #FFF; ; padding-left: 20px; position:relative;box-shadow: 0 1px 1px #dcdcdc;}
	.subtitle::before { content: ""; position:absolute; left:10px; top:12.5px; height:15px; width:3px; background: #D3270F;}

	
/* 新版聊天系统样式开始 */
	.chat_list {background: #fff; overflow: hidden; padding: 10px 5px;}
	.chat_list li {position: relative; overflow: hidden; padding: 10px;}
	.chat_list li .chat_user_tx {position: absolute;left: 10px;top: 15px; }
	.chat_list li .chat_user_tx a,.chat_list li .chat_me_tx a {display: block; width: 60px; height: 60px; border-radius: 50%; overflow: hidden;}
	.chat_list li .chat_user_tx img,.chat_list li .chat_me_tx img {display: block; width: 100%; height: 100%;}
	.chat_list li .chat_user_name,.chat_list li .chat_me_name {float: left; margin-left: 75px; font-size: 15px; color: #333; line-height: 30px;}
	.chat_list li .chat_user_name a,.chat_list li .chat_me_name a {color: #333;}
	.chat_list li .chat_content,.chat_list li .chat_me_content { background: #E5E5EA; line-height: 25px;position: relative;color: #555;margin: 0 10px 0 75px; padding: 10px 15px; border-radius: 5px; word-wrap: break-word;overflow-wrap: break-word; float: left;clear: both; max-width: 60%; font-size: 14px;}
	.chat_list li .chat_time {float: right;font-size: 12px; color: #ccc; padding-top: 2px; text-align: right; margin-left: 15px;}
	.chat_list li .chat_content:before,.chat_list li .chat_me_content:before {content: ' '; position: absolute; width: 0; height: 0; left: -14px; top: 8px; border: 7px solid; border-color: transparent #E5E5EA transparent transparent;}
	.chat_list li .chat_me_tx { position: absolute; right: 10px; top: 15px;}
	.chat_list li .chat_me_name {float: right!important;margin-left: 0!important; margin-right: 75px;}
	.chat_list li .chat_me_content {background: #54DD81!important; color: #333!important; float: right !important;margin: 0 75px 0 10px !important;}
	.chat_list li .chat_me_content:before {left: auto !important; right: -14px;border-color: transparent transparent transparent #54DD81 !important;}
	.chat_list li .chat_tip {background: #F1F1F1; color: #bbb; font-size: 13px; text-align: center;    padding: 0px 15px; margin: 0 auto; display: table;border-radius: 5px;margin-bottom: -5px; margin-top: -5px;}
	.chat_fayan { padding: 15px 10px; border-bottom: 1px solid #EBEBEB; background: #F8F8F8;;}
	.chat_fayan_txt { width: 100%; padding: 10px; box-sizing: border-box; border-radius: 3px; border: 1px #E0E0E0 solid; font-size: 14px; color: #949494; height: 100px;}
	.chat_f-btn { overflow: hidden; padding: 5px 0;}
	.chat_fayan_select { padding: 7px 10px; box-sizing: border-box; border-radius: 3px; border: 1px #E0E0E0 solid; font-size: 14px; vertical-align: middle; margin: 0; font-family: inherit; float: left; height: 40px;}
	.chat_fayan_btn {float: right; height: 40px; background: #E23C3C; color: #fff; border: 0; border-radius: 3px; -webkit-border-radius: 3px; background: -webkit-linear-gradient(top, #E23C3C, #E23C3C); background: -moz-linear-gradient(top, #E23C3C, #E23C3C); background: -ms-linear-gradient(top, #E23C3C, #E23C3C); background: -o-linear-gradient(top, #E23C3C, #E23C3C); background: linear-gradient(top, #E23C3C, #E23C3C); font-size: 14px; padding: 0 25px; font-family: inherit;}
/* 新版聊天系统样式结束 */
</style>
    
    <%

    string isWebHtml = this.ShowWEB_list(this.classid); //看是存在html代码

    //显示广告
    if (adVo.secondShowTop != "")
    {
        strhtml.Append(adVo.secondShowTop);
    }
    strhtml.Append("<!--web-->");
    //strhtml.Append("<div class=\"subtitle\">"+classVo.classname+"</div>");
    if (this.classid == "0" && this.action != "search")
    {
        strhtml.Append("<div class=\"tip\"><b>栏目ID为0了，请重新选择房间。</b></div>");

    }
    else if (this.INFO == "WAITING")
    {
        strhtml.Append("<div class=\"tip\"><b>你发言太快了，请再过" + this.KL_CheckIPTime + "秒后操作！</b></div>");

    }
    else if (this.INFO == "LOCK")
    {

        strhtml.Append("<div class=\"tip\"><b>抱歉，你已经被禁言了！</b></div>");
    }
    else if (this.INFO == "NULL")
    {

        strhtml.Append("<div class=\"tip\"><b>抱歉，请输入发言内容！</b></div>");
    }
    else if (this.INFO == "ERR_FORMAT")
    {
        strhtml.Append("<div class=\"tip\"><b>取到非法值:“$$(”请更换手机浏览器或重新编辑！</b></div>");
    }
    
    if (classVo.siteimg != "UploadFiles/no.gif" && classVo.siteimg != "NetImages/no.gif")
    {
        strhtml.Append("<div class=\"logo\"><img src=\"" + http_start + classVo.siteimg + "\" alt=\"LOGO\"/></div>");
    }
    if (classVo.introduce != "")
    {
        strhtml.Append(classVo.introduce);
    }
   
    if (this.par0 == "0" || (this.par0 == "1" && this.IsCheckManagerLvl("|00|01", classVo.adminusername) == true))
    {
        %>
         
	<div class="chat_fayan">
		<form name="f" action="/chat/book_list.aspx" method="post">
			<textarea name="content" rows="3" class="chat_fayan_txt"></textarea>
			<div class="chat_f-btn">
			<select name="face" class="chat_fayan_select">
				<option value="">表情</option>
				<option value="1.gif">胜利</option>
				<option value="2.gif">偷笑</option>
				<option value="3.gif">送花</option>
				<option value="4.gif">调皮</option>
				<option value="5.gif">欢迎</option>
				<option value="6.gif">微笑</option>
				<option value="7.gif">暴汗</option>
				<option value="8.gif">大笑</option>
				<option value="9.gif">晕了</option>
				<option value="10.gif">疑惑</option>
				<option value="11.gif">别吵</option>
				<option value="12.gif">流泪</option>
				<option value="13.gif">脸红</option>
				<option value="14.gif">色色</option>
				<option value="15.gif">惊讶</option>
				<option value="16.gif">上火</option>
				<option value="17.gif">飞吻</option>
				<option value="18.gif">摆酷</option>
				<option value="19.gif">睡觉</option>
				<option value="20.gif">闭嘴</option>
				<option value="21.gif">再见</option>
			</select>
			<input type="hidden" name="action" value="add">
			<input type="hidden" name="classid" value="<%=classid %>">
			<input type="hidden" name="siteid" value="<%=siteid %>">
			<input type="hidden" name="sid" value="<%=sid %>">
			<input type="hidden" name="tonickname" value="">
			<input type="submit" name="g" class="chat_fayan_btn" value="发 言">
			</div>
		</form>
	</div>
        
        <%
        
    }


    strhtml.Append("<!--listS-->");
    strhtml.Append("<ul class=\"chat_list\">");
    
    if (chatRob.ToString() != "")
    {
        strhtml.Append("<li><div class=\"chat_tip\">");
        strhtml.Append(chatRob);
        strhtml.Append("</div></li>");
    }
    
   
    kk = kk + ((CurrentPage - 1) * pageSize) - 1;
    for (int i = 0; (listVo != null && i < listVo.Count); i++)
    {
        index = index + kk;

        if (listVo[i].userid.ToString() != this.userid)
        {
            strhtml.Append("<li><div class=\"chat_user_tx\">");
            strhtml.Append("<a href=\"" + this.http_start + "chat/speak.aspx?siteid=" + this.siteid + "&amp;classid=" + this.classid + "&amp;touserid=" + listVo[i].userid + "&amp;tonickname=" + HttpUtility.UrlEncode(listVo[i].nickname) + "\"><img src=\"" + WapTool.GetHeadImgURL(http_start, listVo[i].headimg) + "\"/></a>");
            strhtml.Append("</div>");
            strhtml.Append("<div class=\"chat_user_name\">");
            strhtml.Append("<a href=\"" + this.http_start + "chat/speak.aspx?siteid=" + this.siteid + "&amp;classid=" + this.classid + "&amp;touserid=" + listVo[i].userid + "&amp;tonickname=" + HttpUtility.UrlEncode(listVo[i].nickname) + "\">" + ShowNickName_color(listVo[i].userid, listVo[i].nickname) + "</a>");
            strhtml.Append("<span class=\"chat_time\">" + string.Format("{0:MM-dd HH:mm}", listVo[i].times) + " </span>");
            strhtml.Append("</div>");
            strhtml.Append("<div class=\"chat_content\">");
            strhtml.Append("<p>");
            if (listVo[i].tonickname != "")
            {
                strhtml.Append("@");
                strhtml.Append(listVo[i].tonickname );
            }
            strhtml.Append(" ");
            strhtml.Append(listVo[i].content);
            strhtml.Append("</p>");
            strhtml.Append("</div>");
            strhtml.Append("</li>");

        }
        else
        {

            strhtml.Append("<li><div class=\"chat_me_tx\">");
            strhtml.Append("<a href=\"" + this.http_start + "chat/speak.aspx?siteid=" + this.siteid + "&amp;classid=" + this.classid + "&amp;touserid=" + listVo[i].userid + "&amp;tonickname=" + HttpUtility.UrlEncode(listVo[i].nickname) + "\"><img src=\"" + WapTool.GetHeadImgURL(http_start, listVo[i].headimg) + "\"/></a>");
            strhtml.Append("</div>");
            strhtml.Append("<div class=\"chat_me_name\">");
            strhtml.Append("我");
            strhtml.Append("<span class=\"chat_time\">" + string.Format("{0:MM-dd HH:mm}", listVo[i].times) + "</span>");
            strhtml.Append("</div>");
            strhtml.Append("<div class=\"chat_me_content\">");
            strhtml.Append("<p>");
            if (listVo[i].tonickname != "")
            {
                strhtml.Append("@");
                strhtml.Append(listVo[i].tonickname );
            }
            strhtml.Append(" ");
            strhtml.Append(listVo[i].content.Replace("[br]"," "));
            strhtml.Append("</p>");
            strhtml.Append("</div>");
            strhtml.Append("</li>");
        }
        
        
            

        kk = kk + 1;
    
    }

    if (listVo==null)
    {
        strhtml.Append("<li><div class=\"chat_tip\">暂无无人发言！</div></li>");
    }

    strhtml.Append("</ul>");
    
    strhtml.Append("<!--listE-->");
    strhtml.Append("</div>");
    //显示导航分页
    strhtml.Append(linkURL);

    //strhtml.Append("<div class=\"content\">");

    if (this.IsCheckManagerLvl("|00|01", classVo.adminusername) == true)
    {
        strhtml.Append("<div class=\"btBox\"><div class=\"bt1\">");
        if (par0 == "1")
        {
            strhtml.Append("<a href=\"" + this.http_start + "chat/speak.aspx?siteid=" + this.siteid + "&amp;classid=" + this.classid + "\">点击这里发言</a> ");
        }
        strhtml.Append("<a href=\"" + this.http_start + "chat/classconfigall.aspx?siteid=" + this.siteid + "&amp;classid=" + this.classid + "\">更多栏目属性设置</a>");
        strhtml.Append("</div></div>");
    }

    if (par0 == "0")
    {

        strhtml.Append("<div class=\"btBox\"><div class=\"bt4\">");
        strhtml.Append("<a href=\"" + this.http_start + "chat/speak.aspx?siteid=" + this.siteid + "&amp;classid=" + this.classid + "\">发言</a> <a href=\"" + this.http_start + "chat/book_list.aspx?siteid=" + this.siteid + "&amp;classid=" + this.classid + "&amp;r=" + r + "\">刷新</a> <a href=\"" + this.http_start + "chat/config.aspx?siteid=" + this.siteid + "&amp;classid=" + this.classid + "\">设置</a> <a href=\"" + this.http_start + "chat/showadmin.aspx?siteid=" + this.siteid + "&amp;classid=" + this.classid + "\">聊管</a>");
        strhtml.Append("<a href=\"" + this.http_start + "wapindex.aspx?siteid=" + this.siteid + "&amp;classid=" + classVo.childid + "&amp;r=" + r + "\">换房</a> <a href=\"" + this.http_start + "bbs/ModifyNick.aspx?siteid=" + this.siteid + "&amp;backurl=" + HttpUtility.UrlEncode(this.GetUrlQueryString()) + "\">改名</a> <a href=\"" + this.http_start + "chat/Exit.aspx?siteid=" + this.siteid + "&amp;classid=" + this.classid + "\">退出</a> <a href=\"" + this.http_start + "bbs/onlist.aspx?siteid=" + this.siteid + "&amp;classid=" + this.classid + "\">在线</a>");
        strhtml.Append("</div></div>");


    }
    else
    {
        strhtml.Append("<div class=\"bt3\">");
        strhtml.Append("<a href=\"" + this.http_start + "chat/book_list.aspx?siteid=" + this.siteid + "&amp;classid=" + this.classid + "&amp;r=" + r + "\">刷新</a> <a href=\"" + this.http_start + "chat/config.aspx?siteid=" + this.siteid + "&amp;classid=" + this.classid + "\">设置</a> ");
        strhtml.Append("<a href=\"" + this.http_start + "chat/Exit.aspx?siteid=" + this.siteid + "&amp;classid=" + this.classid + "\">退出</a>");
        strhtml.Append("</div>");

    }

    //strhtml.Append("</div>");
    
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

    //显示广告
    if (adVo.secondShowDown != "")
    {
        strhtml.Append(adVo.secondShowDown );
    }
    //导航按钮
   
    if (action == "search")
    {
        strhtml.Append("<div class=\"btBox\"><div class=\"bt1\">");
        strhtml.Append("<a href=\"" + this.http_start + "chat/book_search.aspx?siteid=" + siteid + "&amp;classid=" + classid + "" + "\">返回搜索</a> ");
        strhtml.Append("</div></div>");
    }
    strhtml.Append("<div class=\"btBox\"><div class=\"bt2\">");
    strhtml.Append("<a href=\"" + this.http_start + "wapindex.aspx?siteid=" + siteid + "&amp;classid=" + classVo.childid + "" + "\">返回上级</a> ");
    strhtml.Append("<a href=\"" + this.http_start + "wapindex.aspx?siteid=" + siteid + "&amp;classid=0" + "\">返回首页</a>");
    strhtml.Append("</div></div>");

    //输出
    Response.Write(WapTool.ToWML(strhtml.ToString(), wmlVo));
                                                                                                                                                                           




}
                                                                                                                                                                               
Response.Write(ERROR);                                                                                                                                                                              
//显示底部
Response.Write(WapTool.showDown(wmlVo)); %>

