<%@ Page Language="C#" AutoEventWireup="true" CodeBehind="alipay_WAPShop.aspx.cs" Inherits="KeLin.WebSite.chinabank_WAP.alipay_WAPShop" %><%@ Import namespace="KeLin.ClassManager.Tool" %><%
if(this.INFO =="OK"){
    //wmlVo.timer = "1";
    wmlVo.strUrl = this.toURL;
    Response.Write("<script type='text/javascript'> var go=1; setInterval(function(){ if(go==1){ window.location.href='" + wmlVo.strUrl.Replace("&amp;", "&") + "'; go=0;} },2000); </script>");


}
Response.Write(WapTool.showTop(this.GetLang("WAP支付宝余额支付|WAP支付宝余额支付|select bank"), wmlVo));//显示头                                                                                                                                                                       


    Response.Write("<div class=\"subtitle\">" + this.GetLang("WAP支付宝余额支付|WAP支付宝余额支付|select bank") + "</div>");
    
    Response.Write("<div class=\"content\">");
    if (this.isclose == "0")
    {
        Response.Write("此支付接口关闭中，如果有需要请联系站长开通！");

    }else if (action == "")
    {
        Response.Write("支付金额:￥" + total_fee);
        Response.Write("<br/><form name=\"f\" aciont=\"" + this.http_start + "chinabank_wap/alipay_wapShop.aspx\" method=\"post\">");   
        Response.Write("<input type=\"hidden\" name=\"action\" value=\"go\"/>");
        Response.Write("<input type=\"hidden\" name=\"siteid\" value=\"" + siteid + "\"/>");
        Response.Write("<input type=\"hidden\" name=\"classid\" value=\"" + classid + "\"/>");
        Response.Write("<input type=\"hidden\" name=\"orderid\" value=\"" + orderid + "\"/>");
        Response.Write("<input type=\"hidden\" name=\"sid\" value=\"" + sid + "\"/>");
        Response.Write("<input type=\"hidden\" name=\"r\" value=\"" + r + "\"/>");
        Response.Write("<input type=\"submit\" name=\"g\" value=\"" + this.GetLang("下一步|下一步|Next") + "\"/><br/>");
        Response.Write("注意:支付成功后点击“<b>返回商户</b>”即可回到本站。<br/>");
        Response.Write("</form>");
    }
    else if (action == "go")
    {
        if (this.ERROR != "")
        {
            Response.Write("错误信息:<br/>" + token + this.ERROR + "<br/><br/>");
        }

        if (this.INFO == "OK")
        {
            Response.Write("正在进入支付宝支付界面，请稍后...<br/>如不能自动跳转请<a href=\"" + this.toURL + "\">点击此进入</a>。<br/><br/>");
        }

        Response.Write("<a href=\"" + this.http_start + "shop/book_view_add.aspx?action=OK&amp;showid=" + sVo.id + "&amp;siteid=" + this.siteid + "&amp;classid=" + this.classid + "\">" + this.GetLang("返回上级|返回上级|Back to list") + "</a>");
    


    }
    Response.Write("</div>");
    Response.Write("<div class=\"tip\">");
    Response.Write(WapTool.ToWML(this.remark, wmlVo));
    Response.Write("</div>");
    Response.Write("<div class=\"btBox\"><div class=\"bt1\">");
    Response.Write("<a href=\"" + this.http_start + "shop/book_view_add.aspx?action=OK&amp;showid=" + sVo.id + "&amp;siteid=" + this.siteid + "&amp;classid=" + this.classid + "\">" + this.GetLang("返回上级|返回上级|Back to list") + "</a>");
    

    Response.Write("</div></div>");




                                                                                                                                                                               
                                                                                                                                                                             
//显示底部
Response.Write(WapTool.showDown(wmlVo)); %>


