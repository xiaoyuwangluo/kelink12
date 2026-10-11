<%@ Page Language="C#" AutoEventWireup="true" CodeBehind="orderlistWAP.aspx.cs" Inherits="KeLin.WebSite.shop.orderlistWAP" %><%@ Import namespace="KeLin.ClassManager.Tool" %><%
wmlVo.timer ="1800";
wmlVo.strUrl = "shop/orderlistWAP.aspx?state="+this.state +"&amp;action=class&amp;siteid="+this.siteid +"&amp;classid="+this.classid+"&amp;r="+this.r  ;
Response.Write(WapTool.showTop(this.GetLang("订单管理|订单管理|View Reply"), wmlVo));//显示头                                                                                                                                                                       
Response.Write("<script language=\"JavaScript\" src=\"/NetCSS/My97DatePicker/WdatePicker.js\"></script>");
    
    strhtml.Append(ERROR);
    if (this.INFO == "DELOK")
    {
        strhtml.Append("<div class=\"tip\"><b>操作成功！</b></div>");
    }
    strhtml.Append("<div class=\"subtitle\">" + this.GetLang("订单管理|订单管理|View Reply") + "</div>");
    strhtml.Append("<div class=\"content\">");
    strhtml.Append("<form name=\"f\" aciont=\"" + http_start + "shop/orderlistWAP.aspx\" method=\"get\">");
   
    strhtml.Append("状态：<select name=\"state\">");
    strhtml.Append("<option value=\"" + this.state + "\">" + this.state + "</option>");
    strhtml.Append("<option value=\"\">所有</option>");
   
        strhtml.Append("<option value=\"0\">0_待买家付款</option>");
        strhtml.Append("<option value=\"11\">11_买家已付款</option>");
        strhtml.Append("<option value=\"12\">12_卖家已发货</option>");
        strhtml.Append("<option value=\"13\">13_交易成功</option>");
        strhtml.Append("<option value=\"14\">14_交易关闭</option>");
        strhtml.Append("<option value=\"15\">15_退款中</option>");

    
    strhtml.Append("</select> ");
    strhtml.Append("订单号：<input type=\"text\" name=\"orderid\" value=\"" + id + "\" size=\"5\"/> ");
    strhtml.Append("关键字：<input type=\"text\" name=\"key\" value=\"" + key + "\" size=\"5\"/> ");
    strhtml.Append("最近：<input type=\"text\" name=\"days\" value=\"" + days + "\" size=\"5\"/>天 ");
    strhtml.Append("会员ID：<input type=\"text\" name=\"touserid\" value=\"" + touserid + "\" size=\"5\"/> ");
    strhtml.Append("姓名：<input type=\"text\" name=\"toname\" value=\"" + toname + "\" size=\"5\"/> ");
    strhtml.Append("电话：<input type=\"text\" name=\"totel\" value=\"" + totel + "\" size=\"5\"/> ");
    strhtml.Append("时间从<input type=\"text\" id=\"starttime\" name=\"starttime\" value=\"" + starttime + "\" size=\"8\"/><img id=\"imgdate\" style=\"cursor: hand;\" src=\"/NetImages/choose.gif\" onclick=\"WdatePicker({el:'starttime'})\"  title=\"选择日期\" />");
    strhtml.Append("至<input type=\"text\" id=\"endtime\" name=\"endtime\" value=\"" + endtime + "\" size=\"8\"/><img id=\"img1\" style=\"cursor: hand;\" src=\"/NetImages/choose.gif\" onclick=\"WdatePicker({el:'endtime'})\"  title=\"选择日期\" /> ");
    strhtml.Append("来自会员ID：<input type=\"text\" name=\"fromuserid\" value=\"" + fromuserid + "\" size=\"5\"/> ");
    strhtml.Append("来自友链ID：<input type=\"text\" name=\"fromlinkiD\" value=\"" + fromlinkid + "\" size=\"5\"/> ");

    strhtml.Append("<input type=\"hidden\" name=\"action\" value=\"class\" />");
    strhtml.Append("<input type=\"hidden\" name=\"siteid\" value=\"" + siteid + "\" />");
    strhtml.Append("<input type=\"hidden\" name=\"classid\" value=\"" + classid + "\" />");
    strhtml.Append("<input type=\"hidden\" name=\"sid\" value=\"" + sid + "\" />");
    strhtml.Append("&nbsp;&nbsp;&nbsp;<input type=\"submit\" name=\"g\" value=\" " + this.GetLang("搜索|搜索|Search") + " \"/>");
    strhtml.Append("</form><br/>");
    strhtml.Append("</div>");
    strhtml.Append("<div class=\"btBox\"><div class=\"bt1\">");
    strhtml.Append("<a href=\"javascript:window.location.reload();\">刷 新</a><br/>");
    strhtml.Append("</div></div>");

    strhtml.Append("<div class=\"btBox\"><div class=\"bt5\">");
    strhtml.Append("<a");
    if (days == "1")
    {
        strhtml.Append(" class=\"btSelect\"");
    }
    strhtml.Append("  href=\"" + this.http_start + "shop/orderlistwap.aspx?action=class&amp;siteid=" + siteid + "&amp;classid=" + classid + "&amp;days=1&amp;key=" + HttpUtility.UrlEncode(key) + "&amp;state=" + state + "" + "\">今天</a> ");
    strhtml.Append("<a");
    if (days == "7")
    {
        strhtml.Append(" class=\"btSelect\"");
    }
    strhtml.Append("  href=\"" + this.http_start + "shop/orderlistwap.aspx?action=class&amp;siteid=" + siteid + "&amp;classid=" + classid + "&amp;days=7&amp;key=" + HttpUtility.UrlEncode(key) + "&amp;state=" + state + "" + "\">一周内</a> ");
    strhtml.Append("<a");
    if (days == "30")
    {
        strhtml.Append(" class=\"btSelect\"");
    }
    strhtml.Append("  href=\"" + this.http_start + "shop/orderlistwap.aspx?action=class&amp;siteid=" + siteid + "&amp;classid=" + classid + "&amp;days=30&amp;key=" + HttpUtility.UrlEncode(key) + "&amp;state=" + state + "" + "\">一月内</a> ");
    strhtml.Append("<a");
    if (days == "90")
    {
        strhtml.Append(" class=\"btSelect\"");
    }
    strhtml.Append("  href=\"" + this.http_start + "shop/orderlistwap.aspx?action=class&amp;siteid=" + siteid + "&amp;classid=" + classid + "&amp;days=90&amp;key=" + HttpUtility.UrlEncode(key) + "&amp;state=" + state + "" + "\">三月内</a>");
    strhtml.Append("<a");
    if (days == "")
    {
        strhtml.Append(" class=\"btSelect\"");
    }
    strhtml.Append("  href=\"" + this.http_start + "shop/orderlistwap.aspx?action=class&amp;siteid=" + siteid + "&amp;classid=" + classid + "&amp;days=&amp;key=" + HttpUtility.UrlEncode(key) + "&amp;state=" + state + "" + "\">所有</a>");
    strhtml.Append("</div></div>");

    strhtml.Append("<div class=\"subtitle\">");

    strhtml.Append("<a href=\"" + this.http_start + "shop/orderlistWAP.aspx?state=0&amp;action=class&amp;siteid=" + this.siteid + "&amp;classid=" + this.classid + "&amp;id=0\">待买家付款(" + this.state0 + ")</a>  &nbsp;&nbsp;&nbsp;<a href=\"" + this.http_start + "shop/orderlistWAP.aspx?state=11&amp;action=class&amp;siteid=" + this.siteid + "&amp;classid=" + this.classid + "&amp;id=0\">待发货(" + this.state11 + ")</a> &nbsp;&nbsp;&nbsp;");

    strhtml.Append("<span class=\"shoporderlistright\"><font color=\"red\">总价:￥" + totalMoney.ToString("f2") + "&nbsp;&nbsp;</font></span>");
   strhtml.Append("</div>");

    //显示列表

    kk = kk + ((CurrentPage - 1) * pageSize) - 1;
    for (int i = 0; (listVo != null && i < listVo.Count); i++)
    {
        if (i % 2 == 0)
        {
            strhtml.Append("<div class=\"shoporderlist\">");
        }
        else
        {
            strhtml.Append("<div class=\"shoporderlist\">");
        }
        strhtml.Append("<div class=\"shoporderlistlinetop\" >");
        //(total - kk)
        strhtml.Append("订单号:<b>" + listVo[i].orderID + "</b> ");
		
		strhtml.Append("<span class=\"shijian\">" + string.Format("{0:yyyy-MM-dd HH:mm}", listVo[i].orderdate) + "</span>");

        strhtml.Append("<div class=\"shoporderlistpay\">");


        if (listVo[i].state == 0)
        {
            strhtml.Append("待买家付款");
           
        }
        else if (listVo[i].state == 11)
        {
            strhtml.Append("已付款待发货");

        }
        else if (listVo[i].state == 12)
        {
            strhtml.Append("已发货待收货");
           
        }
        else if (listVo[i].state == 13)
        {
            strhtml.Append("交易成功");
        }
        else if (listVo[i].state == 14)
        {
            strhtml.Append("交易关闭");
        }
        else if (listVo[i].state == 15)
        {
            strhtml.Append("退款中");
           
        }
		strhtml.Append("<span class=\"right\">");
        strhtml.Append("&nbsp;" + (total - kk) + "楼");
        
       


        strhtml.Append("</span></div></div>");
        if (listVo[i].shoplistBuy == null)
        {
            strhtml.Append("" + listVo[i].productname + "");
        }
        else
        {

            for (int k = 0; (listVo[i].shoplistBuy != null && k < listVo[i].shoplistBuy.Count); k++)
            {
                if (k % 2 == 0)
                {
                    strhtml.Append("<div class=\"line1\">");
                }
                else
                {
                    strhtml.Append("<div class=\"line2\">");
                }


                /*******************新版排版*******************/
                strhtml.Append("<div id=\"shop-list" + i.ToString() + k.ToString() + "\" class=\"shop-list\">");
                strhtml.Append("<div class=\"shop-list-view\">");
                strhtml.Append("<div class=\"ico\"><a href=\"/shop/view.aspx?id=" + listVo[i].shoplistBuy[k].shopID + "\" title=\"\"><img class=\"p-pic\" src=\"" + listVo[i].shoplistBuy[k].shopImage + "\" style=\"visibility: visible;\"></a></div>");
                strhtml.Append("<div class=\"d\"> <a href=\"/shop/view.aspx?id=" + listVo[i].shoplistBuy[k].shopID + "\" title=\"\">");
                strhtml.Append("<h3 class=\"d-title\">" + listVo[i].shoplistBuy[k].shopName + "</h3>");
                strhtml.Append("</a>");
                strhtml.Append("<p class=\"d-price\"><em class=\"h\">优惠价￥" + listVo[i].shoplistBuy[k].shopRMB + "</em> </p>");
                strhtml.Append("<div class=\"d-main\" style=\"height:35px\">");
                strhtml.Append("<p class=\"d-freight\">");
                if (listVo[i].shoplistBuy[k].shopColor != "")
                {
                    strhtml.Append(listVo[i].shoplistBuy[k].shopColor + "&nbsp;&nbsp;&nbsp;&nbsp;");
                }
                if (listVo[i].shoplistBuy[k].shopXinghao != "")
                {
                    strhtml.Append(listVo[i].shoplistBuy[k].shopXinghao);
                }
                strhtml.Append("</p>");

                strhtml.Append("<p class=\"d-num\" ><div class=\"buy\"><div class=\"mete\"> ");
                strhtml.Append("&nbsp;<input name=\"shopnum\" id=\"shopnum" + i.ToString() + k.ToString() + "\" type=\"text\" class=\"tb-text mui-amount-input\" value=\"" + listVo[i].shoplistBuy[k].shopNum + "\" maxlength=\"8\" title=\"数量\">&nbsp;");
                strhtml.Append("</div></div></p>");
                strhtml.Append("</div>");
                strhtml.Append("</div>");
                strhtml.Append("</div>");
                strhtml.Append("</div>");

                strhtml.Append("</div>");

            }

        }

        //strhtml.Append("<br/>送货跟踪:" + WapTool.ToWML(listVo[i].book_reply, wmlVo));        
        //strhtml.Append("<br/>店长回复:" + listVo[i].book_reply);
        strhtml.Append("<div class=\"shoporderlistline\">共 <span class=\"shuliang\">" + listVo[i].productcount + "</span> 件 &nbsp;&nbsp;&nbsp;&nbsp;运费 <span class=\"yunfei\">" + listVo[i].book_charge.ToString("f2") + "</span> 元 &nbsp;&nbsp;&nbsp;&nbsp;共 <span class=\"jiage\">" + listVo[i].productmoney.ToString("f2") + "</span> 元 ");
        strhtml.Append(" &nbsp;&nbsp;&nbsp;&nbsp;<font color=\"red\">");
        if (listVo[i].payState == "2")
        {
            strhtml.Append("货到付款");

        }
        else if (listVo[i].payState == "1")
        {
            strhtml.Append("<a href=\"" + this.http_start + "chinabank_wap/banklist.aspx?siteid=" + this.siteid + "&amp;orderid=" + listVo[i].orderID + "&amp;backurl=" + HttpUtility.UrlEncode("shop/orderlistmy.aspx?siteid=" + this.siteid + "&classid=" + this.classid) + "\">支付成功</a>  &nbsp;&nbsp;" + string.Format("{0:MM-dd HH:mm}", listVo[i].payTime) + "");
        }
/*
        if (listVo[i].state == 1)
        {
            strhtml.Append("已支付 ");
        }
        else
        {
            strhtml.Append("未支付 ");
        }
   */     

        if (listVo[i].ischeck == 1)
        {
            strhtml.Append("【<b>已被客户删除</b>】 ");
        }
        strhtml.Append("</font>");
        strhtml.Append("<span class=\"shoporderlistright\">");
        
        if (listVo[i].state == 0 && "|00|01|".IndexOf(userVo.managerlvl) > 0)
        {
            strhtml.Append("<span class=\"shoporderlistbtn\">");
            strhtml.Append("<a href=\"" + this.http_start + "shop/orderlistWAP_del.aspx?action=go&amp;siteid=" + siteid + "&amp;classid=" + classid + "&amp;id=" + listVo[i].id + "&amp;lpage=" + lpage + "&amp;page=" + this.CurrentPage + "&amp;key=" + HttpUtility.UrlEncode(key) + "&amp;state=" + state + "" + "\">删除</a>");
            strhtml.Append("</span>&nbsp;&nbsp;");
        }
       
        strhtml.Append("<span class=\"shoporderlistbtn\">");
        strhtml.Append("<a href=\"" + this.http_start + "shop/orderlistWAPMod.aspx?action=godel&amp;siteid=" + siteid + "&amp;classid=" + classid + "&amp;id=" + listVo[i].id + "&amp;lpage=" + lpage + "&amp;page=" + this.CurrentPage + "&amp;key=" + HttpUtility.UrlEncode(key) + "&amp;state=" + state + "" + "\">更改价格/处理订单</a>");
        strhtml.Append("</span>");
        strhtml.Append("</span>");
        
        strhtml.Append("<br/>会员推荐ID:" + listVo[i].fromUserID + " &nbsp;&nbsp;&nbsp;&nbsp;友链推荐ID:" + listVo[i].fromLinkID + " ");

        strhtml.Append("<br/><b>姓名：</b>" + listVo[i].nickname + " <a href=\"" + this.http_start + "bbs/userinfo.aspx?siteid=" + this.siteid + "&amp;touserid=" + listVo[i].userid + "&amp;backurl=" + HttpUtility.UrlEncode(this.GetUrlQueryString()) + "\">(" + listVo[i].userid + ")</a><br/>");
        strhtml.Append("<b>联系电话：</b>" + listVo[i].mobile + "<br/>");
        strhtml.Append("<b>收件地址：</b>" + WapTool.ToWML(listVo[i].remark, wmlVo) + "<br/>");

        strhtml.Append("<b>送货物流：</b><a href=\"" + this.http_start + "shop/orderinfo.aspx?siteid=" + siteid + "&amp;classid=" + classid + "&amp;id=" + listVo[i].id + "&amp;page=" + this.CurrentPage + "&amp;state=" + this.state + "" + "\">" + listVo[i].kuaidiName + " " + listVo[i].kuaidiNum + " </a><br/>");
        strhtml.Append("<b>店家回复：</b>" + WapTool.ToWML(listVo[i].book_reply, wmlVo) + "<br/>");

        
        
        strhtml.Append("</div>");
        
        

        strhtml.Append("</div>");
        kk = kk + 1;
    }
    if (listVo == null)
    {
        strhtml.Append("<div class=\"tip\">暂无记录！</div>");
    }
    //显示导航分页
    strhtml.Append(linkURL);
    strhtml.Append("<div class=\"btBox\"><div class=\"bt1\">");
    strhtml.Append("<a href=\"" + this.http_start + "shop/orderlistWAP_del.aspx?action=goall&amp;siteid=" + siteid + "&amp;classid=" + classid + "&amp;lpage=" + lpage + "&amp;key=" + HttpUtility.UrlEncode(key) + "&amp;state=" + state + "" + "\">清空一个月前订单</a><br/>");
    strhtml.Append("</div></div>");

    strhtml.Append("<div class=\"tip\">本页每隔30分钟刷新一次！ 【<a href=\"/admin/sitesetdefault.aspx?siteid="+this.siteid+"\">网站默认设置</a>】需设置以下参数：<br/>【59】建议配置邮件接收，手机微信开启邮箱提醒插件，手机可以收到订单了。<!--<br/>【64】配置查询快递单号的key接口--></div>");
    strhtml.Append("<div class=\"btBox\"><div class=\"bt2\">");

    strhtml.Append("<a href=\"" + this.http_start + "shop/admin_userlistWAP.aspx?siteid=" + siteid + "&amp;classid=" + classid + "&amp;id=" + id + "&amp;lpage=" + lpage + "" + "\">返回列表</a> ");
    
    strhtml.Append("<a href=\"" + this.http_start + "wapindex.aspx?siteid=" + siteid + "&amp;classid=0" + "\">返回首页</a> ");

     
    strhtml.Append("</div></div>");

    //输出
    Response.Write(WapTool.ToWML(strhtml.ToString(), wmlVo));

    
    





                                                                                                                                                                               
                                                                                                                                                                            
//显示底部
Response.Write(WapTool.showDown(wmlVo)); %>
