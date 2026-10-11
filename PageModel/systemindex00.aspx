<%@ Page Language="C#" AutoEventWireup="true" Codebehind="systemindex00.aspx.cs" Inherits="KeLin.WebSite.PageModel.systemindex00" %>

<%@ Register Assembly="AspNetPager" Namespace="Wuqi.Webdiyer" TagPrefix="webdiyer" %>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head runat="server">
    <title>我的页面模板管理</title>
 <meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<script src="../NetCSS/tree.js" type="text/javascript"></script>
<link href="../NetCSS/style.css" rel="stylesheet" type="text/css">
<link href="../NetCSS/teamdtree.css" type="text/css" rel="stylesheet" />

    <script language="javascript" type="text/javascript">
		<!--       
        function DoDelete() {
            var delHangBiaoShis = GetOnChecked();
            if (delHangBiaoShis == null || delHangBiaoShis == "") {
                alert("请选择删除项！")
                return;
            }
            if (window.confirm("请确认删除，否则选择取消?")) {
                document.getElementById("hidHangBiaoShis").value = delHangBiaoShis;
                document.getElementById("CommandType").value = "del";
                document.getElementById("form1").submit();
            }
        }
        function DoVER() {
            var delHangBiaoShis = GetOnChecked();
            if (delHangBiaoShis == null || delHangBiaoShis == "") {
                alert("请选择项！")
                return;
            }

            document.getElementById("hidHangBiaoShis").value = delHangBiaoShis;
            //document.getElementById("CommandType").value = "dook";
            //document.getElementById("form1").submit();
            window.open("showver00.aspx?classid=0&toid=" + delHangBiaoShis, "vers", "height=380, width=320, toolbar= no, menubar=no, scrollbars=no, resizable=no, location=no, status=no")


        }
        function DoEdit() {
            var SelectNo = checkSelectNo('chkAll');
            if (SelectNo == 0) {
                alert("请选择需要修改的模块！")
                return;
            }
            if (SelectNo > 1) {
                alert("修改数据时不能多选！")
                return;
            }
            
            var edtHangBiaoShis = GetOnChecked();
            document.getElementById("hidHangBiaoShis").value = edtHangBiaoShis;
            document.getElementById("CommandType").value = "Edit";
            document.getElementById("form1").submit();
            
        }
        function GetOnChecked() {
            var sRet = '';
            var sChar = '';
            var varHangBiaoShi = "";
            var a = document.getElementsByName('chkSelect');
            var n = a.length;
            //alert(n);			
            for (var i = 0; i < n; i++) {
                if (a[i].checked) {
                    varHangBiaoShi = varHangBiaoShi + sChar + a[i].value;
                    sChar = ',';
                }

            }
            return varHangBiaoShi;
        }
        

        function shSearch() {
            //alert("aa");
            //var a = document.all.item("searchtable").style.display;
            if (document.all.item("formSearch").style.display == "none")
                document.all.item("formSearch").style.display = "block";
            else
                document.all.item("formSearch").style.display = "none";
        }
		//-->
function checkAll(chkAllID,thisObj) 
{ 
    var chkAll = document.getElementById(chkAllID); 
    var chks = document.getElementsByTagName("input"); 
    var chkNo = 0; 
    var selectNo = 0; 
    for(var i =0; i < chks.length; i++) 
    { 
       if(chks[i].type == "checkbox") 
       { 
          //全选触发事件    
          if(chkAll == thisObj) 
          { 
             chks[i].checked = thisObj.checked;               
          }            
          //非全选触发 
          else 
          { 
            if(chks[i].checked && chks[i].id != chkAllID) 
             selectNo++; 
          } 
          if(chks[i].id != chkAllID) 
          { 
            chkNo++; 
          } 
       } 
    }   
    if(chkAll != thisObj) 
    { 
        chkAll.checked = chkNo==selectNo; 
    } 
} 
function checkSelectNo(chkAllID) 
{ 
    var chks = document.getElementsByTagName("input"); 
    var selectNo =0; 
    for(var i =0; i < chks.length; i++) 
    { 
       if(chks[i].type == "checkbox") 
       { 
            if(chks[i].id != chkAllID && chks[i].checked) 
            { 
                selectNo++; 
            } 
       } 
    }    
    return selectNo; 
} 


function gourl(strurl){
    window.location= strurl ;
	
}
function showpgm() {

    window.open("uploadTXT00.aspx?classid=0", "pgm", "height=200, width=350, toolbar= no, menubar=no, scrollbars=yes, resizable=no, location=no, status=no")

}

    </script>

</head>
<body>
    <form id="form1" runat="server">
        <table height="100%" cellspacing="0" cellpadding="0" width="100%" border="0">
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
                                                超级管理员区&gt;&gt; 页面模板管理 
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                                <td width="13">
                                    <img src="/NetImages/main_title_03.gif" width="13" height="24" /></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td valign="top" align="left" style="width: 100%;">
                        <div class="txt4">
                            <font face="宋体">
                                <table id="Table1" width="100%" class="TABLE6">
                                    <tbody>
                                        
                                        <tr>
                                            <td style="word-wrap: break-word; width: 180px" valign="top" align="left">
<!--开始左边文章分类列表-->
                                                                <table class="adminTable" cellspacing="0" cellpadding="3" width="100%">
                                                                    <tbody>
                                                                        
                                                                        <tr>
                                                                            <td class="column" align="center">
                                                                               请选择分类
                                                                              </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td class="f">

                                                                                <script type="text/javascript">

d = new dTree('d');
d.add(0,-1,'<a href="systemindex00.aspx?typeid=">所有页面模板</a>');
  	                                      
  <%=strJavascript %>                                     

document.write(d);
                                                                                </script>
     <br />
     【<a href="systemmodel00.aspx">分类管理</a>】<br /><br />
     
     【<a href="javascript:showpgm();">导入页面模板文件</a>】<br /><br />
     
     【<a href="/Template/页面模板示例.zip" />点击下载模板示例</a>】
     
     <%if (KL_Open_Web == "1")
       { %>
     <br /><br /><font color="red">注意：请联系超级管理员配置web.config中参数KL_Open_Web设为 0  ，关闭旧版 ，旧版电脑排版<a href="/admin/htmlweb.aspx">点击此进入</a>。</font>
     <%} %>
   
                                                                   
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                                <!--结束左边文章列表-->
                                            </td>
                                            <td colspan="4" valign="top" align="left">
                                                <table id="Table2" cellspacing="0" cellpadding="0" border="0" height="100%" width="100%">
                                                    <tr>
                                                        <td>
                                                            <table id="tblManageRanks" class="tableBorder" cellspacing="1" cellpadding="3" border="0"
                                                                height="100%" width="100%">
                                                                <tr>
                                                                    <td class="fh" align="left">
                                                                    <h1><%if (pagename != "")
                                                                      {%>
                                                                   模板名称：<%=pagename%>
                                                                   <%}
                                                                          else { Response.Write("请在左边选择一个页面模板。"); } %></h1>
                                                                        <input name="" type="button" value=" 新增页面源码 " onclick="window.location='addFunction00.aspx?typeid=<% =typeid%>';" class="bt" />&nbsp;
                                                                         <input name="" type="button" value=" 新增转发地址 " onclick="window.location='addPath00.aspx?typeid=<% =typeid%>';" class="bt" />&nbsp;
                                                                       
                                                                        <input name="" type="button"   value=" 修 改 " onclick="DoEdit()" id="Button1" class="bt"/>&nbsp;
                                                                        <input name="" type="button"   value=" 删 除 " onclick="DoDelete()" id="Button2" class="bt"/>
                                                                      <input name="" type="button"   value=" 分配版本 " onclick="DoVER()" id="Button5" class="bt"/>
                                                                        分类ID：<asp:TextBox runat="server" ID="txt_typeid" Width="100px"></asp:TextBox>
                                                                       
                                                                        页面名称：<asp:TextBox runat="server" ID="txt_typename" Width="184px"></asp:TextBox>
                                                                        <asp:Button ID="bt_find" Width="60" Text=' 查 询 ' runat="server" OnClick="bt_find_Click" CssClass="bt">
                                                                        </asp:Button>
                                                                        &nbsp;&nbsp;
                                                                         <asp:Button ID="Button6" Width="120" Text=' 导出模板(发布) ' runat="server" OnClick="bt_excel_Click" OnClientClick="return confirm('导出文件后，还需要将模板中引用的文件夹放在一起，才能成为一个完整的模板。')" CssClass="bt">
                                                                        
                                                                        </asp:Button>
                                                                         &nbsp;&nbsp;
                                                                         
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <asp:DataGrid ID="DataGrid1" runat="server" DataKeyField="typeid" AllowSorting="True"
                                                                AutoGenerateColumns="False" Width="100%" PageSize="25" ShowFooter="True" OnSortCommand="DataGrid1_SortCommand">
                                                                <HeaderStyle Font-Bold="True" Font-Size="9pt"></HeaderStyle>
                                                                <ItemStyle BackColor="#DDEEFF"></ItemStyle>
                                                                <AlternatingItemStyle CssClass="fh" BackColor="White"></AlternatingItemStyle>
                                                                <Columns>
                                                                    <asp:TemplateColumn HeaderStyle-CssClass="column">
                                                                        <HeaderStyle Width="20px"></HeaderStyle>
                                                                        <ItemStyle Width="20px"></ItemStyle>
                                                                        <HeaderTemplate>
                                                                            <input type="checkbox" id="chkAll" name="chkAll" value="checkbox" onclick="checkAll('chkAll',this);" />
                                                                        </HeaderTemplate>
                                                                        <ItemTemplate>
                                                                            <input type="checkbox" name="chkSelect" value='<%# DataBinder.Eval(Container.DataItem, "id") %>'
                                                                                onclick="checkAll('chkAll',this);" />
                                                                        </ItemTemplate>
                                                                        <FooterTemplate>
                                                                        </FooterTemplate>
                                                                    </asp:TemplateColumn>
                                                                    <asp:TemplateColumn HeaderText="分类ID" SortExpression="typeid" ItemStyle-Width="50px"
                                                                        HeaderStyle-CssClass="column">
                                                                        <ItemTemplate>
                                                                            <%#DataBinder.Eval(Container.DataItem, "typeid")%>
                                                                          
                                                                        </ItemTemplate>
                                                                    </asp:TemplateColumn>
                                                                    <asp:TemplateColumn HeaderText="排序号"  SortExpression="rank" ItemStyle-Width="50px" HeaderStyle-CssClass="column">
                                                                        <ItemTemplate>
                                                                           <!-- <%#(Container.ItemIndex + 1) + AspNetPager1.PageSize * (AspNetPager1.CurrentPageIndex - 1)%>-->
                                                                         <%#DataBinder.Eval(Container.DataItem, "rank")%>
                                                                        </ItemTemplate>
                                                                    </asp:TemplateColumn>
                                                                   
                                                                    <asp:TemplateColumn HeaderText="页面名称" SortExpression="pagename" ItemStyle-Width="150px"
                                                                        HeaderStyle-CssClass="column">
                                                                        <ItemTemplate>
                                                                            
                                                                             <a href="addFunction00.aspx?modelid=<%#DataBinder.Eval(Container.DataItem, "id")%>"><%#DataBinder.Eval(Container.DataItem, "pagename")%></a>
                                                                            
                                                                        </ItemTemplate>
                                                                    </asp:TemplateColumn>
                                                                   
                                                                   
                                                                     <asp:TemplateColumn HeaderText="页面位置"  SortExpression="part" ItemStyle-Width="80px"
                                                                        HeaderStyle-CssClass="column">
                                                                        <ItemTemplate>
                                                                            
                                                                            <%#this.GetPartInfo(DataBinder.Eval(Container.DataItem, "part").ToString())%>
                                                                           
                                                                            
                                                                        </ItemTemplate>
                                                                    </asp:TemplateColumn>
                                                                   
                                                                    <asp:TemplateColumn HeaderText="分配版本" SortExpression="ver" ItemStyle-Width="80px"
                                                                        HeaderStyle-CssClass="column">
                                                                        <ItemTemplate>
                                                                            
                                                                            V<%#DataBinder.Eval(Container.DataItem, "ver").ToString()%>
                                                                           
                                                                            
                                                                        </ItemTemplate>
                                                                    </asp:TemplateColumn>
                                                                    
                                                                    
                                                                    <asp:TemplateColumn HeaderText="指定栏目"  ItemStyle-Width="80px"
                                                                        HeaderStyle-CssClass="column">
                                                                        <ItemTemplate>
                                                                            
                                                                            <%#DataBinder.Eval(Container.DataItem, "keyclassid").ToString()%>
                                                                           
                                                                            
                                                                        </ItemTemplate>
                                                                    </asp:TemplateColumn>
                                                                   
                                                              <asp:TemplateColumn HeaderText="指定模块"  ItemStyle-Width="100"
                                                                        HeaderStyle-CssClass="column">
                                                                        <ItemTemplate>
                                                                          <textarea name="f" rows ="3" ><%#DataBinder.Eval(Container.DataItem, "keyfunction")%></textarea>
                                                                            
                                                                        </ItemTemplate>
                                                                    </asp:TemplateColumn>
                                                                    
                                                                    <asp:TemplateColumn HeaderText="创建时间" SortExpression="addtime" ItemStyle-Width="120px"
                                                                        HeaderStyle-CssClass="column">
                                                                        <ItemTemplate>
                                                                            <%#DataBinder.Eval(Container.DataItem, "addtime")%>
                                                                            <br/<br /> 创建人:<%#DataBinder.Eval(Container.DataItem, "makeid")%>
                                                                        </ItemTemplate>
                                                                    </asp:TemplateColumn>
                                                                    
                                                                    <asp:TemplateColumn HeaderText="操作" ItemStyle-Width="120px"
                                                                        HeaderStyle-CssClass="column">
                                                                        <ItemTemplate>
                                                                            
                                                                             【<a href="addFunction00.aspx?modelid=<%#DataBinder.Eval(Container.DataItem, "id")%>">修改</a>】
                                                                             
                                                                        </ItemTemplate>
                                                                    </asp:TemplateColumn>
                                                                    
                                                                </Columns>
                                                            </asp:DataGrid>
                                                            <webdiyer:AspNetPager ID="AspNetPager1" runat="server" PageSize="25" UrlPaging="true"
                                                                ShowPageIndexBox="Always" PageIndexBoxType="DropDownList" TextBeforePageIndexBox="Go To Page: "
                                                                HorizontalAlign="Left" OnPageChanged="AspNetPager1_PageChanged" EnableTheming="true"
                                                                PageButtonCount="5">
                                                            </webdiyer:AspNetPager>
                                                            转到:<asp:TextBox ID="tb_page" runat="server" Width="30px"></asp:TextBox>
                                                            每页:<asp:TextBox ID="tb_pagecount" runat="server" Width="30px"></asp:TextBox>
                                                            <asp:Button ID="bt_go" runat="server" Text= " Go " OnClick="bt_go_Click" CssClass="bt" />
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                </tr>
            </tbody>
        </table>
        </font> </div> 
                    </td> 
               </tr>
        <tr>
            <td colspan="5">
                <input type="hidden" id="CommandType" name="CommandType" value="<%=strCommandType%>" />
                <input type="hidden" id="hidHangBiaoShis" name="hidHangBiaoShis" />
            </td>
        </tr>
        <tr>
            <td align="center">
                页面执行时间：<%=loadpagetime %>&nbsp;毫秒！</td>
        </tr>
        </tbody> </table>
    </form>
</body>
</html>
