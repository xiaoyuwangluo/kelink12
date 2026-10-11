<%@ Page Language="C#" AutoEventWireup="true" validateRequest="false" Codebehind="add.aspx.cs" Inherits="KeLin.WebSite.models.add" %>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head runat="server">
    <title>管理网站模板</title>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<link href="/NetCSS/style.css" rel="stylesheet" type="text/css">

<script language="javascript" type="text/javascript">

function CheckAll(form)  {
  for (var i=0;i<form.elements.length;i++)    {
    var e = form.elements[i];
    if (e.name != 'chkall')       e.checked = form.chkall.checked;
   }
   ShowSelect();
  }

function bt_return_onclick() 
{
 
    window.location.href = "systemmodel.aspx?typeid=<%=typeid %>";
}
function saveOrder() { var w = window.open("", "预览效果", "height=800,width=480,toolbar=no,menubar=no,scrollbars=no,resizable=on,location=no,status=no"); var dt = new Date();  w.document.write(window.form1.tb_remark.value); }

    </script>

</head>
<body text="#000">
    <form id="form1" runat="server" enctype="multipart/form-data">
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
                                                超级管理员区 >> 网站模板管理 >> <%if (modelid != "") { Response.Write("修改"); } else { Response.Write("新增"); } %>网站模板
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
                                        <td class="column" colspan="2" align="center">
                                       <font color="red"><%if (modelid != "") { Response.Write("修改"); } else { Response.Write("新增"); }   Response.Write(Request.QueryString.Get("info")); %></font>  
                                              
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="f" style="width: 16%">
                                            <font face="宋体">网站名称： </font>
                                        </td>
                                        <td class="fh3">
                                            <asp:TextBox ID="tb_typename" runat="server" Width="200px"></asp:TextBox>
                                            
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="f" style="width: 16%">
                                            <font face="宋体">所属分类：</font>
                                        </td>
                                        <td class="fh3">
                                            <font face="宋体">
                                                <asp:DropDownList ID="ddl_type" runat="server">
                                                </asp:DropDownList></font></td>
                                    </tr>
                                   
                                    
                                    <tr>
                                        <td class="f" style="width: 16%">
                                            <font face="宋体">网站ID： </font>
                                        </td>
                                        <td class="fh3">
                                       <asp:TextBox ID="tb_remark" runat="server" Width="200px"></asp:TextBox>
                                             
                                             
                                       </td>
                                    </tr>
                                    
                                
                                    
                                    <tr>
                                        <td class="f" style="width: 16%">
                                            <font face="宋体">自定义序号： </font>
                                        </td>
                                        <td class="fh3">
                                            <asp:TextBox ID="tb_rank" runat="server" Width="57px"></asp:TextBox> (当序号设为1时，新注册的网站将导入此网站模板，作为模板的网站尽量要简洁，四级栏目内，建议用文章和wml模块等实现)
                                        </td>
                                    </tr>
                                    
                                    <tr>
                                        <td class="fh3" colspan="2" align="center">
                                            <br />
                                            <asp:Button ID="bt_ok" runat="server" Text="保 存" OnClick="bt_ok_Click" Width="70px" CssClass="bt">
                                            </asp:Button>
                                            &nbsp;&nbsp;&nbsp;&nbsp; &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;
                                            <asp:Button ID="bt_return" runat="server" Text="返 回"  Width="70px" OnClick="bt_return_Click" CssClass="bt">
                                            </asp:Button>
                                            <br />
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
