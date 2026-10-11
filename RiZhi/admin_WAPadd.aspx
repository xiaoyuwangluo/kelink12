<%@ Page Language="C#" AutoEventWireup="true" CodeBehind="admin_WAPadd.aspx.cs" Inherits="KeLin.WebSite.rizhi.admin_WAPadd" %><%@ Import namespace="KeLin.ClassManager.Tool" %><%
Response.Write(WapTool.showTop(this.GetLang("发表|发表|content add"), wmlVo));//显示头                                                                                                                                                                       
StringBuilder strhtml = new StringBuilder();



    strhtml.Append("<div class=\"subtitle\">" + this.GetLang("发表|发表|Add operation") + "</div>");
  
    strhtml.Append(this.ERROR);
    if (this.INFO == "OK")
    {
        strhtml.Append("<div class=\"tip\">");
        strhtml.Append("<b>发表成功！</b> ");
        if (siteVo.isCheck == 1)
        {
            strhtml.Append("<b>审核后显示！</b> ");
        }
        strhtml.Append(" <a class=\"urlbtn\" href=\"" + this.http_start + "rizhi/book_list.aspx?siteid=" + this.siteid + "&amp;classid=" + this.classid + "&amp;myfriends=" + this.myfriends + "\">返回上级</a>");
        strhtml.Append("</div>");
    }
    else if (this.INFO == "EXTERR")
    {
        strhtml.Append("<div class=\"tip\">");
        strhtml.Append("<b>上传文件格式错误，只允许上传：" + siteVo.UpFileType + "</b><br/>");
        strhtml.Append("</div>");
    }
    else if (this.INFO == "NOTSPACE")
    {
        strhtml.Append("<div class=\"tip\">");
        strhtml.Append("<b>网站总空间已经大于系统分配给此网站的最大空间了，网站空间：" + siteVo.sitespace + "M；此网站已使用：" + (siteVo.myspace) + "KB</b><br/>");
        strhtml.Append("</div>");
    }
    else if (this.INFO == "MAXFILE")
    {
        strhtml.Append("<div class=\"tip\">");
        strhtml.Append("<b>你上传的单个文件总大小超出了最大限制" + siteVo.MaxFileSize + "KB</b><br/>");
        strhtml.Append("</div>");
    }
    else if (this.INFO == "NULL")
    {
        strhtml.Append("<div class=\"tip\">");
        strhtml.Append("<b>内容不能小于2个字符！</b><br/>");
        strhtml.Append("</div>");

    }
    else if (this.INFO == "ERR_FORMAT")
    {
        strhtml.Append("<div class=\"tip\">");
        strhtml.Append("<b>取到非法值:“$$(”请更换手机浏览器或重新编辑！</b><br/>");
        strhtml.Append("</div>");
    }
    else if (this.INFO == "WAITING")
    {
        strhtml.Append("<div class=\"tip\">");
        strhtml.Append("<b>请再过" + this.KL_CheckIPTime + "秒后操作！</b><br/>");
        strhtml.Append("</div>");

    }
    else if (this.INFO == "LOCK")
    {
        strhtml.Append("<div class=\"tip\">");
        strhtml.Append("<b>抱歉，您已经被加入黑名单，请注意发帖规则！</b><br/>");
        strhtml.Append("</div>");
    }


   
    if (this.INFO != "OK")
    {
        strhtml.Append("<div class=\"content\">");
        
      
        //选择栏目
        strhtml.Append("<form name=\"f\" action=\"" + http_start + "rizhi/admin_WAPadd.aspx\" enctype=\"multipart/form-data\" method=\"post\">");
        strhtml.Append("<div class=\"gdpl\">");
        strhtml.Append("<span id=\"showfacelist\"></span><a href=\"javascript:showface();\" class=\"bbsface\" title=\"表情\"></a>&nbsp;&nbsp;");
   
        strhtml.Append("\n<script type=\"text/javascript\"> \n function showface() \n{ \n var strhtml =\"<div id='SmohanFaceBox'><span class='Corner'></span><div class='Content'><h3><span>常用表情</span><a class='close' href='javascript:hiddenface(1);' title='关闭'></a></h3><ul><img src='/Template/default/img/loads.gif' alt='.'/>正在加载中...</ul></div></div>\" ;\n  document.getElementById('showfacelist').innerHTML = strhtml; myajax('GET','/rizhi/ajax.aspx?type=face&siteid=" + this.siteid + "&classid=" + this.classid + "','bbsface'); \n}  \n");
        strhtml.Append("\n function hiddenface(a) { if(a==1){ document.getElementById('SmohanFaceBox').style.display = 'none'; }else {document.getElementById('SmohanFaceBox').style.display = 'block';} }");
        strhtml.Append("\n function storeCaret() { \n var textEl = f.content; \n if (textEl.createTextRange) \n textEl.caretPos = document.selection.createRange().duplicate(); \n }");
        strhtml.Append("\n function addubb(ubb) {");
        strhtml.Append("\nvar textEl = f.content;");
        strhtml.Append("\nvar text = ubb;");
        strhtml.Append("\nif (textEl.createTextRange && textEl.caretPos) {");
        strhtml.Append("\n   var caretPos = textEl.caretPos;");
        strhtml.Append("\n   caretPos.text = caretPos.text.charAt(caretPos.text.length - 1) == ' ' ? text + ' ' : text;");
        strhtml.Append("\n }");
        strhtml.Append("\n else {");
        strhtml.Append("\n   textEl.value = textEl.value + text; }");
        strhtml.Append("\n} ");
        strhtml.Append("\n</script>\n");

        strhtml.Append("<script type=\"text/javascript\">\n");
        strhtml.Append("function fnTrue(num,messageid){\n");

        strhtml.Append("if(messageid=='bbsface') {\n");
        strhtml.Append("if(num.indexOf(\"SmohanFaceBox\") > 0){ document.getElementById('showfacelist').innerHTML = num; }\n");
        strhtml.Append("}");
        strhtml.Append("\n}");
        strhtml.Append("\n function fnFalse(messageid){showBox('网络不给力，提交失败！');}\n");
        strhtml.Append("</script>\n");
        
        strhtml.Append("<input type=\"radio\" value=\"0\" onclick=\" document.getElementById('rizhi_title').style.display='none';\" ");
        if (this.book_type == "0")
        {
            strhtml.Append(" checked");
        }
        strhtml.Append(" name=\"book_type\">微博&nbsp;&nbsp;&nbsp;&nbsp;");

        strhtml.Append("<input type=\"radio\" value=\"1\" onclick=\"document.getElementById('rizhi_title').style.display='block';\" ");
        if (this.book_type == "1")
        {
            strhtml.Append(" checked");
        }
        strhtml.Append(" name=\"book_type\">日志<br/>");

        if (this.book_type == "1")
        {
            strhtml.Append("<div id=\"rizhi_title\" style=\"display:block\">");
        }
        else
        {
            strhtml.Append("<div id=\"rizhi_title\" style=\"display:none\">");
        }
        //strhtml.Append(this.GetLang("标题|標題|Title") + "*:<br/>");
        strhtml.Append("<textarea name=\"book_title\" placeholder=\"请输入标题\"rows=\"1\" class=\"KL_textarea\" style=\"width:100%\">" + book_title + "</textarea><br/>");
        strhtml.Append("</div>");


        strhtml.Append("<textarea name=\"content\" placeholder=\"快来发表一下，也可以输入URL链接地址哦～\" rows=\"5\"class=\"KL_textarea\" style=\"width:100%\">" + book_content + "</textarea><br/>");

        strhtml.Append(this.GetLang("我的分类|我的分类|Class of rizhi") + ":");
        strhtml.Append("<input type=\"radio\" value=\"0\" checked name=\"smalltypeid\">默认&nbsp;");

        for (int k = 0; (smallTypeList != null && k < smallTypeList.Count); k++)
        {
            string selected = "";
            if (smalltypeid == smallTypeList[k].id.ToString()) selected = "checked";

            strhtml.Append("<input type=\"radio\" value=\"" + smallTypeList[k].id + "\" " + selected + " name=\"smalltypeid\">" + smallTypeList[k].subjectname + "&nbsp;");
            
            
        }
        strhtml.Append("&nbsp;<a href=\"/rizhi/myrizhi_admin.aspx?siteid="+this.siteid+"&classid="+this.classid+"\">管理</a><br/>");
        strhtml.Append(this.GetLang("系统分类|系统分类|Class") + ":");
        strhtml.Append("<input type=\"radio\" value=\"0\" checked name=\"toclassid\">默认&nbsp;&nbsp;");

        for (int i = 0; (classList != null && i < classList.Count); i++)
        {
            string selected = "";
            if (classid == classList[i].classid.ToString()) selected = "checked";

            strhtml.Append("<input type=\"radio\" value=\"" + classList[i].classid + "\" " + selected + " name=\"toclassid\">" + classList[i].classname + "&nbsp;&nbsp;");
            
         
        }
        strhtml.Append("<br/>");
        
        //strhtml.Append(this.GetLang("公开|公开|公开") + "*:");
        //strhtml.Append("<select name=\"ishidden\" value=\"0\">");
        //strhtml.Append("<option value=\"0\">0_是</option>");
        //strhtml.Append("<option value=\"1\">1_否</option>");
        //strhtml.Append("</select><br/>");
       


        //strhtml.Append("选择图片，最多9张。<br/>");
        strhtml.Append("<div id=\"addBtn\" class=\"bt1\"><img src=\"/Template/default/img/pyq_zj.png\" width=\"22px\" height=\"22px\"/></div>");
        strhtml.Append("<ul id=\"addFile\">");
        strhtml.Append("<li>");
        strhtml.Append("<input type=\"file\" name=\"book_file\"  value=\"\">");
        strhtml.Append("</li>");
        strhtml.Append("</ul>");

        strhtml.Append("\r\n<script>\r\n");       
        strhtml.Append("var oAddBtn = document.getElementById('addBtn');\r\n");
        strhtml.Append("var oAddBox = document.getElementById('addFile');\r\n");
        strhtml.Append("var sHtml = '<li><input type=\"file\" name=\"book_file\"  value=\"\"><span onclick=\"this.parentNode.parentNode.removeChild(this.parentNode)\">&nbsp;&nbsp;<img src=\"/Template/default/img/pyq_del.png\" width=\"22px\" height=\"22px\"/></span></li>';\r\n");
        strhtml.Append("oAddBtn.onclick = function () {\r\n");
        strhtml.Append("if (oAddBox.getElementsByTagName('li').length < 9) {oAddBox.innerHTML +=   sHtml;}else {alert('最多上传9个文件哦～');}\r\n");

        strhtml.Append("}\r\n");
        strhtml.Append("</script>\r\n");
        /*
        for (int i = 0; i < 9; i++)
        {
            strhtml.Append((i+1)+".");
            strhtml.Append("<input type=\"file\" name=\"book_file\" value=\"\"/><br/>");
            
        }*/

        strhtml.Append("图压缩最大宽:<input type=\"text\" name=\"swidth\" style=\"width:50px\" value=\"480\"/>px");
        strhtml.Append("<input type=\"hidden\" name=\"sheight\" style=\"width:20px\" value=\"\"/>");
 

        strhtml.Append("<input type=\"hidden\" name=\"action\" value=\"gomod\"/>");
        strhtml.Append("<input type=\"hidden\" name=\"classid\" value=\"" + classid + "\"/>");
        strhtml.Append("<input type=\"hidden\" name=\"siteid\" value=\"" + siteid + "\"/>");
        strhtml.Append("<input type=\"hidden\" name=\"num\" value=\"" + num + "\"/>");
        strhtml.Append("<input type=\"hidden\" name=\"sid\" value=\"" + sid + "\"/>");
        strhtml.Append("<input type=\"submit\"  name=\"bt\" class=\"btn\" value=\"" + this.GetLang("发表|发表|submit") + "\"/>");
        strhtml.Append("</div>");

        strhtml.Append("</form>");
        strhtml.Append("</div>");
    }

                                                                                                                                                                                   
    strhtml.Append("<div class=\"btBox\"><div class=\"bt1\">");
    strhtml.Append("<a href=\"" + this.http_start + "rizhi/book_list.aspx?siteid=" + this.siteid + "&amp;classid=" + this.classid + "&amp;myfriends=" + this.myfriends + "\">" + this.GetLang("返回列表|返回列表|Back to list") + "</a>");

    strhtml.Append("</div></div>");

   

    Response.Write(strhtml);

                                                                                                                                                                               
                                                                                                                                                                             
//显示底部
Response.Write(WapTool.showDown(wmlVo)); %>



