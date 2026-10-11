<%@ Page Language="C#" AutoEventWireup="true" CodeBehind="admin_wapmodify_auto.aspx.cs" Inherits="KeLin.WebSite.shop.admin_wapmodify_auto" %><%@ Import namespace="KeLin.ClassManager.Tool" %><%
string title="管理ID:"+this.bookid+"自动发货内容";

Response.Write(WapTool.showTop(title, wmlVo));//显示头                                                                                                                                                                       



   
    StringBuilder strhtml_list = new StringBuilder(); //[view]UBB对应代码
    strhtml_list.Append("<form id=\"form1\" name=\"form1\" action=\"admin_wapmodify_auto.aspx\" enctype=\"multipart/form-data\" method=\"POST\">");
    strhtml_list.Append("<div class=\"showpage2\">");

    strhtml_list.Append("TXT文件(GB2312,内容换行)：<input type=\"file\" name=\"book_file\" value=\"\"/> ");
    strhtml_list.Append("<br/>");

    strhtml_list.Append("<input id=\"Button5\" name=\"\" type=\"button\" value=\" 上 传 \" onclick=\"DoUpload()\" class=\"bt\"/>");
    strhtml_list.Append("</div>");                                                                                                                                                               
    strhtml_list.Append("<div class=\"btBox\"><div class=\"bt3\">");
    strhtml_list.Append("<a href=\"/shop/admin_wapmodify_auto.aspx?bookid="+this.bookid+"&siteid="+this.siteid+"&classid="+this.classid+"&state=\">所有</a>");

    strhtml_list.Append(" <a href=\"/shop/admin_wapmodify_auto.aspx?bookid=" + this.bookid + "&siteid=" + this.siteid + "&classid=" + this.classid + "&state=1\">已发送</a>");
    strhtml_list.Append(" <a href=\"/shop/admin_wapmodify_auto.aspx?bookid=" + this.bookid + "&siteid=" + this.siteid + "&classid=" + this.classid + "&state=0\">未发送</a>");
    
    strhtml_list.Append("</div></div>");

    strhtml_list.Append("<div class=\"showpage2\">");

    strhtml_list.Append("<input type=\"checkbox\" id=\"chkAll\" name=\"chkAll\" value=\"checkbox\" onclick=\"checkAll('chkAll',this);\" /> 全选   ");
    //strhtml_list.Append("<input id=\"Button5\" name=\"\" type=\"button\" value=\" 审 核 \" onclick=\"DoCheck()\" class=\"bt\"/>");
    //strhtml_list.Append(" &nbsp;&nbsp;");
    //strhtml_list.Append("<input id=\"Button2\" name=\"\" type=\"button\" value=\"取消审核\" onclick=\"DoUnCheck()\" class=\"bt\"/>");
    //strhtml_list.Append("&nbsp;&nbsp;");
    strhtml_list.Append("<input id=\"Button4\" name=\"\" type=\"button\" value=\" 删 除 \" onclick=\"DoDel()\" class=\"bt\"/>");
    strhtml_list.Append("</div>");
                                             
    
   
    //显示列表
    //strhtml.Append(linkTOP);
    string lpagetemp =  "&amp;lpage=" + CurrentPage;
    
    
    strhtml_list.Append("<!--listS-->");




   


    for (int i = 0; (listVo != null && i < listVo.Count); i++)
    {
        index = index + kk;
        if (i % 2 == 0)
        {
            strhtml_list.Append("<div class=\"line1\">");
        }
        else
        {
            strhtml_list.Append("<div class=\"line2\">");
        }


        strhtml_list.Append("<input type=\"checkbox\" name=\"chkSelect\" value='" + listVo[i].id + "' onclick=\"checkAll('chkAll',this);\" />");
        strhtml_list.Append(index + "." + listVo[i].content);
        if (listVo[i].userid > 0)
        {

            strhtml_list.Append("<br/>已发货给:<a href=\"/bbs/userinfo.aspx?siteid="+this.siteid+"&amp;touserid="+listVo[i].userid+"\" target=\"_blank\">" + listVo[i].nickname+"("+listVo[i].userid+")</a>");
            strhtml_list.Append("<br/>发货时间:" + listVo[i].gettime);
            strhtml_list.Append("<br/>订单号：" + listVo[i].orderid);
        }
        
        strhtml_list.Append("</div>");
        
    }
    
    
    
    

    if (listVo==null)
    {
        strhtml_list.Append("<div class=\"tip\">暂无记录！</div>");
    }
    strhtml_list.Append("<!--listE-->");
    
    
   
 strhtml_list.Append("<input type=\"hidden\" id=\"CommandType\" name=\"CommandType\" value=\""+ strCommandType+"\" />");
strhtml_list.Append("<input type=\"hidden\" id=\"hidHangBiaoShis\" name=\"hidHangBiaoShis\" />");
strhtml_list.Append("<input type=\"hidden\"   name=\"siteid\" value =\""+ this.siteid +"\" />");
strhtml_list.Append("<input type=\"hidden\"   name=\"classid\" value =\""+classid +"\" />");
strhtml_list.Append("<input type=\"hidden\"   name=\"bookid\" value =\"" + bookid +"\" />");
strhtml_list.Append("<input type=\"hidden\"   name=\"page\" value =\"" + CurrentPage +"\"  />");
strhtml_list.Append("<input type=\"hidden\"   name=\"state\" value =\"" +state +"\" />");
 strhtml_list.Append("</form>");
   
    //显示导航分页
    
        strhtml_list.Append(linkURL);
   

    
        strhtml.Append(strhtml_list);

    

    
   
   
        strhtml.Append("<div class=\"btBox\"><div class=\"bt1\">");
       
        strhtml.Append("<a href=\"" + this.http_start + "shop/admin_wapmodify.aspx?siteid=" + siteid + "&amp;classid=" + this.classid + "&amp;id="+this.bookid + "\">返回上级</a> ");
           
        strhtml.Append("</div></div>");
   

    
  
    //输出
        Response.Write(strhtml.ToString());
                                                                                                                                                                           





                                                                                                                                                                               
Response.Write(ERROR);
 
%>

 <script language="javascript" type="text/javascript">
		<!--
     function DoEdit() {
         var SelectNo = checkSelectNo('chkAll');
         if (SelectNo == 0) {
             alert("请选择需要修改的用户！")
             return;
         }
         if (SelectNo > 1) {
             alert("修改数据时不能多选！")
             return;
         }
         var edtHangBiaoShis = GetOnChecked();
         window.location.href = "admin_modifyUser.aspx?bookid=" + edtHangBiaoShis;
     }
     function DoDel() {
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
     function DoUpload() {
          
             document.getElementById("CommandType").value = "up";
             document.getElementById("form1").submit();
          
     }
     function DoContent() {
         window.location.href = "admin_userlist.aspx";
     }
     function DoReBack() {
         window.location.href = "admin_guestlist.aspx";
     }
     function DoCheck() {
         var SelectNo = checkSelectNo('chkAll');
         if (SelectNo == 0) {
             alert("请选择需要审核的记录！")
             return;
         }
         var locHangBiaoShis = GetOnChecked();
         document.getElementById("hidHangBiaoShis").value = locHangBiaoShis;
         document.getElementById("CommandType").value = "Check";
         document.getElementById("form1").submit();
     }
     function DoUnCheck() {
         var SelectNo = checkSelectNo('chkAll');
         if (SelectNo == 0) {
             alert("请选择需要弃审的记录！")
             return;
         }
         var locHangBiaoShis = GetOnChecked();
         document.getElementById("hidHangBiaoShis").value = locHangBiaoShis;
         document.getElementById("CommandType").value = "UnCheck";
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
     function checkAll(chkAllID, thisObj) {
         var chkAll = document.getElementById(chkAllID);
         var chks = document.getElementsByTagName("input");
         var chkNo = 0;
         var selectNo = 0;
         for (var i = 0; i < chks.length; i++) {
             if (chks[i].type == "checkbox") {
                 //全选触发事件    
                 if (chkAll == thisObj) {
                     chks[i].checked = thisObj.checked;
                 }
                 //非全选触发 
                 else {
                     if (chks[i].checked && chks[i].id != chkAllID)
                         selectNo++;
                 }
                 if (chks[i].id != chkAllID) {
                     chkNo++;
                 }
             }
         }
         if (chkAll != thisObj) {
             chkAll.checked = chkNo == selectNo;
         }
     }
     function checkSelectNo(chkAllID) {
         var chks = document.getElementsByTagName("input");
         var selectNo = 0;
         for (var i = 0; i < chks.length; i++) {
             if (chks[i].type == "checkbox") {
                 if (chks[i].id != chkAllID && chks[i].checked) {
                     selectNo++;
                 }
             }
         }
         return selectNo;
     } 

    </script>


<%
                                                                                                                                                                         
//显示底部
Response.Write(WapTool.showDown(wmlVo)); %>
