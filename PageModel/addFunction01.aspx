<%@ Page Language="C#" AutoEventWireup="true" validateRequest="false" Codebehind="addFunction01.aspx.cs" Inherits="KeLin.WebSite.PageModel.addFunction01" %>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head runat="server">
    <title>编辑功能模块</title>
    <meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
    <link href="/NetCSS/style.css" rel="stylesheet" type="text/css">
    <style id="visualeditor-style" type="text/css">
* { padding: 0; margin: 0; text-decoration: none; }
*:focus { outline: none; }
#box-visual { display: none; margin: 10px; }
.visualeditor-editor { color: #222; font-size: 13px; font-family: "微软雅黑"; }
.visualeditor-editor textarea { box-sizing: border-box; width: 100%; padding: 2px 8px; border: 1px solid #ccc; line-height: 24px; color: #222; resize: none; }
.visualeditor-editor textarea:focus, .visualeditor-editor textarea:hover { border: 1px solid #DDEEFF; box-shadow: 0 0 5px #DDEEFF; }
.visualeditor-odd textarea { height: 32px; }
.visualeditor-even textarea { height: 200px; }
.visualeditor-title { padding: 0 10px; margin-top: 20px; border-left: 5px solid #9ACEF7; background: #DEF; font-size: 13px; line-height: 32px; color: #333; }
.box-visual-null { padding: 20px; margin: 20px; background: #DEF; }
.box-visual-null h2 { line-height: 32px; }
.box-visual-null p { padding: 10px 0; line-height: 28px; }
</style>
    <script type="text/javascript" src="/NetCSS/visualeditor.js"></script>
    <script type="text/javascript">
		var editor =  new visualeditor({
			source : 'source',
			visual : 'visual',
			textarea : 'tb_remark'
		});
		
	</script>
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
function ShowSelect() {
    var str  = "";
    
   
    for (var i = 0; i < form1.chkAll.length; i++) {
        if (form1.chkAll[i].checked == true) {
            //alert(form1.chkAll[i].value);
            str = str + form1.chkAll[i].value + ";";
        }

        form1.tb_keys.value = str.replace(/\r\n/ig, "");
    }


}
function showINFO(a) {

    if (a == 0) {
        document.getElementById('toinfo').innerHTML = '或';
    } else {
        document.getElementById('toinfo').innerHTML = '并';
    }


}

var a = 1;
var strTemp = "";
var boolone = 1;
var xmlhttp = null;

function LoadHTML() {
    if (boolone == 1) {
        strTemp = document.getElementById('tb_remark').value;
        boolone = 0;
    }
    
    var tourl = document.getElementById('tb_imagespath').value;
    if (tourl == "") {
        alert("地址无效，无法获取。");
        return;
    }

    tourl = "/PageModel/reads.aspx?paths=" + tourl;

  

    try {
        if (window.XMLHttpRequest) {
            xmlhttp = new XMLHttpRequest();
            if (xmlhttp.overrideMimeType) {
                xmlhttp.overrideMimeType("text/xml");
            }
        } else if (window.ActiveXObject) {
            var activexName = ["MSXML2.XMLHTTP", "Microsoft.XMLHTTP", ""];
            for (var i = 0; i < activexName.length; i++) {
                try {
                    xmlhttp = new ActiveXObject(activexName[i]);
                    break;
                } catch (e) { }
            }
        }

        xmlhttp.onreadystatechange = KL_CallBack;
        xmlhttp.open("GET", tourl, true);
        xmlhttp.send(null);
        //xmlhttp.Charset = "UTF-8";
    } catch (e) {
       alert("获取出错，请重试。");
    }


}
function KL_CallBack() {

    if (xmlhttp == null) {
        alert("获取出错，请重试。");
        return;
    }
    if (xmlhttp.readyState == 4) {
        if (xmlhttp.status == 200) {
            var responseText = xmlhttp.responseText;

            if (responseText != "") {
                document.getElementById('tb_remark').value = responseText;
                alert("替换成功！");
            } else {
                alert("无数据！");
            }
        } 
    }
}
function ToBack() {
    document.getElementById('tb_remark').value = strTemp;

}
window.onload = function() {
strTemp = document.getElementById('tb_remark').value;
} 
    </script>
    </head>
    <body text="#000">
    <form id="form1" runat="server" enctype="multipart/form-data">
      <table cellspacing="0" cellpadding="0" width="100%" border="0">
        <tbody>
          <tr>
            <td colspan="4"><table width="100%" border="0" cellpadding="0" cellspacing="0">
                <tr>
                <td width="12"><img src="/NetImages/main_title_01.gif" width="12" height="24"></td>
                <td width="100%" background="/NetImages/main_title_02.gif"><table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
                    <tr>
                    <td width="2%"><div align="center"> <img src="/NetImages/i08.gif" width="16" height="16"></div></td>
                    <td width="98%" height="24" valign="middle"> 网站管理 >> 我的页面模板管理 >>
                        <%if (modelid != "") { Response.Write("修改"); } else { Response.Write("新增"); } %>
                        页面代码 </td>
                  </tr>
                  </table></td>
                <td width="13"><img src="/NetImages/main_title_03.gif" width="13" height="24"></td>
              </tr>
              </table></td>
          </tr>
          <tr>
            <td valign="top" align="left"><div class="txt4">
                <table class="TABLE6" cellspacing="1" cellpadding="3" width="100%">
                <tbody>
                    <tr>
                    <td class="column" colspan="2" align="center"><font color="red">
                      <%if (modelid != "") { Response.Write("修改"); } else { Response.Write("新增"); }   Response.Write(Request.QueryString.Get("info")); %>
                      </font></td>
                  </tr>
                    <tr>
                    <td class="f" style="width: 16%"><font face="宋体">页面名称： </font></td>
                    <td class="fh3"><asp:TextBox ID="tb_typename" runat="server" Width="200px"></asp:TextBox></td>
                  </tr>
                    <tr>
                    <td class="f" style="width: 16%"><font face="宋体">所属分类：</font></td>
                    <td class="fh3"><font face="宋体">
                      <asp:DropDownList ID="ddl_type" runat="server"> </asp:DropDownList>
                      </font></td>
                  </tr>
                    <tr>
                    <td class="f" style="width: 16%"><font face="宋体">页面代码： </font></td>
                    <td class="fh3"><font color="red"><b>优先级一：</b></font>上传html文件，编码*:
                        <select name="encodetxt">
                        <option value="GB2312">GB2312</option>
                        <option value="GB2312">GB2312</option>
                        <option value="Unicode">Unicode</option>
                        <option value="UTF-8">UTF-8</option>
                      </select>
                        <input type="file" name="book_file" value=""/>
                        <br />
                        <br />
                        <font color="red"><b>优先级二：</b></font>手动录入<br />
                        <div id="box-source">
                        <asp:TextBox ID="tb_remark" runat="server" Width="100%" TextMode="MultiLine" Height="250px"></asp:TextBox>
                      </div>
                        <div id="box-visual">
                        <div class="box-visual-null">
                            <h2>出错了，这个页面没有模板配置！</h2>
                            <h3>自定义模板配置例子：将以下代码复制到一个单独页面模板源码中：</h3>
                            <p> &lt;!--#标题，说明--&gt;<br />
                            [配置名称]......配置的内容......[/配置名称] </p>
                            <h3>调用配置内容的方法：</h3>
                            <p>[include=配置名称]页面名称[/include]</p>
                            <p>可以通过自定义模板配置，拓展出强大的页面模板功能！</p>
                          </div>
                      </div>
                        <br />
                        【<input type="button" value="还原源码" onclick="LoadHTML();" />.<input type="button" value="原始路径" onclick="if(a==1) {a=0; document.getElementById('oldhtml').style.display='';}else{a=1; document.getElementById('oldhtml').style.display='none'; }" /><span id="oldhtml" style ="display:none">/Template/<asp:TextBox ID="tb_imagespath" runat="server" Width="250px"></asp:TextBox></span>.<input type="button" value="撤回" onclick="ToBack();" />】【<a id="btn-source" href="javascript:;">源码编辑</a>】【<a id="btn-visual" href="javascript:;">可视化编辑</a>】
                        <br /><br />支持<a href="/admin/ubb2.aspx" target="_blank">UBB</a>，例嵌套功能：[include]页面名称[/include] ，只限取本分类页面名称。 取某个具体内容用[include=自定义标签]页面名称[/include]，例abc中内容是[tel]130888[/tel]，可以用[include=tel]abc[/include]将返回：130888</td>
                  </tr>
                    <tr>
                    <td class="f" style="width: 16%"><font face="宋体">页面分配至： <!--<input type="checkbox" name="chkall" value="" onclick="CheckAll(this.form)"> 全选</font>--></td>
                    <td class="fh3"><b>版本必选：</b>
                        <input type="radio" id="Checkbox1" name="ver" value="1" <%if(this.ver=="1") Response.Write("checked"); %>  />
                        &amp;sid=-1 淘汰
                        <input type="radio" id="Checkbox2" name="ver" value="2" <%if(this.ver=="2") Response.Write("checked"); %>  />
                        &amp;sid=-2 手机彩版
                        <input type="radio" id="Checkbox3" name="ver" value="3" <%if(this.ver=="3") Response.Write("checked"); %>  />
                        &amp;sid=-3 电脑版
                        <input type="radio" id="Checkbox4" name="ver" value="4" <%if(this.ver=="4") Response.Write("checked"); %>  />
                        &amp;sid=-4 扩展
                        <input type="radio" id="Checkbox5" name="ver" value="5" <%if(this.ver=="5") Response.Write("checked"); %>  />
                        &amp;sid=-5 扩展 
                        <input type="radio" id="Radio1" name="ver" value="0" <%if(this.ver=="0") Response.Write("checked"); %>  />
                        全部(响应式页面选这个)  &nbsp;&nbsp;此选项相当于UBB：[wap=X]代码[/wap] X为1-5<br />
                        <br />
                        <b>页面位置：</b>
                        <input type="radio"  name="part" value="1" onclick ="document.getElementById('showfunction').style.display= 'block';showINFO(0);" <%if(this.part== "1") Response.Write("checked"); %> />
                        1.全局顶部
                        <input type="radio"  name="part" value="3" onclick ="document.getElementById('showfunction').style.display= 'block';showINFO(0);"  <%if(this.part== "3") Response.Write("checked"); %> />
                        3.全局低部
                        <input type="radio"  name="part" value="2" onclick ="document.getElementById('showfunction').style.display= 'none';showINFO(0);"  <%if(this.part == "2") Response.Write("checked"); %> />
                        2.普通栏目(页面中间部分)  
                        →→→
                        <input type="radio"  name="part" value="4" onclick ="document.getElementById('showfunction').style.display= 'block';showINFO(1);" <%if(this.part== "4") Response.Write("checked"); %> />
                        4.指定栏目顶部
                        <input type="radio"  name="part" value="6" onclick ="document.getElementById('showfunction').style.display= 'block';showINFO(1);"  <%if(this.part== "6") Response.Write("checked"); %> />
                        6.指定栏目低部
                        <input type="radio"  name="part" value="5" onclick ="document.getElementById('showfunction').style.display= 'block';showINFO(1);"  <%if(this.part == "5") Response.Write("checked"); %> />
                        5.指定栏目中部 <br />
                        <br />
                        <b>指定栏目ID：</b>
                        <input  type ="text" name="toclassid" value="<%=this.toclassid %>" style="width:150px"/>
                        （首页为０，可留空。栏目下级格式用栏目ID+*,例:128*,栏目128下下级用128**,以此类推，多组用,区分，例：128*,158**。）
                        <div id="showfunction" style="<%if(this.part=="2") Response.Write("display:none");%>"> <br />
                        <b><font color="red"><span id="toinfo">或</span></font></b><br />
                        <br />
                        <b>指定模块（如bbs;article,多个用;号区分）：</b> <br />
                        <asp:TextBox ID="tb_keys" runat="server" Width="100%" TextMode="MultiLine" Height="50px"></asp:TextBox>
                        <br />
                        <br />
                        <%
                                               for (int i = 0; (listVo != null && i < listVo.Count); i++)
                                               {
                                                                                            
                                                   Response.Write("<input type=\"checkbox\" id=\"chkAll\" name=\"fun\"  onclick=\"ShowSelect();\" value=\"" + (listVo[i].typepath +"/").Split('/')[0] + "\" ");
                                                   if (this.keys.IndexOf((listVo[i].typepath +"/").Split('/')[0]) >= 0)
                                                   {
                                                       Response.Write("checked");

                                                   }
                                                   Response.Write("/>" );

                                                   
                                                 
                                                   Response.Write(listVo[i].typename);
                                                   Response.Write(" &nbsp;&nbsp;"); 
                                                   
                                               }
                                                %>
                      </div></td>
                  </tr>
                    <tr>
                    <td class="f" style="width: 16%"><font face="宋体">自定义序号： </font></td>
                    <td class="fh3"><asp:TextBox ID="tb_rank" runat="server" Width="57px"></asp:TextBox></td>
                  </tr>
                    <tr>
                    <td class="f" style="width: 16%"><!--  <font face="宋体">是否生效： </font>--></td>
                    <td class="fh3"><!--<select name="ishidden">
                                             <option <%if(this.ishidden=="1") Response.Write("selected"); %>  value ="1">启用</option>
                                             <option <%if(this.ishidden=="0") Response.Write("selected"); %> value ="0">关闭</option>
                                            </select>--> 
                        <br />注意一:<br />页面所引用js/css/图片等文件请先上传至网站目录/Template/自建文件夹名(例：网站ID_开发商英文_唯一标识)/
                        <br /><br />注意二:<br />原系统部分页面所要用到css和js在下面二个，例只复制分页按钮css所需代码：
                       <br />&lt;link rel=&quot;stylesheet&quot; href=&quot;<a target="_blank" href="/Template/default/default.css">/Template/default/default.css</a>&quot; 
type=&quot;text/css&quot; /&gt;<br>
&lt;script src=&quot;<a target="_blank" href="/Template/default/js/common.js">/Template/default/js/common.js</a>&quot; 
type=&quot;text/javascript&quot;&gt;&lt;/script&gt; 
<br /><br />注意三:<br />一个页面默认分全顶，中间，低部三部分。<br />
一个插件（如文章模块）分三级页面，排版页面定义为一级页面（一班为普通栏目/wapindex-1000-栏目ID.html），列表分页定义为二级页面(文章为例/articlelist-栏目ID.html)，查看内容定义为三级页面(文章为例/article-标题ID.html)。<br />
那二级页面和三级页面怎么排版呢？答案还是要先写好全顶，和全低，中间的二级页面的分页内容和三级页面的查看内容,用一个大的div包住，通过css控制div的位置。具体div里面的排版效果请进入【添加删除内容】点击（文章）栏目名称，【更多栏目属性】选布局100，及调用对应的HTML5的排版代码，<a target="_blank" href="/ubb_api/templace/class/ubbDoc/functionData.htm">点击此看二级页面分页排版代码应用二</a>，还有可以设置三级页面的属性，如是否显示评论之类的。


                         </td>
                  </tr>
                    <tr>
                    <td class="fh3" colspan="2" align="center"><br />
                        <asp:Button ID="bt_ok" runat="server" Text="保 存" OnClick="bt_ok_Click" Width="70px" CssClass="bt"> </asp:Button>
                        &nbsp;&nbsp;&nbsp;&nbsp;<input  type="button" name ="" onclick ="window.open('/UBB_API/index.htm');" value ="万能UBB使用手册" style="width:150px" class="bt" />&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;
                        <asp:Button ID="bt_return" runat="server" Text="返 回"  Width="70px" OnClick="bt_return_Click" CssClass="bt"> </asp:Button>
                        <br />
                        <br /></td>
                  </tr>
                  </tbody>
              </table>
              </div></td>
          </tr>
          <tr>
            <td style="height: 15px" align="center"><span class="txt4"></span></td>
          </tr>
          <tr>
            <td align="right"></td>
          </tr>
          <tr>
            <td align="center"> 页面执行时间：<%=loadpagetime %>&nbsp;毫秒！ </td>
          </tr>
        </tbody>
      </table>
    </form>
</body>
</html>
