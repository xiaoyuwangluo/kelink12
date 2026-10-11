<%@ Page Language="C#" AutoEventWireup="true" CodeBehind="book_view_addorder.aspx.cs" Inherits="KeLin.WebSite.shop.book_view_addorder" %><%@ Import namespace="KeLin.ClassManager.Tool" %><%
if(this.INFO=="ADDOK"){
    //wmlVo.timer = "2";
    if (this.payAction == "1")
    {
        wmlVo.strUrl = "/shop/Cash_On_Delivery.aspx?orderid="+this.orderID  +"&amp;siteid=" + this.siteid + "&amp;classid=" + this.classid+"&amp;isAddUser="+this.isAddUser  ;
        
    }
    else
    {
        wmlVo.strUrl = "/shop/book_view_addorder.aspx?action=OK&amp;siteid=" + this.siteid + "&amp;classid=" + this.classid + "&amp;orderid=" + this.orderID + "&amp;isAddUser=" + this.isAddUser;
    }
    Response.Write("<script type='text/javascript'> var go=1; setInterval(function(){ if(go==1){ window.location.href='" + wmlVo.strUrl.Replace("&amp;", "&") + "'; go=0;} },2000); </script>");

}

Response.Write(WapTool.showTop("支付界面", wmlVo));//显示头                                                                                                                                                                     

Response.Write("<div class=\"subtitle\">支付界面</div>");
    
    Response.Write(this.ERROR);
    if (this.INFO == "NULL")
    {
        Response.Write("<div class=\"tip\">");
        Response.Write("姓名、联系电话、收货地址至少二字符以上！  ");
        Response.Write("</div>");
    }     else if (this.INFO == "REPEAT")
    {
        Response.Write("<div class=\"tip\">");
        Response.Write("<b>请不要发重复内容！</b><br/>");
        Response.Write("</div>");
    }
    else if (this.INFO == "WAITING")
    {
        Response.Write("<div class=\"tip\">");  
        Response.Write("<b>请再过" + this.KL_CheckIPTime + "秒后操作！</b><br/>");
        Response.Write("</div>");

    }
   
    else if (this.INFO == "NOMONEY")
    {
        Response.Write("<div class=\"tip\">");  
        Response.Write("<b>我的余额不够支付了！我当前RMB余额￥" + userVo.RMB.ToString("f2") + "，一共需要支付￥" + totalRMB.ToString("f2") + "。<a href=\"/chinabank_wap/selbank_wap.aspx?siteid=" + this.siteid + "\">点击此充值</a>！</b><br/>");
        Response.Write("</div>");
    }
    else if (this.INFO == "OK")
    {
       
        
        Response.Write("<div class=\"content\">");
       // Response.Write("<b>添加成功！</b><br/>");
        if (orderVo.payState == "1")
        {
            Response.Write("已支付成功！<br/>");
        }
        else
        {
            if (showinfo.Trim() != "")
            {
                Response.Write("</div><div class=\"content\">");
                Response.Write(WapTool.ToWML(showinfo, wmlVo));
                Response.Write("</div><div class=\"content\">");
            }
            
            
            
            Response.Write("订单号:" + orderVo.orderID + "<br/>");

            Response.Write("总价格:￥" + orderVo.productmoney.ToString("f2") + "  <span class=\"urlbtn2\"><a href=\"" + this.http_start + "shop/book_view_addorder.aspx?action=OK&amp;siteid=" + this.siteid + "&amp;classid=" + this.classid + "&amp;orderid=" + this.orderID + "&amp;r=" + this.r + "\">刷新价格</a></span> <br/>");
            Response.Write("(可联系客服修改价格后，再支付！)<br/><br/>");
            if (payType == "2" || payType == "0" || this.classid =="0")
            {

                if (isOpenAlipay == "1" || this.classid =="0")
                {
                    Response.Write("<div class=\"btBox\"><div class=\"bt1\">");
                    Response.Write("<a href=\"" + http_start + "chinabank_WAP/alipay_WAPShop.aspx?classid=" + classid + "&amp;siteid=" + siteid + "&amp;orderid=" + orderVo.orderID + "" + "\">支付宝“手机版”付款&gt;&gt;</a> ");
                    Response.Write("</div></div>");
                }

                if (isOpenAlipay_web == "1" || this.classid == "0")
                {
                    Response.Write("<div class=\"btBox\"><div class=\"bt1\">");
                    Response.Write("<a href=\"" + http_start + "shop/alipay_web/godefault.aspx?classid=" + classid + "&amp;siteid=" + siteid + "&amp;orderid=" + orderVo.orderID + "" + "\" target=\"_blank\">支付宝“电脑版”付款&gt;&gt;</a> ");
                    Response.Write("</div></div>");
                }

                if (isOpenRMB == "1" || this.classid == "0")
                {
                    Response.Write("<div class=\"btBox\"><div class=\"bt1\">");
                    Response.Write("<a href=\"" + http_start + "shop/RMB_PAY.aspx?classid=" + classid + "&amp;siteid=" + siteid + "&amp;orderid=" + orderVo.orderID + "" + "\">RMB帐户余额付款&gt;&gt;</a> ");
                    Response.Write("</div></div>");
                }

                if (this.isOpenMoney == "1")
                {
                    Response.Write("<div class=\"btBox\"><div class=\"bt1\">");
                    Response.Write("<a href=\"" + http_start + "shop/Money_PAY.aspx?classid=" + classid + "&amp;siteid=" + siteid + "&amp;orderid=" + orderVo.orderID + "" + "\">" + WapTool.GetSiteMoneyName(siteVo.sitemoneyname, this.lang) + "帐户付款&gt;&gt;</a> ");
                    Response.Write("</div></div>");
                }

            }
            
            if (payType == "1" || payType == "0")
            {
                Response.Write("<div class=\"btBox\"><div class=\"bt1\">");
                Response.Write("<a href=\"" + http_start + "shop/Cash_On_Delivery.aspx?classid=" + classid + "&amp;siteid=" + siteid + "&amp;orderid=" + orderVo.orderID + "" + "\">货到付款&gt;&gt;</a> ");
                Response.Write("</div></div>");
            }
            Response.Write("<div class=\"btBox\"><div class=\"bt2\">");
            Response.Write(" <a href=\"" + http_start + "shop/book_list.aspx?classid=" + classid + "&amp;siteid=" + siteid + "" + "\">返回继续购买</a> ");
            Response.Write("<a href=\"" + http_start + "shop/orderlistmy.aspx?classid=" + classid + "&amp;siteid=" + siteid + "\">我的订单</a> ");
            Response.Write("</div></div>");
        }
        Response.Write("</div>");
       

    }
    else if (this.INFO == "ADDOK")
    {
        
        Response.Write("<div class=\"tip\">");  
        Response.Write("<b>");
        if (this.payAction == "1")
        {
            Response.Write("下单成功！自动跳转至<a href=\"" + this.http_start + wmlVo.strUrl + "\">查看本次订单信息</a>。");
        
        }
        else
        {
            Response.Write("下单成功！自动跳转至<a href=\"" + this.http_start + wmlVo.strUrl + "\">支付页面</a>。");
        }
        
        Response.Write("</b></div>");
    }



    
    Response.Write("<div class=\"btBox\"><div class=\"bt2\">");
    Response.Write("<a href=\"" + this.http_start + "shop/book_list.aspx?siteid=" + this.siteid + "&amp;classid=" + this.classid + "&amp;page=" + this.page + "\">" + this.GetLang("返回上级|返回上级|Back to list") + "</a> ");
    Response.Write("<a href=\"" + this.http_start + "wapindex.aspx?siteid=" + this.siteid + "\">" + this.GetLang("返回首页|返回首页|Back to list") + "</a>");

    Response.Write("</div></div>");
    Response.Write(WapTool.GetVS(wmlVo));
 


                                                                                                                                                                               
                                                                                                                                                                             
//显示底部
Response.Write(WapTool.showDown(wmlVo)); %>


