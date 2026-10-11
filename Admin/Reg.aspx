<%@ Page Language="C#" AutoEventWireup="true" CodeBehind="Reg.aspx.cs" Inherits="KeLin.WebSite.Reg" %><%
Response.Buffer = true; 
Response.Expires = -1;
Response.ExpiresAbsolute = DateTime.Now.AddDays(-1); 
Response.Expires = 0 ;
Response.CacheControl = "no-cache";  
%><!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=GB2312" />
<title>测试体验 -联速科技出品</title>
<meta name="author" content="kelink,kelink.com,chinastrong" />
<meta name="keywords" content="3g,3g手机,3g建站,3g门户,wap,wap建站,手机上网,手机建站,手机看电影,手机游戏,手机看小说,手机流媒体,手机门户,3G门户" />
<meta name="description" content="体验神奇的WAP建站经历，走进一个全新的wap网站时代！无需专业知识，无需特殊软件，只要您会打字，十分钟的简单制作，您就可以拥有自己的精彩wap站点，让亿万手机用户分享您的设计成果与乐趣；同时本站有成熟完善的商业合作模式，致力于与各wap站长一起创造互惠互利的双赢局面,给大家提供一个3G时代创造财富的机会。还犹豫什么呢？赶快申请吧。" />
<link rel="stylesheet" type="text/css" href="/netcss/index/css/start.css"/>
<script src="http://libs.baidu.com/jquery/1.4.2/jquery.min.js"></script>
<script src="/netcss/index/js/jquery.SuperSlide.2.1.1.js"></script>
</head>
<script language="javascript" type="text/javascript">

function siteurltousername(){
	document.form1.tb_username.value=document.form1.tb_domain.value	
}           
</script> 
<body>

    <div class="fill-form-bg">
      <div class="fill-form">
        <form id="form1" runat="server">
          <p><b>赠送域名<i>*</i></b>
            http://<asp:TextBox ID="tb_domain" runat="server" Width="93px" CssClass="gzh-input" onKeyDown="siteurltousername();"
                                            onKeyUp="siteurltousername();" OnTextChanged="tb_domain_TextChanged"></asp:TextBox><strong>.<%=Domain %></strong>&nbsp;
                                	                                <asp:Button runat="server" ID="bt_check" OnClick="LinkButton1_Click" Text=" 域名验证 " CssClass="bt" />
                                	                                <br /><br />
                                        通过上面域名访问您的手机站点，后台还可以帮定自己的域名。
                                	                                
          </p>
          <p><b>网站名称<i>*</i></b>
            <asp:TextBox ID="tb_sitename" runat="server"  CssClass="gzh-input" ></asp:TextBox>&nbsp;
          </p>
          <p><b>网站类别<i>*</i></b>
            <asp:DropDownList ID="DropDownList1" runat="server">
                                        </asp:DropDownList>
          </p>
          <p><b>用户名<i>*</i></b>
            <asp:TextBox ID="tb_username" runat="server"   ReadOnly="True" CssClass="gzh-input" ></asp:TextBox>&nbsp;
                                     
          </p>
          <p><b>个性昵称<i>*</i></b>
             <asp:TextBox ID="tb_nickname" runat="server" CssClass="gzh-input" ></asp:TextBox>&nbsp;
          </p>
          <p><b>登录密码<i>*</i></b>
           <asp:TextBox ID="tb_passwd" runat="server" CssClass="gzh-input"  TextMode="Password"></asp:TextBox>&nbsp; 英文+数字
									  									  									  									  									  									  									  									  									  									  									  									  									  									  									  									  									  									  									  									      
                                            <asp:TextBox ID="tb_hidepasswd" runat="server" Visible="False"></asp:TextBox>
                                            
          </p>
          <p><b>确认密码<i>*</i></b>
             <asp:TextBox ID="tb_repw" runat="server" CssClass="gzh-input"  TextMode="Password"></asp:TextBox> 
                                    
                                    <asp:TextBox ID="tb_hiderepw" runat="server" Visible="False"></asp:TextBox>
          </p>
          <p><b>手机号码<i>*</i></b>
           <asp:TextBox ID="tb_tel" runat="server"  CssClass="gzh-input" ></asp:TextBox>  
          </p>
          
         
          <p><b>电子邮箱<i>*</i></b>
             <asp:TextBox ID="tb_email" runat="server" CssClass="gzh-input"  EnableViewState="False"></asp:TextBox>&nbsp;可取回密码
          </p>
          <p><b>验证码<i>*</i></b>
           <input class="gzh-input" onfocus="this.select()" maxlength="4" size="4" name="tb_checkcode"
                                            id="tb_checkcode" runat="server">
            <span class="yanzm"><img src="<%=strCreatImage %>"></span> </p>
          <p><b>使用协议<i>*</i></b>
           <asp:CheckBox ID="CheckBox1" runat="server" Checked="True" Text="">
                                            </asp:CheckBox><a href="Contract.htm" target="_blank">我已仔细阅读并同意本站免费WAP建站服务使用协议。</a></p>
                                	 </p>
          <div class="form">
          
            <asp:Button ID="bt_ok" runat="server" CssClass="btn btn-gzh"  Text=" 确 定 注 册 " OnClick="bt_ok_Click" OnClientClick="document.all['bt_ok'].style.display= 'none';" >
                                        </asp:Button>  
          </div>
        </form>
      </div>  
     </div> 
</body>
</html>