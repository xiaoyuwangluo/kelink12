
<%@ Page Language="C#" AutoEventWireup="true" CodeBehind="admin_WAPadd.aspx.cs" Inherits="KeLin.WebSite.shop.admin_WAPadd" %><%@ Import namespace="KeLin.ClassManager.Tool" %>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>添加商品</title>
<!-- Bootstrap -->
<link href="/NetCSS/admin/bootstrap-3.3.5/css/bootstrap.min.css" rel="stylesheet" media="screen" type="text/css">
<link href="/NetCSS/admin/summernote/summernote.css" rel="stylesheet" type="text/css">
<link href="/NetCSS/admin/summernote/summernote-bs3.css" rel="stylesheet" type="text/css">
<link href="/NetCSS/admin/summernote/font-awesome.min.css" rel="stylesheet" type="text/css">
<style type="text/css">
.slide {
	padding: 0;
}
.slide li {
	position: relative;
	list-style: none;
}
.slide li .del {
	position: absolute;
	top: -10px;
	right: 5px;
	z-index: 10;
	width: 20px;
	height: 20px;
	background: #fff;
	border-radius: 50%;
	box-shadow: 0 0 3px #ccc;
}
.slide li .thumbnail img {
	width: 100%;
/*	height: 240px;*/
}
.slide li .add {
	height: 240px;
	padding-top: 70px;
}
.slide li .add img {
	display: block;
	width: 90px;
	height: 80px;
	margin: 0 auto;
}
#box-value span {
	margin: 0 5px;
}
.tab-content textarea {
	display: none;
}
</style>
</head>
<body style="padding-top: 40px;">
<div class="container">
  <form action="/shop/admin_WAPadd.aspx" enctype="multipart/form-data" method="post" onsubmit="return validateForm(this)">
    <fieldset>
    
      <!--选择上传的栏目分类-->
      <div class="row">
        <div class="col-md-12">
          <legend>选择添加的栏目</legend>
        </div>
        <div class="col-md-12">
          <select class="form-control" name="toclassid">
          <option value="<% Response.Write(this.classid );%>"><% Response.Write(this.classid );%></option>
          <option value="0">0_默认</option>
          <%
			for (int i = 0; (classList != null && i < classList.Count); i++)
			{
		
				Response.Write("<option value=\"" + classList[i].classid + "\">" + classList[i].classid + "_" + classList[i].classname + "</option>");
		
			}

		  %>
          </select>
        </div>
      </div>
      <div class="form-group"></div>
      <!--基本信息配置-->
      <div class="row">
        <div class="col-md-12">
          <legend>基本信息</legend>
        </div>
        <div class="col-md-4">
          <label for="exampleInputPassword1">商品名称</label>
          <input class="form-control not-null" type="text" name="book_title" placeholder="">
        </div>
        <div class="col-md-4">
          <label for="exampleInputPassword1">一句广告语</label>
          <input class="form-control not-null" type="text" name="book_tip" placeholder="">
        </div>
        <div class="col-md-2">
          <label for="exampleInputPassword1">推荐指数</label>
          <select class="form-control" name="book_xingxing">
            <option value="★★★★★">★★★★★</option>
            <option value="★★★★">★★★★</option>
            <option value="★★★">★★★</option>
            <option value="★★">★★</option>
            <option value="★">★</option>
          </select>
        </div>
        <div class="col-md-2">
          <label for="exampleInputPassword1">是否免运费</label>
          <select class="form-control" name="book_charge">
            <option>是</option>
            <option>否</option>
          </select>
        </div>
      </div>
      <div class="form-group"></div>
      <div class="row">
        <div class="col-md-2">
          <label for="exampleInputPassword1">市场价格</label>
          <input class="form-control not-null" type="text" name="book_jiage" placeholder="">
        </div>
        <div class="col-md-2">
          <label for="exampleInputPassword1">会员价格</label>
          <input class="form-control not-null" type="text" name="book_yhjiage" placeholder="">
        </div>
        <div class="col-md-2">
          <label for="exampleInputPassword1">库存</label>
          <input class="form-control not-null" type="text" name="book_hottel" placeholder="">
        </div>
        <div class="col-md-2">
          <label for="exampleInputPassword1">送金币</label>
          <input class="form-control not-null" type="text" name="book_shortmessage" value="0" placeholder="">
        </div>
        <div class="col-md-2">
          <label for="exampleInputPassword1">人气</label>
          <input class="form-control not-null" type="text" name="book_click" value="0" placeholder="">
        </div>
        <div class="col-md-2">
          <label for="exampleInputPassword1">已售</label>
          <input class="form-control not-null" type="text" name="saleCount" value="0" placeholder="">
        </div>
      </div>
      <div class="form-group"></div>
      <div class="row">
        <div class="col-md-12">
          <legend>商品属性</legend>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <div class="row">
            <div class="col-md-2">
              <input class="form-control" id="type-value-input" type="text" placeholder="">
              <input type="hidden" name="book_xinghao" value="">
            </div>
            <div class="col-md-2">
              <button class="btn btn-default" type="button" onClick="addValue()">添加</button>
            </div>
          </div>
          <div class="row">
            <div class="form-group"></div>
            <div class="col-md-12" id="box-value"> </div>
          </div>
        </div>
      </div>
      <div class="form-group"></div>
      <div class="row">
        <div class="col-md-12">
          <legend>颜色分类</legend>
        </div>
        <div class="col-md-12">
          <table class="table table-bordered" id="box-color">
            <tr>
              <th> 自定义编码 </th>
              <th> 分类 </th>
              <th> 图片地址 </th>
              <th> 市场价 </th>
              <th> 会员价 </th>
              <th> 库存数 </th>
              <th> 送币/积分 </th>
              <th> 操作 </th>
            </tr>
            <tr>
              <td><input type="text" class="form-control not-null" name="customnumber" placeholder=""></td>
              <td><input type="text" class="form-control not-null" name="color" placeholder=""></td>
              <td><input type="text" class="form-control not-null" name="colorimage" placeholder=""></td>
              <td><input type="text" class="form-control not-null" name="colorrmb1" placeholder=""></td>
              <td><input type="text" class="form-control not-null" name="colorrmb2" placeholder=""></td>
              <td><input type="text" class="form-control not-null" name="total" placeholder=""></td>
              <td><input type="text" class="form-control not-null" name="sendmoney" placeholder=""></td>
              <td style=" width:90px; padding-top:18px;"><input type="hidden" name="rank" value="0">
                <i onClick="upMove(this)" class="glyphicon glyphicon-arrow-up" title="向上移动"></i> <i onClick="downMove(this)" class="glyphicon glyphicon-arrow-down" title="向下移动"></i> <i onClick="addColor(this)" class="glyphicon glyphicon-plus" title="添加分类"></i> <i onClick="delColor(this)" class="glyphicon glyphicon-minus" title="删除分类"></i></td>
            </tr>
          </table>
        </div>
      </div>
      
      <!--商品顶部轮播图-->
      <div class="row">
        <div class="col-md-12">
          <legend>轮播图</legend>
        </div>
      </div>
      <ul class="row slide">
        <li class="col-sm-2 col-md-2" id="add-side" onClick="addSide(this)">
          <div class="thumbnail add"><img src="/NetCSS/admin/images/addpic.png" alt="..."> </div>
        </li>
      </ul>
      <div class="row">
        <div class="col-md-12">
          <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"> <a href="#chan-ping-jie-shao" role="tab" data-toggle="tab"> 产品介绍</a> </li>
            <li role="presentation"> <a id="preview" href="#can-shu" role="tab" data-toggle="tab"> 参数</a> </li>
            <li role="presentation"> <a id="preview" href="#shi-pai-tu" role="tab" data-toggle="tab"> 实拍图</a> </li>
            <li role="presentation"> <a id="preview" href="#zi-dong-fa-huo" role="tab" data-toggle="tab"> 自动发货</a> </li>
               <li role="presentation"> <a href=""><font color="red">(下面编辑器需IE9以上或百度浏览器)</font></a></li>
            
          </ul>
          <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="chan-ping-jie-shao" style="padding-top:30px;">
              <div id="chan-ping-jie-shao-box"> </div>
              <textarea name="book_content" rows="20" style="width:100%"></textarea>
            </div>
            <div role="tabpanel" class="tab-pane" id="can-shu" style="padding-top:30px;">
              <div id="can-shu-box"> </div>
              <textarea name="book_gongxiao" rows="20" style="width:100%"></textarea>
              <textarea name="book_tishi" rows="10" style="width:100%"></textarea>
            </div>
            <div role="tabpanel" class="tab-pane" id="shi-pai-tu" style="padding-top:30px;">
              <div id="shi-pai-tu-box"> </div>
              <textarea name="book_content_img"  rows="20" style="width:100%"></textarea>
              <textarea name="book_otherTitle"></textarea>
              <textarea name="book_otherContent"></textarea>
            </div>
            <div role="tabpanel" class="tab-pane" id="zi-dong-fa-huo" style="padding-top:30px;">
              <div class="form-group">
                <label for="exampleInputFile">自动发货内容TXT文件</label>
                <input type="file" name="book_file" id="exampleInputFile">
                <p class="help-block">说明:内容换行区分，GB2312。也可以先不上传TXT,单提交商品信息后修改进去管理TXT上传。</p>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <div class="form-group">
            <input type="hidden" name="action" value="gomod"/>
            <input type="hidden" name="classid" value="<% WapTool.ToWML("[classid]", wmlVo); %>"/>
            <input type="hidden" name="siteid" value="<% WapTool.ToWML("[siteid]", wmlVo); %>"/>
            <input type="hidden" name="num" value="0">
            <input type="hidden" name="sid" value="<% WapTool.ToWML("[sid]", wmlVo); %>">
            <input id="form-post-btn" type="submit" class="btn btn-primary btn-lg btn-block" value="添加商品">
          </div>
        </div>
      </div>
    </fieldset>
  </form>
</div>
<script src="/NetCSS/admin/js/jquery-1.9.1.min.js"></script> 
<script src="/NetCSS/admin/bootstrap-3.3.5/js/bootstrap.min.js"></script> 
<script src="/NetCSS/admin/summernote/summernote.min.js" type="text/javascript" language="javascript"></script> 
<script src="/NetCSS/admin/summernote/summernote-zh-CN.js" type="text/javascript" language="javascript"></script> 
<script type="text/javascript" language="javascript">
var $editorOne = $('#chan-ping-jie-shao-box').summernote({
		height :350,
		lang : 'zh-CN',
		inputId: 'chan-ping-jie-shao-box-input'
	});
var $editorTwo = $('#can-shu-box').summernote({
		height :350,
		lang : 'zh-CN',
		inputId: 'can-shu-box-input'
	});
var $editorThree = $('#shi-pai-tu-box').summernote({
		height :350,
		lang : 'zh-CN',
		inputId: 'shi-pai-tu-box-input'
	});
function UpLoad(id) {
	window.open("/admin/Upload_Photo.aspx?PhotoUrlID="+ id +"&info=文章编辑页面&delpath=", "addpicwindow", "height=180, width=380, toolbar= no, menubar=no, scrollbars=yes, resizable=no, location=no, status=no");
}


function addImgOrFile(src,id){
	var url = 'http://' + window.location.host + '/' + src;

	$('#' + id).val(url);
}

/*添加轮播图图片*/
function addSide (obj){

	var oUl = $(obj).parent();
	var index = $(obj).index();
	var oLi = $('<li class="col-md-2">\
		<input type="file" name="book_file" value="" onchange="change(this)" style="display:none">\
		<input type="hidden" name="book_file_info">\
		<img onclick="delSlide(this)" class="del" src="/NetCSS/admin/images/del.png" alt="删除' + index + '">\
		<div class="thumbnail" onclick="upSlide(this)"> <img src="" alt="请选择图片上传"> </div>\
		</li>');
	

	var oFile = oLi.find('input').eq(0); //获取到创建的图片标签
	
	oFile.click(); //打开窗口选择文件
}
function change (obj){ //选择文件后操作
	var oLi = $(obj).parent();

	if(obj.value == ''){
		oLi.remove();
		return;
	}
	var file = obj.files[0]; 

	if(!/image\/\w+/.test(file.type)){   
			alert("请确保文件为图像类型"); 
			oLi.remove();
			return false; 
	} 
	var reader = new FileReader(); 
	reader.readAsDataURL(file); 
	var _this = obj;

	reader.onload = function(e){ 
		var oImg = oLi.find('.thumbnail').eq(0).find('img').eq(0); //获取到预览图元素
		oImg.attr('src', this.result);
		
		if(!oLi.attr('KLSHOW')){
			$('#add-side').before(oLi);
			oLi.attr('KLSHOW','true')
		}
		
		var oUl = oLi.parent();
		if(oUl.find('li').length >= 7){
			$('#add-side').css({
				display: 'none'
			});
		}
		
	} 
}

/*删除轮播图图片*/
function delSlide(obj){
	var oFu = $(obj).parent();
	var oUl = oFu.parent();
	
	
	var aLi = oUl.find('li');
	
	$(oFu).remove();
	if(aLi.length <= 7){
		aLi.eq(aLi.length-1).css('display', 'block');
	}
}
/*更改轮播图图片*/
function upSlide(obj){
	
	var oLi = $(obj).parent();
	var oFile = oLi.find('input').eq(0);
	oFile.click();
}
/*添加属性*/
function addValue (){
	var value = $('#type-value-input').val();
	if(value == ''){
		return alert('商品属性不能为空！');
	}
	var obj = $('<span onclick="delValue(this)" class="label label-warning">' + value + '</span>');
	
	$('#box-value').append(obj);
	$('#type-value-input').val('');
}
/*删除属性*/
function delValue (obj){
	
	$(obj).remove();
}
/*添加颜色分类*/
function addColor(obj){
	var oTable = $(obj).parent().parent().parent();
	var oTr = $(obj).parent().parent();
	oTr.after(oTr.clone(true));
}
/*删除颜色分类*/
function delColor(obj){
	var oTable = $(obj).parent().parent().parent();
	var oTr = $(obj).parent().parent();
	if(oTable.find('tr').length < 3){
		alert('最少必须留一个分类');
	}else{
		oTr.remove();
	}
}
/*向上移动*/
function upMove(obj){
	var oTable = $(obj).parent().parent().parent();
	var aTr = oTable.find('tr');
	var oTr = $(obj).parent().parent();
	var index = oTr.index();
	var oTopTr = oTable.find('tr').eq(index-1);

	if(oTable.find('tr').length < 3 || index < 2){
		alert('你已经是第一行，无法移动');
	}else{
		oTr.insertBefore(oTopTr);
	}
}
/*向下移动*/
function downMove(obj){
	var oTable = $(obj).parent().parent().parent();
	var aTr = oTable.find('tr');
	var oTr = $(obj).parent().parent();
	var index = oTr.index();
	var oTopTr = oTable.find('tr').eq(index+1);

	if(oTable.find('tr').length < 3 || index == (aTr.length-1)){
		alert('你已经是最后一行，无法移动');
	}else{
		oTr.insertAfter(oTopTr);
	}
}
/*添加商品*/
function validateForm(form){
	var data = {};
	
	data.bookTitle = form.book_title.value; //商品名称
	data.bookTip = form.book_tip.value; //一句广告语
	data.bookXingxing = form.book_xingxing.value; //评分
	data.bookCharge = form.book_charge.value; //是否免运费
	data.bookJiage = form.book_jiage.value; //市场价格
	data.bookYhjiage = form.book_yhjiage.value; //会员价格
	data.bookHottel = form.book_hottel.value; //库存
	data.bookShortmessage = form.book_shortmessage.value; //送金币
	data.bookClick = form.book_click.value; //人气
	data.saleCount = form.saleCount.value; //已售
	data.bookXinghao = (function (){ //商品属性
		var arr = [];
		var str = '';
		$('#box-value').find('span').each(function(index, obj) {
			arr.push(obj.innerHTML);
		});
		str = arr.join('/');
		form.book_xinghao.value = str; //生成对应数据，添加给input表单value

		return str;
	})();
	data.bookContent = (function (){ //产品介绍
		var value = $('#chan-ping-jie-shao-box').code().trim();
		form.book_content.value = value
		return value;
	})();
	data.bookGongxiao = (function (){ //参数
		var value = $('#can-shu-box').code().trim();
		form.book_gongxiao.value = value;
		return value;
	})();
	data.bookContentImg = (function (){ //实拍图
		var value = $('#shi-pai-tu-box').code().trim();
		form.book_content_img.value = value;
		return value;
	})();
	data.bookFile = form.book_file.value;
	
	
	//判断是否为空
	var bNotBull = true;
	$('.not-null').each(function(index, obj) {

		if(obj.value == '' && obj.name != 'colorimage'){
			obj.style.border = '1px solid red';
			bNotBull = false;
		}else{
			obj.style.border = '';
		}
    });
	
	
	if(!bNotBull){
		 alert('红色边框不能为空');
		 $('body,html').animate({scrollTop:0},1000);
	}else if(form.status){
		alert('正在上传中...');
	}else{
		form.status = true;
		$('#form-post-btn').val('正在上传中...');
	}
	return bNotBull; //返回true会提交表单，false则不提交
}
</script>
</body>
</html>