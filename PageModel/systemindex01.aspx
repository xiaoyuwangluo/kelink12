<%@ Page Language="C#" AutoEventWireup="true" Codebehind="systemindex01.aspx.cs" Inherits="KeLin.WebSite.PageModel.systemindex01" %>

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
        function DoOK() {
            var delHangBiaoShis = GetOnChecked();
            if (delHangBiaoShis == null || delHangBiaoShis == "") {
                alert("请选择项！")
                return;
            }
            if (window.confirm("请确认启用，否则选择取消?")) {
                document.getElementById("hidHangBiaoShis").value = delHangBiaoShis;
                document.getElementById("CommandType").value = "dook";
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
                window.open("showver01.aspx?classid=0&toid=" + delHangBiaoShis, "vers", "height=380, width=320, toolbar= no, menubar=no, scrollbars=no, resizable=no, location=no, status=no")

            
        }
        function DoNO() {
            var delHangBiaoShis = GetOnChecked();
            if (delHangBiaoShis == null || delHangBiaoShis == "") {
                alert("请选择项！")
                return;
            }
            if (window.confirm("请确认关闭，否则选择取消?")) {
                document.getElementById("hidHangBiaoShis").value = delHangBiaoShis;
                document.getElementById("CommandType").value = "dono";
                document.getElementById("form1").submit();
            }
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

function showclass() {

    window.open("showclass.aspx?classid=0", "class", "height=380, width=320, toolbar= no, menubar=no, scrollbars=no, resizable=no, location=no, status=no")

}
function showpgm() {

    window.open("uploadTXT01.aspx?classid=0", "pgm", "height=200, width=350, toolbar= no, menubar=no, scrollbars=yes, resizable=no, location=no, status=no")

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
                                                网站管理 &gt;&gt; 我的页面模板管理 
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
                                    
                                            <td colspan="4" valign="top" align="left">
                                                <table id="Table2" cellspacing="0" cellpadding="0" border="0" height="100%" width="100%">
                                                    <tr>
                                                        <td>
                                                            <table id="tblManageRanks" class="tableBorder" cellspacing="1" cellpadding="3" border="0"
                                                                height="100%" width="100%">
                                                                <tr>
                                                                    <td class="fh" align="left">
                                                                   
                                                                        <input name="" type="button" value=" 增删改模板名称管理 " onclick="window.location='systemmodel01.aspx';" class="bt" />&nbsp;
                                                                         <input name="" type="button" value="导入系统页面模板 " onclick="showclass()" class="bt" />&nbsp;
                                                                       
                                                                        <input name="" type="button"   value="导入页面模板文件 " onclick="showpgm()" id="Button1" class="bt"/>&nbsp;
                                                                        <input name="" type="button"   value="点击下载模板示例" onclick="window.location='/Template/页面模板示例.zip'" id="Button2" class="bt"/>
                                                                   
   &nbsp;
                                                                     
                                                                       
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
                                                                            <input type="checkbox" name="chkSelect" value='<%# DataBinder.Eval(Container.DataItem, "typeid") %>'
                                                                                onclick="checkAll('chkAll',this);" />
                                                                        </ItemTemplate>
                                                                        <FooterTemplate>
                                                                        </FooterTemplate>
                                                                    </asp:TemplateColumn>
                                                                 <asp:TemplateColumn HeaderText="状态" SortExpression="rank" ItemStyle-Width="40px" 
                                                                        HeaderStyle-CssClass="column">
                                                                        <ItemTemplate>                                                                           
                                                                                <%#this.GetShowMywap_PageModel(DataBinder.Eval(Container.DataItem, "typeid").ToString())%>
                                                                           
                                                                        </ItemTemplate>
                                                                    </asp:TemplateColumn>
                                                                    <asp:TemplateColumn HeaderText="排序号"  SortExpression="rank" ItemStyle-Width="50px" HeaderStyle-CssClass="column">
                                                                        <ItemTemplate>
                                                                           <!-- <%#(Container.ItemIndex + 1) + AspNetPager1.PageSize * (AspNetPager1.CurrentPageIndex - 1)%>-->
                                                                         <%#DataBinder.Eval(Container.DataItem, "rank")%>
                                                                        </ItemTemplate>
                                                                    </asp:TemplateColumn>
                                                                   
                                                                    
                                                                    <asp:TemplateColumn HeaderText="模板名称" SortExpression="typename" 
                                                                        HeaderStyle-CssClass="column">
                                                                        <ItemTemplate>
                                                                            
                                                                             <a href="systemindex01_list.aspx?typeid=<%#DataBinder.Eval(Container.DataItem, "typeid")%>"><%#DataBinder.Eval(Container.DataItem, "typename")%></a>
                                                                            
                                                                        </ItemTemplate>
                                                                    </asp:TemplateColumn>
                                                                  
                                                                   
                                                                    
                                                                    <asp:TemplateColumn HeaderText="创建时间" SortExpression="addtime" ItemStyle-Width="120px"
                                                                        HeaderStyle-CssClass="column">
                                                                        <ItemTemplate>
                                                                            <%#DataBinder.Eval(Container.DataItem, "addtime")%>
                                                                             
                                                                        </ItemTemplate>
                                                                    </asp:TemplateColumn>
                                                                    
                                                                    <asp:TemplateColumn HeaderText="操作" ItemStyle-Width="210px"
                                                                        HeaderStyle-CssClass="column">
                                                                        <ItemTemplate>
                                                                            <br /> 
                                                                             【<a href="systemindex01.aspx?CommandType=dook&typeid=<%#DataBinder.Eval(Container.DataItem, "typeid")%>">开启</a>】【<a href="systemindex01.aspx?CommandType=dono&typeid=<%#DataBinder.Eval(Container.DataItem, "typeid")%>">关闭</a>】
                                                                            <br /> <br />【<a href="javascript:if(confirm('导出文件后，还需要将模板中引用的文件夹放在一起，才能成为一个完整的模板。')){window.location='systemindex01.aspx?CommandType=toexcel&typeid=<%#DataBinder.Eval(Container.DataItem, "typeid")%>';}">导出发布模板</a>】
                                                                              <br /> &nbsp;
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
        <br /><br /><font color="red">技巧：建议在【排版方式一】中建上下级栏目，建好了栏目就会有资源地址，【<a href="/admin/ubb3.aspx" target="_blank">查看资源链接地址</a>】，然后使用这里的【排版方式二】进行排版部署。
        <br /><br />在这里可以排版【全顶】【全底】【首页】，指定栏目界面等等，要排版(例文章)列表有上一页下一页界面怎么办？请进入【添加删除内容】->点击【(例文章)栏目】->【更多栏目属性】-->设置布局100，然后按说明进行操作。</font>
        
         <%if (KL_Open_Web == "1")
       { %>
     <br /><br /><font color="red">注意：请联系超级管理员配置web.config中参数KL_Open_Web设为 0 ，关闭旧版 ，旧版电脑排版<a href="/admin/htmlweb.aspx">点击此进入</a>。</font>
     <%} %>  
     
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
                
        </tr>
        </tbody> </table>
    </form>
</body>
</html>
