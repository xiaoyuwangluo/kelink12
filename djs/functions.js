// JavaScript Document
function showWithHide(s,h){
	$(s).show();
	$(h).hide();
}

function showAll(s1,s2){
	$(s1).show();
	$(s2).show();
}

function showHaoMaAndShengXiao(){
	checkOnlyOne(0,'haoMaOrShengXiao');
	showAll(".hm",".sx")
}

function showShengXiao(){
	checkOnlyOne(1,'haoMaOrShengXiao');
	showWithHide(".sx",".hm");
}

function showHaoMa(){
	checkOnlyOne(2,'haoMaOrShengXiao');
	showWithHide(".hm",".sx");
}

function checkOnlyOne(position,checkName){
	$("input[type='checkbox'][name='"+checkName+"']").each(function(index, element) {
		var checked = false;
		//alert(position==index);
		if(position==index){	
			checked = true;
		}
		
       	$(element).attr("checked",checked);
		
    });
}
var clicks = new Array();
function initClicks(){	
	bindClicks(".hm");
	bindClicks(".sx");
}

function bindClicks(c){
	$(c).click(function(){
		var obj = $(this).parent().find(".hm");
		var id = obj.attr("id");
		var index = clicks[id];
		if(isNaN(index))index = 1;
		index++;
//alert(index);		
		switch(index){
			case 1 :
				obj.show();
				obj.parent().find("#"+id+"x").show();
				break;
			case 2 :
				obj.show();
				obj.parent().find("#"+id+"x").hide();
				break;
			case 3 :
				obj.hide();
				obj.parent().find("#"+id+"x").show();
				break;
		}
		clicks[id] = index%3;
	});
}

function copyCode(id) {
    var testCode = $("#"+id).val();//document.getElementById(id).innerHTML;
//alert(testCode);    
    if (copy_code(testCode) != false) {
    	//alert("复制成功，你可以使用Ctrl+V 贴到需要的地方去了哦！  ");
    }
}
function copy_code(copyText){
	if (window.clipboardData){
		window.clipboardData.setData("Text", copyText);
		alert("复制成功，你可以使用Ctrl+V 贴到需要的地方去了哦！  ");
	}else {
		
		//alert("不支持");
		/*
		var flashcopier = 'flashcopier';
		if(!document.getElementById(flashcopier)){
			var divholder = document.createElement('div');
			divholder.id = flashcopier;
			document.body.appendChild(divholder);
		}
		document.getElementById(flashcopier).innerHTML = '';
		var divinfo = '<embed src="_clipboard.swf" FlashVars="clipboard='+encodeURIComponent(copyText)+'" width="0" height="0" type="application/x-shockwave-flash"></embed>';
		document.getElementById(flashcopier).innerHTML = divinfo;
		*/
	}
}