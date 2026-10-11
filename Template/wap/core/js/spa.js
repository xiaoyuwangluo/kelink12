 


/*--------------------------------------------------
gettab

--------------------------------------------------*/
$(document).on('click', '.gettab', function() {
	loading();
	var obj = $(this);
	var tabid= obj.attr('tab');
	var geturl= obj.attr('href');
  var top= obj.attr('top')?obj.attr('top'):'original';
	var his= obj.attr('his')?obj.attr('his'):'yes';
	var ut= null;
	
	var hs= hash(geturl);
  if($('.'+hs).length>0){
		if($('.'+hs).hasClass("body_main")==false){
	    var upvar=sessionStorage.getItem(tabid);
	    if(upvar){
				if(his!='yes'){
					setaction("showtab('"+tabid+"')");
				}
				
				var upvar = JSON.parse(upvar);
				$('.headerarea').html(upvar.hd);
				$('.footerarea').html(upvar.ft);
		
				$('.body_main').addClass('main_hide').removeClass('body_main cut_in');
				$('.'+hs).addClass('body_main cut_in').removeClass('main_hide');
				popup.close();
				return false;				
			}else{
				var gonext=1;
			}
		}
	}else{
		var gonext=1;
	}
	if(gonext==1){
		if(tabid==''){
			window.location.href = geturl;
			return false;
		}
		if($('#'+tabid).length){
			var gettype= 'update';
		}else{
			var gettype= 'create';
		}
		var value = {
			url: geturl,
			type: gettype,
			tabid: tabid,
			top: top,
			his: his,
			html: ''
		};
		loadtab('ajax',value);
		return false;		
	}
});

function showtab(tabid){
	unsetaction();
	if($('#'+tabid).length>0){
		var upvar=sessionStorage.getItem(tabid);
		if(upvar){
			$('.headerarea').html(upvar.hd);
			$('.footerarea').html(upvar.ft);
	
			$('.body_main').addClass('main_hide').removeClass('body_main cut_in');
			$('.'+hs).addClass('body_main cut_in').removeClass('main_hide');
			return false;
		}
	}
}

/*loadtab*/
function loadtab(readtype,value){
	unsetaction();
	if(readtype=='ajax'){
			
		if(value.url.indexOf('?')>0){
			var geturl= value.url + '&getpage=1';
		}else{
			window.location.href = value.url;
			return false;
		}
		
		var tabid=value.tabid;		
		var type=value.type;
		var top=value.top;
    var upid = $('.body_main').attr('form');
		$.ajax({
			type : 'GET',
			url : geturl,
			dataType : 'html'
		})

		.success(function(s) {
			if(!s){
				setaction("closepop()");
				popup.open('目标网页存在错误','alert');
				return false;
			}
			
			if(s.indexOf('<div class="body_main')==-1){
				window.location.href = value.url;
				return false;
			}	
			
			if(!$(s).find('#'+tabid)){
				window.location.href = value.url;
				return false;
			}
			if(value.his=='no'){
				setaction("closetab('"+tabid+"');");
			}else{
				unsetaction();
				var state = {
					url: value.url,
					tabid: tabid,
					html: s
				};
				history.pushState(state,null,value.url);
			}			
			creattab(s,tabid,type,value.url,top,value.his);
		})
		.error(function() {
			window.location.href = value.url;
			popup.close();
			return false;
		});
	}else{
		var tabid=value.tabid;
		var s=value.html;
		creattab(s,tabid,'destroy');
	}
}

/*creattab*/
function creattab(s,tabid,type,url,top,his){
	$('html').removeClass('closeside');
	unsetaction();

	var upid = $('.body_main').attr('id');

	var title = $(s).find('.title_name').text();
	var css = $(s).find('.resetcolor').text();

	var header = $(s).find('.headerarea').html();
	var mainarea = $(s).find('.mainarea').html();
	var main = $(s).find('.body_main').html();
	var footer = $(s).find('.footerarea').html();
  if(url){
		var hs=hash(url);
	}
	
	sessionStorage.setItem(tabid, JSON.stringify({hd:header,ft:footer,url:url}));
	$('.headerarea').html(header);
	$('.footerarea').html(footer);
	
	if(andriod){
		document.title = title;
	}else{
		var $body = $('body');
		document.title = title;
		var $iframe = $('<iframe src="favicon.ico"></iframe>');
		$iframe.on('load',function() {
			setTimeout(function() {
				$iframe.off('load').remove();
			}, 0);
		}).appendTo($body);		
	}

	$('#resetcolor').text(css);
	
	if(type=='update'){
		var thisclass = $(s).find('#'+tabid).attr('class');
		var thisscroll = $(s).find('#'+tabid).attr('onscroll')?$(s).find('#'+tabid).attr('onscroll'):'';
		

		
		$('#'+tabid).attr('onscroll',thisscroll);
		$('#'+tabid).html(main);
		  
		if(upid!=tabid){
			$('#'+tabid).attr('form',upid);
		}
		$('.body_main').addClass('main_hide').removeClass('body_main cut_in');
		$('#'+tabid).addClass('body_main cut_in '+thisclass).removeClass('main_hide');			
		if(hs && his=='yes'){
			$('#'+tabid).addClass(hs);
		}
    if(top=='top'){
			$('.body_main').animate({scrollTop:0}, 400);
		}
	}else if(type=='create'){

		$('.body_main').addClass('main_hide').removeClass('body_main cut_in');
		$('.mainarea').append(mainarea);
		$('.body_main').addClass('cut_in');
		$('#'+tabid).attr('form',upid);
		if(hs && his=='yes'){
			$('#'+tabid).addClass(hs);
		}
	}else if(type=='destroy'){
		if($('#'+tabid).length){
			if(upid!=tabid){
				$('#'+upid).remove();
				$('#'+tabid).removeClass('main_hide').addClass('body_main cut_in');
			}else{
				var thisclass = $(s).find('#'+tabid).attr('class');
				var thisscroll = $(s).find('#'+tabid).attr('onscroll')?$(s).find('#'+tabid).attr('onscroll'):'';
				$('#'+tabid).attr('onscroll',thisscroll);
				$('#'+tabid).addClass(thisclass);
				$('#'+tabid).html(main);
			}
		}else{
			$('.body_main').addClass('main_hide').removeClass('body_main cut_in');
			$('.mainarea').append(mainarea);
			$('.body_main').addClass('cut_in');
		}
	}
  $(".body_main .si").scrollLoading();
	scrollmain();
	popup.close();
	if($(s).find('#'+tabid).attr('class')){
		evalscript(s);
		evalcss(s);
	}
}

function destab(upid){
	$('#'+upid).remove();
	$('.body_main').removeClass('cut_side').css({'position':'', 'z-index':''});
}
function closetab(id){
	unsetaction();
  var tabid=$('#'+id).attr('form');
	var upvar=sessionStorage.getItem(tabid);
	
	if(upvar){
		var upvar = JSON.parse(upvar);
		$('.headerarea').html(upvar.hd);
		$('.footerarea').html(upvar.ft);
		$('#'+id).remove();
		$('#'+tabid).addClass('body_main').removeClass('main_hide');		
	}else{
		history.back(-1);
	}
}

/*--------------------------------------------------
getpage

--------------------------------------------------*/
$(document).on('click', '.getpage', function() {
	
	loading();
	var obj = $(this);
	var gettype= obj.attr('gettype');
	var getid= obj.attr('getid');
	var geturl= obj.attr('href');
		
	
	
	var value = {
		url: geturl,
		type: gettype,
		id: getid,
		html: ''
	};
	loadpage('ajax',value);
	popup.close();
	return false;

});

/*loadpage*/
function loadpage(readtype,value){
	
	if(value.url.indexOf('?')>0){
		var geturl= value.url + '&getpage=1';
	}else{
		var geturl= value.url + '?getpage=1';
	}
	var gettype=value.type;
	var getid=value.id;			
	if(readtype=='ajax'){
		
		$.ajax({
			type : 'GET',
			url : geturl,
			dataType : 'html'
		})
		.success(function(s) {
			var state = {
				geturl:value.url,
				gettype: gettype,
				getid: getid,
				html: s
			};
			history.pushState(state,null,value.url);			
	    creatpage(s,gettype,getid);
		})
		.error(function() {
			window.location.href = geturl;
		});
	}else if(readtype=='history'){
		var s=value.html;
		creatpage(s,gettype,getid);
	}
}

/*creatpage*/
function creatpage(s,gettype,getid){
	$('html').removeClass('closeside');
	if(gettype=='one'){
		var content = $(s).find('.'+getid).html();
		$('.'+getid).html(content);					
	}else if(gettype=='dispersed'){
		
		var header = $(s).find('.headerarea').html();
		var main = $(s).find('.mainarea').html();
		var footer = $(s).find('.footerarea').html();
		
		$('.headerarea').html(header);
		$('.mainarea').html(main);
		
		$('.footerarea').html(footer);
	}else{
		$('div.wrap').html($(s).find('.wrap').html());
	}
}


		
/*--------------------------------------------------
backbtn

--------------------------------------------------*/
$(window).on("load", function(event) {
	var tabid=$('.body_main').attr('id');

	var header = $('.headerarea').html();
	var footer = $('.footerarea').html();
	sessionStorage.setItem(tabid, JSON.stringify({hd:header,ft:footer,url:window.location.href}));
	
	var state = {
		url: window.location.href,
		tabid: tabid,
		html: $('body').html(),
	};
	history.replaceState(state,null,window.location.href);
});

$(window).on("popstate", function(event) {
	var state = event.originalEvent.state;
	if(!state){
	}else{
		var tabid='';
		var upvar={};
		if(lastaction){
			var tabid=$('.body_main').attr('id');
			if(tabid){
				var upvar=sessionStorage.getItem(tabid);
				if(upvar){
					var upvar = JSON.parse(upvar);
					var state = {
						url: upvar.url,
						tabid: tabid,
						html: $('body').html(),
					};
					history.pushState(state,null,upvar.url);					
				}
			}			
			eval(lastaction);
		}else{
			if(state.html){
				if(state.tabid){
					loadtab('history',state);
				}else{
					getpage('history',state);
				}
			}else{
				goto(state.url,state.tabid);
			}			
		}
	}
});