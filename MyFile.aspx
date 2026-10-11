<%@ Page Language="C#" AutoEventWireup="true" CodeBehind="MyFile.aspx.cs" Inherits="KeLin.WebSite.MyFile" %><%@ Import namespace="KeLin.ClassManager.Tool" %><%
    StringBuilder sb = new StringBuilder();
  
 //看是否有电脑版html代码
string isWebHtml = this.ShowWEB_list ("-1");
//显示头
Response.Write(WapTool.showTop(this.GetLang("我的地盘|我的地盤|my zone"), wmlVo));

if (this.ver == "1")
{
    Response.Write("<p>");
    if (strHtml.IndexOf("[myfile]") > -1)
    {
        sb.Append("欢迎您：" + WapTool.GetColorNickName(userVo.idname, userVo.nickname, lang, ver,userVo.endTime) + "<br/>");

        sb.Append("<a href=\"" + http_start + "bbs/messagelist.aspx?siteid=" + siteid + "&amp;classid=0&amp;types=0" + "\">信箱</a>:(" + messagecount + "/" + messageAll + ") <a href=\"" + http_start + "bbs/FriendList.aspx?siteid=" + siteid + "&amp;classid=0&amp;friendtype=0" + "\">好友</a>(" + goodfriend + ")" + "<br/>");
        sb.Append("我的ID: " + userVo.userid + WapTool.GetOLtimePic(http_start, siteVo.lvlTimeImg, userVo.LoginTimes) + "<br/>");
        sb.Append("用户名: " + userVo.username + " <a href=\"" + this.http_start + "waplogout.aspx?siteid=" + siteid + "" + "\">退出</a>" + "<br/>");

        sb.Append("我的" + WapTool.GetSiteMoneyName(siteVo.sitemoneyname, this.lang) + ":" + userVo.money + "" + "<br/>");
        sb.Append("<a href=\"" + http_start + "chinabank_wap/RMBtoMoney.aspx?siteid=" + siteid + "" + "\">购买" + siteVo.sitemoneyname + "</a>/<a href=\"" + this.http_start + "bbs/tomoney.aspx?siteid=" + this.siteid + "\">转帐</a>/<a href=\"" + http_start + "bbs/banklist.aspx?siteid=" + siteid + "&amp;classid=0&amp;key=" + this.userid + "" + "\">明细</a>" + "<br/>");

        sb.Append("银行存款:" + userVo.myBankMoney + " <a href=\"" + this.http_start + "bbs/tomybankmoney.aspx?siteid=" + this.siteid + "\">管理</a>" + "<br/>");
        sb.Append("我的经验:" + userVo.expr + "<br/>");
        sb.Append("我的等级:" + WapTool.GetLevl(siteVo.lvlNumer, userVo.expr, userVo.money, type) + "<br/>");
        sb.Append("我的头衔:" + WapTool.GetHandle(siteVo.lvlNumer, userVo.expr, userVo.money, type) + "<br/>");
        sb.Append("我的身份:" + WapTool.GetMyID(userVo.idname, lang, userVo.endTime) + "<br/><a href=\"" + this.http_start + "bbs/toGroupInfo.aspx?siteid=" + this.siteid + "\">" + WapTool.showIDEndTime(userVo.siteid, userVo.userid, userVo.endTime, this.lang) + "</a>" + "<br/>");
        sb.Append("管理权限:" + WapTool.GetIDName(siteid, this.userid, userVo.managerlvl, this.lang) + "<br/>");

        sb.Append("我的勋章:" + WapTool.GetMedal(userVo.userid.ToString(), userVo.moneyname, WapTool.GetSiteDefault(siteVo.Version, 47), wmlVo) + "<br/>");


        sb.Append("<a href=\"" + http_start + "xinzhang/book_list.aspx?siteid=" + siteid + "&amp;classid=0" + "\">购买勋章</a>/<a href=\"" + http_start + "xinzhang/book_view_my.aspx?siteid=" + siteid + "&amp;classid=0" + "\">管理勋章</a>" + "<br/>");



        sb.Append("我的RMB:￥" + userVo.RMB.ToString("f2") + "<br/><a href=\"" + http_start + "chinabank_wap/selbank_wap.aspx?siteid=" + siteid + "" + "\">在线充值</a>/<a href=\"" + http_start + "chinabank_wap/banklist.aspx?siteid=" + siteid + "&amp;tositeid=" + this.siteid + "&amp;touserid=" + this.userid + "" + "\">明细</a>" + "<br/>");
    
    //sb.Append("电子邮箱 通讯地址" + "<br/>");
    sb.Append("我的设置" + "<br/>");
    sb.Append("<a href=\"" + this.http_start + "bbs/userinfo.aspx?siteid=" + this.siteid + "&amp;touserid=" + this.userid + "\">我的空间</a> <a href=\"" + this.http_start + "bbs/modifyuserinfo.aspx?siteid=" + siteid + "" + "\">修改资料</a>" + "<br/>");
    sb.Append("<a href=\"" + http_start + "bbs/myfav.aspx?siteid=" + siteid + "&amp;classid=0" + "\">我的收藏</a> <a href=\"" + http_start + "album/myalbum.aspx?siteid=" + siteid + "&amp;touserid=" + this.userid + "" + "\">我的相册</a>" + "<br/>");
    sb.Append("<a href=\"" + http_start + "rizhi/book_list.aspx?siteid="+this.siteid+"&amp;classid=0&amp;myfriends=1\">我的朋友圈</a> <a href=\"" + this.http_start + "bbs/userguessbook.aspx?action=search&amp;siteid=" + this.siteid + "&amp;classid=" + this.classid + "&amp;touserid=" + this.userid + "\">留言管理</a>" + "<br/>");
    sb.Append("<a href=\"" + this.http_start + "bbs/toDefine.aspx?siteid=" + this.siteid + "\">浏览定制</a> <a href=\"" + this.http_start + "bbs/shareSite.aspx?siteid=" + this.siteid + "\">推荐奖励</a>" + "<br/>");

    sb.Append("我的相关信息" + "<br/>");
    sb.Append("<a href=\"" + this.http_start + "bbs/book_list.aspx?action=search&amp;siteid=" + this.siteid + "&amp;classid=0&amp;key=" + this.userid + "&amp;type=pub\">我的帖子(" + userVo.bbsCount + ")</a> <a href=\"" + this.http_start + "bbs/book_re_my.aspx?action=class&amp;siteid=" + siteid + "&amp;classid=0&amp;touserid=" + userid + "&amp;" + "\">回复帖(" + userVo.bbsReCount + ")</a>" + "<br/>");
    sb.Append("<a href=\"" + http_start + "bbs/FriendList.aspx?siteid=" + siteid + "&amp;classid=0&amp;friendtype=2" + "\">我的追求</a> <a href=\"" + http_start + "bbs/FriendList.aspx?siteid=" + siteid + "&amp;classid=0&amp;friendtype=4" + "\">追求我的人</a>" + "<br/>");
    sb.Append("<a href=\"" + http_start + "yuehui/book_list.aspx?key=" + userid + "&amp;type=touserid&amp;action=search&amp;siteid=" + this.siteid + "&amp;classid=0\">我的约会</a> <a href=\"" + http_start + "bbs/FriendList.aspx?siteid=" + siteid + "&amp;classid=0&amp;friendtype=1" + "\">我的黑名单</a>" + "<br/>");
    sb.Append("<a href=\"" + http_start + "clan/main.aspx?siteid=" + this.siteid + "&amp;classid=0\">我的家族</a> <a href=\"" + http_start + "fun/marry.asp?siteid=" + this.siteid + "&amp;classid=0&amp;sid="+this.sid+"\">婚姻家汽车</a>" + "<br/>");

    sb.Append("网站规则" + "<br/>");
    sb.Append("<a href=\"" + this.http_start + "bbs/tomoneyinfo.aspx?siteid=" + this.siteid + "\">" + siteVo.sitemoneyname + "获取消费规则</a><br/><a href=\"" + this.http_start + "bbs/tolvlinfo.aspx?siteid=" + this.siteid + "\">经验头衔等级规则</a><br/><a href=\"" + this.http_start + "bbs/toGroupInfo.aspx?siteid=" + this.siteid + "" + "\">购买身份级别规则</a><br/><a href=\"" + this.http_start + "bbs/toXunZhangInfo.aspx?siteid=" + this.siteid + "" + "\">自动奖励勋章规则</a><br/><a href=\"" + this.http_start + "bbs/totimeinfo.aspx?siteid=" + this.siteid + "" + "\">在线时间图标规则</a>" + "<br/>");
    
        strHtml = sb.ToString();
    }
    

    Response.Write(WapTool.ToWML(strHtml, wmlVo));
    
    bool isclassadm = WapTool.isClassAdmin(siteid, userid);
    if ("|00|01|03|".IndexOf(userVo.managerlvl) > 0 || isclassadm == true) Response.Write("网站管理后台<a href=\"" + this.http_start + "admin/loginwap.aspx?siteid=" + this.siteid + "\">&gt;&gt;</a>" + "<br/>");

    if (userVo.managerlvl == "00" || userVo.managerlvl == "01")
    {
        Response.Write("[<a href=\"" + this.http_start + "admin/basesitemodifywml.aspx?siteid=" + siteid + "" + "\">站长管理后台</a>]" + "<br/>");
    }
    if (userVo.managerlvl == "00")
    {

        Response.Write("[<a href=\"" + this.http_start + "admin/basesitemodifywml00.aspx?siteid=" + siteid + "" + "\">超级管理员后台</a>]" + "<br/>");
    }
    if (userVo.managerlvl == "03")
    {

        Response.Write("[<a href=\"" + this.http_start + "admin/admin_waplist.aspx?siteid=" + siteid + "" + "\">总编辑管理后台</a>]" + "<br/>");
    }
    if (isclassadm == true)
    {

        Response.Write("[<a href=\"" + this.http_start + "admin/admin_waplist.aspx?siteid=" + siteid + "" + "\">栏目管理员后台</a>]" + "<br/>");
    }


    Response.Write("[<a href=\"" + this.http_start + "waplogout.aspx?siteid=" + siteid + "" + "\">安全退出</a>" + "-");
    Response.Write("<a href=\"" + this.http_start + "wapindex.aspx?siteid=" + siteid + "&amp;classid=" + classid + "" + "\">返回上级</a>]");
  
    Response.Write(WapTool.GetVS(wmlVo));
    Response.Write("</p>");
}
else
{

    //显示中间部分
    if (strHtml.IndexOf("[myfile]") > -1 || isWebHtml.IndexOf("[view]") > -1)
    {

        //sb.Append("<div class=\"main\">");
        sb.Append("<div class=\"myfile\">");
        sb.Append("<div class=\"filetop\">");
        sb.Append("<span class=\"Home\"><a href=\"/wapindex.aspx?siteid="+this.siteid+"\"> </a></span>");
        sb.Append("我的地盘");
        sb.Append("<span class=\"Back\"><a href=\"javascript:window.history.back();\"> </a></span>");
        sb.Append("</div>");
        sb.Append("<div class=\"fileuser\">");
        sb.Append("<div class=\"Head\"> " + WapTool.GetHeadImgHTML(http_start, userVo.headimg) + "</div>");
        sb.Append("<div class=\"r-item\">");
        sb.Append(" <div class=\"name\">"+userVo.nickname+" <span class=\"Sex\">");
        if (userVo.sex == 1)
        {
            sb.Append("<img src=\"/Template/default/useinfo/Mr.png\">");
        }
        else
        {
            sb.Append("<img src=\"/Template/default/useinfo/Miss.png\">");
        }

        sb.Append("</span></div>");
        sb.Append(" <p>"+userVo.remark+"</p>");
        sb.Append("</div>");
        sb.Append("</div>");

        sb.Append("<div class=\"type mb\"> <a href=\"" + http_start + "bbs/messagelist.aspx?siteid=" + siteid + "&amp;classid=0&amp;types=0" + "\">信箱");
        if (long.Parse(messagecount) > 0)
        {
            sb.Append("<span class=\"Mail\">" + messagecount + "</span>");
        }
        sb.Append("</a> <a href=\"" + http_start + "bbs/FriendList.aspx?siteid=" + siteid + "&amp;classid=0&amp;friendtype=0" + "\">好友</a> <a href=\"" + http_start + "bbs/myfav.aspx?siteid=" + siteid + "&amp;classid=0" + "\">收藏</a> <a href=\"" + this.http_start + "bbs/userinfo.aspx?siteid=" + this.siteid + "&amp;touserid=" + this.userid + "\">空间</a> </div>");
        sb.Append("<div class=\"list\"><li>ID</li> <p class=\"Output\">" + userVo.userid + "</p></div>");
        sb.Append("<div class=\"list\"><li>用户名</li> <p class=\"Output\">" + userVo.username+ "</p></div>");
        sb.Append("<div class=\"list\"><li>身份</li> <p class=\"Output\">"+WapTool.GetMyID(userVo.idname, lang,userVo.endTime) + " <a href=\"" + this.http_start + "bbs/toGroupInfo.aspx?siteid=" + this.siteid + "\">" + WapTool.showIDEndTime(userVo.siteid, userVo.userid, userVo.endTime, this.lang) + "</a></p></div>");
        sb.Append("<div class=\"list\"><li>权限</li> <p class=\"Output\">"+ WapTool.GetIDName(siteid, this.userid, userVo.managerlvl, this.lang) +"</p></div>");
        sb.Append("<div id=\"myfilejb\">");
        sb.Append("<div class=\"list\"><li>" + WapTool.GetSiteMoneyName(siteVo.sitemoneyname, this.lang) + "</li> <div class=\"filezk\"> </div><p class=\"Output\">" + userVo.money + "</p></div>");
        sb.Append("</div>");
        sb.Append("<div class=\"listcd\" id=\"Money\" style=\"display:none;\">");
        sb.Append("<a href=\"" + http_start + "bbs/banklist.aspx?siteid=" + siteid + "&amp;classid=0&amp;key=" + this.userid + "" + "\">明细</a>");
        sb.Append("<a href=\"" + http_start + "chinabank_wap/RMBtoMoney.aspx?siteid=" + siteid + "" + "\">购买</a>");
        
        sb.Append("<a href=\"" + this.http_start + "bbs/tomoney.aspx?siteid=" + this.siteid + "\">转账</a>");
        sb.Append("<a href=\"/bbs/tomybankmoney.aspx?siteid=" + this.siteid + "\">银行</a>");
        sb.Append("</div>");

        
        sb.Append("<div id=\"myfilermb\">");
        sb.Append("<div class=\"list\"><li>RMB</li> <div class=\"filezk\"> </div><p class=\"Output\">"+userVo.RMB.ToString("f2")+"</p></div>");
        sb.Append("</div>");
        sb.Append("<div class=\"listrmb\" id=\"rmb\" style=\"display:none;\">");
        sb.Append("<a href=\"" + http_start + "chinabank_wap/banklist.aspx?siteid=" + siteid + "&amp;tositeid=" + this.siteid + "&amp;touserid=" + this.userid  + "\">明细</a>");
        sb.Append("<a href=\"" + http_start + "chinabank_wap/selbank_wap.aspx?siteid=" + siteid  + "\">充值</a>");
        sb.Append("</div>");

        sb.Append("<div class=\"list\"><li>经验</li> <p class=\"Output\">"+userVo.expr+"</p></div>");
        sb.Append("<div class=\"list\"><li>等级</li> <p class=\"Output\">" + WapTool.GetLevl(siteVo.lvlNumer, userVo.expr, userVo.money, type) + "</p></div>");
        sb.Append("<div class=\"list\"><li>头衔</li> <p class=\"Output\">" + WapTool.GetHandle(siteVo.lvlNumer, userVo.expr, userVo.money, type) + "</p></div>");

        sb.Append("<div class=\"list mb\"><li>勋章</li> <p class=\"Output\">" + WapTool.GetMedal(userVo.userid.ToString(), userVo.moneyname, WapTool.GetSiteDefault(siteVo.Version, 47), wmlVo) + " <a href=\"" + http_start + "xinzhang/book_list.aspx?siteid=" + siteid + "&amp;classid=0" + "\">管理</a></p></div>");

        sb.Append("<div class=\"list\"><a href=\"" + this.http_start + "bbs/book_list.aspx?action=search&amp;siteid=" + this.siteid + "&amp;classid=0&amp;key=" + this.userid + "&amp;type=pub\"><i><img src=\"/Template/default/img/tiezi.png\"></i><span>我的帖子</span></a><p class=\"Output\">" + userVo.bbsCount + "</p></div>");
        sb.Append("<div class=\"list\"><a href=\"" + this.http_start + "bbs/book_re_my.aspx?action=class&amp;siteid=" + siteid + "&amp;classid=0&amp;touserid=" + userid + "\"> <i><img src=\"/Template/default/img/huitie.png\"></i><span>我的回帖</span> </a><p class=\"Output\">" + userVo.bbsReCount + "</p></div>");
        sb.Append("<div class=\"list\"><a href=\"" + http_start + "album/myalbum.aspx?siteid=" + siteid + "&amp;touserid=" + this.userid + "\"> <i><img src=\"/Template/default/img/img.png\"></i><span>我的相册</span></a> </div>");
        sb.Append("<div class=\"list\"><a href=\"" + http_start + "rizhi/book_list.aspx?siteid=" + this.siteid + "&amp;classid=0&amp;myfriends=1\"> <i><img src=\"/Template/default/img/weibo.png\"></i><span>我的朋友圈</span></a> </div>");
        sb.Append("<div class=\"list\"><a href=\"" + http_start + "clan/main.aspx?siteid=" + this.siteid + "&amp;classid=0\"> <i><img src=\"/Template/default/img/jiazu.png\"></i><span>我的家族</span></a> </div>");
        sb.Append("<div class=\"list\"><a href=\"" + this.http_start + "bbs/userguessbook.aspx?action=search&amp;siteid=" + this.siteid + "&amp;classid=" + this.classid + "&amp;touserid=" + this.userid + "\"> <i><img src=\"/Template/default/img/icon_friendsnews.png\"></i><span>留言管理</span></a> </div>");
        sb.Append("<div class=\"list mb\"><a href=\"" + this.http_start + "bbs/modifyuserinfo.aspx?siteid=" + siteid + "\"> <i><img src=\"/Template/default/img/icon_qiandao.png\"></i><span>修改资料</span></a> </div>");
        sb.Append("<div id=\"myfilemore\">");
        sb.Append("<div class=\"list\"> <i><img src=\"/Template/default/img/shezhi.png\"></i><span>其他功能</span> <div class=\"filezk\"> </div></div>");
        sb.Append("</div>");
        sb.Append("<div class=\"listmore\" id=\"more\" style=\"display:none;\">");
        sb.Append("<a href=\"/bbs/FriendList.aspx?siteid=" + siteid + "&amp;classid=0&amp;friendtype=2\">我的追求</a><a href=\"/bbs/FriendList.aspx?siteid=" + siteid + "&amp;classid=0&amp;friendtype=4\">追求我的</a>");
        sb.Append("<a href=\"/yuehui/book_list.aspx?key=" + userid + "&amp;type=touserid&amp;action=search&amp;siteid=" + this.siteid + "&amp;classid=0\">我的约会</a><a href=\"/bbs/FriendList.aspx?siteid=" + siteid + "&amp;classid=0&amp;friendtype=1\">加黑名单</a>");
        sb.Append("<a href=\"/bbs/shareSite.aspx?siteid=" + this.siteid + "\">推荐奖励</a><a href=\"/fun/marry.asp?siteid=" + this.siteid + "&amp;classid=0&amp;sid=" + this.sid + "\">婚姻汽车</a>");
        sb.Append("<a href=\"/bbs/tomoneyinfo.aspx?siteid=" + this.siteid + "\">" + siteVo.sitemoneyname + "获取消费</a><a href=\"/bbs/tolvlinfo.aspx?siteid=" + this.siteid + "\">经验头衔等级</a>");
        sb.Append("<a href=\"/bbs/toGroupInfo.aspx?siteid=" + this.siteid + "\">购买身份级别</a><a href=\"/bbs/toXunZhangInfo.aspx?siteid=" + this.siteid + "\">自动奖励勋章</a>");
        sb.Append("<a href=\"/bbs/totimeinfo.aspx?siteid=" + this.siteid + "\">在线时间图标</a><a href=\"/bbs/toDefine.aspx?siteid=" + this.siteid + "\">页面浏览定制</a>");
        sb.Append("</div>");


        //sb.Append("<div class=\"list mb\"><a href=\"/bbs/toDefine.aspx?siteid=" + this.siteid + "\"> <i><img src=\"/Template/default/img/desktop_history.png\"></i><span>浏览定制</span></a> </div>");
         bool isclassadm = WapTool.isClassAdmin(siteid, userid);

         if ("|00|01|03|".IndexOf(userVo.managerlvl) > 0 || isclassadm == true)
         {
             if (userVo.managerlvl == "00" || userVo.managerlvl == "01")
             {
                 sb.Append("<div class=\"list\"><a href=\"" + this.http_start + "admin/basesitemodifywml.aspx?siteid=" + siteid + "" + "\"> <i1>1</i1><span>站长管理后台</span></a> </div>");
             }
             if (userVo.managerlvl == "00")
             {

                 sb.Append("<div class=\"list\"><a href=\"" + this.http_start + "admin/basesitemodifywml00.aspx?siteid=" + siteid + "" + "\"> <iA>A</iA><span>超级管理员后台</span></a> </div>");
             }
             if (userVo.managerlvl == "03")
             {

                 sb.Append("<div class=\"list\"><a href=\"" + this.http_start + "admin/admin_waplist.aspx?siteid=" + siteid + "" + "\"> <i3>3</i3><span>总编辑管理后台</span></a> </div>");
             }
             if (isclassadm == true)
             {

                 sb.Append("<div class=\"list\"><a href=\"" + this.http_start + "admin/admin_waplist.aspx?siteid=" + siteid + "" + "\"> <iM>M</iM><span>栏目管理员后台</span></a> </div>");
             }

             sb.Append("<div class=\"list mb\"><a href=\"/admin/loginwap.aspx?siteid=" + this.siteid + "\"> <i><img src=\"/Template/default/img/admin.png\"></i><span>管理后台说明</span></a> </div>");
         }

         sb.Append("<div class=\"list\"><a href=\"/waplogout.aspx?siteid=" + siteid + "\"> <i><img src=\"/Template/default/img/Logout.png\"></i><span>注销登录</span></a> </div>");

         sb.Append("<script type=\"text/javascript\">\n");
         sb.Append("document.getElementById(\"myfilejb\").onclick = function(){myfileShow(\"Money\")};\n");
         sb.Append("document.getElementById(\"myfilermb\").onclick = function(){myfileShow(\"rmb\")};\n");
         sb.Append("document.getElementById(\"myfilemore\").onclick = function(){myfileShow(\"more\")};\n");
         sb.Append("function myfileShow(id){\n");
         sb.Append("	  var oRmb = document.getElementById(id);\n");
         sb.Append("	  if(oRmb.style.display == \"block\"){\n");
         sb.Append("		  oRmb.style.display = \"none\";\n");
         sb.Append("	  }else{\n");
         sb.Append("	  oRmb.style.display = \"block\";\n");
         sb.Append("  }\n");
         sb.Append("}\n");
         sb.Append("</script>\n");
        
         sb.Append("</div>");
        
        strHtml = strHtml.Replace("[myfile]", sb.ToString());



        isWebHtml = isWebHtml.Replace("[view]", sb.ToString());



    }
    //显示电脑效果
    if (isWebHtml != "")
    {
        Response.Clear();
        Response.Write(WapTool.ToWML(isWebHtml, wmlVo));
        Response.End();

    }

    //解析UBB方法:
    if (strHtml.Trim() == "")
    {
        Response.Write("<b>请在WEB/WAP后台---页面综合排版---编辑我的地盘---[顶] 录入[myfile]或自己排版</b><br/>");
    }
    else
    {
        Response.Write(WapTool.ToWML(strHtml, wmlVo));
    }




   
    Response.Write(WapTool.GetVS(wmlVo));

}                                                                                                                                                                                                                                                                                                                            
//显示尾

Response.Write(WapTool.showDown(wmlVo));                                                                                                                                                                                                                      
%>
