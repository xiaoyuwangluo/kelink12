<%@ Page Language="C#" AutoEventWireup="true" CodeBehind="book_view_add.aspx.cs" Inherits="KeLin.WebSite.shop.book_view_add" %><%@ Import namespace="KeLin.ClassManager.Tool" %><%

Response.Write(WapTool.showTop("我的购物车", wmlVo));//显示头                                                                                                                                                                       


    Response.Write("<div class=\"subtitle\">我的购物车</div>");
    
    Response.Write(this.ERROR);
        
    /*  
     
      if (action == "")
      {
          Response.Write("<div class=\"btBox\"><div class=\"bt2\"><a href=\"javascript:window.history.back();\">返回上级</a> ");
          Response.Write("<a href=\"" + this.http_start + "shop/book_list.aspx?siteid=" + this.siteid + "&amp;classid=" + this.classid + "&amp;page=" + this.page + "\">继续选购</a></div></div>");
     
      }
      else
      {
          if (this.id != "" && this.id != "0")
          {
              Response.Write("<div class=\"btBox\"><div class=\"bt2\"><a href=\"" + this.http_start + "shop/book_view.aspx?siteid=" + this.siteid + "&amp;classid=" + this.classid + "&amp;id=" + this.id + "\">返回上级</a> ");
              Response.Write("<a href=\"" + this.http_start + "shop/book_list.aspx?siteid=" + this.siteid + "&amp;classid=" + this.classid + "&amp;page=" + this.page + "\">继续选购</a></div></div>");

          }
          else
          {
              Response.Write("<div class=\"btBox\"><div class=\"bt1\"><a href=\"" + this.http_start + "shop/book_list.aspx?siteid=" + this.siteid + "&amp;classid=" + this.classid + "&amp;page=" + this.page + "\">继续选购</a></div></div>");
     
          }
      }
*/

      
      Response.Write("<form name=\"form1\" action=\"/shop/book_view_addorder.aspx\" method=\"post\" >");
       
      for (int i = 0; (this.shopCarListVo != null && i < shopCarListVo.Count); i++)
      {
          if (i % 2 == 0)
          {
              Response.Write("<div class=\"line1\">");
          }
          else
          {
             Response.Write("<div class=\"line2\">");
          }


          /*******************新版排版*******************/
          Response.Write("<div id=\"shop-list"+i+"\" class=\"shop-list\">");
          Response.Write("<div class=\"shop-list-view\">");
          Response.Write("<div class=\"ico\"><a href=\"/shop/view.aspx?id=" + shopCarListVo[i].shopID + "\" title=\"\"><img class=\"p-pic\" src=\"" + shopCarListVo [i].shopImage+ "\" style=\"visibility: visible;\"></a></div>");
          Response.Write("<div class=\"d\"> <a href=\"/shop/view.aspx?id=" + shopCarListVo[i].shopID + "\" title=\"\">");
          Response.Write("<h3 class=\"d-title\">" + shopCarListVo [i].shopName+ "</h3>");
          Response.Write("</a>");
          Response.Write("<p class=\"d-price\" style=\"line-height:30px\"><em class=\"h\">￥" + shopCarListVo[i].shopRMB + "</em> <span class=\"d-freight\"><span class=\"cut\" onClick=\" document.getElementById('shopnum" + i + "').value -- ; if(parseInt(document.getElementById('shopnum" + i + "').value) <1) {document.getElementById('shopnum" + i + "').value = '1' ; showBox('至少选一件哦！');} ; goChecking();  \">&nbsp;&nbsp;-&nbsp;&nbsp;</span>");
          Response.Write("&nbsp;<input name=\"shopnum\" id=\"shopnum"+i+"\" type=\"text\" class=\"tb-text mui-amount-input\" value=\"" + shopCarListVo [i].shopNum+ "\" maxlength=\"8\" style=\"width:30px;\" title=\"请输入购买量\">&nbsp;");
          Response.Write("<span class=\"cut\" onClick=\"document.getElementById('shopnum" + i + "').value ++ ; if(parseInt(document.getElementById('shopnum" + i + "').value) > parseInt(document.getElementById('shoptotal" + i + "').value) ) {document.getElementById('shopnum" + i + "').value = document.getElementById('shoptotal" + i + "').value ; showBox('超过库存啦！');}; goChecking();\">&nbsp;&nbsp;+&nbsp;&nbsp;</span></span></p>");
          Response.Write("<div class=\"d-main\" style=\"height:35px\">");
          Response.Write("<p class=\"d-freight\" style=\"line-height:35px\">" + shopCarListVo[i].shopColor + "&nbsp;&nbsp;&nbsp;" + shopCarListVo [i].shopXinghao+ "</p><div class=\"quxiao\"><span class=\"qx\" onClick=\"window.location.href='/shop/book_view_add.aspx?action=del&siteid="+this.siteid +"&classid="+this.classid+"&delid="+shopCarListVo [i].id+"';\">删除</span> </div></div>");

          Response.Write("</div>");
          Response.Write("</div>");
          Response.Write("</div>");

          Response.Write("<input type=\"hidden\" name=\"shopname\" id=\"shopname" + i + "\" value=\"" + shopCarListVo[i].shopName + "\">");
          Response.Write("<input type=\"hidden\" name=\"shoprmb\" id=\"shoprmb" + i + "\" value=\"" + shopCarListVo[i].shopRMB.ToString("f2") + "\">");
          Response.Write("<input type=\"hidden\" name=\"shoptotal\" id=\"shoptotal" + i + "\" value=\"" + shopCarListVo[i].shopTotal + "\">");
          Response.Write("<input type=\"hidden\" name=\"sendmoney\" id=\"sendmoney" + i + "\" value=\"0\">");
          Response.Write("<input type=\"hidden\" name=\"shopcolor\" id=\"shopcolor" + i + "\" value=\"" + shopCarListVo[i].shopColor + "\">");
          Response.Write("<input type=\"hidden\" name=\"shopimage\" id=\"shopimage" + i + "\" value=\"" + shopCarListVo[i].shopImage + "\">");
          Response.Write("<input type=\"hidden\" name=\"shopxinghao\" id=\"shopxinghao" + i + "\" value=\"" + shopCarListVo[i].shopXinghao + "\">");
          Response.Write("<input type=\"hidden\" name=\"shoplistid\" id=\"shoplistid" + i + "\" value=\"" + shopCarListVo[i].shopListID + "\">");
          Response.Write("<input type=\"hidden\" name=\"shopid\" id=\"shopid" + i + "\" value=\"" + shopCarListVo[i].shopID + "\">");
          Response.Write("<input type=\"hidden\" name=\"shopcolor_typename\" id=\"shopcolor_typename" + i + "\" value=\"" + shopCarListVo[i].shopColor_typeName + "\">");
          Response.Write("<input type=\"hidden\" name=\"shopxinghao_typename\" id=\"shopxinghao_typename" + i + "\" value=\"" + shopCarListVo[i].shopXinghao_typeName + "\">");
  
          Response.Write("</div>");
   
      }
      if (this.shopCarListVo == null)
      {
          Response.Write("<div class=\"line1\">还没有选购商品，<a href=\"" + this.http_start + "shop/book_list.aspx?siteid=" + this.siteid + "&amp;classid=" + this.classid + "&amp;page=" + this.page + "\">去瞧瞧～</a></div>");
      }

      Response.Write("<div class=\"buy-form\"><p>");
      
      if (idlistVo != null && idlistVo.Count > 0)
      {
          Response.Write("运费:<select id=\"book_charge\" name=\"book_charge\" onChange = \"goChecking();\">");
          for (int i = 0; (idlistVo != null && i < idlistVo.Count); i++)
          {
              Response.Write("<option value=\"" + idlistVo[i].rank + "\">" + idlistVo[i].subclassName + ":￥" + idlistVo[i].rank + ".00</option>");
          }
          Response.Write("</select>");
      }
      else
      {
          Response.Write("免运费<input type=\"hidden\" id=\"book_charge\" name=\"book_charge\" value=\"0\">");
      }

      Response.Write("</p>");
      Response.Write("<p class=\"checkbox-btn\">共 <span id=\"totalcount\"  class=\"shuliang\">0</span> 件 合计:<span id=\"totalmoney\" class=\"jiage\">0</span>元</p>");
      Response.Write("<!--<div class=\"buy-form-btn\">结算</div>-->");
      Response.Write("</div>");


      //计费开始
      Response.Write("\n<script language=\"javascript\">\n ");
      if (this.shopCarListVo != null)
      {
          Response.Write("var count = " + this.shopCarListVo.Count + "; \n ");
      }
      else
      {
          Response.Write("var count = 0;\n ");
      }
      Response.Write("function goChecking() { \n ");    
      
      Response.Write("var total_count = 0;\n ");
      Response.Write("var total_money = 0;\n ");
      //Response.Write("alert(count);\n ");
      Response.Write(" for(var i=0; i<count; i++){ \n ");
      Response.Write(" total_count = total_count + parseInt(document.getElementById('shopnum' + i).value); \n ");
      Response.Write(" total_money = total_money + parseFloat(document.getElementById('shoprmb' + i).value) * parseInt(document.getElementById('shopnum' + i).value) ;  \n ");
      
      Response.Write("} \n ");
      Response.Write(" total_money = total_money  + parseFloat(document.getElementById('book_charge').value);\n ");
      Response.Write(" total_money = total_money.toFixed(2) \n ");
      Response.Write(" document.getElementById('totalcount').innerHTML =  total_count; \n ");
      Response.Write(" document.getElementById('totalmoney').innerHTML =  total_money; \n ");
      Response.Write("} \n ");
      Response.Write("window.onload = goChecking();\n");
      Response.Write("</script>\n ");
    
    Response.Write("<div class=\"subtitle\">填写收货信息</div>");
    Response.Write("<div class=\"content\">");
    Response.Write(this.GetLang("我的姓名|我的姓名|Name") + "*:<span class=\"right\"><a href=\"" + this.http_start + "shop/modifyaddress.aspx?siteid=" + this.siteid + "&amp;classid=" + this.classid + "&amp;id=" + this.id + "\"><span class=\"urlbtn2\">编辑地址</span></a></span><br/>");
    Response.Write("<input type=\"text\" name=\"myname\" class=\"txt\" value=\"" + this.myname + "\"/><br/>");
    Response.Write( "手机号码*:<br/>");
    Response.Write("<input type=\"text\" name=\"tel\" class=\"txt\" value=\"" + this.tel + "\"/><br/>");

    Response.Write(this.GetLang("收货地址|收货地址|address") + "*:<br/>");
    Response.Write("<input type=\"text\" name=\"address\" class=\"txt\" value=\"" + this.address + "\"/><br/>");

    Response.Write("下单留言(如备用电话):<br/>");
    Response.Write("<input type=\"text\" name=\"remark\" class=\"txt\" value=\"" + this.content + "\"/><br/>");
    Response.Write("付费方式:");
    Response.Write("<select name=\"payAction\" class=\"txt\">");
        if (payType == "1")
      {
          Response.Write("<option value=\"1\">货到付款</option>");
      }
        else if (payType == "2")
        {
            Response.Write("<option value=\"0\">在线支付</option>");
        }
        else if (payType == "3")
        {
            Response.Write("<option value=\"3\">我的RMB帐户额支付</option>");
        }
        else
        {
            Response.Write("<option value=\"0\">在线支付</option>");
            Response.Write("<option value=\"1\">货到付款</option>");
           
        }
    Response.Write("</select><br/>");
        
    Response.Write("<input type=\"hidden\" name=\"action\" value=\"gomod\"/>");
    Response.Write("<input type=\"hidden\" name=\"id\" value=\"" + id + "\"/>");
    Response.Write("<input type=\"hidden\" name=\"classid\" value=\"" + classid + "\"/>");
    
    Response.Write("<input type=\"hidden\" name=\"siteid\" value=\"" + siteid + "\"/>");    
    Response.Write("<input type=\"hidden\" name=\"sid\" value=\"" + sid + "\"/>");
    Response.Write("<input type=\"button\" id=\"bt\" name=\"bt\" class=\"btn\" onClick=\"if(count <1){showBox('亲，还没有选商品呢。');return;}; for(var i=0; i<count; i++){if(document.getElementById('shopnum' + i).value < 1) { showBox('亲，第'+(i+1)+'个商品数至少一件哦');return;};};  document.getElementById('bt').disabled = 'disabled'; document.getElementById('bt').value='正在提交中...'; form1.submit();\" value=\"提交订单\"/></form>");
 
    Response.Write("</div>");
        
    
   
    Response.Write("<div class=\"btBox\"><div class=\"bt2\">");
    Response.Write("<a href=\"" + this.http_start + "shop/book_list.aspx?siteid=" + this.siteid + "&amp;classid=" + this.classid + "&amp;page=" + this.page + "\">" + this.GetLang("返回上级|返回上级|Back to list") + "</a> ");
    Response.Write("<a href=\"" + this.http_start + "wapindex.aspx?siteid=" + this.siteid + "\">" + this.GetLang("返回首页|返回首页|Back to list") + "</a>");

    Response.Write("</div></div>");
    Response.Write(WapTool.GetVS(wmlVo));
 


                                                                                                                                                                               
                                                                                                                                                                             
//显示底部
Response.Write(WapTool.showDown(wmlVo)); %>


