ï»?<%@ Page Language="C#" AutoEventWireup="true" CodeBehind="VIPshow.aspx.cs" Inherits="KeLin.WebSite.WeiXin.VIPshow" %><?xml version="1.0" encoding="UTF-8"?><!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="application/xhtml+xml; charset=utf-8"/>
<meta http-equiv="Cache-Control" content="max-age=0"/>
<meta name="viewport" content="width=device-width, initial-scale=0.5, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<meta name="author" content="Ð¡ÓîÍøÂç" />
<meta name="keywords" content="å¾?ä¿?CMS,å¾?ä¿¡æ?¥å??,å¾?ä¿¡è?¥é??CMS,å¾?ç½?ç«?,wap,cms,wapå»ºç??,wapå»ºç??ç³»ç??,wap?????©å»ºç«?,???ä¿¡ç??,3g,3gåº????,3G??????,wapç½?ç«?,wapä¸?è½?"/>
<meta name="description" content="ä¸?æ³¨ä??WAPå»ºç??,3Gå»ºç??,?????ºå»ºç«?,WAPç½?ç«?,CMS,å¾?ä¿?CMS,å¾?ä¿¡æ?¥å??,å¾?ä¿¡è?¥é??CMS,å¾?ç½?ç«?,?????¸ä¸ºå¹¿å¤§??¨æ?·æ??ä¾?å®???´å?¨é?¢ç?????çº¿å????¨è§£??³æ?¹æ?????"/>
<style type="text/css">

</style>
<title>??????ä¼???????</title>
</head>
<BODY background="images/test_01.jpg" LEFTMARGIN=0 TOPMARGIN=0 MARGINWIDTH=0 MARGINHEIGHT=0>
<div align="center">
<TABLE WIDTH=320 BORDER=0 CELLPADDING=0 CELLSPACING=0 >
	<TR>
		<TD background="images/test_01.jpg">
			&nbsp;</TD>
	</TR>
	<TR>
		<TD background ="<%=this.cardImg %>" WIDTH=320 HEIGHT=189 valign="top">
		
    <table border="0" width="100%" id="table1" height="165">
		<tr>
			<td>???</td>
			<td width="139">???</td>
		</tr>
		<tr>
			<td height="51">???</td>
			<td width="139" height="51"><%=this.getuserid %></td>
		</tr>
	</table>
		
    </TD>
	</TR>
	<TR>
		<TD background="images/test_03.jpg">
			&nbsp;</TD>
	</TR>
	<TR>
		<TD background ="images/test_04.jpg" WIDTH=320 HEIGHT=150 valign="top">
			
			<table border="0" width="100%" id="table2" height="100%">
				<tr>
					<td width="9">???</td>
					<td valign="top"><%=this.cardInfo %></td>
					<td width="15">???</td>
				</tr>
			</table>
			
			</TD>
	</TR>
	<TR>
		<TD>
			<IMG SRC="images/test_05.jpg" WIDTH=320 HEIGHT=22 ALT=""></TD>
	</TR>
</TABLE>
</div>
</body>
</html>