<%@ Page Language="C#" AutoEventWireup="true" validateRequest="false" Codebehind="addPath01.aspx.cs" Inherits="KeLin.WebSite.PageModel.addPath01" %>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head runat="server">
    <title>编辑功能模块</title>
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
 
    window.location.href = "systemindex01.aspx?typeid=<%=typeid %>";
}
function saveOrder() { var w = window.open("", "预览效果", "height=800,width=480,toolbar=no,menubar=no,scrollbars=no,resizable=on,location=no,status=no"); var dt = new Date();  w.document.write(window.form1.tb_remark.value); }
function dataToTxt() {
    var file_name = window.prompt("请指定输出文件名称(.html)", "C:\\Export<%=modelid %>.html");
    if (file_name != null) {
        
        //file_name = file_name.split("//").join("////");
        alert(file_name);
        var FSO = new ActiveXObject("Scripting.FileSystemObject");
        var f1 = FSO.CreateTextFile(file_name, true);

        f1.write(window.form1.tb_remark.value);
        
        f1.close();
    }
}

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
                                                网站管理 >> 我的页面模板管理 >> <%if (modelid != "") { Response.Write("修改"); } else { Response.Write("新增"); } %>路由路径
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
                                            <font face="宋体">原页面地址： </font>
                                        </td>
                                        <td class="fh3">
                                            <asp:TextBox ID="tb_typename" runat="server" Width="500px"></asp:TextBox> 
                                            <br />（如：/bbs/book_list.aspx 再如：/bbs/book_view.aspx，一定要/开头，首页固定标识地址：/defalut.aspx）
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
                                            <font face="宋体">转向地址： </font>
                                        </td>
                                        <td class="fh3">
                                       <asp:TextBox ID="tb_remark" runat="server" Width="500px"></asp:TextBox>
                                             <br />（务必要/开头，例/Template/自己定义文件名.aspx ，需要保留原来页面第一行的代码，即新aspx页面继承原来的.cs，这样可以直接取原来页面的对象。 ）
                                             
                                         </td>
                                    </tr>
                                    
                                    <tr>
                                        <td class="f" style="width: 16%">
                                            <font face="宋体">页面分配至： <!--<input type="checkbox" name="chkall" value="" onclick="CheckAll(this.form)"> 全选</font>-->
                                        </td>
                                        <td class="fh3">
                                              
                                            <font color="red"><b>版本必选：</b></font>  <input type="radio" id="Checkbox3" name="ver" value="3" <%if(this.ver=="3") Response.Write("checked"); %>  /><font color="red"><b>[ver] = 3 电脑版 </b> </font>    
                                                <input type="radio" id="Checkbox1" name="ver" value="1" <%if(this.ver=="1") Response.Write("checked"); %>  /> &amp;sid=-1 淘汰                                                
                                                <input type="radio" id="Checkbox2" name="ver" value="2" <%if(this.ver=="2") Response.Write("checked"); %>  /> &amp;sid=-2 手机彩版    
                                                
                                                <input type="radio" id="Checkbox4" name="ver" value="4" <%if(this.ver=="4") Response.Write("checked"); %>  /> &amp;sid=-4 扩展     
                                                <input type="radio" id="Checkbox5" name="ver" value="5" <%if(this.ver=="5") Response.Write("checked"); %>  /> &amp;sid=-5 扩展  
                                                <input type="radio" id="Checkbox6" name="ver" value="0" <%if(this.ver=="0") Response.Write("checked"); %>  />
                         全部(响应式页面选这个)   
                                       <br /><br /> 
                                       <!--
                                            <font color="red"><b>页面分解：</b> </font>
                                         
                                            
                                            <input type="radio"  name="part" value="1" onclick ="document.getElementById('showfunction').style.display= 'block';" <%if(this.part== "1") Response.Write("checked"); %> />全局顶部                                                 
                                            <input type="radio"  name="part" value="3" onclick ="document.getElementById('showfunction').style.display= 'block';"  <%if(this.part== "3") Response.Write("checked"); %> />全局低部  
                                            <input type="radio"  name="part" value="2" onclick ="document.getElementById('showfunction').style.display= 'none';"  <%if(this.part == "2") Response.Write("checked"); %> />普通栏目(页面中间部分)  
                                           
                                            <br /> <br />
                                            <font color="red"><b>特殊指定普通栏目ID：</b></font><input  type ="text" name="toclassid" value="<%=this.toclassid %>" style="width:150px"/> 　（首页为０，可留空。栏目所有下级格式用栏目ID+*,例:128*）
                                            <br /> <br />
                                            <div id="showfunction" style="<%if(this.part=="2") Response.Write("display:none");%>">
                                            <font color="red"><b>特殊指定模块（上面选了全顶和全低，下面不用再选，如果选了是特殊指定）：</b></font>
                                            
                                            <br /><br />
                                           <%
                                               for (int i = 0; (listVo != null && i < listVo.Count); i++)
                                               {
                                                                                             
                                                   Response.Write("<input type=\"checkbox\" id=\"chkAll\" name=\"fun\" value=\"" + (listVo[i].typepath +"/").Split('/')[0] + "\" ");
                                                   if (this.keys.IndexOf((listVo[i].typepath +"/").Split('/')[0]) >= 0)
                                                   {
                                                       Response.Write("checked");

                                                   }
                                                   Response.Write("/>" );

                                                   
                                                   Response.Write(" &nbsp;&nbsp;");
                                                   Response.Write(listVo[i].typename);
                                                   //Response.Write(" &nbsp;&nbsp;标识:");
                                                   //Response.Write((listVo[i].typepath + "/").Split('/')[0]);
                                                   Response.Write("<br/>");
                                               }
                                                %>
                                            </div>

                                            -->
                                       </td>
                                    </tr>
                                    
                                
                                    
                                    <tr>
                                        <td class="f" style="width: 16%">
                                            <font face="宋体">自定义序号： </font>
                                        </td>
                                        <td class="fh3">
                                            <asp:TextBox ID="tb_rank" runat="server" Width="57px"></asp:TextBox>
                                        </td>
                                    </tr>
                                     <tr>
                                        <td class="f" style="width: 16%">
                                           <!-- <font face="宋体">是否生效： </font>-->
                                        </td>
                                        <td class="fh3">
                                         <!--   <select name="ishidden">
                                             <option <%if(this.ishidden=="1") Response.Write("selected"); %>  value ="1">启用</option>
                                             <option <%if(this.ishidden=="0") Response.Write("selected"); %> value ="0">关闭</option>
                                            </select>-->
                                                 
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
