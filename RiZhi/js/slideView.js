//function slideView(id){
//	var obj = document.getElementById(id);
//	var oLi = obj.getElementsByTagName('li');
//	for(var i=0;i<oLi.length;i++){
//		oLi[i].index = i;
//		oLi[i].onclick = function (){
//			
//			if(document.getElementById('slideView-' + id)){
//				console.log('已经存在');
//			}else{
//				console.log('创建元素');
//				var newObj = document.createElement('div');
//				newObj.id = 'new-' + id;
//				newObj.className = 'slideView';
//				newObj.innerHTML = '<div class="slideView-tit"><i class="del" onclick="document.body.removeChild(document.getElementById(\'new-' + id +'\'))"></i></div><div class="focus"><div class="hd"><ul></ul></div><div class="bd">' + obj.innerHTML + '</div></div>';
//				document.body.appendChild(newObj);
//					TouchSlide({ 
//					slideCell:'#' + newObj.id,
//					titCell:".hd ul",
//					mainCell:".bd ul", 
//					effect:"left",
//					pageStateCell : '.pageState',
//					defaultIndex : parseInt(this.index),
//					autoPage:true, //自动分页
//					switchLoad:"_src" //切换加载，真实图片路径为"_src" 
//				});
//
//			}
//
//			return false;
//		}
//	}
//	
//}

function slideView(obj){
	var newObj = document.createElement('div');
	var oSlideView = obj.parentNode.parentNode.parentNode.parentNode;
	var oUl = obj.parentNode.parentNode.parentNode;
	var oLi = oUl.getElementsByTagName('li');
	var iIndex = 0;
	for(var i=0;i<oLi.length;i++){
		if(oLi[i] == obj.parentNode.parentNode){
			iIndex = i;
			break;
		}
	}
	console.log(oSlideView.id);
	newObj.id = 'new-' + oSlideView.id;
	newObj.className = 'slideView';
	newObj.innerHTML = '<div class="slideView-tit"><i class="del" onclick="document.body.removeChild(document.getElementById(\'new-' + oSlideView.id +'\'))"></i></div><div class="focus"><div class="hd"><ul></ul></div><div class="bd">' + oSlideView.innerHTML + '</div></div>';
	document.body.appendChild(newObj);
		TouchSlide({ 
		slideCell: 'new-' + oSlideView.id,
		titCell:".hd ul",
		mainCell:".bd ul", 
		effect:"left",
		pageStateCell : '.pageState',
		defaultIndex : iIndex,
		autoPage:true, //自动分页
		switchLoad:"_src" //切换加载，真实图片路径为"_src" 
	});
return false;
	
}