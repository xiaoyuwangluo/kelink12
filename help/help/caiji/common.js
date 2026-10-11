/*
****************
* time 2015年8月13日
* var 狼族小狈
* 企业QQ：800007757
* 官方网址：http://kelink.com
****************
*/
$(function (){
	var sHtml = '';
	var url = window.location.href;

	if(url.indexOf('index.htm') > -1){
		$('#header-nav').append('<h2><span>' + document.title + '</span></h2><ul></ul>')
	}else{
		$('#header-nav').append('<h2><span><a href="../index.htm">首页</a></span>&gt;&gt;<span>' + document.title + '</span></h2><ul></ul>')
	}
	
	$('#main .var-type h2').each(function (i,obj){
		$('#main .var-type').eq(i).attr('id', 'var-type-' + i);
		$('#header-nav ul').append('<li><a href="#var-type-' + i + '">' + obj.innerHTML + '</a></li>');
	});
});