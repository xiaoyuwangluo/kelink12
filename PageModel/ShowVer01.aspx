<%@ Page Language="C#" AutoEventWireup="true" CodeBehind="ShowVer01.aspx.cs" Inherits="KeLin.WebSite.PageModel.ShowVer01" %>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<link href="../NetCSS/style.css" rel="stylesheet" type="text/css" /><link href="../NetCSS/teamdtree.css" type="text/css" rel="stylesheet" />

<title>批量分配版本</title>
<script language="JavaScript" type="text/JavaScript">
<%if(INFO=="OK"){ %>
alert("更新成功！");
window.opener.location.reload();
window.close();

<%} %>
</script>
</head>
<body>
<form name="form1" action="showver01.aspx" method="POST">
<input type="hidden" name="action" value="go" />
<input type="hidden" name="toid" value="<%=toid %>" />
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
                	选择版本号
                	
                  		           
 
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
                                                                                <font color="red"><b>版本必选：</b></font>   <br /><br />
                                                                                
                                                <input type="radio" id="Checkbox3" name="ver" value="3"   /><font color="red"><b>[ver] = 3 电脑版 </b> </font>    <br /><br />
                                                <input type="radio" id="Checkbox1" name="ver" value="1"  />[ver] = 1 淘汰   <br />  <br />                                            
                                                <input type="radio" id="Checkbox2" name="ver" value="2"  />[ver] = 2 手机彩版     <br /><br />                                                
                                                <input type="radio" id="Checkbox4" name="ver" value="4"   />[ver] = 4 扩展    <br /><br />
                                                <input type="radio" id="Checkbox5" name="ver" value="5"   />[ver] = 5 扩展    <br /><br />
                                       <br /><br /> 
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
                【<a href="javascript:form1.submit();">确定批量更新</a>】【<a href="#" onclick="window.close();">关闭</a>】
            </td>
        </tr>
    </form>
</body>
</html>
