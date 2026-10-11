<%@ Page Language="C#" AutoEventWireup="true" EnableEventValidation="false" ValidateRequest="false"
    Codebehind="addUser.aspx.cs" Inherits="KeLin.WebSite.article.addUser" %>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head runat="server">

<title>文章内容</title>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<link href="/NetCSS/style.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="/EditTool/xheditor/jquery.js"></script>
<script type="text/javascript" src="/EditTool/xheditor/xheditor.js"></script>
<style type="text/css">
.pic{ overflow:hidden; padding:0; padding:10px;}
.pic li{ position:relative; display:inline-block; width:80px; height:80px; margin-right:10px;}
.pic li img{ border:none; width:100%; height:100%;}
.pic li .del{ position:absolute; top:-10px; right:-10px; z-index:10; width:20px; height:20px; background:#fff; border-radius:50%; box-shadow:0 0 3px #ccc;}
</style>
<script type="text/javascript">

    var editor;
    window.onload = function() {
        editor = $('#FreeTextBox1').xheditor();

    }
    
</script>

<script type="text/javascript">
    
 
     //function UpLoad(ii) {
        

       // window.open("/admin/Upload_Photo.aspx?PhotoUrlID="+ii+"&info=文章编辑页面&delpath=", "addpicwindow", "height=180, width=380, toolbar= no, menubar=no, scrollbars=yes, resizable=no, location=no, status=no");
    //}

function daoru() { 
window.open ("../picture/systemindex.aspx?site=69&classid=00&seq=1", "daoruwindow", "height=520, width=790, toolbar= no, menubar=no, scrollbars=yes, resizable=no, location=no, status=no")
}
function daoruFile() {
    window.open("../admin/systemindexFiles.aspx?site=69&classid=00&seq=1", "daoruwindow", "height=520, width=790, toolbar= no, menubar=no, scrollbars=yes, resizable=no, location=no, status=no")
}
 
function addImgOrFile(a, b) {

   if(b=="chan-ping-jie-shao-box-input"){
        var url = 'http://' + window.location.host + '/' + a;
        editor.pasteHTML(url);  
        return;
    } else {




        a = '/' + a;
        if (b == 'img') {

            var sExt;
            sExt = a.substr(a.lastIndexOf(".") + 1);
            sExt = sExt.toUpperCase();
            switch (sExt) {
                case "GIF":
                case "JPG":
                case "PNG":
                case "BMP":
                case "JPEG":
                    //show('[img]' + a + '[/img]');
                    editor.pasteHTML('<img src="' + a +'" alt="" />');
                    break;
                default:
                    //show('[url=' + a + ']点击下载[/url]');
                    editor.pasteHTML('<a href="' + a + '">点击下载</a>');
                    break;
            }


        } else if (b == "file") {
        //show('[url=' + a + ']点击下载[/url]');
         editor.pasteHTML('<a href="' + a + '">点击下载</a>');
        } else if (b == "book_small") {

            form1.book_small.value = a;


            var oLi = $('<li class="add"><img src="' + a + '" alt="缩略图"><img onclick="delLi(this,\'' + a + '\',\'' + b + '\')" class="del" src="/NetCSS/admin/images/del.png"></li>');
            var iLi = $('#book_small-con').find('li').length;
            if (iLi == 1) {
                $('#book_small-con').prepend(oLi);
            } else {
                $('#book_small-con').find('img').eq(0).attr('src', a);
            }




        } else if (b == "book_file") {

            if (form1.book_file.value == "") {

                form1.book_file.value = a;

            } else {

                form1.book_file.value = form1.book_file.value + "|" + a;
            }

            var oLi = $('<li class="add"><img src="' + a + '" alt="缩略图"><img onclick="delLi(this,\'' + a + '\',\'' + b + '\')" class="del" src="/NetCSS/admin/images/del.png"></li>');
            var oFile = $('#book_file-con').find('li').eq($('#book_file-con').find('li').length - 1);
            oFile.before(oLi);


        }

    }
}
function delLi (obj,src,id){
	var oIpnut = document.getElementById(id);
	var aSrc = oIpnut.value.split('|');
	var aNull = [];
	
	for(var i=0;i<aSrc.length;i++){
		if(aSrc[i] != src){
			aNull.push(aSrc[i]);
		}
	}
	oIpnut.value = aNull.join('|');
	$(obj).parent().remove();
	
}
function wap2(){
	window.open("/admin/ubb3.aspx");
	
}
function openubb() {
    window.open("/admin/ubb2.aspx");
 
}
function addpic(){
    window.open("/admin/Upload_Photo.aspx?PhotoUrlID=img&info=WEB页面综合排版&delpath=11", "addclasswindow", "height=180, width=380, toolbar= no, menubar=no, scrollbars=yes, resizable=no, location=no, status=no") 
 
}
function addfile(){
    window.open("/admin/Upload_Photo.aspx?PhotoUrlID=file&info=WEB页面综合排版&delpath=11", "addclasswindow", "height=180, width=380, toolbar= no, menubar=no, scrollbars=yes, resizable=no, location=no, status=no") 
 
}

function setpic(temp){
show(temp);
}

function show(ubb){
    editor.pasteHTML(ubb);
    return;
    
    
var textEl = form1.FreeTextBox1;
            var text = ubb;
            if (textEl.createTextRange && textEl.caretPos) {
                var caretPos = textEl.caretPos;
                caretPos.text = caretPos.text.charAt(caretPos.text.length - 1) == ' ' ? text + ' ' : text;
            }
            else{
            
                textEl.value = textEl.value + text;
                
            }

}
function storeCaret() {
            var textEl = form1.FreeTextBox1;
            if (textEl.createTextRange)
                textEl.caretPos = document.selection.createRange().duplicate();
        }

$(function (){
	var oBookFile = document.getElementById('book_file');
	
	if(oBookFile.value != ''){
		var aSrc = oBookFile.value.split('|');
		for(var i=0;i<aSrc.length;i++){
	
		   var oLi = $('<li class="add"><img src="' + aSrc[i] + '" alt="缩略图"><img onclick="delLi(this,\'' + aSrc[i] + '\',\'book_file\')" class="del" src="/NetCSS/admin/images/del.png"></li>');
		   var oFile = $('#book_file-con').find('li').eq($('#book_file-con').find('li').length-1);
		   oFile.before(oLi);
		}
	}
	
	var oBookSmall = document.getElementById('book_small');
	
	if(oBookSmall.value != ''){
		var aSrc = oBookSmall.value.split('|');
		for(var i=0;i<aSrc.length;i++){
	
		   var oLi = $('<li class="add"><img src="' + aSrc[i] + '" alt="缩略图"><img onclick="delLi(this,\'' + aSrc[i] + '\',\'book_small\')" class="del" src="/NetCSS/admin/images/del.png"></li>');
		   var oFile = $('#book_small-con').find('li').eq($('#book_small-con').find('li').length-1);
		   oFile.before(oLi);
		}
	}
});
</script>

</head>
<body>
<form id="form1" runat="server">
  <table cellspacing="0" cellpadding="0" width="100%" border="0">
    <tbody>
      <tr>
        <td colspan="4"><table width="100%" border="0" cellpadding="0" cellspacing="0">
            <tr>
              <td width="12"><img src="/NetImages/main_title_01.gif" width="12" height="24"></td>
              <td width="100%" background="/NetImages/main_title_02.gif"><table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
                  <tr>
                    <td width="2%"><div align="center"> <img src="/NetImages/i08.gif" width="16" height="16"></div></td>
                    <td width="98%" height="24" valign="middle"> 网站管理>>文章模块内容>>文章内容 </td>
                  </tr>
                </table></td>
              <td width="13"><img src="/NetImages/main_title_03.gif" width="13" height="24"></td>
            </tr>
          </table></td>
      </tr>
      <tr>
        <td valign="top" align="left"><div class="txt4">
            <table class="TABLE6" cellspacing="1" cellpadding="3" width="100%">
              <tbody>
                <tr>
                  <td class="column" colspan="4">&nbsp;
                    <%

if (INFO == "UPOK"){
    Response.Write("<font color=red><b>修改成功！</b></font>");
}
else if (INFO == "ADDOK")
{
    Response.Write("<font color=red><b>新增成功！</b></font>");
    tb_title.Text = "";
    //FreeTextBox1.Text = "";

}
else if (INFO == "REP")
{
    Response.Write("<font color=red><b>新增失败，标题已存在！</b></font>");
    //tb_title.Text = "";
   // FreeTextBox1.Text = "";
    
}
Response.Write(ERROR);
                                               
 %></td>
                </tr>
                <tr>
                  <td class="f" style="width: 15%"><font face="宋体">缩放图地址： </font></td>
                  <td colspan="3" class="fh3">
                  <asp:TextBox ID="book_small" runat="server" style="display:none;"></asp:TextBox>
                  
                  <ul id="book_small-con" class="pic"><li><img src="/NetCSS/admin/images/addpic.png" alt="添加图片" onclick="UpLoad('book_small');" ></li></ul>
                  
                    </td>
                </tr>
                <tr>
                  <td class="f" style="width: 15%"><font face="宋体">展示图地址： </font></td>
                  <td colspan="3" class="fh3"><asp:TextBox ID="book_file" runat="server" style="display:none;"></asp:TextBox>
                  
                   <ul id="book_file-con" class="pic"><li><img src="/NetCSS/admin/images/addpic.png" alt="添加图片" onclick="UpLoad('book_file');" ></li></ul>
                    </td>
                </tr>
                <tr>
                  <td class="f" style="width: 15%"><font face="宋体">展示图说明： </font></td>
                  <td colspan="3" class="fh3"><asp:TextBox ID="book_fileINFO" runat="server" Height="50px"  TextMode="MultiLine" Width="100%" ></asp:TextBox>
                    <br/>对应上面多图,多个说明用|区别。 </td>
                </tr>
                <tr>
                  <td class="f" style="width: 15%"><font face="宋体"> </font></td>
                  <td colspan="3" class="fh3">&nbsp;</td>
                </tr>
                <tr>
                  <td class="f" style="width: 15%"><font face="宋体">*标题： </font></td>
                  <td colspan="3" class="fh3"><asp:TextBox ID="tb_title" runat="server" Width="369px"></asp:TextBox></td>
                </tr>
                <tr>
                  <td class="f" style="width: 15%"><font face="宋体">作者： </font></td>
                  <td colspan="3" class="fh3"><asp:TextBox ID="tb_author" runat="server" Width="146px"></asp:TextBox></td>
                </tr>
                <tr>
                  <td class="f" style="width: 15%"><font face="宋体">出处： </font></td>
                  <td colspan="3" class="fh3"><asp:TextBox ID="tb_source" runat="server" Width="370px"></asp:TextBox></td>
                </tr>
                <tr>
                  <td class="f" style="width: 15%"><font face="宋体">文章分类：</font></td>
                  <td width="84%" class="fh3"><font face="宋体">
                    <asp:DropDownList ID="ddl_type" runat="server"> </asp:DropDownList>
                    </font></td>
                </tr>
                <tr>
                  <td class="f" style="width: 15%"><font face="宋体">是否加入系统资源： </font></td>
                  <td colspan="3" class="fh3"><asp:DropDownList ID="ddl_systype" runat="server"> </asp:DropDownList></td>
                </tr>
                <tr>
                  <td class="f" style="width: 15%"><font face="宋体">*内容： </font></td>
                  <td colspan="3" class="fh3">
                   【<a href="javascript:daoru()" >导入图片</a>】【<a href="javascript:daoruFile()" >导入文件</a>】【<a href="javascript:addpic()">上传图片</a>】【<a href="javascript:addfile()">上传文件</a>】【<a href="javascript:wap2()">网站资源链接</a>】【<a href="javascript:openubb()">UBB大全</a>】 
                 <br />
                    <INPUT type="hidden" name="content" id='edit' />
                    <textarea name="FreeTextBox1" id="FreeTextBox1" style="width:100%;height:500px" onselect="storeCaret();" onclick="storeCaret();" onkeyup="storeCaret();" ><%= this.content%></textarea>
                    
                   
                                                <br />
                                               
                    <font color="red">注意：需IE9以上或百度浏览器。</font>自定义分页: <a href="javascript:show('[next]')" >[next]</a>，技巧:内容后面放[next]强制不分页。支持[buyrmb=币数]内容[/buyrmb] </td>
               
               
                </tr>
                <tr>
                  <td class="f" style="width: 15%"><font face="宋体">SEO关键字keywords 150字内：</font></td>
                  <td width="84%" class="fh3"><asp:TextBox ID="keywords" runat="server" Height="50px"  TextMode="MultiLine" Width="100%" ></asp:TextBox></td>
                </tr>
                <tr>
                  <td class="f" style="width: 15%"><font face="宋体">SEO描述descriptions 150字内：</font></td>
                  <td width="84%" class="fh3"><asp:TextBox ID="descriptions" runat="server" Height="50px"  TextMode="MultiLine" Width="100%" ></asp:TextBox></td>
                </tr>
                <tr>
                  <td class="fh3" colspan="4" align="center"><br />
                    <% if (bookid != "")
                                               {
                                                   bt_ok.Text = "修 改";
                                               }
                                               else
                                               {
                                                   bt_ok.Text = "新 增";

                                               } %>
                  <div style="display:none"><input type="hidden" name="bookid" value="<%=bookid %>"/> <asp:Button ID="bt_ok" runat="server" Text="保 存"   OnClick="bt_ok_Click"  Width="70px" CssClass="bt"> </asp:Button></div>
                    <input type="button" name="go" value=" 保 存 " onclick ="validateForm(this);"  class="bt"/>
                    &nbsp;&nbsp;&nbsp;&nbsp; &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;
                    <asp:Button ID="Button1" runat="server" Text=" 返 回 " OnClick="Button1_Click" CssClass="bt" Width="70px"/>
                    <br />
                    <br /></td>
                </tr>
              </tbody>
            </table>
          </div></td>
      </tr>
      <tr>
        <td style="height: 15px" align="center"><span class="txt4"></span></td>
      </tr>
      <tr>
        <td align="right"></td>
      </tr>
      <tr>
        <td align="center"> 页面执行时间：<%=loadpagetime %>&nbsp;毫秒！ </td>
      </tr>
    </tbody>
  </table>
</form>

<script type="text/javascript" language="javascript">

   
 
    function UpLoad(id) {
        window.open("/admin/Upload_Photo.aspx?PhotoUrlID=" + id + "&info=文章编辑页面&delpath=", "addpicwindow", "height=180, width=380, toolbar= no, menubar=no, scrollbars=yes, resizable=no, location=no, status=no");
    }


    //function addImgOrFile(src, id) {
    //    var url = 'http://' + window.location.host + '/' + src;

     //   editor.pasteHTML(url);
   //}

  
   
    /*添加商品*/
    function validateForm(form) {

        if (form1.tb_title.value == "") {

            alert("标题不能为空！");
            return;
        }
        if (form1.ddl_type.value == "") {
            alert("栏目分类不能为空！");
            return;
        }
        

        form1.content.value = $('#FreeTextBox1').val();
        
        
        if (form1.content.value == "") {
            alert("内容不能为空！");
            return;
        }

        $('#bt_ok').click();
            

        }
      
</script>
</body>
</html>
