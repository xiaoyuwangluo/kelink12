<%@ Page Language="C#" AutoEventWireup="true" CodeBehind="admin_WAPmodify.aspx.cs" Inherits="KeLin.WebSite.shop.admin_WAPmodify" %><%@ Import namespace="KeLin.ClassManager.Tool" %><%
//Response.Write(WapTool.showTop(this.GetLang("修改内容|修改內容|content modification"), wmlVo));//显示头    
%>
<!DOCTYPE html><html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<meta http-equiv="Cache-Control" content="no-cache"/>

<link href="/NetCSS/admin/bootstrap-3.3.5/css/bootstrap.min.css" rel="stylesheet" media="screen" type="text/css">
<link href="/NetCSS/admin/summernote/summernote.css" rel="stylesheet" type="text/css">
<link href="/NetCSS/admin/summernote/summernote-bs3.css" rel="stylesheet" type="text/css">
<link href="/NetCSS/admin/summernote/font-awesome.min.css" rel="stylesheet" type="text/css">
<script type="text/javascript" language="javascript" src="/netcss/admin/js/jquery-1.9.1.min.js"></script>
<style type="text/css">
    * { margin: 0; padding: 0; list-style: none; }
body { line-height: 28px; font: 16px/30px "Microsoft YaHei"; margin-bottom: 0px; BACKGROUND: #e7e8eb url(/Template/default/img/bg.jpg); box-shadow: 0 0 15px #ccc; }
.content { background: #FCFCFC; }
.subtitle { line-height: 38px; color: #000; border-bottom: 2px solid #2b73df; border-top: 1px solid #E3E3E3; letter-spacing: 0px; background: url(/Template/default/img/subtitle.gif) no-repeat 2px 50% #FFF; ; padding-left: 15px; }
.subtitle { line-height: 38px; color: #000; border-bottom: 2px solid #2b73df; border-top: 1px solid #E3E3E3; letter-spacing: 0px; background: url(/Template/default/img/subtitle.gif) no-repeat 2px 50% #FFF; ; padding-left: 15px; }
.subtitle a { color: #000; font-weight: 700; }
.tip { line-height: 24px; margin: 10px; border: solid 1px #b3def4; background: #f6fcff; color: #f48a6e; padding: 5px 7px; text-shadow: 0 0 1px rgba(114,106,86,0.3); border-radius: 3px; font-size: 13px; }

.pic{ overflow:hidden; padding:0; padding:10px;}
.pic li{ position:relative; display:inline-block; width:80px; height:80px; margin-right:10px;}
.pic li img{ border:none; width:100%; height:100%;}
.pic li .del{ position:absolute; top:-10px; right:-10px; z-index:10; width:20px; height:20px; background:#fff; border-radius:50%; box-shadow:0 0 3px #ccc;}
</style>
<meta name="keywords" content=" 成品网站&amp;模版 微网站，手机网站，APP制作" />
<meta name="description" content=" 柯林程序官方网_手机自助建站平台,柯林CMS,手机CMS,APP制作,微网站 柯林程序官方网_手机自助建站平台,柯林CMS,手机CMS,APP制作,微网站 kelink.com专注于WAP建站,手机建站,WAP网站,WAPCMS,微信CMS,微信营销CMS,微网站,APP制作 " />
<meta name="author" content="柯林程序官方网_手机自助建站平台,柯林CMS,手机CMS,APP制作,微网站 kelink.com" />
<meta name="baidu-site-verification" content="585b8f9e100f8cf17ebc100554648927" />
<title>修改内容</title>
<body>
<%                                                                                                                                                                   
    Response.Write("<div style=\"max-width:900px;margin:0px auto;\">");
    Response.Write("<SCRIPT language=javascript>\r\n");
    Response.Write("function addImgOrFile(file,typeid){ \r\n");
    Response.Write("if (typeid == \"1\")\r\n");
    Response.Write("{\r\n");
    Response.Write("document.getElementById('book_content').value = document.getElementById('book_content').value + \"[img]\"+file+\"[/img]\";\r\n");
    Response.Write("}else if(typeid==\"2\"){\r\n");

    Response.Write("document.getElementById('book_content_img').value = document.getElementById('book_content_img').value + \"[img]\"+file+\"[/img][br][br]\";\r\n");
    Response.Write("}else if(typeid==\"3\"){\r\n");
    Response.Write("document.getElementById('book_gongxiao').value = document.getElementById('book_gongxiao').value + \"[img]\"+file+\"[/img][br][br]\";\r\n");
    Response.Write("}else if(typeid==\"4\"){\r\n");
    Response.Write("document.getElementById('book_tishi').value = document.getElementById('book_tishi').value + \"[img]\"+file+\"[/img][br][br]\";\r\n");
    Response.Write("}else if(typeid==\"5\"){\r\n");
    Response.Write("document.getElementById('book_otherContent').value = document.getElementById('book_otherContent').value + \"[img]\"+file+\"[/img][br][br]\";\r\n");
    
    Response.Write("}\r\n");

    Response.Write("}\r\n");

    Response.Write("function addpic(a){\r\n");
    Response.Write("window.open(\"uploadPIC.aspx?siteid=" + this.siteid + "&amp;classid=" + this.classid + "&amp;typeid=\"+a, \"addpic\", \"height=180, width=380, toolbar= no, menubar=no, scrollbars=yes, resizable=no, location=no, status=no\");\r\n");

    Response.Write("}\r\n");
    Response.Write("</script>");
    Response.Write("<div class=\"subtitle\">" + this.GetLang("修改操作|修改操作|Modify operation") + "</div>");
    Response.Write("<div class=\"tip\">");
    Response.Write(this.ERROR);
    if (this.INFO == "OK")
    {
        Response.Write("<b>");
        Response.Write(this.GetLang("修改成功！|修改成功！|Successfully modified"));
        Response.Write("</b>");
        Response.Write("<a href=\"" + this.http_start + "shop/admin_userlistWAP.aspx?siteid=" + this.siteid + "&amp;classid=" + this.classid + "&amp;page=" + this.page + "\">" + this.GetLang("返回列表|返回列表|Back to list") + "</a><br/>");
    }
    else if (this.INFO == "NULL")
    {
        Response.Write("<b>标题不能小于2个字符！</b><br/>");

    }
    else if (this.INFO == "COUNTNULL")
    {
        Response.Write("<b>自定义宣传标题和内容中的“|”个数不一致！</b><br/>");

    }
    else
    {
        Response.Write("<b>此页面包含动态脚本，建议用电脑IE9访问此页面。</b><br/>");
    }
    Response.Write("</div>");
    Response.Write("<div class=\"content\">");
    Response.Write("<form name=\"go\" action=\"" + this.http_start + "shop/admin_WAPmodify.aspx\" method=\"post\">");
    Response.Write("列表显示图:");
    Response.Write("<a href=\"" + this.http_start + "shop/admin_wapmodifyadd.aspx?types=1&siteid=" + this.siteid + "&classid=" + this.classid + "&id=" + this.id + "&sid=" + this.sid + "\">【编辑图片】</a><br/>");
    Response.Write("<img src=\"/shop/" + bookVo.book_smallimg + "\" style=\"width:150px;height:150px\" alt=\"列表显示图\"/><br/>");
   
    Response.Write("<input type=\"hidden\" name=\"book_img\" value=\"" + bookVo.book_smallimg + "\" style=\"width:100%\"/>");    
   
    Response.Write("顶部划图,多个用|区分,");
    Response.Write("<a href=\"" + this.http_start + "shop/admin_wapmodifyadd.aspx?types=2&siteid=" + this.siteid + "&classid=" + this.classid + "&id=" + this.id + "&sid=" + this.sid + "\">【编辑图片】</a><br/>");

    string[] arrys = bookVo.book_file.Split('|');
    for (int y = 0; y < arrys.Length; y++)
    {
        if (arrys[y].Trim() != "")
        {
            Response.Write("<img src=\"/shop/" + arrys[y] + "\" style=\"width:150px;height:150px\" alt=\"划动图\"/>");
        }
    }
    Response.Write("<input type=\"hidden\" name=\"book_file\" value=\"" + bookVo.book_file + "\" style=\"width:100%\" /><br/>");
    //Response.Write("图片介绍:(对应上面，多个地址用|隔开)<br/>");

    Response.Write("商品名称*:<br/>");
    Response.Write("<input type=\"text\" name=\"book_title\" value=\"" + bookVo.book_title + "\" style=\"width:100%\"/><br/>");

    Response.Write("<input type=\"hidden\" name=\"book_fileINFO\" value=\"" + bookVo.book_fileINFO + "\"/>");
    Response.Write("一句话广告语:<br/><input type=\"text\" name=\"book_tip\" value=\"" + bookVo.book_tip + "\" style=\"width:100%\"/><br/>");



   
    Response.Write("默认显示"+this.GetLang("市场价格|市场价格|Title") + "*:");
    Response.Write("<input type=\"text\" name=\"book_jiage\" value=\"" + bookVo.book_jiage.ToString("f2") + "\" size=\"5\"/> ");
    Response.Write(this.GetLang("会员价格|会员价格|Title") + "*:");
    Response.Write("<input type=\"text\" name=\"book_yhjiage\" value=\"" + bookVo.book_yhjiage.ToString("f2") + "\" size=\"5\"/>");
    Response.Write(this.GetLang("库存|库存|库存") + "*:");
    Response.Write("<input type=\"text\" name=\"book_hottel\" value=\"" + bookVo.book_hottel + "\" size=\"5\"/> ");
    Response.Write("送" + siteVo.sitemoneyname + ":");
    Response.Write("<input type=\"text\" name=\"book_shortmessage\" value=\"" + bookVo.book_shortmessage + "\" size=\"5\"/>");
    

    Response.Write("<br/>");
    Response.Write("免运费:<select name=\"book_charge\">");
    if (bookVo.book_charge.ToString("f2") == "0.00")
    {
        Response.Write("<option value=\"0.00\">是</option>");
    }
    Response.Write("<option value=\"1.00\">否</option>");
    Response.Write("<option value=\"0.00\">是</option>");
    Response.Write("</select>");
    
     
    //Response.Write(this.GetLang("短信咨询|短信咨询|短信咨询") + "*:");
    //Response.Write("<input type=\"text\" name=\"book_shortmessage\" value=\"" + bookVo.book_shortmessage + "\"/><br/>");

    Response.Write(this.GetLang("人气|人气|人气") + " :");
    Response.Write("<input type=\"text\" name=\"book_click\" value=\"" + bookVo.book_click + "\" size=\"5\"/> ");

    Response.Write(this.GetLang("已售|已售|已售") + " :");
    Response.Write("<input type=\"text\" name=\"saleCount\" value=\"" + bookVo.saleCount + "\" size=\"5\"/> ");

    Response.Write("推荐指数:<select name=\"book_xingxing\">");
    Response.Write("<option value=\"" + bookVo.book_xingxing + "\">" + bookVo.book_xingxing + "</option>");
    Response.Write("<option value=\"★★★★★\">★★★★★</option>");
    Response.Write("<option value=\"★★★★\">★★★★</option>");
    Response.Write("<option value=\"★★★\">★★★</option>");
    Response.Write("<option value=\"★★\">★★</option>");
    Response.Write("<option value=\"★\">★</option>");
    Response.Write("</select><br/>");


    Response.Write("商品属性多个用/区分,如30码/35码/42码:");
    Response.Write("<input type=\"text\" name=\"book_xinghao\" value=\"" + bookVo.book_xinghao + "\" style=\"width:100%\"/><br/>");
 Response.Write("颜色分类，可输入套餐等，选中自动变化价格库存等：<br/>");
    %>
    
      <table id="batchTableAdd" width="100%" bordercolorlight="#C0C0C0" border="1" cellpadding="0" cellspacing="0" bordercolor="#C0C0C0" bordercolordark="#C0C0C0">
	<tr>
		<td width="120" align="center">操作</td>
		<td width="120" align="center">唯一编码*</td>
		<td width="120" align="center">分类名称*</td>
		<td width="120" align="center">图片地址</td>
		<td width="80" align="center">市场价*</td>
		<td width="80" align="center">会员价*</td>
		<td width="80" align="center">库存数*</td>
		<td width="80" align="center">送币/积分</td>
		<td width="80" align="center">排序</td>
	</tr>
	
	<%for (int i = 0; (listVo != null && i < listVo.Count); i++)
   { %>
	<tr id="TRLine">
		<td>
		<p align="center">
		<a href="javascript:addRow('batchTableAdd','TRLine')">添加</a>/<a href="javascript:deleteRow('batchTableAdd','TRLine')">删除</a></td>
		<td><input type="text" name="customnumber" id="customnumber" value="<%=listVo[i].customNumber %>" style="width:90%"></td>
		<td><input type="text" name="color" id="color" value="<%=listVo[i].color %>"  style="width:90%"></td>
		<td><input type="text" name="colorimage" id="colorimage" value="<%=listVo[i].colorImage %>"  style="width:90%"></td>
		<td><input type="text" name="colorrmb1" id="colorrmb1" value="<%=listVo[i].colorRMB1 %>"  style="width:90%"></td>
		<td><input type="text" name="colorrmb2" id="colorrmb2" value="<%=listVo[i].colorRMB2 %>"  style="width:90%"></td>
		<td><input type="text" name="total" id="total" value="<%=listVo[i].total %>"  style="width:90%"></td>
		<td><input type="text" name="sendmoney" id="sendmoney" value="<%=listVo[i].sendMoney %>"  style="width:90%"></td>		
		<td><input type="text" name="rank" id="rank" value="<%=listVo[i].rank %>"  style="width:90%"><input type="hidden" name="keyid" id="keyid" value="<%=listVo[i].keyID %>" ></td>

	</tr>
	<%}

   if (listVo == null)
   {    %>
     
	 	<tr id="TRLine">
		<td>
		<p align="center">
		<a href="javascript:addRow('batchTableAdd','TRLine')">添加</a>/<a href="javascript:deleteRow('batchTableAdd','TRLine')">删除</a></td>
		<td><input type="text" name="customnumber" id="Text1" style="width:90%"></td>
		<td><input type="text" name="color" id="Text2" value=""  style="width:90%"></td>
		<td><input type="text" name="colorimage" id="Text3" value=""  style="width:90%"></td>
		<td><input type="text" name="colorrmb1" id="Text4" value=""  style="width:90%"></td>
		<td><input type="text" name="colorrmb2" id="Text5" value=""  style="width:90%"></td>
		<td><input type="text" name="total" id="Text6" value=""  style="width:90%"></td>
		<td><input type="text" name="sendmoney" id="Text7" value=""  style="width:90%"></td>		
		<td><input type="text" name="rank" id="Text8" value=""  style="width:90%"><input type="hidden" name="keyid" id="keyid"></td>

	</tr>
	 
	 <%} %>
	
</table>
<script language="JavaScript">
    function addRow(tableId, trId) {
        var tableObj = document.getElementById(tableId);
        var trObj = document.getElementById(trId);
        //var trIndex = trObj.rowIndex+1;  
        var rows = tableObj.rows.length;
        var cell = "";
        var tr = tableObj.insertRow(rows);
        for (i = 0; i < trObj.cells.length; i++) {
            cell = tableObj.rows(rows).insertCell(i);
            cell.innerText = trObj.cells(i).innerText;
            cell.className = trObj.cells(i).className;
            cell.innerHTML = trObj.cells(i).innerHTML;
        }
    }
    function deleteRow(tableId, trId) {

        var tableObj = document.getElementById(tableId);
        var trObj = document.getElementById(trId);

        //var trIndex = trObj.rowIndex;//复制tr的行号  
        var rows = tableObj.rows.length;
        if (rows > 2) {
            tableObj.deleteRow(rows - 1);
        } else if (rows == 2) {
            alert("第一行不能删除！");
        } else {
            alert("无可删除的信息！");
        }
    }

    /*添加商品*/
    function validateForm(form) {

        if (go.book_title.value == "") {

            alert("标题不能为空！");
            return;
        }
        

        var value = $('#chan-ping-jie-shao-box').code().trim();
        go.book_content.value = value
        if (value == "") {
            alert("内容不能为空！");
            return;
        }
        go.submit();


    }
    window.onload = function() {

        $('#chan-ping-jie-shao-box').code(go.book_content.value);
    }
</script>                                                                                                                                                                          
     <%                                                                                                                                                                             
    Response.Write("<hr>");
    Response.Write(this.GetLang("产品介绍|产品介绍|Introduction") + " :<font color=\"red\">此编辑器需IE9以上或百度浏览器。</font><br/>");
   %>
   <input type="hidden" name="book_content" id='edit' value="<%=Server.HtmlEncode(bookVo.book_content)%>">
                    <%//=this.sb.ToString()%> 
                     <div id="chan-ping-jie-shao-box"> </div>
                    
                   
                 
                    <br />
              
   
   <%
         //Response.Write("<textarea name=\"book_content\"  id=\"book_content\" rows=\"20\" style=\"width:100%\">" + bookVo.book_content.Replace("[br]", "\r\n").Replace("<", "&lt;").Replace(">", "&gt;") + "</textarea><br/>");

    Response.Write("参数 :<a href=\"javascript:addpic(3);\">上传图片</a><br/>");
    Response.Write("<textarea name=\"book_gongxiao\"  id=\"book_gongxiao\" rows=\"5\" style=\"width:100%\">" + bookVo.book_gongxiao.Replace("[br]", "\r\n").Replace("<", "&lt;").Replace(">", "&gt;") + "</textarea><br/>");


    Response.Write("参数页面显示“提示” :(可留空)<a href=\"javascript:addpic(4);\">上传图片</a><br/>");
    Response.Write("<textarea name=\"book_tishi\" id=\"book_tishi\" rows=\"5\" style=\"width:100%\">" + bookVo.book_tishi.Replace("[br]", "\r\n").Replace("<", "&lt;").Replace(">", "&gt;") + "</textarea><br/>");


    Response.Write("实拍图 : <a href=\"javascript:addpic(2);\">上传图片</a><br/>");
    Response.Write("<textarea name=\"book_content_img\" id=\"book_content_img\" rows=\"5\" style=\"width:100%\">" + bookVo.book_content_img.Replace("[br]", "\r\n").Replace("<", "&lt;").Replace(">", "&gt;") + "</textarea><br/>");

    Response.Write("自定义宣传标题：多个用|区分<br/>");
    Response.Write("<textarea name=\"book_otherTitle\" rows=\"5\" style=\"width:100%\">" + bookVo.book_otherTitle.Replace("[br]", "\r\n").Replace("<", "&lt;").Replace(">", "&gt;") + "</textarea><br/>");
    Response.Write("自定义宣传内容：多个用|区分，对应上面标题 <a href=\"javascript:addpic(5);\">上传图片</a><br/>");
    Response.Write("<textarea name=\"book_otherContent\" id=\"book_otherContent\" rows=\"5\" style=\"width:100%\">" + bookVo.book_otherContent.Replace("[br]", "\r\n").Replace("<", "&lt;").Replace(">", "&gt;") + "</textarea><br/>");
    Response.Write("<hr/>自动发货内容:"+bookVo.autoDelivery +" 【<a href=\""+this.http_start+"shop/admin_wapmodify_auto.aspx?siteid="+this.siteid+"&amp;classid="+this.bookVo.book_classid+"&amp;bookid="+bookVo.id+"\">管理</a>】<br/>");
    

   
    Response.Write("<input type=\"hidden\" name=\"action\" value=\"gomod\"/>");
    Response.Write("<input type=\"hidden\" name=\"id\" value=\"" + id + "\"/>");
    Response.Write("<input type=\"hidden\" name=\"classid\" value=\"" + classid + "\"/>");
    Response.Write("<input type=\"hidden\" name=\"siteid\" value=\"" + siteid + "\"/>");
    Response.Write("<input type=\"hidden\" name=\"page\" value=\"" + page + "\"/>");
    Response.Write("<input type=\"hidden\" name=\"sid\" value=\"" + sid + "\"/>");
    Response.Write("<input type=\"button\" onclick=\"validateForm(this);\" name=\"bt\" value=\"" + this.GetLang("修 改|修 改|Modify") + "\"/>");

    Response.Write("</form></div>");
    Response.Write("<div class=\"btBox\"><div class=\"bt1\">");
    Response.Write("<a href=\"" + this.http_start + "shop/admin_userlistWAP.aspx?siteid=" + this.siteid + "&amp;classid=" + this.classid + "&amp;page=" + this.page + "\">" + this.GetLang("返回列表|返回列表|Back to list") + "</a>");
   
    Response.Write("</div></div>");


    Response.Write("</div>");
                                                                                                                                                                               
                                                                                                                                                                             
 %>
<script src="/NetCSS/admin/js/jquery-1.9.1.min.js"></script> 
<script src="/NetCSS/admin/bootstrap-3.3.5/js/bootstrap.min.js"></script> 
<script src="/NetCSS/admin/summernote/summernote.min.js" type="text/javascript" language="javascript"></script> 
<script src="/NetCSS/admin/summernote/summernote-zh-CN.js" type="text/javascript" language="javascript"></script> 
<script type="text/javascript" language="javascript">
    var $editorOne = $('#chan-ping-jie-shao-box').summernote({
        height: 350,
        lang: 'zh-CN',
        inputId: 'chan-ping-jie-shao-box-input'
    });
</script>
    </body></html>


