 if(!String.prototype.trim){ 
	String.prototype.trim = function(){ 
		return this.replace(/^\s+|\s+$/g, '').replace(/[ ]/g,''); 
	} ;
}
  var ball_red=new Array(1,2,7,8,12,13,18,19,23,24,29,30,34,35,40,45,46);
  var ball_blue=new Array(3,4,9,10,14,15,20,25,26,31,36,37,41,42,47,48);
  var ball_green=new Array(5,6,11,16,17,21,22,27,28,32,33,38,39,43,44,49);
  var ball_xiao=new Array();
  ball_xiao[0]=new Array("鸡","猴","羊","马","蛇","龙","兔","虎","牛","鼠","猪","狗");
  ball_xiao[1]=new Array(1,13,25,37,49);
  ball_xiao[2]=new Array(2,14,26,38);
  ball_xiao[3]=new Array(3,15,27,39);
  ball_xiao[4]=new Array(4,16,28,40);
  ball_xiao[5]=new Array(5,17,29,41);
  ball_xiao[6]=new Array(6,18,30,42);
  ball_xiao[7]=new Array(7,19,31,43);
  ball_xiao[8]=new Array(8,20,32,44);
  ball_xiao[9]=new Array(9,21,33,45);
  ball_xiao[10]=new Array(10,22,34,46);
  ball_xiao[11]=new Array(11,23,35,47);
  ball_xiao[12]=new Array(12,24,36,48);
  var old_scores=new Array();
  var new_scores=new Array();
  var get_score_ok=0;
  var get_score_error=0;
  var update_run=true;
  var ov='';


var _$=["/report.xml","\x47\x45\x54","\x68\x74\x6d\x6c","\x73\x3d","\x67\x65\x74\x5f\x78\x6d\x6c\x28\x29\x3b","\x33\x30\x30\x30\x30\x30\x30","\x23\x74\x6d\x70\x69\x6e\x66\x6f","\x23\x77\x61\x69\x74\x5f\x74\x65\x78\x74","\u66f4\u65b0\u6b63\u5e38","\x23\x74\x6d\x70\x69\x6e\x66\x6f","\x2c"];function get_xml(){$.ajax({url:_$[0],type:_$[1],dataType:_$[2],data:_$[3]+Math.random()*0x2710,beforeSend:function(xbv6dd){},complete:function(xbv5fc){setTimeout(_$[4],0x7d1)},timeout:_$[5],error:function(){window.location.reload()},success:function(xbvb3b){$(_$[6]).html(xbvb3b);$(_$[7]).html(_$[8]);new_scores=$(_$[9]).text().trim().split(_$[10]);if(new_scores.length>0x0){show_scores();show_openNumsDateTime();setShareInfo()}}})}




function show_openNumsDateTime(){
	$("#nextQiShu").html(new_scores[8]);
	$("#nextMonth").html(new_scores[9]);
	$("#nextDay").html(new_scores[10]);
	$("#nextWeek").html(new_scores[11]);
	$("#nextTime").html(new_scores[12]);
}

function show_scores(){
   if (new_scores!=old_scores){
      old_scores=new_scores;
	  var kyear='2016';
	  var kinfo=$.trim(new_scores[8]);
	  var kinfoadd=$.trim(new_scores[9]);
	  var kqi=$.trim(new_scores[0]);
	  var b1=$.trim(new_scores[1]);
	  var b2=$.trim(new_scores[2]);
	  var b3=$.trim(new_scores[3]);
	  var b4=$.trim(new_scores[4]);
	  var b5=$.trim(new_scores[5]);
	  var b6=$.trim(new_scores[6]);
	  var b7=$.trim(new_scores[7]);
	  if ($("#q").text()!=kqi){$("#q").text(kqi);}
	  if (kinfoadd!=""){if ($("#kinfo").text()!=kinfo){$("#kinfo").text(kinfo);}}
	  if (kinfoadd!=""){if ($("#kinfoadd").text()!=kinfoadd){$("#kinfoadd").text(kinfoadd);}}
	  if ($("#w1").text()!=kqi){$("#m1").text(b1);css_scores("w1","m1x",b1);}
	  if ($("#w2").text()!=kqi){$("#m2").text(b2);css_scores("w2","m2x",b2);}
	  if ($("#w3").text()!=kqi){$("#m3").text(b3);css_scores("w3","m3x",b3);}
	  if ($("#w4").text()!=kqi){$("#m4").text(b4);css_scores("w4","m4x",b4);}
	  if ($("#w5").text()!=kqi){$("#m5").text(b5);css_scores("w5","m5x",b5);}
	  if ($("#w6").text()!=kqi){$("#m6").text(b6);css_scores("w6","m6x",b6);}
	  if ($("#w7").text()!=kqi){$("#s1").text(b7);css_scores("w7","s1x",b7);}
   }
}

function css_scores(vb,vx,t){
  var v=0;
  if (in_array(parseInt(t,10), ball_red)){$("#"+vb).addClass("m0").css("background-color","#FF0000").css("color","#FFFFFF");v=1;}
  if (in_array(parseInt(t,10), ball_blue)){$("#"+vb).addClass("m0").css("background-color","#0000FF").css("color","#FFFFFF");v=1;}
  if (in_array(parseInt(t,10), ball_green)){$("#"+vb).addClass("m0").css("background-color","#009900").css("color","#FFFFFF");v=1;}
  if (v==0){
     $("#"+vb).css("background-color","#999999").css("color","#000000");
	 $("#"+vx).text("");
  }
  for (var i=1;i<=12;i++){
    if (in_array(parseInt(t,10), ball_xiao[i])){$("#"+vx).text(ball_xiao[0][i-1]);}
  }
}

function in_array(needle, haystack) {
 type = typeof needle;
 if(type == "string" || type =="number") {
  for(var i in haystack) {
   if(haystack[i] == needle) {
     return true;
   }
  }
 }
 return false;
}

function changesx(dm,dx){
var s=$("#"+dm).css("display");
if (s=="block"){$("#"+dx).show();$("#"+dm).hide();
}else{$("#"+dm).show();$("#"+dx).hide();}
}
function setShareInfo(){
	var t = $("#s1").text();
	var shareInfo = $("#q").text()+"期搅珠结果："
					+$("#m1").text()+"["+$("#m1x").text()+"]-"
					+$("#m2").text()+"["+$("#m2x").text()+"]-"
					+$("#m3").text()+"["+$("#m3x").text()+"]-"
					+$("#m4").text()+"["+$("#m4x").text()+"]-"
					+$("#m5").text()+"["+$("#m5x").text()+"]-"
					+$("#m6").text()+"["+$("#m6x").text()+"]特："
					+t+"["+$("#s1x").text()+"]";
	var color = "";
	if (in_array(parseInt(t,10), ball_red)){
		color="红";
	}else if (in_array(parseInt(t,10), ball_blue)){
		color="蓝";
	}else if (in_array(parseInt(t,10), ball_green)){
		color="绿";
	}
	shareInfo = shareInfo+"["+color+"]";
	$("#shareInfo").val(shareInfo);
}
$(document).ready(function(){get_xml();});