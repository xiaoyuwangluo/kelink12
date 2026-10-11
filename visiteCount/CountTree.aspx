<%@ Page Language="C#" AutoEventWireup="true" Codebehind="CountTree.aspx.cs" Inherits="KeLin.WebSite.visiteCount.CountTree" %>
<html xmlns="http://www.w3.org/1999/xhtml">
<head runat="server">
<title>统计</title>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<script src="../NetCSS/tree.js" type="text/javascript"></script>
<link href="../NetCSS/style.css" rel="stylesheet" type="text/css">
<link href="../NetCSS/teamdtree.css" type="text/css" rel="stylesheet" />
<script language="JavaScript" type="text/JavaScript">
function gourl(strurl,classid,siteid){
    document.getElementById("iframe").src =  strurl ;
	
}
    </script>

</head>
<body>
    <form id="form1" runat="server">
        <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
            <tr>
                <td colspan="4">
                    <table width="100%" border="0" cellpadding="0" cellspacing="0">
                        <tr>
                            <td width="12">
                                <img src="../NetImages/main_title_01.gif" width="12" height="24"></td>
                            <td width="100%" background="../NetImages/main_title_02.gif">
                                <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td width="2%">
                                            <div align="center">
                                                <img src="../NetImages/i08.gif" width="16" height="16"></div>
                                        </td>
                                        <td width="98%" height="24" valign="middle">
                                            系统管理 &gt;&gt; 流量统计
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <td width="13">
                                <img src="../NetImages/main_title_03.gif" width="13" height="24"></td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td width="100%" colspan="4">
                    <table width="100%" border="0" cellpadding="0" cellspacing="0" class="TABLE6">
                        <tr>
                            <td>
                                
                                            <div class="txt4">
                                                <table id="Table1" width="100%">
                                                    <tr>
                                                        <td style="word-wrap: break-word; width: 180px; height: 520px;" valign="top" align="left">
                                                         <!--div id="Div1" style="z-index: 1; left: 3px; visibility: inherit; overflow: auto;
                                                                width: 180px; position: absolute; top: 31px; height: 520px"-->
                                                                         <!--开始左边文章分类列表-->
                                                                <table class="adminTable" cellspacing="0" cellpadding="3" width="100%">
                                                                    <tbody>
                                                                       
                                                                        <tr>
                                                                            <td class="column" align="center">
                                                                                请选择</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td class="f">
<script type="text/javascript">

d = new dTree('d');
d.add(0, -1, '访问量统计');
d.add('1', '0', '网站统计概况', 'javascript:gourl(\'waiting.aspx?url=index.aspx\',\'1278\',\'1000\');');
d.add('2', '0', '网站详细统计', 'javascript:gourl(\'everyDateList.aspx\',\'1278\',\'1000\');');
d.add('3', '0', '访问明细记录', 'javascript:gourl(\'datailList.aspx\',\'1278\',\'1000\');');
d.add('4', '0', '地区访问统计', 'javascript:gourl(\'city.aspx\',\'1278\',\'1000\');');
d.add('5', '0', '终端访问统计', 'javascript:gourl(\'ua.aspx\',\'1278\',\'1000\');');
d.add('6', '0', '搜索引擎统计', 'javascript:gourl(\'search.aspx\',\'1278\',\'1000\');');
d.add('8', '0', '当前在线活动', 'javascript:gourl(\'fydetal.aspx\',\'1278\',\'1000\');');
d.add('9', '0', '网站栏目统计', 'javascript:gourl(\'/admin/ClassTree.aspx\',\'1278\',\'1000\');');

d.add('10', '0', '总系统访问量统计', 'javascript:gourl(\'systemindex.aspx\',\'1278\',\'1000\');');
d.add('11', '0', '总在线人活动统计', 'javascript:gourl(\'fydetal.aspx?alls=yes\',\'1278\',\'1000\');');
d.add('12', '0', '<font color="red">服务器安全狗免费使用</font>', 'javascript:gourl(\'safeDog.aspx\',\'1278\',\'1000\');');   

document.write(d);
</script>
<br />
<br />当前状态：<font color="#FF0000" size="6"><%if (KL_VisiteCount_Close != "0") { Response.Write("关闭"); } else { Response.Write("开启"); } %></font>
<br />提示：系统自带的流量统计如果开启会消耗大量CPU，系统默认关闭，你可以调用第三方的流量统计代码，<a href="https://www.baidu.com/s?ie=utf-8&f=8&rsv_bp=1&rsv_idx=1&dsp=baidubrowser&tn=baidu&wd=%E7%BD%91%E7%AB%99%E6%B5%81%E9%87%8F%E7%BB%9F%E8%AE%A1&oq=%25E7%25BD%2591%25E7%25AB%2599%25E6%25B5%258F%25E8%25A7%2588%25E7%25BB%259F%25E8%25AE%25A1&rsv_pq=b2169d7c0000d65a&rsv_t=fc62IKUGpU58raEVjBlGQVpzUlOyQIstgevJk8rr6jH1o9lX0q0hnIaHVvI&rqlang=cn&rsv_enter=1&rsv_sug3=2&rsv_sug1=1&rsv_sug7=100&rsv_n=2&bs=%E7%BD%91%E7%AB%99%E6%B5%8F%E8%A7%88%E7%BB%9F%E8%AE%A1" target ="_blank">点击此申请</a>，申请好后将js代码放至网站全底或放到指定页面底部。如需开启请进入web.config文件，参数：<font color="#FFFFFF"><span style="background-color: #0000FF">KL_VisiteCount_Close</span></font> 设为0，如果不存在此参数，请自行加入。
 
 
                                                                        
                                                         
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                                <!--结束左边文章列表-->
                                                           <!--</div>-->
                                                           
                                                        </td>
                                                        <td colspan="4" valign="top" align="left" height="630px">
                                                            <iframe src="waiting.aspx?url=index.aspx" id="iframe" width="100%" height="2000" frameborder="0"  scrolling="yes"></iframe>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr align="center">
                <td style="height: 14px">
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                   
                </td>
            </tr>
            <tr>
                <td class="bg">
                </td>
            </tr>
        </table>
    </form>
</body>
</html>
