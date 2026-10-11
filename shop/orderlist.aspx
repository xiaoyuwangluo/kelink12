<%@ Page Language="C#" AutoEventWireup="true" Codebehind="orderlist.aspx.cs" Inherits="KeLin.WebSite.shop.orderlist" %>
<%@ Register Assembly="AspNetPager" Namespace="Wuqi.Webdiyer" TagPrefix="webdiyer" %>
<%
    Response.Buffer = true;
    Response.Expires = -1;
    Response.ExpiresAbsolute = DateTime.Now.AddDays(-1);
    Response.Expires = 0;
    Response.CacheControl = "no-cache"; 
 %>