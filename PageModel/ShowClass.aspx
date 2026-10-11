<%@ Page Language="C#" AutoEventWireup="true" CodeBehind="ShowClass.aspx.cs" Inherits="KeLin.WebSite.PageModel.ShowClass" %>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<link href="../NetCSS/style.css" rel="stylesheet" type="text/css" /><link href="../NetCSS/teamdtree.css" type="text/css" rel="stylesheet" />

<title>页面模板显示</title>
<script language="JavaScript" type="text/JavaScript">
<%if(INFO=="OK"){ %>
alert("执行导入成功！");
window.opener.location.reload();
window.close();

<%} %>
</script>
</head>
<body>
<form name="form1" action="showclass.aspx" method="POST">
<input type="hidden" name="action" value="go" />
<table width="100%"  border="0" cellpadding="0" cellspacing="0" >
  <tr>	
    <td valign="top">
      <table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
          <td><table width="100%" border="0" cellpadding="0" cellspacing="0">
            <tr>
              <td width="12"><img src="/NetImages/main_title_01.gif" width="12" height="24"></td>
              <td width="100%" background="/NetImages/main_title_02.gif"><table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0">
                <tr>
                  <td width="2%"><img src="/NetImages/i08.gif" width="16" height="16"></td>
                <td width="98%" height="24" valign="middle">
                	选择页面模板分类
                	
                  		           
 
                  		 </td>
                </tr>
              </table></td>
              <td width="13"><img src="/NetImages/main_title_03.gif" width="13" height="24"></td>
            </tr>
          </table></td>
        </tr>
        <tr>
          <td width="100%"><table width="100%"  border="0" cellpadding="0" cellspacing="0" bordercolor="#3399FF" class="TABLE6" >
            <tr>
                                    <td>
                                        <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td width="310" height="320" valign="top">
                                                    <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                                        <tr>
                                                            <td width="220px">
                                                            </td>
                                                        </tr>
                                                    </table>
                                                    <div id="Layer1" style=" width: 310; height: 317; z-index: 1;visibility: inherit; overflow: auto; left: 3; top: 31;position: absolute;">
                                                        <table width="100%" border="0" cellspacing="0" cellpadding="0" >
                                                            <tr>
                                                                <td width="100%" valign="top" background="/NetImages/left_6.gif" >
                                                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                                        <tr>
                                                                            <td valign="top"  height="317">
                                                                               <br /> <%Response.Write(sb.ToString()); %>
                                                                            </td>
                                                                            <td>
                                                                            </td>
                                                                        </tr>
                                                                    </table>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                </td>
                                                <td valign="top" width="680">
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
    </table>
    <table>
        <tr>
            <td class="bg" align="center">
                【<a href="javascript:form1.submit();">导入我网站</a>】【<a href="#" onclick="window.close();">关闭</a>】
            </td>
        </tr>
    </form>
</body>
</html>
