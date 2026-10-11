<%@ Page Language="C#" AutoEventWireup="true" Codebehind="systemindex.aspx.cs" Inherits="KeLin.WebSite.models.systemindex" %>
<%@ Register Assembly="AspNetPager" Namespace="Wuqi.Webdiyer" TagPrefix="webdiyer" %>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head id="Head1" runat="server">
<title>模板快速建站向导</title>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<link href="../NetCSS/style.css" rel="stylesheet" type="text/css">
<link href="../NetCSS/wwww_add.css" rel="stylesheet" type="text/css">
<link href="../NetCSS/admin/css/luck.css" rel="stylesheet" type="text/css">
<script src="../netcss/admin/js/jquery-1.9.1.min.js" type="text/javascript"></script>
<script src="../netcss/admin/js/luck.js" type="text/javascript"></script>
<style type="text/css">
.style7 { width: 422px; }
* { padding: 0; margin: 0; list-style: none; text-decoration: none; }
*:focus { outline: none; }
.main { font-family: "Microsoft Yahei", "微软雅黑", "宋体", sans-serif !important; font-size: 14px; }
.left{ float:left;}
/*搜索*/
.search { position: relative; padding: 20px 10px; border-bottom: 1px solid #7fcafe; }
.search h2 { position: absolute; top: 20px; left: 10px; z-index: 10; line-height: 24px; color: #000; font-size: 14px; }
.search .find { padding-left: 120px; }
.search .find input[type=text] { border: 1px solid #CCC; height: 24px; line-height: 24px; padding: 0 5px; width: 135px; }
.search .find input[type=submit] { margin-left: 5px; width: 60px; height: 26px; border: 1px solid #CCCCCC; background: #FFF; cursor: pointer; }
/*导航菜单*/
.nav-type { position: relative; padding: 20px 10px; margin-bottom: 20px; border-bottom: 1px solid #7fcafe; background: #fff; }
.nav-type h2 { position: absolute; top: 20px; left: 10px; z-index: 10; line-height: 24px; color: #000; font-size: 14px; }
.nav-type ul { overflow: hidden; max-width: 960px; padding-left: 120px; }
.nav-type ul li { overflow: hidden; float: left; width: 140px; }
.nav-type ul li a { padding: 3px 5px; line-height: 28px; color: #000; font-size: 14px; }
.nav-type ul li.cur a, .nav-type ul li:hover a { background: #1E90FF; }
.nav-type ul li.cur a, .nav-type ul li:hover a { color: #FDFDFD; }
/*列表*/
.test-list { overflow:hidden; border-top: 1px solid #7fcafe; }
.test-list ul { overflow: hidden; background: #fff; }
.test-list ul li { overflow:hidden; float: left; padding: 0 10px 0 10px; }
.test-list ul li iframe { width: 320px; padding: 105px 30px 96px 29px; background: #fff; background: url("/NetCSS/admin/images/iPhone.png") no-repeat center center; border: none; }
.test-list ul li .box { position: relative; padding: 0 0 0 90px; margin:0 55px; }
.test-list ul li .box .coderq { position: absolute; top: 0; left: 0; z-index:10; width: 80px; height: 80px; border: 1px solid #fff; }
.test-list ul li .box h3 { margin-bottom: 5px; font-size: 14px; color: #000; line-height: 28px; }
.test-list ul li .box .find-list{width:170px; height:28px;}
.test-list ul li .box .find-list a{ display:block; width:55px; padding: 2px 5px; margin:0 10px 1px 0; text-align:center; background-color: #ebeef1; border: 1px solid #dedfdf; color: green;  line-height:24px; font-size:13px;}
.test-list ul li .box .find-list a:hover { background-color: green; color: #FFFFFF; }
.test-list ul li .box .find-list span{ overflow:hidden;}
.test-list ul li .box .find-list span a.hide{ visibility:hidden;}
.test-list ul li .box .find-list span:hover a.hide{ visibility:visible;}
</style>
</head>
<body style="overflow:auto;">
<form id="form1" runat="server">
  <table height="100%" cellspacing="0" cellpadding="0" width="100%" border="0">
    <tr>
      <td><!--修改begin-->
        
        <div id="title">
          <div class="tit_right"><img src="/NetImages/tit_right.gif" /></div>
          <div class="tit_left"><img src="/NetImages/tit_left.gif" /></div>
          <div class="tit_center" style=" *width:96.65%; _width:97.65%;">
            <ul>
              <li><a href="../admin/SitePreview.aspx">页面综合排版</a></li>
              <li><a href="../admin/ContentTree.aspx">内容管理</a></li>
              <li><a href="../WapStyle/Style_List.aspx">皮肤CSS样式</a></li>
              <li><a href="../models/systemindex.aspx" class="check">模版向导</a></li>
              <li><a href="/pageModel/systemindex01.aspx">页面模板</a></li>
              <li><a href="../admin/SitePreviewGo.aspx"  style="color:red;">↙帮助↘</a></li>
            </ul>
          </div>
        </div>
        
        <!--修改end--></td>
    </tr>
    <tr>
      <td class="TABLE6"><div class="main"> 
          <!--search-->
          <div class="search">
            <h2> 名称或编号： </h2>
            <div class="find">
              <asp:TextBox runat="server" ID="txt_typename" Width="200px"></asp:TextBox>
              <asp:TextBox runat="server" ID="txt_typeid" Width="0px" Visible="false"></asp:TextBox>
              <asp:Button ID="bt_find" Width="60" Text=' 查 询 ' runat="server" OnClick="bt_find_Click" CssClass="bt"> </asp:Button>
              &nbsp;&nbsp;&nbsp;&nbsp;<font size="2">推荐:</font><a href="javascript:form1.txt_typename.value='178';form1.submit();"><font size="2">电脑手机同步178</font></a>、<a href="javascript:form1.txt_typename.value='179';form1.submit();"><font size="2">电脑站179</font></a>、<a href="javascript:form1.txt_typename.value='176';form1.submit();"><font size="2">企业站176</font></a>、<a href="javascript:form1.txt_typename.value='130';form1.submit();"><font size="2">官方站130</font></a>
            </div>
          </div>
          <!--type-->
          <div class="nav-type">
            <h2>所属的行业：</h2>
            <ul>
              <%=strJavascript%>
            </ul>
          </div>
          <!--list-->
          <div class="test-list">
            <ul id="test-list-ul">
            </ul>
          </div>
          <webdiyer:AspNetPager ID="AspNetPager1" runat="server" PageSize="6" UrlPaging="true"
                                                                ShowPageIndexBox="Always" PageIndexBoxType="DropDownList" TextBeforePageIndexBox="Go To Page: "
                                                                HorizontalAlign="Left" OnPageChanged="AspNetPager1_PageChanged" EnableTheming="true"
                                                                PageButtonCount="5"> </webdiyer:AspNetPager>
          转到:
          <asp:TextBox ID="tb_page" runat="server" Width="30px"></asp:TextBox>
          每页:
          <asp:TextBox ID="tb_pagecount" runat="server" Width="30px"></asp:TextBox>
          <asp:Button ID="bt_go" runat="server" Text= " Go " OnClick="bt_go_Click" CssClass="bt" />
        </div></td>
    </tr>
  </table>
  <script type="text/javascript" language="javascript">

(function (){
	
	var aList = <%=json %>;
	var iConut = 0;
	 
	$(function (){
		
		try{
		append();
		codeRQ();
		}catch(e){
			
		}
	});
	
	function append(){
		var oUl = $('#test-list-ul');
		var url = '/wapindex.aspx?siteid=' + aList[iConut].siteid + '&sid=-' + aList[iConut].ver;
		
		for(var i=0;i<aList.length;i++){

			var oLi = $('<li>\
			  <iframe id="templae-' + aList[i].siteid + '" width="320" height="480"></iframe>\
			  <div class="box">\
			  <h3>编号:' + aList[i].siteid + ' ' + aList[i].title +'</h3>\
			  <img class="coderq" src="' + aList[i].codeRQ + '" width="80" height="80" />\
			  <div class="find-list" id="find-list-' + aList[i].siteid + '">\
			    <a class="left" href="javascript:;" onclick="popUp(\'' + aList[i].title + '\',\'' + aList[i].siteid + '\')">导入模板</a> \
				<span class="left">\
			    <a href="/wapindex.aspx?siteid=' + aList[i].siteid + '&sid=-2" target="_blank">查看演示 </a>\
				<a class="hide" href="/wapindex.aspx?siteid=' + aList[i].siteid + '&sid=-2" target="_blank">手机版 </a>\
			  	<a class="hide" href="/wapindex.aspx?siteid=' + aList[i].siteid + '&sid=-3" target="_blank">电脑版 </a>\
				</span>\
			  </div>\
			  </div>\
			  </li>');
			oUl.append(oLi);
			var subdoc = $(document.getElementById('templae-' + aList[i].siteid).contentWindow.document);

			subdoc.find('body').eq(0).html('<style type="text/css">\
			*{ padding:0; margin:0; font-style:normal;}\
			body{ width:100%; height:100%; line-height:180px; color:#555; text-align:center;}\
			.loadingTip{ width:100%; height:100%; background:#fff; text-align:center; font-size:20px; font-weight:bold; color:#ccc;}\
			.loadingTip i:nth-child(1){ animation:loading .6s infinite;-webkit-animation:loading .8s infinite;}\
			.loadingTip i:nth-child(2){ animation:loading .6s .15s infinite;-webkit-animation:loading .6s .15s infinite}\
			.loadingTip i:nth-child(3){ animation:loading .6s .3s infinite;-webkit-animation:loading .6s .3s infinite}\
			.loadingTip i:nth-child(4){ animation:loading .6s .45s infinite;-webkit-animation:loading .6s .45s infinite}\
			.loadingTip i:nth-child(5){ animation:loading .6s .6s infinite;-webkit-animation:loading .6s .6s infinite}\
			@keyframes loading{\
				0%{color:#21D0FD}  \
				100%{ color:#ccc;}\
			}\
			@-webkit-keyframes loading{\
				0%{color:#21D0FD}  \
				100%{ color:#ccc;}\
			}\
			</style><div class="loadingTip"><i>页</i><i>面</i><i>加</i><i>载</i><i>中</i></div>');
		}
		
	}
	
	function codeRQ(){
		var url = '/wapindex.aspx?siteid=' + aList[iConut].siteid + '&sid=-2';

		var oIframe = $('#templae-' + aList[iConut].siteid);
		oIframe.attr({
			src : url
		});
		oIframe.load(function (){
			iConut++;
			if(iConut >= aList.length){
				return;
			}
			codeRQ();
		});
		
	}
	
})();	

function setSubmit(){
 
     document.getElementById("CommandType").value= "daoru";   

}

function setState(a){
 
     document.getElementById("state").value= a;   

}
function setpw(b){
 
 document.getElementById("mypassword").value= b;  
  
}
function popUp(title, siteid){
   document.getElementById("hidHangBiaoShis").value= siteid;
    luck.open({
        title:"温馨提示",
        content:'<p align="center" style="line-height:60px">确认你要安装：' + title + '，编号是' + siteid + '<br/>选择方式: <input type="radio" onClick="setState(1);"  name="state" checked >覆盖我的网站  <input type="radio" onClick="setState(0);" name="state" >追加我的网站 <br/>输入验证码:<input type ="text" id="pw" name="pw"  onKeyDown="setpw(this.value);" onKeyUp="setpw(this.value);"  value ="" size="6"/> <img src="/admin/CreatImage.aspx?r=<%=rnd %>" /> <br/><input type ="button" onClick="setSubmit();form1.submit();" id="go" name="go" value =" 确 定 执 行 " style="width:120px;"/></p>',
        width:"500px",
        height:"300px"
    });
}
</script>
  <input type="hidden" id="CommandType" name="CommandType" value="" />
  <input type="hidden" id="hidHangBiaoShis" name="hidHangBiaoShis" />
   <input type="hidden" id="state" name="state" value="1"/>
    <input type="hidden" id="mypassword" name="mypassword" value=""/>
</form>
</body>
</html>