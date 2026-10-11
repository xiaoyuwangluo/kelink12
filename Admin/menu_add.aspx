<%@ Page Language="C#" AutoEventWireup="true" EnableEventValidation="false" ValidateRequest="false"
    Codebehind="menu_add.aspx.cs" Inherits="KeLin.WebSite.admin.menu_add" %>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head runat="server">
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<link href="/NetCSS/style.css" rel="stylesheet" type="text/css">
<style type="text/css">
* { padding: 0; margin: 0; list-style: none; text-decoration: none; }
*:focus { outline: none; }
ul { overflow: hidden; }
input { border: 1px solid #EAEAEA; width: 160px; padding: 5px; margin: 5px 0; border-radius: 5px; color: #515151; }
input:focus { border: 1px solid #FC0; }

body { width: 100%; height: 100%; background: #F9FFF6; }
.main { padding: 10px; border: 1px solid #EAEAEA; ; }
.main button { width: 60px; line-height: 28px; background: #36B51B; border-radius: 8px; border: 1px solid #EAEAEA; color: #fff; }
.type-tit { padding: 10px; margin: 20px 0; background: #fff; border: 1px solid #EAEAEA; border-radius: 5px; }
.type-tit:hover { border: 1px solid #36B51B; }
.type-tit-classname { line-height: 18px; }
.type-list li { padding-left: 100px; }
.type-list li:hover { background: #F8F8F8; border-radius: 5px; }
.type-save { display: block; width: 160px !important; margin: 0 auto; background: #F63 !important; }
</style>
<!--<script src="http://kelink.com/netcss/admin/js/jquery-1.9.1.min.js" type="text/javascript"></script>-->
<script type="text/ecmascript" language="javascript">
    var myMenu = <%=content %>;
    bind(window, 'load', function() {/*将json转成html*/
        var oForm = document.getElementById('form');
        var sHtml = '';
        for (var i = 0; i < myMenu.length; i++) {
            sHtml += '\
			<div class="type-tit">\
			<input class="type-tit-classname" type="text" onchange="inputChange(this)" placeholder="分类名称" value="' + myMenu[i].name + '" />\
			<button class="type-add" onclick="addTypeTit(this);">添加</button>\
			<button class="type-delete" onclick="deleteTypeTit(this);">删除</button>\
			<ul class="type-list">' + listShtml(myMenu[i].smalllistVo) + '</ul>\
			</div>\
		';
        }

        function listShtml(smalllistVo) {
            var sHtml = '';
            for (var i = 0; i < smalllistVo.length; i++) {
                sHtml += '\
				<li>\
				  <input class="type-list-img" type="text" onchange="inputChange(this)" placeholder="图片地址" value="' + smalllistVo[i].img + '" />\
				  <input class="type-list-name" type="text" onchange="inputChange(this)" placeholder="链接名称" value="' + smalllistVo[i].name + '" />\
				  <input class="type-list-url" type="text" onchange="inputChange(this)" placeholder="链接地址" value="' + smalllistVo[i].url + '" />\
				  <button class="type-add" onclick="addTypeTit(this);">添加</button>\
				  <button class="type-delete" onclick="deleteTypeTit(this);">删除</button>\
				</li>\
			';
            }
            return sHtml;
        }
        oForm.innerHTML = sHtml;
    });
    function bind(obj, type, fn) { //事件绑定
        if (obj.addEventListener) {
            obj.addEventListener(type, fn, false);
        } else if (obj.attachEvent) {
            obj.attachEvent('on' + type, fn);
        } else {
            obj['on' + type] = fn;
        }
    }
    function getByClass(oParent, sClass) { //获取class元素
        var arr = [];
        var aEle = oParent.getElementsByTagName('*');
        var re = new RegExp('\\b' + sClass + '\\s');
        for (var i = 0; i < aEle.length; i++) {
            if (re.test(aEle[i].className)) {
                arr.push(aEle[i]);
            }
        }
        return arr;
    }
    function objIndex(obj, tagName) {/*获取索引*/
        var f = obj.parentNode;
        var a = f.getElementsByTagName(tagName);
        for (var i = 0; i < a.length; i++) {
            if (a[i] == obj) {
                return i;
            }
        }

    }
    function addTypeTit(obj, type) {/*添加*/
        var objOne = obj.parentNode;
        var tagName = objOne.tagName.toLowerCase();
        var objTwo = obj.parentNode.parentNode;
        var aOne = objTwo.getElementsByTagName(tagName);
        var index = objIndex(obj.parentNode, tagName) + 1;
        var addTag = document.createElement(tagName);
        addTag.className = objOne.className;
        var i = 0,
	    j = 0,
		json = {};
        if (tagName == 'li') {
            i = objIndex(objTwo.parentNode, 'div'); /*获取大类索引*/
            j = objIndex(objOne, tagName) + 1; /*获取子类索引*/
            json = {
                "img": "",
                "url": "",
                "name": "",
                "rank": 0,
                "HangBiaoShi": 0
            };
            myMenu[i].smalllistVo.splice([j], 0, json); /*添加子分类*/
            //console.log('添加子分类成功');
            addTag.innerHTML = '\
			<input class="type-list-img" type="text" onchange="inputChange(this)" placeholder="图片地址" value="" />\
			<input class="type-list-name" type="text" onchange="inputChange(this)" placeholder="链接名称" value="" />\
			<input class="type-list-url" type="text" onchange="inputChange(this)" placeholder="链接地址" value="" />\
			<button class="type-add" onclick="addTypeTit(this);">添加</button>\
			<button class="type-delete" onclick="deleteTypeTit(this);">删除</button>\
		';
        } else {
            i = objIndex(objOne, tagName) + 1; /*获取大类索引*/
            json = {
                "name": "这是大分类的名称",
                "smalllistVo": [
				{
				    "img": "",
				    "url": "",
				    "name": "",
				    "rank": 0,
				    "HangBiaoShi": 0
				}
			]
            };
            myMenu.splice([i], 0, json); /*添加大类*/
            //console.log('添加大分类成功');
            addTag.innerHTML = '\
			<input class="type-tit-classname" type="text" onchange="inputChange(this)" placeholder="分类名称" value="" />\
			<button class="type-add" onclick="addTypeTit(this);">添加</button>\
			<button class="type-delete" onclick="deleteTypeTit(this);">删除</button>\
			<ul class="type-list">\
			  <li>\
				<input class="type-list-img" style=" border: 1px solid #EAEAEA;" type="text" onchange="inputChange(this)" placeholder="图片地址" value="" />\
				<input class="type-list-name" style=" border: 1px solid #EAEAEA;" type="text" onchange="inputChange(this)" placeholder="链接名称" value="" />\
				<input class="type-list-url" style=" border: 1px solid #EAEAEA;" type="text" onchange="inputChange(this)" placeholder="链接地址" value="" />\
				<button class="type-add" onclick="addTypeTit(this);">添加</button>\
				<button class="type-delete" onclick="deleteTypeTit(this);">删除</button>\
			  </li>\
			</ul>\
		';
        };
		try{
        	objTwo.insertBefore(addTag, aOne[index]);
		}catch (e){
			objTwo.appendChild(addTag);
		};
    }
    function deleteTypeTit(obj, i, j) {/*删除对象*/
        var objOne = obj.parentNode;
        var objTwo = objOne.parentNode;
        var objThree = objTwo.parentNode;
        var tagName = objTwo.tagName.toLowerCase() == 'ul' ? 'li' : 'div';
        var aTag = objThree.getElementsByTagName(tagName);

        if ((tagName == 'li' && aTag.length <= 1) || (tagName == 'div' && aTag.length <= 2)) {
            alert('只剩我下一个了，不能再删除了');
        } else {
            var i = 0,
			j = 0;
            if (tagName == 'li') {
                i = objIndex(objTwo.parentNode, 'div'); /*获取大类索引*/
                j = objIndex(objOne, tagName); /*获取子类索引*/
                delete myMenu[i].smalllistVo[j]; /*删除子类*/
            } else {
                i = objIndex(objOne, tagName); /*获取大类索引*/
                delete myMenu[i];
            }
            objTwo.removeChild(objOne);

        }
    }
    function inputChange(obj) {
        var objOne = obj.parentNode;
        var objTwo = objOne.parentNode;
        var objThree = objTwo.parentNode;
        var tagName = objTwo.tagName.toLowerCase() == 'ul' ? 'li' : 'div';
        var aTag = objThree.getElementsByTagName(tagName);

        var i = 0,
		j = 0;
        if (tagName == 'li') {
            i = objIndex(objTwo.parentNode, 'div'); /*获取大类索引*/
            j = objIndex(objOne, tagName); /*获取子类索引*/

            switch (obj.className) {
                case 'type-list-img':
                    myMenu[i].smalllistVo[j].img = obj.value; /*修改图片值*/
                    break;
                case 'type-list-name':
                    myMenu[i].smalllistVo[j].name = obj.value; /*修改名称值*/
                    break;
                case 'type-list-url':
                    myMenu[i].smalllistVo[j].url = obj.value; /*修改链接值*/
                    break;
            }

        } else {
            i = objIndex(objOne, tagName); /*获取大类索引*/
            myMenu[i].name = obj.value; /*修改大类名称*/
        }
        if (obj.value == '') {
            obj.style.border = '1px solid red';
        } else {
            obj.style.border = '';
        }
    }
    function saveJson() {
       
        var oForm = document.getElementById('form');
        var aInput = oForm.getElementsByTagName('input');
        var btn = true;
        for (var i = 0; i < aInput.length; i++) {
            if (aInput[i].value == '') {
                //aInput[i].value = '不能为空';
                aInput[i].style.border = '1px solid red';
                btn = false;
            }
        }

     	if(form1.title.value=="" || form1.rank.value=="" ){
			btn = false;
        }
		
        if (btn) {
            form1.content.value = JSON.stringify(myMenu);
			form1.submit();
        } else {
            alert('所有输入框不能为空！');
            return ;
        }
    }


</script>
    <title>用户管理 &gt;&gt; 自定义后台菜单</title>

</head>
<body>

    <form name="form1" method="post" action="menu_add.aspx" id="form1" onsubmit="return false;" >
        <table cellspacing="0" cellpadding="0" width="100%" border="0">
            <tbody>
                <tr>
                    <td colspan="4">
                        <table width="100%" border="0" cellpadding="0" cellspacing="0">
                            <tr>
                                <td width="12">
                                    <img src="/NetImages/main_title_01.gif" width="12" height="24"></td>
                                <td width="100%" background="/NetImages/main_title_02.gif">
                                    <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td width="2%">
                                                <div align="center">
                                                    <img src="/NetImages/i08.gif" width="16" height="16"></div>
                                            </td>
                                            <td width="98%" height="24" valign="middle">
                                                用户管理 &gt;&gt; 自定义后台菜单
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                                <td width="13">
                                    <img src="/NetImages/main_title_03.gif" width="13" height="24"></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td valign="top" align="left">
                        <div class="txt4">
                            <table class="TABLE6" cellspacing="1" cellpadding="3" width="100%">
                                <tbody>
                                    <tr>
                                        <td class="column" colspan="4">
                                           &nbsp;
                                           <%

if (INFO == "UPOK"){
    Response.Write("<font color=red><b>修改成功！</b></font>");
}
else if (INFO == "ADDOK")
{
    Response.Write("<font color=red><b>新增成功！</b></font>");
    
    

}

Response.Write(ERROR);
                                               
 %>
                                        </td>
                                    </tr>
                                   
                                    
                                    <tr>
                                        <td class="f" style="width: 15%">
                                            <font face="宋体">*角色名称： </font>
                                        </td>
                                        <td colspan="3" class="fh3">
                                             <input name="title" type="text" id="tb_title" style="width:369px;" value="<%=title %>"  />

                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="f" style="width: 15%">
                                            <font face="宋体">排序号： </font>
                                        </td>
                                        <td colspan="3" class="fh3">
                                            <input name="rank" type="text" id="tb_rank" style="width:146px;" value="<%=rank %>"/>

                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="f" style="width: 15%">
                                            <font face="宋体">分配给会员ID： </font>
                                        </td>
                                        <td colspan="3" class="fh3">
                                           <textarea name="touserid" rows="2" cols="20" id="tb_touserid" style="height:100px;width:500px;"><%=touserid %></textarea>
                                                <br />(多个用会员ID用,区别。)

                                                
                                        </td>
                                    </tr>
                                   
                                    <tr>
                                        <td class="f" style="width: 15%">
                                            <font face="宋体">*电脑后台菜单： </font>
                                        </td>
                                        <td colspan="3" class="fh3">
                                        
                                       <textarea name="content" rows="2" cols="20" id="tb_content" style="display:none;"></textarea>
  <div class="main" id="main">
  <div class="form" id="form"> </div>
  </div>
                        
                        <br /> <b>小图地址：</b>请用电脑IE登录，点击左边菜单中的小图标，右键属性，可以查看到地址(URL)，复制即是。<br />
                         <b>链接地址：</b>请用电脑IE登录，点击页面，右键属性，可以查看到地址(URL)，复制即是，如果地址中带&sid=XXX，改成&sid=[sid]就可以了。  <br />
                           <font color="red"><b>特别提醒：小分类至少二个，否则可能会出错。</b>  </font> 
                                        </td>
                                    </tr>
                                    
                                    
                                    <tr>
                                        <td class="fh3" colspan="4" align="center">
                                            <br />
                                            <% if (bookid != "")
                                               {
                                                   Response.Write("<input type=\"button\" value=\"修 改\" onclick=\"saveJson();\" class=\"bt\"/>");
                                               }
                                               else
                                               {
                                                   Response.Write("<input type=\"button\" value=\"新 增\" onclick=\"saveJson();\" class=\"bt\"/>");
                                              

                                               } %>
                                            
                                            &nbsp;&nbsp;&nbsp;&nbsp; &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;
                                            <input type="button" value="返 回" onclick="window.location.href='menu_list.aspx';"  class="bt"/><br />
                                            <input type="hidden" name="bookid"  value="<%=bookid %>" />
                                            <input type="hidden" name="command"  value="mod" />
                                            <br />
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td style="height: 15px" align="center">
                        <span class="txt4"></span>
                    </td>
                </tr>
                <tr>
                    <td align="right">
                    </td>
                </tr>
                <tr>
                    <td align="center">
                        页面执行时间：<%=loadpagetime %>&nbsp;毫秒！
                    </td>
                </tr>
            </tbody>
        </table>
    </form>
</body>
</html>
