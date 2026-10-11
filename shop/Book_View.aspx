<%@ Page Language="C#" AutoEventWireup="true" CodeBehind="Book_View.aspx.cs" Inherits="KeLin.WebSite.shop.Book_View" %><%@ Import namespace="KeLin.ClassManager.Tool" %><% 
Response.Write(WapTool.showTop(bookVo.book_title, wmlVo));//显示头  
string[] xinghaolist = bookVo.book_xinghao.Split('/');  //商品型号多种 

strhtml.Append("<script type=\"text/javascript\">var selcolorcount=0;var selxinghaocount=0 ;</script>");
   
    //显示广告
    if (adVo.threeShowTop != "")
    {
        strhtml.Append(adVo.threeShowTop );
    }
    strhtml.Append("<!--web-->");
    strhtml.Append("<form name=\"form1\" action=\"/shop/book_view_add.aspx\" method=\"post\" >");
    strhtml.Append("<div id=\"slideBox_shop\" class=\"slideBox_shop\">");
    strhtml.Append("<div class=\"bd\">");
    strhtml.Append("<ul>");
 
    string[] arrys = bookVo.book_file.Split('|');
    for (int y = 0; y < arrys.Length; y++)
    {
        if (arrys[y].Trim() != "")
        {
            strhtml.Append("<li><a class=\"pic\" href=\"/shop/" + arrys[y].Trim() + "\"><img src=\"/shop/" + arrys[y].Trim() + "\" /></a></li>");
        }
    }

    strhtml.Append("</ul>");
    strhtml.Append("</div>");
    strhtml.Append("<div class=\"hd\">");
    strhtml.Append("<ul></ul>");
    strhtml.Append("</div>");
    strhtml.Append("</div>");
    strhtml.Append("<script type=\"text/javascript\">\n");
    strhtml.Append("TouchSlide({ \n");
    strhtml.Append("slideCell:\"#slideBox_shop\",\n");
    strhtml.Append("titCell:\".hd ul\", //开启自动分页 autoPage:true ，此时设置 titCell 为导航元素包裹层\n");
    strhtml.Append("mainCell:\".bd ul\", \n");
    strhtml.Append("effect:\"leftLoop\", \n");
    strhtml.Append("autoPage:true,//自动分页\n");
    strhtml.Append("autoPlay:true //自动播放\n");
    strhtml.Append("});\n");
    strhtml.Append("</script>\n");
    
    strhtml.Append("<div class=\"shopTitle\"><h1>");
    if (bookVo.book_top == 1)
    {
        strhtml.Append("<img src=\"" + this.http_start + "NetImages/ding.gif\" alt=\"顶\"/>");

    }
    if (bookVo.book_good == 1)
    {
        strhtml.Append("<img src=\"" + this.http_start + "NetImages/jing.gif\" alt=\"精\"/>");

    }
    strhtml.Append(bookVo.book_title + "</h1>");
    strhtml.Append("<p>"+bookVo.book_tip + "</p>");
    strhtml.Append("</div>");
    
    strhtml.Append("<div id=\"detail\">");
    strhtml.Append("<div class=\"shopPrice\">");
    strhtml.Append("<dl class=\"tm-promo-panel tm-promo-cur\">");
    strhtml.Append("<dt class=\"tb-metatit\">促销价</dt>");
    strhtml.Append("<dd>");
    strhtml.Append("<div class=\"tm-promo-price\"> <em class=\"tm-yen\">¥</em> <span class=\"tm-price\"><span id=\"rmb2\">" + bookVo.book_yhjiage.ToString("f2") + "</span></span><em class=\"tm-promo-type \"><s></s><span id=\"dazhe\">" + WapTool.OnSale(bookVo.book_jiage, bookVo.book_yhjiage) + "</span>折</em> </div>");
    strhtml.Append("<p></p>");
    strhtml.Append("</dd>");
    strhtml.Append("</dl>");
    strhtml.Append("<dl class=\"tm-price-panel\">");
    strhtml.Append("<dt class=\"tb-metatit\">原　价</dt>");
    strhtml.Append("<dd><em class=\"tm-yen\">¥</em> <span class=\"tm-prices\"><span  id=\"rmb1\" class=\"gray\" style=\"text-decoration:line-through\">" + bookVo.book_jiage.ToString("f2") + "</span></span></dd>");
    strhtml.Append("</dl>");
    
    
    strhtml.Append("<dl class=\"tm-price-panel\">");
    strhtml.Append("<dt class=\"tb-metatit\">运　费</dt>");
    if (bookVo.book_charge.ToString("f2") != "0.00")
    {
        strhtml.Append("<dd><em class=\"yunfei\">");
        strhtml.Append("<select id=\"book_charge\"  name=\"book_charge\">");
        for (int i = 0; (idlistVo != null && i < idlistVo.Count); i++)
        {
            strhtml.Append("<option value=\"" + idlistVo[i].rank + "\">" + idlistVo[i].subclassName + ":￥" + idlistVo[i].rank + ".00</option>");
        }
        strhtml.Append("</select>");

        strhtml.Append("</em></dd>");
    }
    else
    {
        strhtml.Append("<dd><em class=\"yunfei\">免运费</em></dd>");
    }
    
    strhtml.Append("</dl>");


    if (bookVo.autoDelivery != 0)
    {
        strhtml.Append("<dl class=\"tm-price-panel\">");
        strhtml.Append("<dt class=\"tb-metatit\">方　式</dt>");
        strhtml.Append("<dd><em class=\"yunfei\">自动发货(需单独购买提交)</em></dd>");
        strhtml.Append("</dl>");
    }
    
    strhtml.Append("</div>");
  

    strhtml.Append("<div class=\"tb-meta\">");
    strhtml.Append("<dl class=\"tm-delivery-panel\">");
    strhtml.Append("<dt class=\"tb-metatit\">好　评</dt>");
    strhtml.Append("<dd>");
    strhtml.Append("<div class=\"tb-postAge\">" + bookVo.book_xingxing + "</div> ");
    strhtml.Append("</dd>");
    strhtml.Append(" </dl>");
    strhtml.Append("</div>");
    strhtml.Append("<div class=\"tb-meta\">");
    strhtml.Append(" <dl class=\"tm-delivery-panel\">");
    strhtml.Append("<dt class=\"tb-metatit\">人　气</dt>");
    strhtml.Append("<dd>");
    strhtml.Append("<div class=\"signText\">" + bookVo.book_click + "</div>");
    strhtml.Append("</dd>");
    strhtml.Append("</dl>");
    strhtml.Append("</div>");
    strhtml.Append("<div class=\"tm-ind-panel\">");
    strhtml.Append("<ul>");
    strhtml.Append("<li>已售");
    strhtml.Append("<p>" + bookVo.saleCount + "件</p>");
    strhtml.Append("</li>");
    strhtml.Append("<li><a href=\"" + http_start + "shop/book_re.aspx?pageback=&amp;getTotal=" + bookVo.book_re + "&amp;id=" + id + "&amp;classid=" + classid + "&amp;siteid=" + siteid + "" + "\">累计评价");
    strhtml.Append("<p>" + bookVo.book_re + "</p>");
    strhtml.Append("</a></li>");
    strhtml.Append("<li style=\"border-right:none;\">送" + siteVo.sitemoneyname);
    strhtml.Append(" <p><span id=\"color_sendmoney\">" + bookVo.book_shortmessage + "</span></p>");
    strhtml.Append(" </li>");
    strhtml.Append("</ul>");
    strhtml.Append(" </div>");

    strhtml.Append(" <div class=\"tb-sku\">");
    strhtml.Append(" <dl class=\"tb-prop tm-sale-prop tm-clear tm-img-prop\">");
    strhtml.Append("  <dt class=\"tb-metatit\">" + shopcolor_typename + "</dt>");
    strhtml.Append("  <dd>");
    strhtml.Append("   <ul data-property=\"" + shopcolor_typename + "\" class=\"tm-clear J_TSaleProp tb-img\">");
    if (listVo != null)
    {
        
        for (int i = 0; (listVo != null && i < listVo.Count); i++)
        {
            if (listVo[i].colorImage != "")
            {
                strhtml.Append("<li class=\"tb-txt\"><a class=\"\" href=\"javascript:;\" onClick=\"javascript:selcolor('" + listVo[i].shopID + "','" + listVo[i].color + "','" + listVo[i].colorRMB1.ToString("f2") + "','" + listVo[i].colorRMB2.ToString("f2") + "','" + listVo[i].total + "','" + listVo[i].sendMoney + "','" + listVo[i].keyID + "','" + listVo[i].colorImage + "',this)\"><span><img src=\"" + listVo[i].colorImage + "\" alt=\"" + listVo[i].color + "\" style=\"width:50px;height:38px\"/></span></a>");

            }
            else
            {
                strhtml.Append("<li class=\"tb-txt\"><a class=\"\" href=\"javascript:;\" onClick=\"javascript:selcolor('" + listVo[i].shopID + "','" + listVo[i].color + "','" + listVo[i].colorRMB1.ToString("f2") + "','" + listVo[i].colorRMB2.ToString("f2") + "','" + listVo[i].total + "','" + listVo[i].sendMoney + "','" + listVo[i].keyID + "','" + listVo[i].colorImage + "',this)\"><span>" + listVo[i].color + "</span></a>");
            }
        }

        strhtml.Append("<script language=\"javascript\">\n selcolorcount= 1;\n </script>");
    }
    strhtml.Append("<input type=\"hidden\" id=\"selcolored\" name=\"selcolored\" />");
                                                                                                                                                                           
                                                                                                                                                                               
    strhtml.Append(" </ul>");
    strhtml.Append("</dd>");
    strhtml.Append("</dl>");
    strhtml.Append("</div>");


    strhtml.Append("<div class=\"tb-sku\">");
    if (xinghaolist.Length > 1)
    {
    strhtml.Append("<dl class=\"tb-prop tm-sale-prop tm-clear tm-img-prop \">");
    strhtml.Append("<dt class=\"tb-metatit\">" + shopxinghao_typename + "</dt>");
    strhtml.Append("<dd>");
    strhtml.Append(" <ul data-property=\"" + shopxinghao_typename + "\" class=\"tm-clear J_TSaleProp tb-img  \">");
    for (int i = 0; i < xinghaolist.Length; i++)
    {

        strhtml.Append("<li class=\"tb-txt\"><a class=\"\" href=\"javascript:;\" onClick=\"javascript:selshop('" + xinghaolist[i] + "',this)\"><span>" + xinghaolist[i] + "</span></a></li>");
        
    }
    strhtml.Append("<script language=\"javascript\">\n selxinghaocount=1;  </script>");
    
  
    strhtml.Append("</ul>");
    
    strhtml.Append("</dd>");
    strhtml.Append("</dl>");
    }
    strhtml.Append("<a name=\"sel\"></a><dl class=\"tb-amount tm-clear\">");
    strhtml.Append("<dt class=\"tb-metatit\">数量</dt>");
    strhtml.Append(" <dd id=\"J_Amount\"><span class=\"tb-amount-widget mui-amount-wrap\"> <span class=\"mui-amount-btn\"  onClick=\"shopAmoun('-')\"> <span class=\"mui-amount-decrease\">-</span> </span>");
    strhtml.Append(" <input id=\"shopnum\" type=\"text\" name=\"shopnum\" class=\"tb-text mui-amount-input\" value=\"1\" maxlength=\"8\" title=\"请输入购买量\">");
    strhtml.Append(" <span class=\"mui-amount-btn\" onClick=\"shopAmoun('+')\"> <span class=\"mui-amount-increase\">+</span> </span> <span class=\"mui-amount-unit\">件</span> </span> <em id=\"J_EmStock\" class=\"tb-hidden\" style=\"display: inline;\">库存<span id=\"kucun\">" + bookVo.book_hottel + "</span>件</em> </dd>");
    strhtml.Append("</dl>");

    strhtml.Append("</div>");
    strhtml.Append("<div class=\"tb-action tm-clear\">");
    strhtml.Append("  <div class=\"tb-btn-buy tb-btn-sku\"> <a id=\"J_LinkBuy\" href=\"javascript:;\" onClick=\"go();\" rel=\"nofollow\" data-addfastbuy=\"true\" title=\"点击此按钮，到下一步确认购买信息。\">立刻购买<span class=\"ensureText\">确认</span></a> </div>");
    strhtml.Append("  <div class=\"tb-btn-basket tb-btn-sku \"><a href=\"javascript:;\" onClick=\"goCar();\"rel=\"nofollow\" id=\"J_LinkBasket\"><i> </i>加入购物车<span class=\"ensureText\">确认</span></a></div>");
    strhtml.Append("</div>");
    //strhtml.Append("</div>");

    strhtml.Append("</div>");

    strhtml.Append("<div class=\"shop_view_nav shop_ul\">");

    strhtml.Append("<li><a");
    if (viewtype == "1")
    {
        strhtml.Append(" class=\"yes\"");
    }
    strhtml.Append(" href=\"" + this.http_start + "shop/book_view.aspx?siteid=" + siteid + "&amp;classid=" + classid + "&amp;id=" + bookVo.id + "&amp;viewtype=1#sel\">产品介绍</a></li>");



    strhtml.Append("<li><a");
    if (viewtype == "2")
    {
        strhtml.Append(" class=\"yes\"");
    }
    strhtml.Append(" href=\"" + this.http_start + "shop/book_view.aspx?siteid=" + siteid + "&amp;classid=" + classid + "&amp;id=" + bookVo.id + "&amp;viewtype=2#sel\">参数</a></li>");

    strhtml.Append("<li><a");
    if (viewtype == "3")
    {
        strhtml.Append(" class=\"yes\"");
    }
    strhtml.Append(" href=\"" + this.http_start + "shop/book_view.aspx?siteid=" + siteid + "&amp;classid=" + classid + "&amp;id=" + bookVo.id + "&amp;viewtype=3#sel\">实拍图</a></li>");

    strhtml.Append("<li><a");
    if (viewtype == "4")
    {
        strhtml.Append(" class=\"yes\"");
    }
    strhtml.Append(" href=\"" + this.http_start + "shop/book_view.aspx?siteid=" + siteid + "&amp;classid=" + classid + "&amp;id=" + bookVo.id + "&amp;viewtype=4#sel\">热评(" + bookVo.book_re + ")</a></li>");
    strhtml.Append("</div>");


    strhtml.Append("<div class=\"content\">");
    

    //显示内容
    if (this.viewtype == "3" && content == "") //实拍图，兼容旧的
    {

        if (bookVo.book_file.Trim() == "") bookVo.book_file = bookVo.book_smallimg;
        string[] filelist = bookVo.book_file.Split('|');
        for (int i = 0; (i < filelist.Length); i++)
        {
            if (filelist[i] != "")
            {
                if (filelist[i].StartsWith("/") || filelist[i].ToLower().StartsWith("http://"))
                {
                    strhtml.Append("<img src=\"" + filelist[i] + "\" alt=\"load...\"/><br/><br/>");
                }
                else
                {
                    strhtml.Append("<img src=\"" + this.http_start + "shop/" + filelist[i] + "\" alt=\"load...\"/><br/><br/>");
                }
            }

        }


    }
    else if (this.viewtype == "4")
    {
        int kk = 0;
        for (int i = 0; (relistVo != null && i < relistVo.Count); i++)
        {
            if (i % 2 == 0)
            {
                strhtml.Append("<div class=\"line1\">");
            }
            else
            {
                strhtml.Append("<div class=\"line2\">");
            }
            strhtml.Append("<span class=\"right\">");
            strhtml.Append(string.Format("{0:MM-dd HH:mm}", relistVo[i].redate));
            strhtml.Append("&nbsp;&nbsp;" + (bookVo.book_re - kk) + "楼 <img src=\"/Template/default/img/huifu.png\" style=\"width:16px;height:16px\" onClick=\"window.location.href='" + this.http_start + "shop/book_re_add.aspx?siteid=" + this.siteid + "&amp;classid=" + this.classid + "&amp;page=" + this.CurrentPage + "&amp;id=" + this.id + "&amp;reid=" + relistVo[i].id + "&amp;sid=" + this.sid + "';\" alt=\"回复\"/>");
            strhtml.Append("</span>");
            strhtml.Append("<a href=\"" + http_start + "bbs/userinfo.aspx?siteid=" + siteid + "&amp;touserid=" + relistVo[i].userid + "&amp;id=" + relistVo[i].id + "&amp;backurl=" + "\">" + relistVo[i].nickname + "</a>:<br/>");
            strhtml.Append(relistVo[i].content);
            //strhtml.Append("<span class=\"right\"></span>");
            strhtml.Append("</div>");
            kk = kk + 1;
        }
        if (relistVo == null)
        {
            strhtml.Append("<div class=\"tip\">暂无评论记录！</div>");
            strhtml.Append("<div class=\"btBox\"><div class=\"bt1\"><a href=\"" + http_start + "shop/book_re.aspx?pageback=&amp;getTotal=" + bookVo.book_re + "&amp;id=" + id + "&amp;classid=" + classid + "&amp;siteid=" + siteid + "" + "\">我要评论</a></div></div>");

        }
       
        
    }
    else
    {
        strhtml.Append(content);

        if (bookVo.book_otherTitle != "")
        {
            strhtml.Append("<br/>");
            for (int i = 0; book_otherTitle != null && i < book_otherTitle.Length; i++)
            {
                strhtml.Append("<a href=\"" + this.http_start + "shop/book_view.aspx?siteid=" + siteid + "&amp;classid=" + classid + "&amp;id=" + this.id + "&amp;lpage=" + this.lpage + "&amp;viewtype=" + this.viewtype + "&amp;other=" + i + "" + "\">");
                strhtml.Append(book_otherTitle[i]);
                strhtml.Append("</a><br/>");
            }

            
        }
        if (bookVo.book_tishi.Trim() != "")
        {
            strhtml.Append("</div><div class=\"subtitle\">温馨提示</div><div class=\"content\">" + bookVo.book_tishi);
        }
    }


    strhtml.Append("</div>");

    if (this.viewtype == "4" && relistVo != null)
    {
        strhtml.Append("<div class=\"btBox\"><div class=\"bt1\"><a href=\"" + http_start + "shop/book_re.aspx?pageback=&amp;getTotal=" + bookVo.book_re + "&amp;id=" + id + "&amp;classid=" + classid + "&amp;siteid=" + siteid + "" + "\">查看更多评论</a></div></div>");

    }



    if (WapTool.getArryString(classVo.smallimg, '|', 29) != "1")
    {

        strhtml.Append("<div id=\"picture_share\" class=\"as-gf\">");
        strhtml.Append("<div class=\"as-tit\">");
        strhtml.Append("分享至: ");
        strhtml.Append("</div>");
        strhtml.Append("<div class=\"as-icon\">");
        strhtml.Append("</div>");
        strhtml.Append("<div class=\"bdsharebuttonbox\"><a href=\"#\" class=\"bds_more\" data-cmd=\"more\"></a><a href=\"#\" class=\"bds_qzone\" data-cmd=\"qzone\" title=\"分享到QQ空间\"></a><a href=\"#\" class=\"bds_tsina\" data-cmd=\"tsina\" title=\"分享到新浪微博\"></a><a href=\"#\" class=\"bds_tqq\" data-cmd=\"tqq\" title=\"分享到腾讯微博\"></a><a href=\"#\" class=\"bds_renren\" data-cmd=\"renren\" title=\"分享到人人网\"></a><a href=\"#\" class=\"bds_weixin\" data-cmd=\"weixin\" title=\"分享到微信\"></a>");
        strhtml.Append("<a title=\"收藏\" class=\"s-fav\" href=\"" + this.http_start + "article/Share.aspx?action=fav&amp;siteid=" + this.siteid + "&amp;classid=" + this.classid + "&amp;id=" + this.id + "&amp;vpage=" + this.CurrentPage + "&amp;lpage=" + this.lpage + "\"></a>");
        strhtml.Append("</div><script>window._bd_share_config={\"common\":{\"bdSnsKey\":{},\"bdText\":\"\",\"bdMini\":\"2\",\"bdMiniList\":false,\"bdPic\":\"\",\"bdStyle\":\"0\",\"bdSize\":\"16\"},\"share\":{}};with(document)0[(getElementsByTagName('head')[0]||body).appendChild(createElement('script')).src='http://bdimg.share.baidu.com/static/api/js/share.js?v=89860593.js?cdnversion='+~(-new Date()/36e5)];</script>");
        strhtml.Append("</div>");
    }
    
    strhtml.Append(linkURL);

    
    
    
   
   
    //strhtml.Append("<a href=\"" + this.http_start + "shop/Share.aspx?action=fav&amp;siteid=" + this.siteid + "&amp;classid=" + this.classid + "&amp;id=" + this.id + "&amp;lpage=" + this.lpage + "\">收藏</a> <a href=\"" + this.http_start + "shop/share.aspx?siteid=" + siteid + "&amp;classid=" + classid + "&amp;id=" + this.id + "&amp;lpage=" + this.lpage + "" + "\">分享</a><br/> ");
  
    //显示上一页下一页
    //if (WapTool.getArryString(classVo.smallimg, '|', 16) != "1")
    //{
    //    strhtml.Append(preNextTitle);
    //}

    
    
    strhtml.Append("<input type=\"hidden\" name=\"action\" id=\"action\" value=\"add\">");
    strhtml.Append("<input type=\"hidden\" name=\"siteid\" id=\"siteid\" value=\""+this.siteid+"\">");
    strhtml.Append("<input type=\"hidden\" name=\"classid\" id=\"classid\" value=\"" + this.classid + "\">");
    strhtml.Append("<input type=\"hidden\" name=\"id\" id=\"bookid\" value=\"" + bookVo.id + "\">");                                                                                                                                                                        
    strhtml.Append("<input type=\"hidden\" name=\"shopname\" id=\"shopname\" value=\""+bookVo.book_title+"\">");
    strhtml.Append("<input type=\"hidden\" name=\"shoprmb\" id=\"shoprmb\" value=\"" + bookVo.book_yhjiage.ToString("f2") + "\">");
    strhtml.Append("<input type=\"hidden\" name=\"shoptotal\" id=\"shoptotal\" value=\"" + bookVo.book_hottel + "\">");
    strhtml.Append("<input type=\"hidden\" name=\"sendmoney\" id=\"sendmoney\" value=\"" + bookVo.book_shortmessage + "\">");
    strhtml.Append("<input type=\"hidden\" name=\"shopcolor\" id=\"shopcolor\">");
    strhtml.Append("<input type=\"hidden\" name=\"shopimage\" id=\"shopimage\">");
    strhtml.Append("<input type=\"hidden\" name=\"bookimage\" id=\"bookimage\" value=\""+bookVo.book_smallimg+"\">");
    strhtml.Append("<input type=\"hidden\" name=\"shopxinghao\" id=\"shopxinghao\">");
    strhtml.Append("<input type=\"hidden\" name=\"shoplistid\" id=\"shoplistid\">");
    strhtml.Append("<input type=\"hidden\" name=\"shopid\" id=\"shopid\" value=\"" + bookVo.id + "\">");
    strhtml.Append("<input type=\"hidden\" name=\"buyorcar\" id=\"buyorcar\">");
    strhtml.Append("<input type=\"hidden\" name=\"shopcolor_typename\" id=\"shopcolor_typename\" value=\"" + shopcolor_typename + "\">");
    strhtml.Append("<input type=\"hidden\" name=\"shopxinghao_typename\" id=\"shopxinghao_typename\" value=\"" + shopxinghao_typename + "\">");
    strhtml.Append("</form>");
    
    strhtml.Append("<script language=\"javascript\">\n ");
    strhtml.Append("function go(){ \n if(document.getElementById('shopnum').value < 1 ) { showBox('亲，商品数不能为负数哦');return;} \n if(selcolorcount==1 && document.getElementById('shopcolor').value ==\"\"){ showBox('请选择" + shopcolor_typename + "！');return;} \n if(selxinghaocount==1 && document.getElementById('shopxinghao').value ==\"\"){ showBox('请选择" + shopxinghao_typename + "！');return;} \n document.getElementById('buyorcar').value = 'buy'; \n form1.submit(); \n} \n");
    strhtml.Append("function goCar(){\n if(document.getElementById('shopnum').value < 1) { showBox('亲，商品数不能为负数哦');return;} \n if(selcolorcount==1 && document.getElementById('shopcolor').value ==\"\"){ showBox('请选择" + shopcolor_typename + "！');return;} \n if(selxinghaocount==1 && document.getElementById('shopxinghao').value ==\"\"){ showBox('请选择" + shopxinghao_typename + "！');return;} \n document.getElementById('buyorcar').value = 'car'; \n myajax('POST',getFormUrl(this.form1),'');\n}\n");
    strhtml.Append("function fnTrue(str,messageid){ if(str==\"OK\"){showBox('添加成功！');}	else{	showBox('添加失败！请先登录网站哦！');}}\n");
    strhtml.Append("function fnFalse(messageid){showBox('添加失败！请先登录网站哦!');}");
    strhtml.Append("</script>");

    strhtml.Append("<script language=\"javascript\">");
    strhtml.Append("function shopAmoun(type){");
    strhtml.Append("var oShopAmount = document.getElementById(\"shopnum\");");
    strhtml.Append("var kucuns = document.getElementById(\"shoptotal\");");
    strhtml.Append("if(type == \"+\"){");
    strhtml.Append("	 oShopAmount.value++;  if(parseInt(oShopAmount.value) > parseInt(kucuns.value) ) {oShopAmount.value = kucuns.value ; showBox('超过库存啦！');}");
    strhtml.Append("}else{");
    strhtml.Append("	 if(oShopAmount.value > 1) { oShopAmount.value--; }else {showBox('至少选一件哦！');} ");
    strhtml.Append("}");
    strhtml.Append("}</script>");

    strhtml.Append("<script language=\"javascript\">\n function selshop(str,obj)\n { \n document.getElementById('shopxinghao').value = str; \n var aTag = obj.parentNode.parentNode.getElementsByTagName('a'); \n for(var i=0;i<aTag.length;i++) \n{ \n aTag[i].className = ''; \n } \n obj.className = 'a'; \n}  \n </script>");
    strhtml.Append("<script language=\"javascript\">\n function selcolor(a1,a2,a3,a4,a5,a6,a7,a8,obj) \n{ \n document.getElementById('shopid').value=a1; \n document.getElementById('shopcolor').value=a2;\n document.getElementById('shoprmb').value=a4;\n document.getElementById('shoptotal').value=a5; \n document.getElementById('sendmoney').value=a6;\n document.getElementById('shoplistid').value=a7;\n document.getElementById('shopimage').value=a8;  \n     document.getElementById('color_sendmoney').innerHTML = a6; \n document.getElementById('kucun').innerHTML = a5;\n document.getElementById('rmb1').innerHTML = a3; \n document.getElementById('rmb2').innerHTML = a4; \nvar dazhecount=0;\n dazhecount=Math.round( ((parseFloat(a4)  / parseFloat(a3)) * 10 * 10 ) ) / 10;\n if(dazhecount>=10){dazhecount=0;} \ndocument.getElementById('dazhe').innerHTML = dazhecount ; \n var aTag = obj.parentNode.parentNode.getElementsByTagName('a'); \n for(var i=0;i<aTag.length;i++) \n { \n aTag[i].className = '';\n } \n obj.className = 'a';\n} \n </script>");
   
    
    
    
    //显示广告,可以输入猜你喜欢商品
    if (adVo.threeShowDown != "")
    {
        strhtml.Append(adVo.threeShowDown);
    }

    strhtml.Append("<div class=\"btBox\"><div class=\"bt2\">");
    strhtml.Append("<a href=\"" + http_start + "shop/orderlistmy.aspx?id=" + id + "&amp;classid=" + classid + "&amp;siteid=" + siteid + "" + "\">我的订单</a>");
    strhtml.Append("<a href=\"" + http_start + "shop/book_view_add.aspx?id=" + id + "&amp;classid=" + classid + "&amp;siteid=" + siteid + "" + "\">购物车([shopcar])</a>");
    strhtml.Append("</div></div>"); 
    
    if (downLink != "")
    {
        strhtml.Append(downLink);
    }
    else
    {
        strhtml.Append("<div class=\"btBox\"><div class=\"bt2\">");
        if (WapTool.ISAPI_Rewrite3_Open == "1")
        {
            strhtml.Append("<a href=\"" + this.http_start + "shoplist-" + classid + "-" + this.lpage + ".html" + "\">返回上级</a> ");
            strhtml.Append("<a href=\"" + this.http_start + "wapindex-" + siteid + "-0.html" + "\">返回首页</a>");

        }
        else
        {
            strhtml.Append("<a href=\"" + this.http_start + "shop/book_list.aspx?siteid=" + siteid + "&amp;classid=" + classid + "&amp;page=" + this.lpage + "" + "\">返回上级</a> ");
            strhtml.Append("<a href=\"" + this.http_start + "wapindex.aspx?siteid=" + siteid + "&amp;classid=0" + "\">返回首页</a>");
        }
        strhtml.Append("</div></div>");
    }
    
    
    //输出错误
    strhtml.Append(ERROR);
    //输出
    Response.Write(WapTool.ToWML(strhtml.ToString(), wmlVo));



Response.Write(WapTool.showDown(wmlVo)); %>
