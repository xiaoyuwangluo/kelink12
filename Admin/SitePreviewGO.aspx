<%@ Page Language="C#" AutoEventWireup="true" CodeBehind="SitePreviewGO.aspx.cs" Inherits="KeLin.WebSite.admin.SitePreviewGO" %>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=GB2312">
<link href="../NetCSS/style.css" rel="stylesheet" type="text/css">
<script language="JavaScript" src="../NetCSS/table.js" type="text/JavaScript"></script>

<link href="../NetCSS/wwww_add.css" rel="stylesheet" type="text/css">
<title> 预览及帮助</title>
 
</head>
<body>

<table width="100%"  border="0" cellpadding="0" cellspacing="0">
  <tr>	
    <td valign="top">
     <!--
      <table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
          <td><table width="100%" border="0" cellpadding="0" cellspacing="0">
            <tr>
              <td width="1%"><img src="/NetImages/main_title_01.gif" width="12" height="24"></td>
              <td width="98%" background="/NetImages/main_title_02.gif"><table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0">
                <tr>
                  <td width="2%"><img src="/NetImages/i08.gif" width="16" height="16"></td>
                <td width="98%" height="24" valign="middle">
                	网站管理 >> 页面综合排版 >> 预览及帮助
                  		 </td>
                         
                </tr>
              </table>
              
              
          
              </td>
              <td width="1%"><img src="/NetImages/main_title_03.gif" width="13" height="24"></td>
            </tr>
          </table>-->
          
          
<!--修改begin-->
<div id="title">
   <div class="tit_right"><img src="../NetImages/tit_right.gif" /></div>
   <div class="tit_left"><img src="../NetImages/tit_left.gif" /></div>
   <div class="tit_center">
      <ul>
        <li><a href="../admin/SitePreview.aspx">页面综合排版</a></li>
        <li><a href="../admin/ContentTree.aspx">内容管理</a></li>
        <li><a href="../WapStyle/Style_List.aspx">皮肤CSS样式</a></li>
        <li><a href="../models/systemindex.aspx">模版向导</a></li>
        <li><a href="/pageModel/systemindex01.aspx">页面模板</a></li>
        <li><a href="../admin/SitePreviewGo.aspx"  class="check" style="color:red;">↙帮助↘</a></li>
        <!--
        <li><a href="../models/systemmodel.aspx">系统模版管理</a></li>
        <li><a href="../WapStyle/Style_List00.aspx">系统CSS样式</a></li>
        -->
      </ul>
   </div>
</div>
<!--修改end-->
          
          
          
          
          </td>
        </tr>
        <tr>
          <td width="100%"><table width="100%"  border="0" cellpadding="0" cellspacing="0" bordercolor="#3399FF" class="TABLE6" >
            <tr>
                                    <td height="24">
                                        <table width="100%" border="0" style="height: 369px">
                                            <tr>
                                                <td>
<table border="0" width="96%" id="table1" height="54">
		<tr>
			<td height="50"><img border="0" src="/NetImages/ahtm.gif" align="absbottom" width="20" height="20">
    表示编辑CSS&nbsp; <img border="0" src="/NetImages/alist.gif" align="absbottom" width="16" height="16"> 
    表示新增栏目&nbsp;
    <img border="0" src="/NetImages/u.gif" align="absmiddle">  表示栏目上移&nbsp;&nbsp; <img border="0" src="/NetImages/d.gif">  表示栏目下移<br>                           
    <br>
    <input type="button" value=" 顶 " class="bt">  表示插入页面顶部信息&nbsp;<input type="button" value=" 插 " class="bt">  表示插入分行内容&nbsp;<input type="button" value=" 底 " class="bt">  
    表示插入页面底部信息&nbsp;<input type="button" value="全局顶部" class="bt"> 表示全局顶部&nbsp;<input type="button" value="全局底部" class="bt"> 表示全局底部&nbsp;
    <input type="radio" value=" ">
    表示修改栏目属性 <br>
     <br />编辑LOGO：点击编辑区页面顶部的图片可进行更改LOGO图片操作<br>
     <br><font color="#1a8cff"> 
     <br><table border="0" width="100%" height="14">
  <tr>
    <td width="16%" bgcolor="#C0C0C0" height="8"></td>
    <td width="19%" height="8">表示隐藏栏目　</td>
    <td width="15%" bgcolor="#AAD5FF" height="8">　</td>
    <td width="18%" height="8">表示链接栏目</td>
    <td width="12%" height="8" bgcolor="#FFCCFF">　</td>
    <td width="20%" height="8">表示普通栏目</td>
  </tr>
</table>
     <br/><br/></font>
     
<p><font size="4">本系统建站有三种方式，都需要<font color="#FF0000"><b>先在【手机/HTML5页面综合排版】或【添加删除栏目】中建好栏目上下级关系，支持无限级栏目。</b></font></font></p>

<p>　</p><p><b><font size="4">方式一：</b>【手机/HTML5页面综合排版】 + UBB调用源码:[getwml=ID]。其中[getwml=ID]的使用方法是先新增栏目‘abc’指向系统定义_HTML5源码编写，然后在【添加删除内容】点击‘abc’进行增删改源码。



</font></p>
<p>　</p>
<p><font size="4"><b>方式二：</b>【电脑/HTML5页面模板管理】 在这里可以设计电脑或手机任意版面效果。</font></p>
<p>　</p>
<p><font size="4"><b>方式三：</b>【方式一】+【方式二】混合，能录入源码的地方有二个，第一个在【添加删除内容】点击HTML5源码(WML)插件对应的栏目，第二个在【电脑/HTML5页面查模板管理】。</font></p>
    <p>　</p>
    <p><font size="4">【<a href="/HTML5/list.aspx" target="_blank">方式一参考源码示例</a>】【<a href="http://video.kelink.com" target="_blank">方式二视频教程</a>】【<a href="/UBB_API/index.htm" target="_blank">万能UBB文档</a>】【<a href="http://d.kelink.com" target="_blank">其它视频教程</a>】</font></p>
  
<p>　</p>
     <br/><br/>
     <b>特别提醒：<br><br>
	 
	1.</b>新增或修改栏目时，栏目管理员(版主)，允许币数，消费币数，允许会员，访问密码等只能设置在最后一个栏目才会有效！<b><br>
	<br>
	2.</b>关于地址中的sid，例一个完整的首页地址：http://localhost/wapindex.aspx?siteid=1000&sid=-2-0-0-0-500　 
	<br>
	<br />其中sid=-版本-皮肤-语言-机型-屏宽 简为：sid=-版本
																 
	<br>
																<br />在UBB中[sid]相当于-[ver]-[cs]-[lang]-[myua]-[width]
																<br /><br />转成动态的UBB地址是，后面的-[cs]-[lang]-[myua]-[width]可去掉，这样可实现简单切换版本：
																<br />WAP1.0链接:http://localhost/wapindex.aspx?siteid=1000&sid=-1
																<br />WAP2.0链接:http://localhost/wapindex.aspx?siteid=1000&sid=-2
																<br />电脑页面链接:http://localhost/wapindex.aspx?siteid=1000&sid=-3
																<br />平板页面链接:http://localhost/wapindex.aspx?siteid=1000&sid=-4
                                <br />其它页面链接:http://localhost/wapindex.aspx?siteid=1000&sid=-5
<br />地址可设置伪静态.html。
     <br>
　</td>
		</tr>
	</table>
	
	
                                                    
                                                    
                                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td class="bg">
            </td>
        </tr>
    </table>
    </body>
    </html>