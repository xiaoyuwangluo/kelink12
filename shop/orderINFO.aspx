<%@ Page Language="C#" AutoEventWireup="true" CodeBehind="orderINFO.aspx.cs" Inherits="KeLin.WebSite.shop.orderINFO" %><%@ Import namespace="KeLin.ClassManager.Tool" %><%

Response.Write(WapTool.showTop(this.GetLang("详细信息|详细信息|详细信息"), wmlVo));//显示头                                                                                                                                                                       


    Response.Write("<div class=\"subtitle\">详细信息</div>");


    Response.Write("<div class=\"line1\">");


    Response.Write("<b>订单编号：</b>" + orderVo.orderID + "<br/>");
    Response.Write("<b>订单状态:</b>");


    Response.Write("<font color=\"red\">");
    

        if (orderVo.state == 0)
        {
            Response.Write("待付款");
           
        }
        else if (orderVo.state == 11)
        {
            Response.Write("已付款待发货");

        }
        else if (orderVo.state == 12)
        {
            Response.Write("已发货待收货");
           
        }
        else if (orderVo.state == 13)
        {
            Response.Write("交易成功");
        }
        else if (orderVo.state == 14)
        {
            Response.Write("交易关闭");
        }
        else if (orderVo.state == 15)
        {
            Response.Write("退款中");
            
        }


    
    
    
    Response.Write("</font><br/>");
    Response.Write("<b>下订时间：</b>" + orderVo.orderdate + "<br/>");    
    Response.Write("<b>下订会员：</b><a href=\"" + this.http_start + "bbs/userinfo.aspx?siteid=" + this.siteid + "&amp;touserid=" + orderVo.userid + "&amp;backurl=" + HttpUtility.UrlEncode(this.GetUrlQueryString()) + "\">" + orderVo.nickname + "(" + orderVo.userid + ")</a>");
    Response.Write("</div>");


    if (orderVo.shoplistBuy == null)
    {
        Response.Write("" + orderVo.productname + "");
    }
    else
    {

        for (int k = 0; (orderVo.shoplistBuy != null && k < orderVo.shoplistBuy.Count); k++)
        {
            if (k % 2 == 0)
            {
                Response.Write("<div class=\"line1\">");
            }
            else
            {
                Response.Write("<div class=\"line2\">");
            }


            /*******************新版排版*******************/
            Response.Write("<div id=\"shop-list" + k.ToString() + "\" class=\"shop-list\">");
            Response.Write("<div class=\"shop-list-view\">");
            Response.Write("<div class=\"ico\"><a href=\"/shop/view.aspx?id=" + orderVo.shoplistBuy[k].shopID + "\" title=\"\"><img class=\"p-pic\" src=\"" + orderVo.shoplistBuy[k].shopImage + "\" style=\"visibility: visible;\"></a></div>");
            Response.Write("<div class=\"d\"> <a href=\"/shop/view.aspx?id=" + orderVo.shoplistBuy[k].shopID + "\" title=\"\">");
            Response.Write("<h3 class=\"d-title\">" + orderVo.shoplistBuy[k].shopName + "</h3>");
            Response.Write("</a>");
            Response.Write("<p class=\"d-price\"><em class=\"h\">优惠价￥" + orderVo.shoplistBuy[k].shopRMB + "</em> </p>");
            Response.Write("<div class=\"d-main\" style=\"height:35px\">");
            Response.Write("<p class=\"d-freight\">");
            if (orderVo.shoplistBuy[k].shopColor != "")
            {
                Response.Write(orderVo.shoplistBuy[k].shopColor + "&nbsp;&nbsp;&nbsp;&nbsp;");
            }
            if (orderVo.shoplistBuy[k].shopXinghao != "")
            {
                Response.Write(orderVo.shoplistBuy[k].shopXinghao);
            }
            Response.Write("</p>");

            Response.Write("<p class=\"d-num\" ><div class=\"buy\"><div class=\"mete\"> ");
            Response.Write("&nbsp;<input name=\"shopnum\" id=\"shopnum" + k.ToString() + "\" type=\"text\" class=\"tb-text mui-amount-input\" value=\"" + orderVo.shoplistBuy[k].shopNum + "\" maxlength=\"8\" title=\"数量\">&nbsp;");
            Response.Write("</div></div></p>");
            Response.Write("</div>");
            Response.Write("</div>");
            Response.Write("</div>");
            Response.Write("</div>");

            Response.Write("</div>");

        }

    }
    Response.Write("<div class=\"line1\">");
    Response.Write("<b>商品总量：</b>" + orderVo.productcount + "<br/>");
    
    Response.Write("<b>商品运费：</b>￥" + orderVo.book_charge.ToString("f2") + "<br/>");
    Response.Write("<b>订单总价：</b>￥" + orderVo.productmoney.ToString("f2") + "<br/>");
    if (orderVo.payState == "1")
    {
        Response.Write("<b>在线支付：</b><a href=\"" + this.http_start + "chinabank_wap/banklist.aspx?siteid=" + this.siteid + "&amp;orderid=" + orderVo.orderID + "&amp;backurl=" + HttpUtility.UrlEncode("shop/orderlistmy.aspx?siteid=" + this.siteid + "&classid=" + this.classid) + "\">已支付</a><br/>");
        Response.Write("<b>支付时间：</b>" + orderVo.payTime + "<br/>");
    }
    else
    {
        Response.Write("<b>在线支付：</b>未支付<br/>");
    }

    if (this.userid == "0")
    {
        orderVo.remark = "当前游客身份，信息保密。";
        orderVo.book_reply = "当前游客身份，信息保密，请联系客服。";
        orderVo.nickname = "保密";
        orderVo.mobile = "保密";
    }
    Response.Write("</div>");
    Response.Write("<div class=\"line2\">");
    Response.Write("<b>姓名：</b>" + orderVo.nickname + "<br/>");
    Response.Write("<b>联系电话：</b>" + orderVo.mobile + "<br/>");
    Response.Write("<b>收件地址：</b>" + WapTool.ToWML(orderVo.remark, wmlVo) );
    Response.Write("</div>");
    Response.Write("<div class=\"line1\">");
    Response.Write("<b>店家回复：</b>" + WapTool.ToWML(orderVo.book_reply, wmlVo));
    Response.Write("<br/><b>物流信息：</b>"+orderVo.kuaidiName+ " "+ orderVo.kuaidiNum);
    Response.Write("</div>");
    if (detail.ToLower().StartsWith("http://"))
    {
        Response.Write("<iframe name=\"kuaidi100\" src=\""+detail+"\" width=\"480\" height=\"380\" marginwidth=\"0\" marginheight=\"0\" hspace=\"0\" vspace=\"0\" frameborder=\"0\" scrolling=\"yes\"></iframe>");
    }else{
        Response.Write(detail.Replace("查询结果如下所示：", ""));
    }
    
    
    Response.Write("<div class=\"btBox\"><div class=\"bt1\">");
    Response.Write("<a href=\"javascript:window.history.back();\">" + this.GetLang("返回上级|返回上级|Back to list") + "</a> ");
   // Response.Write("<a href=\"" + this.http_start + "wapindex.aspx?siteid=" + this.siteid + "\">" + this.GetLang("返回首页|返回首页|Back to list") + "</a>");    
   
    Response.Write("</div></div>");
 


                                                                                                                                                                               
                                                                                                                                                                             
//显示底部
Response.Write(WapTool.showDown(wmlVo)); %>


