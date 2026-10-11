<%@ Page Language="C#" AutoEventWireup="true" CodeBehind="orderlistmy.aspx.cs" Inherits="KeLin.WebSite.shop.orderlistmy" %><%@ Import namespace="KeLin.ClassManager.Tool" %><%
Response.Write(WapTool.showTop(this.GetLang("查看我的订单|查看我的订单|View Reply"), wmlVo));//显示头                                                                                                                                                                       

    
    strhtml.Append(ERROR);
    if (this.INFO == "DELOK")
    {
        strhtml.Append("<div class=\"tip\"><b>操作成功！</b></div>");
    }
    
    
    strhtml.Append("<div class=\"subtitle\">" + this.GetLang("查看我的订单|查看我的订单|View Reply") + "</div>");
    if (this.userid == "0")
    {
        strhtml.Append("<div class=\"tip\"><b>亲，您当前身份为游客，需按订单号查询！</b></div>");
    } 
    strhtml.Append("<div class=\"content\">");
    strhtml.Append("<form name=\"f\" aciont=\"" + http_start + "shop/orderlistmy.aspx\" method=\"get\">");
   
    strhtml.Append("状态：<select name=\"state\">");
    strhtml.Append("<option value=\"" + this.state + "\">" + this.state + "</option>");
    strhtml.Append("<option value=\"\">所有</option>");
    if (order_types == "0")
    {
        strhtml.Append("<option value=\"0\">0_未确认未发货</option>");
        strhtml.Append("<option value=\"4\">4_已确认未发货*</option>");
        strhtml.Append("<option value=\"1\">1_已确认已发货</option>");
        strhtml.Append("<option value=\"2\">2_已发货已签收</option>");
        strhtml.Append("<option value=\"3\">3_已发货未签收</option>");
       
    }
    else
    {
        strhtml.Append("<option value=\"0\">0_等待买家付款</option>");
        strhtml.Append("<option value=\"11\">11_买家已付款</option>");
        strhtml.Append("<option value=\"12\">12_卖家已发货</option>");
        strhtml.Append("<option value=\"13\">13_交易成功</option>");
        strhtml.Append("<option value=\"14\">14_交易关闭</option>");
        strhtml.Append("<option value=\"15\">15_退款中</option>");
    }
    strhtml.Append("</select>");
    strhtml.Append(" 订单号：<input type=\"text\" name=\"orderid\" value=\"" + orderid + "\" size=\"5\"/><br/>");
    strhtml.Append("关键字：<input type=\"text\" name=\"key\" value=\"" + key + "\" size=\"5\"/> ");
    strhtml.Append("最近：<input type=\"text\" name=\"days\" value=\"" + days + "\" size=\"5\"/>天 ");

    strhtml.Append("<input type=\"hidden\" name=\"action\" value=\"class\" />");
    strhtml.Append("<input type=\"hidden\" name=\"siteid\" value=\"" + siteid + "\" />");
    strhtml.Append("<input type=\"hidden\" name=\"classid\" value=\"" + classid + "\" />");
    strhtml.Append("<input type=\"hidden\" name=\"id\" value=\"" + id + "\" />");
    strhtml.Append("<input type=\"hidden\" name=\"sid\" value=\"" + sid + "\" />");
    strhtml.Append("<input type=\"submit\" name=\"g\" value=\" " + this.GetLang("搜索|搜索|Search") + " \"/>");

    strhtml.Append("</form></div>");


    strhtml.Append("<div class=\"btBox\"><div class=\"bt5\">");
    strhtml.Append("<a");
    if (days == "1")
    {
        strhtml.Append(" class=\"btSelect\"");
    }
    strhtml.Append(" href=\"" + this.http_start + "shop/orderlistmy.aspx?action=class&amp;siteid=" + siteid + "&amp;classid=" + classid + "&amp;days=1&amp;key=" + HttpUtility.UrlEncode(key) + "&amp;state=" + state + "" + "\">今天</a> ");
    strhtml.Append("<a");
    if (days == "7")
    {
        strhtml.Append(" class=\"btSelect\"");
    }
    strhtml.Append("  href=\"" + this.http_start + "shop/orderlistmy.aspx?action=class&amp;siteid=" + siteid + "&amp;classid=" + classid + "&amp;days=7&amp;key=" + HttpUtility.UrlEncode(key) + "&amp;state=" + state + "" + "\">一周内</a> ");
    strhtml.Append("<a");
    if (days == "30")
    {
        strhtml.Append(" class=\"btSelect\"");
    }
    strhtml.Append("  href=\"" + this.http_start + "shop/orderlistmy.aspx?action=class&amp;siteid=" + siteid + "&amp;classid=" + classid + "&amp;days=30&amp;key=" + HttpUtility.UrlEncode(key) + "&amp;state=" + state + "" + "\">一月内</a> ");
    strhtml.Append("<a");
    if (days == "90")
    {
        strhtml.Append(" class=\"btSelect\"");
    }
    strhtml.Append("  href=\"" + this.http_start + "shop/orderlistmy.aspx?action=class&amp;siteid=" + siteid + "&amp;classid=" + classid + "&amp;days=90&amp;key=" + HttpUtility.UrlEncode(key) + "&amp;state=" + state + "" + "\">三月内</a>");
    strhtml.Append("<a");
    if (days == "")
    {
        strhtml.Append(" class=\"btSelect\"");
    }
    strhtml.Append("  href=\"" + this.http_start + "shop/orderlistmy.aspx?action=class&amp;siteid=" + siteid + "&amp;classid=" + classid + "&amp;days=&amp;key=" + HttpUtility.UrlEncode(key) + "&amp;state=" + state + "" + "\">所有</a>");
    strhtml.Append("</div></div>");
  
    
    strhtml.Append("<div class=\"subtitle\">");
    if (order_types != "0")
    {
        strhtml.Append("<a href=\"" + this.http_start + "shop/orderlistmy.aspx?state=0&amp;action=class&amp;siteid=" + this.siteid + "&amp;classid=" + this.classid + "&amp;id=0\">待付款([shopstate]0[/shopstate])</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"" + this.http_start + "shop/orderlistmy.aspx?state=12&amp;action=class&amp;siteid=" + this.siteid + "&amp;classid=" + this.classid + "&amp;id=0\">待收货([shopstate]12[/shopstate])</a> ");
    }
   
    
    strhtml.Append("<span class=\"right\">总价:￥" + totalMoney.ToString("f2") + "</span>");
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
        strhtml.Append("订单号:<b>" + listVo[i].orderID + "</b><span class=\"shijian\">" + string.Format("{0:yyyy-MM-dd HH:mm}", listVo[i].orderdate) + " </span>");

        strhtml.Append("<div class=\"shoporderlistpay\"><div class=\"Operate\">");


        if (listVo[i].state == 0)
        {
            strhtml.Append("待付款");
            strhtml.Append(" <span class=\"fukuan\"><a href=\"" + this.http_start + "shop/book_view_addorder.aspx?action=OK&amp;siteid=" + siteid + "&amp;classid=" + listVo[i].book_classid + "&amp;orderid=" + listVo[i].orderID + "" + "\">立即付款</a></span> <span class=\"quxiao\"><a  href=\"" + this.http_start + "shop/orderlistmy_del.aspx?action=go&amp;siteid=" + siteid + "&amp;classid=" + classid + "&amp;id=" + listVo[i].id + "&amp;lpage=" + lpage + "&amp;page=" + this.CurrentPage + "&amp;key=" + HttpUtility.UrlEncode(key) + "&amp;state=" + state + "" + "\">取消订单</a></span>");

        }
        else if (listVo[i].state == 11)
        {
            strhtml.Append("已付款待发货");

        }
        else if (listVo[i].state == 12)
        {
            strhtml.Append("已发货待收货");
            strhtml.Append("<span class=\"fukuan\"><a href=\"" + this.http_start + "shop/orderlistmy_ok.aspx?action=go&amp;siteid=" + siteid + "&amp;classid=" + classid + "&amp;id=" + listVo[i].id + "&amp;lpage=" + lpage + "&amp;page=" + this.CurrentPage + "&amp;key=" + HttpUtility.UrlEncode(key) + "&amp;state=" + state + "" + "\">确认收货</a></span>");

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
            strhtml.Append(",收到款请<span class=\"quxiao\"><a href=\"" + this.http_start + "shop/orderlistmy_del.aspx?action=go&amp;siteid=" + siteid + "&amp;classid=" + classid + "&amp;id=" + listVo[i].id + "&amp;lpage=" + lpage + "&amp;page=" + this.CurrentPage + "&amp;key=" + HttpUtility.UrlEncode(key) + "&amp;state=" + state + "" + "\">取消订单</a></span>");

        }
        strhtml.Append("</div></div></div>");
        //if (listVo[i].state == 0 && order_types == "0")
        //{
        //    strhtml.Append("[<a href=\"" + this.http_start + "shop/orderlistmy_del.aspx?action=go&amp;siteid=" + siteid + "&amp;classid=" + classid + "&amp;id=" + listVo[i].id + "&amp;lpage=" + lpage + "&amp;page=" + this.CurrentPage + "&amp;key=" + HttpUtility.UrlEncode(key) + "&amp;state=" + state + "" + "\">取消订单</a>]");
        //}

       


        
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
                if (listVo[i].shoplistBuy[k].shopColor !=""){
                   strhtml.Append( listVo[i].shoplistBuy[k].shopColor + "&nbsp;&nbsp;&nbsp;&nbsp;" );
                }
                if(listVo[i].shoplistBuy[k].shopXinghao!=""){
                    strhtml.Append(listVo[i].shoplistBuy[k].shopXinghao  );
                }
                strhtml.Append("</p>");
                
                strhtml.Append("<p class=\"d-num\" ><div class=\"buy\"><div class=\"mete\"> ");
                strhtml.Append("&nbsp;<input name=\"shopnum\" id=\"shopnum" + i.ToString() + k.ToString() + "\" type=\"text\" class=\"tb-text mui-amount-input\" value=\"" + listVo[i].shoplistBuy[k].shopNum + "\" maxlength=\"8\" title=\"数量\">&nbsp;");

                if (listVo[i].state == 13)
                {
                    //strhtml.Append("<span class=\" shoporderlistbtn\"><a href=\"" + this.http_start + "shop/book_re.aspx?siteid=" + siteid + "&amp;classid=" + listVo[i].book_classid + "&amp;id=" + listVo[i].shoplistBuy[k].shopID + "\">评论</a></span>");
                }
                strhtml.Append("</div></div></p>");
                strhtml.Append("</div>");
                strhtml.Append("</div>");
                strhtml.Append("</div>");
                strhtml.Append("</div>");

                strhtml.Append("</div>");

            }

        }
       
        //strhtml.Append("<br/>物流跟踪:" + WapTool.ToWML(listVo[i].book_reply, wmlVo));        
        //strhtml.Append("<br/>店长回复:" + listVo[i].book_reply);
        strhtml.Append("<div class=\"shoporderlistline\">共 <span class=\"shuliang\">" + listVo[i].productcount + "</span> 件 &nbsp;&nbsp;&nbsp;运费 <span class=\"yunfei\">" + listVo[i].book_charge.ToString("f2") + "</span> 元 &nbsp;&nbsp;&nbsp;共 <span class=\"jiage\">" + listVo[i].productmoney.ToString("f2") + "</span> 元 ");
        if (listVo[i].payState == "2")
        {
            strhtml.Append("货到付款");

        }
        else if (listVo[i].payState == "1")
        {
            //strhtml.Append("在线支付(<a href=\"" + this.http_start + "chinabank_wap/banklist.aspx?siteid=" + this.siteid + "&amp;orderid=" + listVo[i].orderID + "&amp;backurl=" + HttpUtility.UrlEncode("shop/orderlistmy.aspx?siteid=" + this.siteid + "&classid=" + this.classid) + "\">支付成功</a>:" + string.Format("{0:MM-dd HH:mm}", listVo[i].payTime) + ")");
        }
        else
        {
            if (listVo[i].state == 1)
            {
                //strhtml.Append("(已支付)");
            }
            else
            {
                //strhtml.Append("在线支付 <span class=\"shoporderlistbtn\"><a href=\"" + this.http_start + "shop/book_view_add.aspx?action=OK&amp;siteid=" + siteid + "&amp;classid=" + listVo[i].book_classid + "&amp;showid=" + listVo[i].id + "" + "\">立即付款</a></span>");
            }
        }
        strhtml.Append("<br/><b>" + listVo[i].kuaidiName + " " + listVo[i].kuaidiNum+ "</b> <span class=\"shoporderlistright shoporderlistbtn\"><a href=\"" + this.http_start + "shop/orderinfo.aspx?siteid=" + siteid + "&amp;classid=" + classid + "&amp;id=" + listVo[i].id + "&amp;page=" + this.CurrentPage + "&amp;state=" + this.state + "" + "\">查看物流</a></span>");

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
    if (CheckManagerLvl("03",classVo.adminusername)==true)
    {
        strhtml.Append("<div class=\"tip\">");
        strhtml.Append("管理员您好，订单管理在【我的地盘】→【网站管理后台】→【添加删除内容】→【商店栏目管理】→【<a href=\"" + this.http_start + "shop/orderlistWAP.aspx?siteid=" + this.siteid + "&amp;classid=" + this.classid + "\">订单管理</a>】或WEB后台添加删除内容中管理。订单状态模式可以到【<a href=\"" + this.http_start + "admin/sitesetdefault.aspx?siteid=" + this.siteid + "&amp;classid=" + this.classid + "\">网站默认设置</a>】【40】中设置货到付款模式和淘宝模式。订单状态统计UBB:［shopstate］X［/shopstate］，X为状态值，如待收货(［shopstate］12［/shopstate］)，购物车数量［shopcar］");
        strhtml.Append("</div>");
    }
    //显示电脑版
    string isWebHtml = this.ShowWEB_view(this.classid); //看是存在html代码    
    if (isWebHtml != "")
    {
        string strhtml_list = strhtml.ToString();
        //int s = strhtml_list.IndexOf("<div class=\"title\">");
        //strhtml_list = strhtml_list.Substring(s, strhtml_list.Length - s);

        Response.Clear();
        Response.Write(WapTool.ToWML(isWebHtml.Replace("[view]", strhtml_list), wmlVo));
        Response.End();
    }

    strhtml.Append("<div class=\"btBox\"><div class=\"bt1\">");
    if (id != "" && id != "0")
    {
        strhtml.Append("<a href=\"" + this.http_start + "shop/book_view.aspx?siteid=" + siteid + "&amp;classid=" + classid + "&amp;id=" + id + "&amp;lpage=" + lpage + "" + "\">返回上级</a> ");
    }
        strhtml.Append("<a href=\"" + this.http_start + "shop/book_list.aspx?action=class&amp;siteid=" + siteid + "&amp;classid=" + classid + "&amp;page=" + lpage + "" + "\">返回列表</a> ");


        strhtml.Append("</div></div>");
    strhtml.Append(WapTool.GetVS(wmlVo));

    //输出
    Response.Write(WapTool.ToWML(strhtml.ToString(), wmlVo));

    
                                                                                                                                                                        
//显示底部
Response.Write(WapTool.showDown(wmlVo)); %>
