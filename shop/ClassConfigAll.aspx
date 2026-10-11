<%@ Page Language="C#" AutoEventWireup="true" CodeBehind="ClassConfigAll.aspx.cs" Inherits="KeLin.WebSite.shop.ClassConfigAll" %><%@ Import namespace="KeLin.ClassManager.Tool" %><%
Response.Write(WapTool.showTop(this.GetLang("更多栏目属性设置|更多栏目属性设置|site setup"), wmlVo));//显示头                                                                                                                                                                       

    Response.Write("<div class=\"subtitle\">" + this.GetLang("更多栏目属性设置|更多栏目属性设置|site setup") + "</div>");
    if (this.INFO == "OK")
    {
        Response.Write("<div class=\"tip\"><b>");
        Response.Write(this.GetLang("更新成功！|更新成功！|Successfully Update"));
        Response.Write("</b></div>");

    }

    Response.Write("<div class=\"content\">");
    Response.Write("<form name=\"f\" action=\"" + http_start + "shop/ClassConfigAll.aspx\" method=\"post\">");
    Response.Write("[9]支付方式显示:<br/>");
    Response.Write("<select name=\"par9\">");
    Response.Write("<option value=\"" + this.par9 + "\">" + this.par9 + "</option>");
    Response.Write("<option value=\"0\">0_显示货到付款+在线支付</option>");
    Response.Write("<option value=\"1\">1_显示货到付款</option>");
    Response.Write("<option value=\"2\">2_显示在线支付</option>");
    //Response.Write("<option value=\"3\">3_下订即扣RMB帐户&amp;[1]要开启</option>");
    Response.Write("</select><br/>");
                                                                                                                                                                                      
    Response.Write("[20]开启支付宝电脑版“双功能收款”支付功能:");
    Response.Write("<select name=\"par20\">");
    Response.Write("<option value=\"" + par20 + "\">" + par20 + "</option>");
    Response.Write("<option value=\"0\">0_关闭</option>");
    Response.Write("<option value=\"1\">1_开启</option>");
    Response.Write("</select><br/>");

    
    Response.Write("[0]开启支付宝无线产品“手机网站”支付功能:");
    Response.Write("<select name=\"par0\">");
    Response.Write("<option value=\"" + par0 + "\">" + par0 + "</option>");
    Response.Write("<option value=\"0\">0_关闭</option>");
    Response.Write("<option value=\"1\">1_开启</option>");
    Response.Write("</select><br/>（配置id和key到电脑后台，网站管理，会员充值设置）<br/>");

    Response.Write("[1]开启RMB帐户余额支付功能:");
    Response.Write("<select name=\"par1\">");
    Response.Write("<option value=\"" + par1 + "\">" + par1 + "</option>");
    Response.Write("<option value=\"0\">0_关闭</option>");
    Response.Write("<option value=\"1\">1_开启</option>");
    Response.Write("</select><br/>");

    Response.Write("[13]开启 " + WapTool.GetSiteMoneyName(siteVo.sitemoneyname, this.lang) + " 支付功能:");
    Response.Write("<select name=\"par13\">");
    Response.Write("<option value=\"" + par13 + "\">" + par13 + "</option>");
    Response.Write("<option value=\"0\">0_关闭</option>");
    Response.Write("<option value=\"1\">1_开启</option>");    
    Response.Write("</select><br/>（￥1.00 = "+ WapTool.getArryString(siteVo.Version, '|', 5)  +" 虚拟币，网站默认设置【5】配制。）<br/>");


    Response.Write("[14]支付宝收款页面地址(已无效):");
    Response.Write("<input type=\"text\" name=\"par14\" value=\"" + this.par14 + "\"/><br/>(例:https://me.alipay.com/kelink)<br/>");
   
    
    Response.Write("[2]货到付款说明:");
    Response.Write("<input type=\"text\" name=\"par2\" value=\"" + this.par2 + "\"/><br/>");
    Response.Write("例:<input type=\"text\" name=\"p" + r + "\"  value=\"[b]订单已提交成功，接下来：[/b][br](1)您的订单已成功提交，我们客服人员将会与您联系确认收货信息并发货，请您认准我们的官方唯一呼出号码:13048036868[br](2)严密打包您订购的商品后交由快递公司送货上门到您手中[br](3)货到请您付款给快递人员并验货，完成交易[br](4)如有其它购买相关疑问，可查看问题解答\"/><br/>");
    
    Response.Write("(支持UBB语法)<br/>");

    Response.Write("[11]在线支付顶部说明:");
    Response.Write("<input type=\"text\" name=\"par11\"  value=\"" + this.par11 + "\"/><br/>");
    Response.Write("例:<input type=\"text\" name=\"y" + r + "\"  value=\"[b]订单已提交成功，订单如下：[/b][br]\"/><br/>");
    Response.Write("(支持UBB语法)<br/>");

    Response.Write("[3]允许游客评论(无效):");
    Response.Write("<select name=\"par3\">");
    Response.Write("<option value=\"" + par3 + "\">" + par3 + "</option>");
    Response.Write("<option value=\"0\">0_开启</option>");
    Response.Write("<option value=\"1\">1_关闭</option>");
    Response.Write("</select><br/>");

    Response.Write("[6]自己的评论只能自己和客服回复:");
    Response.Write("<select name=\"par6\">");
    Response.Write("<option value=\"" + this.par6 + "\">" + this.par6 + "</option>");
    Response.Write("<option value=\"0\">0_关闭</option>");
    Response.Write("<option value=\"1\">1_开启</option>");
    Response.Write("</select><br/>");

    Response.Write("[7]游客提交订单后按手机号注册会员(已无效):<br/>");
    Response.Write("优先级一：全局设置KL_SHOP_REG当前为" + KL_SHOP_REG + "(0为关，1为开)在web.config配制<br/>");
    Response.Write("优先级二：<select name=\"par7\">");
    Response.Write("<option value=\"" + this.par7 + "\">" + this.par7 + "</option>");
    Response.Write("<option value=\"0\">0_开启</option>");
    Response.Write("<option value=\"1\">1_关闭</option>");
    Response.Write("</select><br/>");

    Response.Write("[8]总编辑(标识03)只允许管理订单:<br/>");
    Response.Write("<select name=\"par8\">");
    Response.Write("<option value=\"" + this.par8 + "\">" + this.par8 + "</option>");
    Response.Write("<option value=\"0\">0_开启</option>");
    Response.Write("<option value=\"1\">1_关闭</option>");
    Response.Write("</select><br/>");
    

   
   

    Response.Write("[12]列表默认排序:<br/>");
    Response.Write("<select name=\"par12\">");
    Response.Write("<option value=\"" + this.par12 + "\">" + this.par12 + "</option>");
    Response.Write("<option value=\"0\">0_新上架</option>");
    Response.Write("<option value=\"1\">1_按人气</option>");
    Response.Write("</select><br/>");
    
    Response.Write("[4]二级页面返回上级/首页替换:<br/>");
    Response.Write("<input type=\"text\" name=\"par4\" value=\"" + this.par4 + "\"/><br/>");
    Response.Write("例:<input type=\"text\" name=\"infos" + r + "\" value=\"[url=/wapindex.aspx?siteid=[siteid]&amp;classid=[parentid]]返回上级[/url]-[url=/wapindex.aspx?siteid=[siteid]]返回首页[/url]\"/><br/>");

    Response.Write("[5]三级页面返回上级/首页替换:<br/>");
    Response.Write("<input type=\"text\" name=\"par5\" value=\"" + this.par5 + "\"/><br/>");
    Response.Write("例:<input type=\"text\" name=\"infos" + r + "\" value=\"[url=/shop/book_list.aspx?siteid=[siteid]&amp;classid=[classid]]返回[classname][/url]-[url=/wapindex.aspx?siteid=[siteid]]返回首页[/url]\"/><br/>");

    Response.Write("[15]列表中的“加入购物车”改成:<br/>");
    Response.Write("<input type=\"text\" name=\"par15\" value=\"" + this.par15 + "\"/>(如加入购物车)<br/>");

    Response.Write("[16]显示上一条下一条:");
    Response.Write("<select name=\"par16\">");
    Response.Write("<option value=\"" + this.par16 + "\">" + this.par16 + "</option>");
    Response.Write("<option value=\"0\">0_开启</option>");
    Response.Write("<option value=\"1\">1_关闭</option>");
    Response.Write("</select><br/>");
    Response.Write("(关闭提高性能，优先级web.cofig配置)<br/>");
    Response.Write("[17]显示上一页下一页，默认有加载更多按钮:");
    Response.Write("<select name=\"par17\">");
    Response.Write("<option value=\"" + this.par17 + "\">" + this.par17 + "</option>");
    Response.Write("<option value=\"0\">0_关闭</option>");
    Response.Write("<option value=\"1\">1_开启</option>");
    Response.Write("</select><br/>");


    Response.Write("[18]热线电话号码:<br/>");
    Response.Write("<input type=\"text\" name=\"par18\" value=\"" + this.par18 + "\"/>(可为空)<br/>");

    Response.Write("[19]短信咨询号码:<br/>");
    Response.Write("<input type=\"text\" name=\"par19\" value=\"" + this.par19 + "\"/>(可为空)<br/>");
    Response.Write("[21]显示运费:");
    Response.Write("<select name=\"par21\">");
    Response.Write("<option value=\"" + this.par21 + "\">" + this.par21 + "</option>");
    Response.Write("<option value=\"0\">0_开启</option>");
    Response.Write("<option value=\"1\">1_关闭</option>");
    Response.Write("</select><br/>");
    Response.Write("[22]允许游客下订(无效):");
    Response.Write("<select name=\"par22\">");
    Response.Write("<option value=\"" + this.par22 + "\">" + this.par22 + "</option>");
    Response.Write("<option value=\"0\">0_开启</option>");
    Response.Write("<option value=\"1\">1_关闭</option>");
    Response.Write("</select><br/>");
    Response.Write("[10]二级页面列表显示图片宽*高:<br/>");
    Response.Write("<input type=\"text\" name=\"par10\" size=\"7\"  value=\"" + this.par10 + "\"/> 格式300*250或300*0<br/>");
    Response.Write("----------<br/>");
    Response.Write("[23]二级页面布局效果，建议0,6,12,13:");
    Response.Write("<select name=\"par23\">");
    Response.Write("<option value=\"" + this.par23 + "\">" + this.par23 + "</option>");
    Response.Write("<option value=\"0\">布局0默认</option>");
    for (int i = 0; i < 14; i++)
    {
        if (i == 12)
        {
            Response.Write("<option value=\"12\">布局12(左图正方)</option>");

        }
        else if (i == 13)
        {
            Response.Write("<option value=\"13\">布局13(图长方)</option>");

        }
        else
        {
            Response.Write("<option value=\"" + (i + 1) + "\">布局" + (i + 1) + "</option>");
        }
    }
    Response.Write("<option value=\"100\">100_自定义万能UBB布局，配合【26】</option>");
    Response.Write("</select><br/>");
    Response.Write("[26]用自定义万能UBB编辑好代码(填写html/WML模块中的标题ID):<br/>");
    Response.Write("[getwml=<input type=\"text\" name=\"par26\"  size=\"5\" value=\"" + this.par26 + "\"/>]  <a href=\"/UBB_API/templace/class/ubbDoc/functionData.htm\" target=\"_blank\">参考应用二</a><br/>");
    Response.Write("----------<br/>");
    Response.Write("[24]三级页面中“颜色分类”改成如套餐、尺寸、颜色等，价格会跟着变:<br/>");
    Response.Write("<input type=\"text\" name=\"par24\" value=\"" + this.par24 + "\"/><br/>");

    Response.Write("[25]三级页面中“其它属性”改成如颜色，价格不会变<br/>");
    Response.Write("<input type=\"text\" name=\"par25\" value=\"" + this.par25 + "\"/><br/>");

    Response.Write("[27]交易成功后才可以评论:");
    Response.Write("<select name=\"par27\">");
    Response.Write("<option value=\"" + par27 + "\">" + par27 + "</option>");
    Response.Write("<option value=\"0\">0_关闭</option>");
    Response.Write("<option value=\"1\">1_开启</option>");
    Response.Write("</select><br/>");
    Response.Write("[28]库存为0时自动下架:");
    Response.Write("<select name=\"par28\">");
    Response.Write("<option value=\"" + par28 + "\">" + par28 + "</option>");
    Response.Write("<option value=\"0\">0_关闭</option>");
    Response.Write("<option value=\"1\">1_开启</option>");
    Response.Write("</select><br/>");
    Response.Write("[29]显示分享:");
    Response.Write("<select name=\"par29\">");
    Response.Write("<option value=\"" + par29 + "\">" + par29 + "</option>");
    Response.Write("<option value=\"0\">0_开启</option>");
    Response.Write("<option value=\"1\">1_关闭</option>");
    Response.Write("</select><br/>");
    
    if (userVo.managerlvl == "00" || userVo.managerlvl == "01")
    {
        Response.Write("<hr/>");
        Response.Write("是否更新所有商店栏目？");
        Response.Write("<br/><input type=\"text\"  name=\"chkall\" size=\"3\" value=\"no\"/>如果是输入yes");
        Response.Write("<hr/>");
    }
    Response.Write("<input type=\"hidden\" name=\"action\" value=\"gomod\"/>");
    Response.Write("<input type=\"hidden\" name=\"classid\" value=\"" + classid + "\"/>");
    Response.Write("<input type=\"hidden\" name=\"siteid\" value=\"" + siteid + "\"/>");
    Response.Write("<input type=\"hidden\" name=\"sid\" value=\"" + sid + "\"/>");
    Response.Write("<input type=\"submit\" name=\"g\" value=\"" + this.GetLang("保 存|保 存|save") + "\"/>");
    Response.Write("</form>");
    Response.Write("</form></div>");
    Response.Write("<div class=\"tip\">");
    Response.Write("说明:其它请到【页面综合排版】→【修改栏目】中配置。");
    Response.Write("</div>");
    Response.Write("<div class=\"btBox\"><div class=\"bt1\">");
    Response.Write("<a href=\"" + this.http_start + "shop/admin_userlistWAP.aspx?siteid=" + this.siteid + "&amp;classid=" + this.classid + "\">" + this.GetLang("返回上级|返回上级|Back to set") + "</a> ");
  
    Response.Write("</div></div>");
 

                                                                                                                                                                               
                                                                                                                                                                             
//显示底部
Response.Write(WapTool.showDown(wmlVo)); %>


