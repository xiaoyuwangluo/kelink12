<%@ Page Language="C#" AutoEventWireup="true" validateRequest="false" CodeBehind="orderlistWAPMod.aspx.cs" Inherits="KeLin.WebSite.shop.orderlistWAPMod" %><%@ Import namespace="KeLin.ClassManager.Tool" %><%
Response.Write(WapTool.showTop(this.GetLang("处理订单|处理订单|content modification"), wmlVo));//显示头                                                                                                                                                                       


    Response.Write("<div class=\"subtitle\">" + this.GetLang("处理操作|处理操作|Modify operation") + "</div>");
    Response.Write("<div class=\"tip\">");
    Response.Write(this.ERROR);
    if (sHtmlText != "")
    {
        Response.Write("向支付宝服务器发送“确认发货”信号：" + sHtmlText + "<br/><br/>");

    }
    if (this.INFO == "OK")
    {
        Response.Write("<b>");
        Response.Write(this.GetLang("处理成功！|处理成功！|Successfully modified"));
        Response.Write("</b> ");
        Response.Write("<a href=\"" + this.http_start + "shop/orderlistWAP.aspx?siteid=" + this.siteid + "&amp;classid=" + this.classid + "&amp;page=" + this.page + "\">" + this.GetLang("返回列表|返回列表|Back to list") + "</a><br/>");
    }
    else if (this.INFO == "NULL")
    {
        Response.Write("<b>");
        Response.Write(this.GetLang("回复不能为空！|內容不能爲空！|The content can not be empty!"));
        Response.Write("</b><br/>");

    }
    else if (this.INFO == "MONEYERR")
    {
        Response.Write("<b>");
        Response.Write(this.GetLang("价格格式错误！|价格格式错误！|money Err!"));
        Response.Write("</b><br/>");

    }
    else if (this.INFO == "MAX")
    {
        Response.Write("<b>");
        Response.Write("短信内容只能70个汉字或140个英文，当前长度为：" + WapTool.CalStringLength(book_sms));
        Response.Write("</b><br/>");

    }
    Response.Write("</div>");
    Response.Write("<div class=\"content\">");
    Response.Write("<form name=\"go\" action=\"" + this.http_start + "shop/orderlistWAPmod.aspx\" method=\"post\">");
    Response.Write("订单号:" + bookVo.orderID + "<br/>");
    Response.Write("状态：<select name=\"state\">");
    Response.Write("<option value=\"" + bookVo.state + "\">" + bookVo.state + "</option>");
    if(order_types =="0"){
    Response.Write("<option value=\"0\">0_未确认未发货</option>");
    Response.Write("<option value=\"4\">4_已确认未发货*</option>");
    Response.Write("<option value=\"1\">1_已确认已发货</option>");
    Response.Write("<option value=\"2\">2_已发货已签收</option>");
    Response.Write("<option value=\"3\">3_已发货未签收</option>");
    }
    else
    {
        Response.Write("<option value=\"0\">0_等待买家付款</option>");
        Response.Write("<option value=\"11\">11_买家已付款</option>");
        Response.Write("<option value=\"12\">12_卖家已发货</option>");
        Response.Write("<option value=\"13\">13_交易成功</option>");
        Response.Write("<option value=\"14\">14_交易关闭</option>");
        Response.Write("<option value=\"15\">15_退款中</option>");

    }
    Response.Write("</select><br/>");
    Response.Write(this.GetLang("修改价格|修改价格|Content") + "*:<br/>");
    Response.Write("<input type=\"text\"  name=\"productmoney\" value=\"" + bookVo.productmoney.ToString("f2") + "\" /><br/>");
    Response.Write("物流:<select name=\"kuaidiName\">");
    Response.Write("<option value=\"" + bookVo.kuaidiName + "\">" + bookVo.kuaidiName + "</option>");
    Response.Write("<option value=\"无需物流\">无需物流</option>");
    WapTool.setKuaidi();
    foreach (System.Collections.Generic.KeyValuePair<string, string> temp in WapTool.kuaidi)
    {
        Response.Write("<option value=\"" + temp.Key + "\">" + temp.Key + "</option>");
    }

  

    Response.Write("</select><br/>");
    Response.Write("订单号:<input type=\"text\"  name=\"kuaidiNum\" value=\"" + bookVo.kuaidiNum + "\" /><br/>");
    
                                                                                                                                                                                                                Response.Write(this.GetLang("店长回复|店长內容|reply") + "(录入备注):<br/>");
    Response.Write("<textarea name=\"book_reply\" rows=\"3\" style=\"width:100%\">" + bookVo.book_reply + "</textarea><br/>");
    Response.Write("是否向客户(手机号码:"+this.mobile+")发短信:<br/>");
    Response.Write("<textarea name=\"book_sms\" rows=\"3\" style=\"width:100%\">" + book_sms + "</textarea><br/>");
    Response.Write("(不发送留空)<br/>");
    
    Response.Write("<input type=\"hidden\" name=\"action\" value=\"gomod\"/>");
    Response.Write("<input type=\"hidden\" name=\"id\" value=\"" + id + "\"/>");
    Response.Write("<input type=\"hidden\" name=\"classid\" value=\"" + classid + "\"/>");
    Response.Write("<input type=\"hidden\" name=\"siteid\" value=\"" + siteid + "\"/>");
    Response.Write("<input type=\"hidden\" name=\"page\" value=\"" + page + "\"/>");
    Response.Write("<input type=\"hidden\" name=\"sid\" value=\"" + sid + "\"/>");
    Response.Write("<input type=\"submit\" name=\"bt\" value=\"" + this.GetLang("确 定|确 定|Modify") + "\"/>");

    Response.Write("</form></div>");
    Response.Write("<div class=\"btBox\"><div class=\"bt1\">");
    Response.Write("<a href=\"" + this.http_start + "shop/orderlistWAP.aspx?siteid=" + this.siteid + "&amp;classid=" + this.classid + "&amp;page=" + this.page + "\">" + this.GetLang("返回列表|返回列表|Back to list") + "</a>");
 
    Response.Write("</div></div>");
 


                                                                                                                                                                               
                                                                                                                                                                             
//显示底部
Response.Write(WapTool.showDown(wmlVo)); %>


