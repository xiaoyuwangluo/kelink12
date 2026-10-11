<%@ Page Language="C#" Debug="true" AutoEventWireup="true" CodeBehind="UserInfo.aspx.cs" Inherits="KeLin.WebSite.bbs.UserInfo" %>

<%@ Import Namespace="KeLin.ClassManager.Tool" %>

<%
    Response.Write(WapTool.showTop(this.GetLang(toUserVo.nickname + "空间|" + toUserVo.nickname + "空间|" + toUserVo.nickname + " Zome"), wmlVo));//显示头                                                                                                                                            
    string TA = "TA";
    string nicknamelink = this.http_start + "bbs/ModifyNick.aspx?siteid=" + siteid ;
    if (this.userid == touserid)
    {
        TA = "我";
    }
    else
    {
        nicknamelink = http_start + "bbs/userinfomore.aspx?siteid=" + this.siteid + "&touserid=" + touserid;
        if (toUserVo.sex.ToString() == "1")
        {
            TA = "他";
        }
        else if (toUserVo.sex.ToString() == "0")
        {
            TA = "她";
        }
    }
%>


<div class="userinfoqj">

    <div class="userinfotop">
        <li><%Response.Write("<a href=\"" + http_start + "wapindex.aspx?siteid=" + siteid + "" + "\"> <img src=\"/Template/default/useinfo/home.png\" alt=\".\" /></a>"); %>
        </li>
        <% Response.Write(toUserVo.nickname); %>的个人空间
    <y><%Response.Write("<a href=\"javascript:window.history.back();\"><img src=\"/Template/default/useinfo/fh.png\" alt=\".\"/></a>"); %></y>
    </div>
    <%if (userid == touserid)
      { %>
    <div class="userinfobg" id="userinfobg" onclick="window.open('<%Response.Write(http_start); %>background/book_list.aspx?siteid=<%Response.Write(siteid); %>&classid=<%Response.Write(classid); %>','_self')" style ="background-image:url(<% Response.Write(WapTool.GetBGimage(siteid,touserid)); %>);">
        <%}
      else
      { %>
          <div class="userinfobg" id="userinfobg" style ="background-image:url(<% Response.Write(WapTool.GetBGimage(siteid,touserid)); %>);">
        <%} %>
        <y>粉丝:<% Response.Write(this.fans); %><br/>
人气:<%Response.Write(toUserVo.zoneCount); %></y>

        <%Response.Write("<a href=\"" + this.http_start + "bbs/FriendList.aspx?action=addfriend&amp;friendtype=0&amp;siteid=" + this.siteid + "&amp;touserid=" + touserid + "&amp;backurl=" + HttpUtility.UrlEncode(backurl) + "\" class=\"userinfozan\"></a>"); %>
        <%Response.Write("<a href=\"/bbs/messagelist_add.aspx?action=gomod&touseridlist=" + touserid + "&siteid=" + this.siteid + "&classid=" + this.classid + "\" class=\"userinfoxin\" style=\"color: #fff;\">发私信</a>"); %>
    </div>
    <div class="userinfotx">
        <li><%Response.Write(WapTool.GetHeadImgHTML(http_start, toUserVo.headimg)); %></li>
        <span><% Response.Write("<a href=\"" + nicknamelink + "\">" + toUserVo.nickname + "</a>  "); %>
            <%if (toUserVo.sex == 1 && toUserVo.isonline == "1")
              { 
                 Response.Write("<img src=\"/Template/default/useinfo/mr.png\" alt=\".\" />");
               } else if (toUserVo.sex == 1 && toUserVo.isonline == "0")
              {

                 Response.Write("<img src=\"/Template/default/useinfo/mr-off.png\" alt=\".\" />");
              } else if (toUserVo.sex == 0 && toUserVo.isonline == "1")
              {

                 Response.Write("<img src=\"/Template/default/useinfo/miss.png\" alt=\".\" />");
           
              
              }else{

                  Response.Write("<img src=\"/Template/default/useinfo/miss-off.png\" alt=\".\" />");
           
            } %>
            <p>
                <%Response.Write(WapTool.ToWML(toUserVo.remark, wmlVo)); %>
            </p>
        </span>
    </div>
    <div class="userinfohxcd">
        <li>帖子<br>
            <% Response.Write("<a href=\"" + this.http_start + "bbs/book_list.aspx?action=search&amp;siteid=" + this.siteid + "&amp;classid=0&amp;key=" + this.touserid + "&amp;type=pub\">" + toUserVo.bbsCount + "</a>"); %></li>
        <li>回复<br>
            <% Response.Write("<a href=\"" + this.http_start + "bbs/book_re_my.aspx?action=class&amp;siteid=" + siteid + "&amp;classid=0&amp;touserid=" + this.touserid + "" + "\">" + toUserVo.bbsReCount + "</a>"); %></li>
        <li>照片<br>
            <%                
                Response.Write("<a href=\"" + this.http_start + "album/myalbum.aspx?siteid=" + this.siteid + "&amp;classid=0&amp;touserid=" + this.touserid + "\">" + this.albumCount + "</a>"); %></li>
        <li>说说<br>
            <%
                Response.Write("<a href=\"" + this.http_start + "rizhi/book_list.aspx?siteid="+this.siteid+"&classid=0&myfriends=0&touserid="+this.touserid+"\">" + this.rizhiCount + "</a>"); %></li>
        <li>留言<br>
            <%
                Response.Write("<a href=\"" + this.http_start + "bbs/userguessbook.aspx?action=search&amp;siteid=" + this.siteid + "&amp;classid=" + this.classid + "&amp;touserid=" + this.touserid + "\">" + this.guessBookCount + "</a>"); %></a></li>
    </div>
    <div class="userinfolcd">
        <ul>
            <li>
                <p>ID　</p>
                <span>
<% Response.Write(toUserVo.userid); %><% Response.Write(WapTool.GetOLtimePic(this.http_start, siteVo.lvlTimeImg, toUserVo.LoginTimes)); %>
                </span>
            </li>
            <li>
                <p>荣誉</p>
                <span>
                    <%Response.Write(WapTool.GetMedal(toUserVo.userid.ToString(), toUserVo.moneyname, WapTool.GetSiteDefault(siteVo.Version, 47), wmlVo)); %>
                </span>
                <% if (this.isAdmin == true)
                      { %>
                <y><a href="<%Response.Write(this.http_start + "xinzhang/book_view_my.aspx?siteid=" + siteid + "&classid=" + classid); %>">管理</a></y>
                <%} %>
            </li>
            <li>
                <p>经验</p>
                <span><% Response.Write(toUserVo.expr);%>(<% Response.Write(WapTool.GetLevl(siteVo.lvlNumer, toUserVo.expr, toUserVo.money, type));%> <%Response.Write(" <a href=\"" + http_start + "bbs/tolvlInfo.aspx?siteid=" + siteid + "&backurl=bbs%2fuserinfo.aspx%3fsiteid%3d" + siteid + "%26amp%3btouserid%3d" + userid + "\">" + WapTool.GetHandle(siteVo.lvlNumer, toUserVo.expr, toUserVo.money, type)); %></a>)</span></li>
            <li>
                <p>金币</p>
                <span><% Response.Write(toUserVo.money);%>
                    <%Response.Write(" <a href=\"" + this.http_start + "bbs/tomoney.aspx?siteid=" + this.siteid + "&amp;backurl=" + HttpUtility.UrlEncode("bbs/userinfo.aspx?siteid=" + this.siteid + "&amp;touserid=" + this.touserid) + "\">转</a>/<a href=\"" + this.http_start + "bbs/tomybankmoney.aspx?siteid=" + this.siteid + "&amp;backurl=" + HttpUtility.UrlEncode("bbs/userinfo.aspx?siteid=" + this.siteid + "&amp;touserid=" + this.touserid) + "\">存</a>");
                      if (this.isAdmin == true)
                      {
                          Response.Write("/<a href=\"" + this.http_start + "bbs/banklist.aspx?siteid=" + this.siteid + "&amp;classid=" + this.classid + "&amp;key=" + touserid + "\">明细</a>/<a href=\"" + this.http_start + "/chinabank_wap/banklist.aspx?siteid=" + this.siteid + "&amp;sid=" + this.sid + "\">RMB</a>");
                      } %>
                    <!--这个在用户中心操作即可a href="#">转</!--a>/<a href="#">存</a>/<a href="#">明细</a>/<a href="#">RMB</a></span></li-->
            <li>
                <p>身份</p>
                <span><a href="#"><%Response.Write("<a href=\"" + this.http_start + "bbs/toGroupInfo.aspx?siteid=" + this.siteid + "\">" + WapTool.GetMyID(toUserVo.idname, this.lang) + "</a>");%></a> <%if (toUserVo.sex == 1) { Response.Write(this.GetLang("男|男|Male")); } else { Response.Write(this.GetLang("女|女|Female")); } %>  <%Response.Write(" " + toUserVo.age); %>岁</span></li>
            <li>
                <p>权限</p>
                <span><%Response.Write(idtype); %></span></li>
            <%if (this.AdminClass != "")
              { %>
            <li class="banzhu">
                <p>版主</p>
                <span><%Response.Write(this.AdminClass); %></span></li>
            <%} %>
            <li class="banzhu">
                <p>家族</p>
                <span><a href="#"><%Response.Write(this.showClan); %></a></span></li>
        </ul>
    </div>
    <div class="userinfoerjicd">
        <li><%Response.Write("<a href=\"" + http_start + "yuehui/book_list.aspx?key=" + userid + "&type=touserid&action=search&siteid=" + siteid + "&classid=" + classid + "\">"); %>
            <img src="/Template/default/useinfo/ql.png" alt="." /><br/>情侣</a></li>
        <li><%Response.Write("<a href=\"" + http_start + "bbs/FriendList.aspx?siteid=" + siteid + "&classid=" + classid + "&friendtype=0&backurl=" + HttpUtility.UrlEncode(backurl) + "\">"); %>
            <img src="/Template/default/useinfo/haoy.png" alt="." /><br/>好友</a></li>
 
        <li><%Response.Write("<a href=\"" + http_start + "guangbo/Book_List.aspx?siteid=" + this.siteid + "&amp;classid=0&amp;touserid=" + this.touserid + "\">"); %>
            <img src="/Template/default/useinfo/guangbo.png" alt="." /><br/>广播</a></li>
        <li><%Response.Write("<a href=\"" + this.http_start + "bbs/userinfomore.aspx?siteid=" + this.siteid + "&amp;touserid=" + this.touserid + "&amp;backurl=" + HttpUtility.UrlEncode(backurl) + "\">"); %>
            <img src="/Template/default/useinfo/ziliao.png" alt="." /><br/>资料</a></li>
    </div>
    <div class="userinfoxcd">
        <ul>
            <div class="userinfoxc">
                <div class="userinfoxcti">
                    <p><%Response.Write(TA); %>的动态</p>
                    <y><%Response.Write("<a  href=\"" + http_start + "bbs/book_list_log.aspx?action=my&amp;siteid=" + siteid + "&amp;classid=0&amp;touserid=" + this.touserid + "" + "\">更多</a>"); %></y>
                </div>
                <div class="userinfoxcimg">
                    <%
                        for (int i = 0; (this.loglistVo != null && i < loglistVo.Count); i++)
                        {
                            Response.Write(WapTool.DateToString(loglistVo[i].oper_time, lang, 1) + "前" + loglistVo[i].log_info.Replace("[sid]", this.sid) + "<br/>");
                        }
                        if (loglistVo == null)
                        {
                            Response.Write("(暂无动态)");
                        }
                    %>
                </div>
            </div>
        </ul>
    </div>
    <div class="userinfoxcd">
        <ul>
            <div class="userinfoxc">
                <div class="userinfoxcti">
                    <p><%Response.Write(TA); %>的微博</p>
                    <y><%Response.Write("<a href=\"" + this.http_start + "rizhi/book_list.aspx?siteid=" + this.siteid + "&classid=0&myfriends=0&touserid=" + this.touserid + "\">更多</a>"); %></y>
                </div>
                <div class="userinfoline5">
                    <%
                        for (int i = 0; (this.sRZlistVo != null && i < sRZlistVo.Count); i++)
                        {
                            if (sRZlistVo[i].book_title.Length > 20) sRZlistVo[i].book_title = sRZlistVo[i].book_title.Substring(0, 20);
                            Response.Write("<a href=\"" + http_start + "rizhi/book_view.aspx?siteid=" + siteid + "&amp;classid=" + sRZlistVo[i].book_classid + "&amp;id=" + sRZlistVo[i].id + "" + "\">" + sRZlistVo[i].book_title + "</a><br/>");
                        }

                        if (sRZlistVo == null)
                        {
                            Response.Write("(暂无微博)");
                        }
                    %>
                </div>
            </div>
        </ul>
    </div>
    <div class="userinfoxcd">
        <ul>
            <div class="userinfoxc">
                <div class="userinfoxcti">
                    <p><%Response.Write(TA); %>的相册</p>
                    <y><%Response.Write("<a href=\"" + this.http_start + "album/myalbum.aspx?siteid=" + this.siteid + "&amp;classid=0&amp;touserid=" + this.touserid + "\">更多</a>"); %></a></y>
                </div>
                <div class="userinfoxcimg">
                    <%
                        for (int i = 0; (this.albumlistVo != null && i < albumlistVo.Count); i++)
                        {
                            Response.Write("<a href=\"" + http_start + "album/book_view.aspx?siteid=" + siteid + "&amp;classid=" + albumlistVo[i].book_classid + "&amp;id=" + albumlistVo[i].id + "" + "\"><img src=\"" + this.http_start + "album/" + albumlistVo[i].book_img + "\" alt=\"load...\"/></a> ");
                            if (i == (albumlistVo.Count - 1)) strhtml.Append("<br/>");
                        }

                        if (albumlistVo == null)
                        {
                            Response.Write("(暂无相片)<br/>");
                        }
                    %>
                </div>
            </div>
        </ul>
    </div>
    <div class="userinfoxcd">
        <ul>
            <div class="userinfoxc">
                <div class="userinfoxcti">
                    <p>留言板</p>
                    <y><%Response.Write("<a href=\"" + this.http_start + "bbs/userguessbook.aspx?action=search&amp;siteid=" + this.siteid + "&amp;classid=" + this.classid + "&amp;touserid=" + this.touserid + "\">更多</a>"); %></y>
                </div>
                <%
                    StringBuilder gbstr = new StringBuilder();
                    for (int i = 0; (gblistVo != null && i < gblistVo.Count); i++)
                    {

                        gbstr.Append("<div class=\"userinfoliuyan\">");

                        gbstr.Append(gblistVo[i].content + "</div>");
                    }

                    if (gblistVo == null)
                    {
                        strhtml.Append("(暂无留言)<br/>");
                    }
                    else
                    {
                        Response.Write(WapTool.ToWML(gbstr.ToString(), wmlVo));

                    }
                %>


                <form name="f5" action="<%Response.Write(http_start);%>bbs/userguessbook.aspx" method="post">
                    <input type="text" class="txt" name="content" style="width: 72%; height: 22px; margin-left: 7px;" placeholder="我来踩踩" size="8" />
                    <input type="hidden" name="action" value="add" /><input type="hidden" name="touserid" value="<%Response.Write(touserid);%>" /><input type="hidden" name="siteid" value="<%Response.Write(siteid);%>" /><input type="hidden" name="classid" value="<%Response.Write(classid);%>" /><input type="hidden" name="sid" value="<%Response.Write(sid);%>" /><input type="submit" style="width: 20%; height: 30px;" value="我要留言" />
                </form>
            </div>
    </div>
    </ul>
</div>




<%    //显示底部
    Response.Write(WapTool.showDown(wmlVo)); %>


