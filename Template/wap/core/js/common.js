var supporttouch = "ontouchend" in document;
//!supporttouch && (window.location.href = 'forum.php?mobile=1');

var xmlHttpRequest;
$(function(){
  if(window.XMLHttpRequest){
    xmlHttpRequest=new XMLHttpRequest();
  }else{
    xmlHttpRequest=new ActiveXObject("Microsoft.XMLHTTP");
  }
  xmlHttpRequest.open("GET","AjaxServlet",true);
});
	
var platform = navigator.platform;
var ua = navigator.userAgent;
var ios = /iPhone|iPad|iPod/.test(platform) && ua.indexOf( "AppleWebKit" ) > -1;
var andriod = ua.indexOf( "Android" ) > -1;
var lastaction='';


(function($, window, document, undefined) {
	var dataPropertyName = "virtualMouseBindings",
		touchTargetPropertyName = "virtualTouchID",
		virtualEventNames = "vmouseover vmousedown vmousemove vmouseup vclick vmouseout vmousecancel".split(" "),
		touchEventProps = "clientX clientY pageX pageY screenX screenY".split( " " ),
		mouseHookProps = $.event.mouseHooks ? $.event.mouseHooks.props : [],
		mouseEventProps = $.event.props.concat( mouseHookProps ),
		activeDocHandlers = {},
		resetTimerID = 0,
		startX = 0,
		startY = 0,
		didScroll = false,
		clickBlockList = [],
		blockMouseTriggers = false,
		blockTouchTriggers = false,
		eventCaptureSupported = "addEventListener" in document,
		$document = $(document),
		nextTouchID = 1,
		lastTouchID = 0, threshold;
	$.vmouse = {
		moveDistanceThreshold: 10,
		clickDistanceThreshold: 10,
		resetTimerDuration: 1500
	};
	function getNativeEvent(event) {
		while( event && typeof event.originalEvent !== "undefined" ) {
			event = event.originalEvent;
		}
		return event;
	}
	function createVirtualEvent(event, eventType) {
		var t = event.type, oe, props, ne, prop, ct, touch, i, j, len;
		event = $.Event(event);
		event.type = eventType;
		oe = event.originalEvent;
		props = $.event.props;
		if(t.search(/^(mouse|click)/) > -1 ) {
			props = mouseEventProps;
		}
		if(oe) {
			for(i = props.length, prop; i;) {
				prop = props[ --i ];
				event[ prop ] = oe[ prop ];
			}
		}
		if(t.search(/mouse(down|up)|click/) > -1 && !event.which) {
			event.which = 1;
		}
		if(t.search(/^touch/) !== -1) {
			ne = getNativeEvent(oe);
			t = ne.touches;
			ct = ne.changedTouches;
			touch = (t && t.length) ? t[0] : (( ct && ct.length) ? ct[0] : undefined);
			if(touch) {
				for(j = 0, len = touchEventProps.length; j < len; j++) {
					prop = touchEventProps[j];
					event[prop] = touch[prop];
				}
			}
		}
		return event;
	}
	function getVirtualBindingFlags(element) {
		var flags = {},
			b, k;
		while(element) {
			b = $.data(element, dataPropertyName);
			for(k in b) {
				if(b[k]) {
					flags[k] = flags.hasVirtualBinding = true;
				}
			}
			element = element.parentNode;
		}
		return flags;
	}
	function getClosestElementWithVirtualBinding(element, eventType) {
		var b;
		while(element) {
			b = $.data( element, dataPropertyName );
			if(b && (!eventType || b[eventType])) {
				return element;
			}
			element = element.parentNode;
		}
		return null;
	}
	function enableTouchBindings() {
		blockTouchTriggers = false;
	}
	function disableTouchBindings() {
		blockTouchTriggers = true;
	}
	function enableMouseBindings() {
		lastTouchID = 0;
		clickBlockList.length = 0;
		blockMouseTriggers = false;
		disableTouchBindings();
	}
	function disableMouseBindings() {
		enableTouchBindings();
	}
	function startResetTimer() {
		clearResetTimer();
		resetTimerID = setTimeout(function() {
			resetTimerID = 0;
			enableMouseBindings();
		}, $.vmouse.resetTimerDuration);
	}
	function clearResetTimer() {
		if(resetTimerID ) {
			clearTimeout(resetTimerID);
			resetTimerID = 0;
		}
	}
	function triggerVirtualEvent(eventType, event, flags) {
		var ve;
		if((flags && flags[eventType]) ||
					(!flags && getClosestElementWithVirtualBinding(event.target, eventType))) {
			ve = createVirtualEvent(event, eventType);
			$(event.target).trigger(ve);
		}
		return ve;
	}
	function mouseEventCallback(event) {
		var touchID = $.data(event.target, touchTargetPropertyName);
		if(!blockMouseTriggers && (!lastTouchID || lastTouchID !== touchID)) {
			var ve = triggerVirtualEvent("v" + event.type, event);
			if(ve) {
				if(ve.isDefaultPrevented()) {
					event.preventDefault();
				}
				if(ve.isPropagationStopped()) {
					event.stopPropagation();
				}
				if(ve.isImmediatePropagationStopped()) {
					event.stopImmediatePropagation();
				}
			}
		}
	}
	function handleTouchStart(event) {
		var touches = getNativeEvent(event).touches,
			target, flags;
		if(touches && touches.length === 1) {
			target = event.target;
			flags = getVirtualBindingFlags(target);
			if(flags.hasVirtualBinding) {
				lastTouchID = nextTouchID++;
				$.data(target, touchTargetPropertyName, lastTouchID);
				clearResetTimer();
				disableMouseBindings();
				didScroll = false;
				var t = getNativeEvent(event).touches[0];
				startX = t.pageX;
				startY = t.pageY;
				triggerVirtualEvent("vmouseover", event, flags);
				triggerVirtualEvent("vmousedown", event, flags);
			}
		}
	}
	function handleScroll(event) {
		if(blockTouchTriggers) {
			return;
		}
		if(!didScroll) {
			triggerVirtualEvent("vmousecancel", event, getVirtualBindingFlags(event.target));
		}
		didScroll = true;
		startResetTimer();
	}
	function handleTouchMove(event) {
		if(blockTouchTriggers) {
			return;
		}
		var t = getNativeEvent(event).touches[0],
			didCancel = didScroll,
			moveThreshold = $.vmouse.moveDistanceThreshold,
			flags = getVirtualBindingFlags(event.target);
			didScroll = didScroll ||
				(Math.abs(t.pageX - startX) > moveThreshold ||
					Math.abs(t.pageY - startY) > moveThreshold);
		if(didScroll && !didCancel) {
			triggerVirtualEvent("vmousecancel", event, flags);
		}
		triggerVirtualEvent("vmousemove", event, flags);
		startResetTimer();
	}
	function handleTouchEnd(event) {
		if(blockTouchTriggers) {
			return;
		}
		disableTouchBindings();
		var flags = getVirtualBindingFlags(event.target), t;
		triggerVirtualEvent("vmouseup", event, flags);
		if(!didScroll) {
			var ve = triggerVirtualEvent("vclick", event, flags);
			if(ve && ve.isDefaultPrevented()) {
				t = getNativeEvent(event).changedTouches[0];
				clickBlockList.push({
					touchID: lastTouchID,
					x: t.clientX,
					y: t.clientY
				});
				blockMouseTriggers = true;
			}
		}
		triggerVirtualEvent("vmouseout", event, flags);
		didScroll = false;
		startResetTimer();
	}
	function hasVirtualBindings(ele) {
		var bindings = $.data( ele, dataPropertyName ), k;
		if(bindings) {
			for(k in bindings) {
				if(bindings[k]) {
					return true;
				}
			}
		}
		return false;
	}
	function dummyMouseHandler() {}

	function getSpecialEventObject(eventType) {
		var realType = eventType.substr(1);
		return {
			setup: function(data, namespace) {
				if(!hasVirtualBindings(this)) {
					$.data(this, dataPropertyName, {});
				}
				var bindings = $.data(this, dataPropertyName);
				bindings[eventType] = true;
				activeDocHandlers[eventType] = (activeDocHandlers[eventType] || 0) + 1;
				if(activeDocHandlers[eventType] === 1) {
					$document.bind(realType, mouseEventCallback);
				}
				$(this).bind(realType, dummyMouseHandler);
				if(eventCaptureSupported) {
					activeDocHandlers["touchstart"] = (activeDocHandlers["touchstart"] || 0) + 1;
					if(activeDocHandlers["touchstart"] === 1) {
						$document.bind("touchstart", handleTouchStart)
							.bind("touchend", handleTouchEnd)
							.bind("touchmove", handleTouchMove)
							.bind("scroll", handleScroll);
					}
				}
			},
			teardown: function(data, namespace) {
				--activeDocHandlers[eventType];
				if(!activeDocHandlers[eventType]) {
					$document.unbind(realType, mouseEventCallback);
				}
				if(eventCaptureSupported) {
					--activeDocHandlers["touchstart"];
					if(!activeDocHandlers["touchstart"]) {
						$document.unbind("touchstart", handleTouchStart)
							.unbind("touchmove", handleTouchMove)
							.unbind("touchend", handleTouchEnd)
							.unbind("scroll", handleScroll);
					}
				}
				var $this = $(this),
					bindings = $.data(this, dataPropertyName);
				if(bindings) {
					bindings[eventType] = false;
				}
				$this.unbind(realType, dummyMouseHandler);
				if(!hasVirtualBindings(this)) {
					$this.removeData(dataPropertyName);
				}
			}
		};
	}
	for(var i = 0; i < virtualEventNames.length; i++) {
		$.event.special[virtualEventNames[i]] = getSpecialEventObject(virtualEventNames[i]);
	}
	if(eventCaptureSupported) {
		document.addEventListener("click", function(e) {
			var cnt = clickBlockList.length,
				target = e.target,
				x, y, ele, i, o, touchID;
			if(cnt) {
				x = e.clientX;
				y = e.clientY;
				threshold = $.vmouse.clickDistanceThreshold;
				ele = target;
				while(ele) {
					for(i = 0; i < cnt; i++) {
						o = clickBlockList[i];
						touchID = 0;
						if((ele === target && Math.abs(o.x - x) < threshold && Math.abs(o.y - y) < threshold) ||
									$.data(ele, touchTargetPropertyName) === o.touchID) {
							e.preventDefault();
							e.stopPropagation();
							return;
						}
					}
					ele = ele.parentNode;
				}
			}
		}, true);
	}
})(jQuery, window, document);

(function($, window, undefined) {
	function triggercustomevent(obj, eventtype, event) {
		var origtype = event.type;
		event.type = eventtype;
		$.event.handle.call(obj, event);
		event.type = origtype;
	}

	$.event.special.tap = {
		setup : function() {
			var thisobj = this;
			var obj = $(thisobj);
			obj.on('vmousedown', function(e) {
				if(e.which && e.which !== 1) {
					return false;
				}
				var origtarget = e.target;
				var origevent = e.originalEvent;
				var timer;

				function cleartaptimer() {
					clearTimeout(timer);
				}
				function cleartaphandlers() {
					cleartaptimer();
					obj.off('vclick', clickhandler)
						.off('vmouseup', cleartaptimer);
					$(document).off('vmousecancel', cleartaphandlers);
				}

				function clickhandler(e) {
					cleartaphandlers();
					if(origtarget === e.target) {
						triggercustomevent(thisobj, 'tap', e);
					}
					return false;
				}

				obj.on('vmouseup', cleartaptimer)
					.on('vclick', clickhandler)
				$(document).on('touchcancel', cleartaphandlers);

				timer = setTimeout(function() {
					triggercustomevent(thisobj, 'taphold', $.Event('taphold', {target:origtarget}));
				}, 750);
				return false;
			});
		}
	};
	$.each(('tap').split(' '), function(index, name) {
		$.fn[name] = function(fn) {
			return this.on(name, fn);
		};
	});

})(jQuery, this);

var atap = {
	init : function() {
		$('.atap').on('tap', function() {
			var obj = $(this);
			obj.css({'background':'#6FACD5', 'color':'#FFFFFF', 'font-weight':'bold', 'text-decoration':'none', 'text-shadow':'0 1px 1px #3373A5'});
			return false;
		});
		$('.atap a').off('click');
	}
};


var POPMENU = new Object;
var popup = {
	init : function() {
		var $this = this;
		$('.popup').each(function(index, obj) {
			obj = $(obj);
			var pop = $(obj.attr('href'));
			if(pop && pop.attr('popup')) {
				pop.css({'display':'none'});
				obj.on('click', function(e) {
					$this.open(pop,'html');
				});
			}
		});
		this.maskinit();
	},
	maskinit : function() {
		var $this = this;
		$('#dark').off().on('click', function() {$this.close();});
		$('#light').off().on('click', function() {$this.close();});
	},

	open : function(pop, type, url) {
		this.close();
		this.maskinit();
		if(typeof pop == 'string') {
			$('#ntcmsg').remove();
			if(type == 'alert') {
				pop = '<div class="tip"><dt class="tc">'+ pop +'</dt><dd class="flex_box bo_t"><a href="javascript:;" class="cc flex" onclick="popup.close();">确定</a></dd></div>';
				var popwidth=window.innerWidth*0.8;
			} else if(type == 'confirm') {
				pop = '<div class="tip"><dt class="tc">'+ pop +'</dt><dd class="flex_box bo_t"><a class="cc flex bo_r" href="'+ url +'">确定</a><a href="javascript:;" class="flex" onclick="popup.close();">取消</a></dd></div>'
				var popwidth=window.innerWidth*0.8;
			} else if(type == 'loading') {
				
			}
			$('body').append('<div id="ntcmsg" style="display:none;">'+ pop +'</div>');
			pop = $('#ntcmsg');
		}
		if(type=='html'){
		  var popwidth=window.innerWidth*0.8;
		}
		if(!popwidth){
			var popwidth=pop.width();
		}
		/*
		if(POPMENU[pop.attr('id')]) {
			$('#' + pop.attr('id') + '_popmenu').html(pop.html()).css({'height':pop.height()+'px', 'width':popwidth+'px'});
		} else {
			pop.parent().append('<div class="dialogbox" id="'+ pop.attr('id') +'_popmenu" style="height:'+ pop.height() +'px;width:'+ popwidth +'px;">'+ pop.html() +'</div>');
		}
		*/
		pop.parent().append('<div class="dialogbox" id="'+ pop.attr('id') +'_popmenu" style="height:'+ pop.height() +'px;width:'+ popwidth +'px;">'+ pop.html() +'</div>');
		var popupobj = $('#' + pop.attr('id') + '_popmenu');
		var left = (window.innerWidth - popwidth) / 2;
		var top = (document.documentElement.clientHeight - popupobj.height()) / 2;
		popupobj.css({'display':'block','position':'fixed','left':left,'top':top,'z-index':1010,'opacity':1});
		if(type == 'loading_comm'){
			$('#loading').css('display', 'block');
		}else if(type == 'loading_page'){
		}else{
			$('#dark').css('display', 'block');
			$('#light').css({'display':'block','height':'50px'});			
		}
		POPMENU[pop.attr('id')] = pop;
	},
	close : function() {
		$('#loading').css('display', 'none');
		$('#mongolia').css('display', 'none');
		$('#dark').css('display', 'none');
		$('#light').css({'display':'none','height':''});
		$.each(POPMENU, function(index, obj) {
			$('#' + index + '_popmenu').remove();
			$('#ntcmsg').remove();
		});
	}
};

var dialog = {
	init : function() {
		$(document).on('click', '.dialog', function() {
			var obj = $(this);
			loading();
			$.ajax({
				type : 'GET',
				url : obj.attr('href') + '&inajax=1',
				dataType : 'xml'
			})

			.success(function(s) {
				popup.open(s.lastChild.firstChild.nodeValue,'html');
				evalscript(s.lastChild.firstChild.nodeValue);
			})
			.error(function() {
				window.location.href = obj.attr('href');
				popup.close();
			});
			return false;
		});
	},

};



var formdialog = {
	init : function() {
		$(document).on('click', '.formdialog', function() {
			loading();
			var obj = $(this);
			var formobj = $(this.form);

			$.ajax({
				type:'POST',
				url:formobj.attr('action') + '&handlekey='+ formobj.attr('id') +'&inajax=1',
				data:formobj.serialize(),
				dataType:'xml'
			})
			.success(function(s) {
				if(s.lastChild.firstChild.nodeValue.indexOf('<div class="tip">')!=-1){
					popup.open(s.lastChild.firstChild.nodeValue,'html');
					evalscript(s.lastChild.firstChild.nodeValue);		
				}else{
					alert(formobj.attr('rel'));
					if(formobj.attr('rel')){
						openpop('<dt class="tc">操作成功</dt><dd class="flex_box bo_t"><a class="cc flex" href="'+ formobj.attr('rel') +'" tab="'+formobj.attr('tab')+'">确定</a></dd>');
					}else{
						popup.open('操作成功', 'alert');
					}
				}
			})
			.error(function() {
				 popup.open('AJAX操作失败', 'alert');
			});
			return false;
		});
	}
};

var redirect = {
	init : function() {
		$(document).on('click', '.redirect', function() {
			var obj = $(this);
			popup.close();
			window.location.href = obj.attr('href');
		});
	}
};

var DISMENU = new Object;
var display = {
	init : function() {
		var $this = this;
		$('.display').each(function(index, obj) {
			obj = $(obj);
			var dis = $(obj.attr('href'));
			if(dis && dis.attr('display')) {
				dis.css({'display':'none'});
				dis.css({'z-index':'102'});
				DISMENU[dis.attr('id')] = dis;
				obj.on('click', function(e) {
					if(in_array(e.target.tagName, ['A', 'IMG', 'INPUT'])) return;
					$this.maskinit();
					if(dis.attr('display') == 'true') {
						dis.css('display', 'block');
						dis.attr('display', 'false');
						$('#dark').css('display', 'block');
					}
					return false;
				});
			}
		});
	},
	maskinit : function() {
		var $this = this;
		$('#dark').off().on('touchstart', function() {
			$this.hide();
		});
	},
	hide : function() {
		$('#dark').css('display', 'none');
		$.each(DISMENU, function(index, obj) {
			obj.css('display', 'none');
			obj.attr('display', 'true');
		});
	}
};
/*
$("body").on("touchmove",function(event){
        event.preventDefault;
    }, false)
*/
var pullrefresh = {
	init : function() {
		var pos = {};
		var status = false;
		var divobj = null;
		var contentobj = null;
		var reloadflag = false;
		var lsw = null;
		var rsw = null;
		var swidth=$(window).width();
		
		$('body').on('touchstart', function(e) {
			e = mygetnativeevent(e);
			pos.startx = e.touches[0].pageX;
			pos.starty = e.touches[0].pageY;
		})
		.on('touchmove', function(e) {
			e = mygetnativeevent(e);
			pos.curposx = e.touches[0].pageX;
			pos.curposy = e.touches[0].pageY;
			if(pos.curposy - pos.starty < 0 && !status) {
				return;
			}
      if($('html').hasClass("openside") && pos.startx<pos.curposx){
				rsw=pos.curposx-pos.startx;
				status = true;
				if(status == true && rsw>0){
					$('.imui_side').css({'-webkit-transform':'translateX('+rsw+'px)','-webkit-transition':'-webkit-transform 0s ease-out 0s'});	
				}
				e.preventDefault();
			}else if($('html').hasClass("openside")==false && pos.startx>swidth*0.4 && pos.startx<swidth*0.60){
				var fun =$('.body_main .pullrefresh').attr('fun');
				if(fun){
					var mh=(pos.curposy-pos.starty);
					eval(fun+'_star('+mh+')');
				}else{
					if(!status && $('.body_main').scrollTop() <= 0) {
						status = true;
						divobj = document.createElement('div');
						divobj = $(divobj);
						divobj.addClass('imui_reload tc cg');
						$('.body_main').prepend(divobj);
						divobj.prepend('<div class="imui_reload_area"><h4><span></span></h4><p class="size_14">下拉可以刷新</p></div>');
						pos.topx = pos.curposx;
						pos.topy = pos.curposy;
					}
					if(!status) {
						return;
					}
					if(status == true) {
						var pullheight = pos.curposy - pos.topy;
						if(pullheight >= 0 && pullheight < 200) {
							
							$('.imui_reload').css({'height': pullheight/2 + 'px'})
							if(reloadflag) {
								$('.imui_reload h4 span').css({'-webkit-transform':'rotate(0deg)', '-moz-transform':'rotate(0deg)', '-o-transform':'rotate(0deg)', 'transform':'rotate(0deg)'});
								$('.imui_reload p').html('下拉可以刷新');
							}
							reloadflag = false;
						} else if(pullheight >= 200) {
							if(!reloadflag) {
								$('.imui_reload h4 span').css({'-webkit-transform':'rotate(180deg)', '-moz-transform':'rotate(180deg)', '-o-transform':'rotate(180deg)', 'transform':'rotate(180deg)'});
								$('.imui_reload p').html('可以松手了');
							}
							reloadflag = true;
						}
					}
					e.preventDefault();
				}				
			}else if($('html').hasClass("openside")==false && pos.startx<swidth*0.05){
				if(!status && pos.curposx-pos.startx>60){
					status = true;
					lsw=pos.curposx-pos.startx;
				}
				if(!status) {
					return;
				}
				e.preventDefault();
			}else if($('html').hasClass("openside")==false && pos.startx>swidth*0.95){
				if(pos.startx>pos.curposx && pos.startx-pos.curposx>10){
					status = true;
					rsw=280-(pos.startx-pos.curposx);
					$('.imui_sidebg').show();					
				}
				if(!status) {
					return;
				}
				if(status == true && rsw>0){
					$('.imui_side').css({'-webkit-transform':'translateX('+rsw+'px)','-webkit-transition':'-webkit-transform 0s ease-out 0s'});	
				}
				e.preventDefault();
				$('.body_main').css({'overflow-y':'hidden'});
			}
		})
		.on('touchend', function(e) {
			var fun =$('.body_main .pullrefresh').attr('fun');
			if(fun){
				eval(fun+'_end()');
			}else if(divobj){
				if(status == true) {
					if(reloadflag) {
						loading('page');
						$('.imui_reload').animate({'height': '0px'}, 618, 'linear', function() {
							window.location.reload();
						});
						return;
					}else{
						$('.imui_reload').animate({'height': '0px'}, 400, 'linear', function() {
              divobj.remove();
							divobj = null;
							status = false;
							pos = {};
						});
					}
				}
			}else if(lsw){
				window.history.back(-1);
				lsw= null;
				lsw= null;
				status = false;
			}else if(rsw){
				if($('html').hasClass("openside")){
          if(pos.curposx-pos.startx>60){
					  openside();
				  }else{
						$('.imui_side').css({'-webkit-transform':'translateX(0)','-webkit-transition':'-webkit-transform 0.4s ease-out 0s'});
					}
				}else{
          if(pos.startx-pos.curposx>100){
					  openside();
				  }else{
						$('.imui_sidebg').hide();
						$('.imui_side').css({'-webkit-transform':'translateX(100%)','-webkit-transition':'-webkit-transform 0.4s ease-out 0s'});
						$('.body_main').css({'overflow-y':'scroll'});
					}
				}
				
				rsw= null;
				status = false;
			}
		});
	}
};

function mygetnativeevent(event) {
	while(event && typeof event.originalEvent !== "undefined") {
		event = event.originalEvent;
	}
	return event;
}

function evalcss(s) {
	if(s.indexOf('<link') == -1) return s;
	var p = /<link[^\>]*?>/ig;
	var arr = [];
	
	while(arr = p.exec(s)) {
	  arr1=$(arr[0]).attr('href');
		if(arr1){
			appendcss(arr1);
		}
	}
	return s;
}
var evalcsss = [];
function appendcss(href){
	if(in_array(href, evalcsss)) return;
	evalcsss.push(href);
	cssNode='<link rel="stylesheet" href="'+href+'" type="text/css" media="all">';
	$('head').append(cssNode);
}

function evalscript(s,type) {
	if(s.indexOf('<script') == -1) return s;
	var p = /<script[^\>]*?>([^\x00]*?)<\/script>/ig;
	var arr = [];
	while(arr = p.exec(s)) {
    
		var p1 = /<script[^\>]*?src=\"([^\>]*?)\"[^\>]*?(reload=\"1\")?(?:id=\"([\w\-]+?)\")?><\/script>/i;
		var arr1 = [];
		arr1 = p1.exec(arr[0]);
		if(arr1) {
			reload = arr1[2] ? true : false;
			appendscript(arr1[1], '', reload, type, arr1[3]);
		} else {
			p1 = /<script[^\>]*?id=\"([^\>]*?)\"[^\>]*?(reload=\"1\")?>([^\x00]+?)<\/script>/i;
			arr1 = p1.exec(arr[0]);
			if(arr1){
				var reload = arr1[2] ? true : false;
				appendscript('', arr1[3], reload,type,arr1[1]);				
			}else{
			  p1 = /<script(.*?)>([^\x00]+?)<\/script>/i;
			  arr1 = p1.exec(arr[0]);
			  appendscript('', arr1[2], arr1[1].indexOf('reload=') != -1);
			}
		}
	}
	return s;
}

var safescripts = {}, evalscripts = [];
function appendscript(src, text, reload, type, id) {
	var JSLOADED = [];
  if(!id){
		var id = hash(src + text);
	}
	if(!reload && in_array(id, evalscripts)) return;
	if(reload && $('#' + id)[0]) {
		$('#' + id)[0].parentNode.removeChild($('#' + id)[0]);
	}else{
		evalscripts.push(id);
	}

	var scriptNode = document.createElement("script");
	scriptNode.type = "text/javascript";
  scriptNode.id = id;
	scriptNode.charset = !document.charset ? document.characterSet : document.charset;
	
	try {
		if(src) {
			scriptNode.src = src;
			scriptNode.onloadDone = false;
			scriptNode.onload = function () {
				scriptNode.onloadDone = true;
				JSLOADED[src] = 1;
			};
			scriptNode.onreadystatechange = function () {
				if((scriptNode.readyState == 'loaded' || scriptNode.readyState == 'complete') && !scriptNode.onloadDone) {
					scriptNode.onloadDone = true;
					JSLOADED[src] = 1;
				}
			};
		} else if(text){
			scriptNode.text = text;
		}
		document.getElementById('modscript').appendChild(scriptNode);
	} catch(e) {}
}

function hash(string, length) {
	var length = length ? length : 32;
	var start = 0;
	var i = 0;
	var result = '';
	filllen = length - string.length % length;
	for(i = 0; i < filllen; i++){
		string += "0";
	}
	while(start < string.length) {
		result = stringxor(result, string.substr(start, length));
		start += length;
	}
	return result;
}

function stringxor(s1, s2) {
	var s = '';
	var hash = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	var max = Math.max(s1.length, s2.length);
	for(var i=0; i<max; i++) {
		var k = s1.charCodeAt(i) ^ s2.charCodeAt(i);
		s += hash.charAt(k % 52);
	}
	return s;
}

function in_array(needle, haystack) {
	if(typeof needle == 'string' || typeof needle == 'number') {
		for(var i in haystack) {
			if(haystack[i] == needle) {
					return true;
			}
		}
	}
	return false;
}

function isUndefined(variable) {
	return typeof variable == 'undefined' ? true : false;
}


function setcookie(cookieName, cookieValue, seconds, path, domain, secure) {
	if(cookieValue == '' || seconds < 0) {
		cookieValue = '';
		seconds = -2592000;
	}
	if(seconds) {
		var expires = new Date();
		expires.setTime(expires.getTime() + seconds * 1000);
	}
	domain = !domain ? cookiedomain : domain;
	path = !path ? cookiepath : path;
	document.cookie = escape(cookiepre + cookieName) + '=' + escape(cookieValue)
		+ (expires ? '; expires=' + expires.toGMTString() : '')
		+ (path ? '; path=' + path : '/')
		+ (domain ? '; domain=' + domain : '')
		+ (secure ? '; secure' : '');
}

function getcookie(name, nounescape) {
	name = cookiepre + name;
	var cookie_start = document.cookie.indexOf(name);
	var cookie_end = document.cookie.indexOf(";", cookie_start);
	if(cookie_start == -1) {
		return '';
	} else {
		var v = document.cookie.substring(cookie_start + name.length + 1, (cookie_end > cookie_start ? cookie_end : document.cookie.length));
		return !nounescape ? unescape(v) : v;
	}
}

$(document).ready(function() {

	if($('.popup').length > 0) {
		popup.init();
	}
	if($('.display').length > 0) {
		display.init();
	}
	if($('.atap').length > 0) {
		atap.init();
	}
	if($('.pullrefresh').length > 0) {
		pullrefresh.init();
	}

	dialog.init();
	formdialog.init();
	redirect.init();
});

/*--------------------------------------------------
showpage

--------------------------------------------------*/

$(document).on('click', '.showpage', function() {
	var obj = $(this);
	if(obj.attr('id')==''){
		window.location.href = geturl;
		return false;
	}
	loading();
	$('html').removeClass();
	var type = obj.attr('type');
	var id = obj.attr('id');
  var url=obj.attr('href') + '&spa=1';
	if(type=='getpage'){
		var url=obj.attr('href') + '&getpage=1';
		var dataType='html';
	}else{
		var url=obj.attr('href') + '&inajax=1';
		var dataType='xml';
	}
	if(url.indexOf('mobile=2')<0){
		var url= url + '&mobile=2';
	}
	
	$.ajax({
		type : 'GET',
		url : url,
		dataType : dataType
	})
	
	.success(function(s) {
		if(!s){
			popup.open('目标网页存在错误','alert');
			return false;
		}
		if(type=='getpage'){
			var html=s;
		}else{
			var html=s.lastChild.firstChild.nodeValue;
		}
		
		if(html.indexOf('<div class="tip">')!=-1){
			setaction("closepop()");
			popup.open(html,'html');
			evalscript(html);
		}else{
			
			if(html.indexOf('<div class="body_main')==-1){
				window.location.href = obj.attr('href');
				return false;
			}
			setaction("closepage('#"+id+"_area')");
			var main = $(html).find('.wrap').html();
			popup.close();
			$('body').append('<div id="'+id+'_area" class="showpagearea showpage_show"><div class="boxarea"><div class="wrap">'+ main +'</div></div></div>');
			$(".showpage_show .si").scrollLoading();
			evalscript(html);
			$(document).on('click', '.showpagearea a.go', function() {
				closepage('#'+id+'_area');
			});
		}
	})
	.error(function() {
		window.location.href = obj.attr('href');
	});
	return false;
});


/*--------------------add--------------------*/

$(document).on('click', '.scrolltop', function() {
	$('.body_main').scrollTop('0', '1');
});
		
$(document).on('input propertychange focus', '.autoheight', function() {
	this.style.height=this.scrollHeight + 'px';
})
$(document).on('click', '#autopbn', function() {
	var btn=$(this);
	var obj={};
	obj.id=btn.attr('tab');
	autopage(obj,'click');
	return false;
});


$(document).on('click', '.body_main .imui_search_text', function() {
	$('.body_main .imui_search_text').hide();
	$('.body_main .imui_search_input').focus();
});
$(document).on('click', '.body_main .imui_search_cancel', function() {
	$('.body_main .imui_search_text').show();
});


function selectquestion(value){
	if(value){
		$('#answer').css('display', 'block');
	}else{
		$('#answer').css('display', 'none');
	}
}
		
function goto(url,tabid,his){
	loading();

	if(url.indexOf('mobile=2')<0){
		url= url + '&mobile=2';
	}
	if($('#'+tabid).length){
		var gettype= 'update';
	}else{
		var gettype= 'create';
	}
	var value = {
		url: url,
		type: gettype,
		tabid: tabid,
		his: his,
		html: ''
	};
	if($.isFunction(window.loadtab)){
		loadtab('ajax',value);
	}else{
		window.location.href = url;
	}
	return false;
}

function notice(msg,type){
	popup.close();
	$('body').append('<div class="imui_notice tc '+type+'"><p>'+msg+'</p></div>');
	var noticewidth = $(".imui_notice").width()
	var left = (window.innerWidth - noticewidth) / 2;
	var top = (document.documentElement.clientHeight - noticewidth) / 2;
	$('.imui_notice').css({'left':left,'top':top});
	removehtml('.imui_notice');
}

function removehtml(id){
	setTimeout(function(){$(id).fadeOut();},1500);
	setTimeout(function(){$(id).remove();},2000);
}
//getnextpage
var nextstar=2;
function getnextpage(url,id){
	$.ajax({
		type : 'GET',
		url : url+'&page='+nextstar,
		dataType : 'xml'
	})
	.success(function(s){
		if(s.lastChild.firstChild.nodeValue){
			$('#'+id).append(s.lastChild.firstChild.nodeValue);
			nextstar++;			
		}else{
			$('#ajaxnext').css('display','none');
		}
	})
	.error(function() {
		return false;
	});
}

/*formsubmit*/
function formsubmit(id,url,tab){
	var formobj = $('#'+id+'_form');
	loading();
	$.ajax({
		type:'POST',
		url:formobj.attr('action') + '&inajax=1&check=1',
		data:formobj.serialize(),
		dataType:'xml'
	})
	.success(function(s) {
		if(s.lastChild.firstChild.nodeValue!='ok'){
			popup.open(s.lastChild.firstChild.nodeValue,'html');
		}else{
			if(url && tab){
				goto(url,tab,'no');
			}else{
				popup.open('操作成功', 'alert');
			}
		}
	})
	.error(function() {
		popup.open('Ajax请求失败','alert');
	});
	closepage('#'+id+'_area');
	return false;
}

/*settime*/
function settime(value,id){
	if(value){
		var val=value.replace("T"," ");
		$('#'+id).val(val); 
	}
}

function showquick(){
	if($('#quickmenu').hasClass("open")){
		$('#quickmenu').removeClass('open');
		$('#quickmenu li.qc').hide();
		$('#quickmenu').animate({width:40,height:40}, 200);
		$('#quickmenu li.q3').removeClass('cf').addClass('cm');
		$('#quickmenu li.q3').css({'background':'rgba(255,255,255,0.75)'});
    $('#quickmenu li.q3').animate({left:0,top:0}, 200);
		$('#quickmenu').css({'background':'none'});
		
	}else{
		$('#quickmenu').addClass('open');
		$('#quickmenu li.qc').fadeIn();
		$('#quickmenu').animate({width:120,height:120}, 200,function(){$('#quickmenu li.qc').fadeIn();});
		$('#quickmenu li.q3').removeClass('cm').addClass('cf');
		$('#quickmenu li.q3').css({'background':'rgba(0,0,0,0.4)'});
		$('#quickmenu li.q3').animate({left:40,top:40}, 200);
		$('#quickmenu').css({'background':'rgba(255,255,255,0.75)'});
	}
}

//showbox
function showbox(id,type){
  if($('#'+id).css('display')=='none'){
    $('#'+id).fadeIn(300);
		if(type=='wrap'){
			$('#light').css({'display':'block','height':'100%'});
			$('#light').off().on('click', function() {showbox(id);});
		}else{
			$('#light').css({'display':'block','height':'50px'});
			$('#dark').css('display', 'block');
			
			$('#light').off().on('click', function() {showbox(id);});
			$('#dark').off().on('click', function() {showbox(id);});			
		}
		
	}else{
		$('#'+id).fadeOut(300);
		if(type=='wrap'){
			$('#light').css({'display':'none','height':''});
		}else{
		  $('#light').css({'display':'none','height':''});
		  $('#dark').css('display', 'none');			
		}
	}
}

function showmore(id){
	if($('#'+id).css('display')=='none'){
		$('#'+id).fadeIn(300);
	}else{
		$('#'+id).fadeOut(300);
	}
}
/*side*/
function openside(){
	if($('html').hasClass("openside")){
		$('html').removeClass('openside').addClass('closeside');
		$('.imui_side').css({'-webkit-transform':'translateX(100%)','-webkit-transition':'-webkit-transform 0.4s ease-out 0s'});
		$('.body_main').css({'overflow-y':'scroll'});
		$('.imui_sidebg').hide();
	}else{
		$('html').removeClass('closeside').addClass('openside');
		$('.imui_side').css({'-webkit-transform':'translateX(0%)','-webkit-transition':'-webkit-transform 0.4s ease-out 0s'});
		$('.imui_sidebg').show();
		$('.body_main').css({'overflow-y':'hidden'});
	}
}
/*sheet*/
function opensheet(id){
	if($(id).hasClass("showsheet")){
		$(id).removeClass('showsheet').addClass('hidesheet');
		$('#light').css({'display':'none','height':''});
		$('#dark').css('display', 'none');
	}else{
		$(id).removeClass('hidesheet').addClass('showsheet');
		$(id).css('display', 'block');
		$('#light').css({'display':'block','height':'50px'});
		$('#dark').css('display', 'block');
		
		$('#light').off().on('click', function() {opensheet(id);});
		$('#dark').off().on('click', function() {opensheet(id);});

		$(document).on('click', '.imui_sheet a.bo_bl', function() {
			$(id).removeClass('showsheet').addClass('hidesheet');
			$('#light').css({'display':'none','height':''});
			$('#dark').css('display', 'none');
		});
	}
}

			
/*closepage*/
function closepage(id){
	$(id).remove();
	unsetaction();
	popup.close();
}

function setaction(fun){
	lastaction=fun+";";
}

function unsetaction(){
	lastaction="";
}

/*preview*/
function preview(id){
	var file=$('#'+id).val();
	var arr=file.split('\\');
	var name=arr[arr.length-1];
	$('#'+id+'_name').html(name);
}
						
function show(id){
	if($('#'+id).css('display')=='none'){
		$('#'+id).css('display', '');
	}else{
		$('#'+id).css('display', 'none');
	}
}
	
//addsmile
function addsmile(smile,id){
  if(id){
		var old=$('#'+id+'_area #postmessage')[0].value;
		$('#'+id+'_area #postmessage').val(old+smile);		
	}else{
		var old=$(".body_main #postmessage")[0].value;
		$(".body_main #postmessage").val(old+smile);		
	}

}

//aituser
function aituser(id){
	if(id){
		var ait=$('#'+id+'_area #aitarea')[0].value;
		var old=$('#'+id+'_area #postmessage')[0].value;
		if(ait){
			$('#'+id+'_area #postmessage').val(old+' @'+ait+' ');
			$('#'+id+'_area #aitarea').val('');
		}
	}else{
		var ait=$("#aitarea")[0].value;
		var old=$("#postmessage")[0].value;
		if(ait){
			$("#postmessage").val(old+' @'+ait+' ');
			$("#aitarea").val('');
		}
	}
}

//addmusic
function addmusic(id){
	if(id){
		var music=$('#'+id+'_area #musicarea')[0].value;
		var old=$('#'+id+'_area #postmessage')[0].value;
		if(music){
			$('#'+id+'_area #postmessage').val(old+'[audio]'+music+'[/audio]');
			$('#'+id+'_area #musicarea').val('');
		}
	}else{
		var music=$("#musicarea")[0].value;
		var old=$("#postmessage")[0].value;
		if(music){
			$("#postmessage").val(old+'[audio]'+music+'[/audio]');
			$("#musicarea").val('');
		}		
	}

}

//addvideo
function addvideo(id){
	if(id){
		var video=$('#'+id+'_area #videoarea')[0].value;
		var old=$('#'+id+'_area #postmessage')[0].value;
		if(video){
			$('#'+id+'_area #postmessage').val(old+'[media=mp3,500,375]'+video+'[/media]');
			$('#'+id+'_area #videoarea').val('');
		}
	}else{
		var video=$("#videoarea")[0].value;
		var old=$("#postmessage")[0].value;
		if(video){
			$("#postmessage").val(old+'[media=mp3,500,375]'+video+'[/media]');
			$("#videoarea").val('');
		}		
	}
}
				
// showhiden
function showhiden(id,area){
	
	if(!area){
		var mainarea='.body_main';
	}else{
		var mainarea='#'+area+'_area';
	}
	if($(mainarea+' #'+id+'_area').css('display')=='none'){
		$(mainarea+' #hidenarea').children().css('display', 'none');
		$(mainarea+' #tabarea').children().removeClass('cc');
		$(mainarea+' #'+id+'_area').fadeIn(300);
		$(mainarea+' #'+id).removeClass('cg').addClass('cc');
	}else{
		$(mainarea+' #'+id+'_area').fadeOut(300);
		$(mainarea+' #'+id).removeClass('cc').addClass('cg');
	}
}

function showsmilies(id,area){
	if(!area){
		var mainarea='.body_main';
	}else{
		var mainarea='#'+area+'_area';
	}
	$(mainarea+' #imui_smilies_content').children().css('display', 'none');
	$(mainarea+' .imui_smilies_type').children().removeClass('b_f');
	
	$(mainarea+' #'+id).fadeIn(300);
	$(mainarea+' #'+id+'_tab').addClass('b_f');
}
/*linkpage*/
function linkage(value,id,level,content){
	if(level=='second'){
		var nextlevel='third';
		var uplevel='first';
	}else if(level=='third'){
		var nextlevel='null';
		var uplevel='second';
	}
	if(level!='null'){
		var v=value.replace('.', '_');
		var lis = $('#dom_' + id +'_'+ level + '_' + v +' li');
		if(lis.length>0){
			var s='<div class="imui_blocks b_f imui_blocks_radio size_16">';
			for(i = 0;i < lis.length;i++) {
				var optionid = lis[i].getAttribute('optionid');
				s += '<label class="imui_block imui_check_label" for="laber_'+id+'_'+optionid+'_'+level+'"><div class="imui_block_bd flex"><p>'+ lis[i].innerHTML +'</p></div><div class="imui_block_ft"><input type="radio" class="imui_check" name="false_'+id+'_'+level+'" id="laber_'+id+'_'+optionid+'_'+level+'" value="'+optionid+'" onclick="linkage(this.value,\''+id+'\',\''+nextlevel+'\',\''+lis[i].innerHTML+'\')"/><span class="imui_icon_checked"></span></div></label>';
			}
			s += '</div>';
			$('#select_'+id+'_'+level+'_area').html(s);	
			$('#select_'+id+'_'+level+'_area').css('display', 'block');
			$('#select_'+id+'_'+uplevel+'_area').css('display', 'none');			
		}else{
			calllinkpage(id);
		}
	}else{
		calllinkpage(id);
	}
	if($('#'+id+'_back').css('display')=='none'){
		$('#'+id+'_back').css('display', 'block');
	}
	if(level=='second'){
		$('#'+id+'_var_1').html(content);
		$('#'+id+'_var_2').html('');
		$('#'+id+'_var_3').html('');
	}else if(level=='third'){
		$('#'+id+'_var_2').html(' - '+content);
		$('#'+id+'_var_3').html('');
	}else{
		$('#'+id+'_var_3').html(' - '+content);
	}
	
	$('#'+id+'_var_title').html('');	
  $('#typeoption_'+id).val(value); 
	$('#alert_'+id).removeClass('cc');
}

function calllinkpage(id){
	
	if($('#select_'+id+'_area').length){
		unsetaction();
		var main = $('#select_'+id+'_area').html();
		$('#select_'+id+'_area').remove();
		$('#select_'+id).html(main);
	}else{
		setaction("calllinkpage('"+id+"')");
		var main = $('#select_'+id).html();
		$('#select_'+id).empty();
		$('#windowarea').append('<div id="select_'+id+'_area" class="showpagearea showpage_in">'+ main +'</div>');
	}
}

function backlinkpage(id){
	
	if($('#select_'+id+'_third_area').html()!='' && $('#select_'+id+'_third_area').css('display')!='none'){
		$('#select_'+id+'_third_area').css('display', 'none');
		$('#select_'+id+'_second_area').css('display', 'block');
	}else{
		if($('#select_'+id+'_second_area').html()!='' && $('#select_'+id+'_second_area').css('display')!='none'){
		  $('#select_'+id+'_second_area').css('display', 'none');
		  $('#select_'+id+'_first_area').css('display', 'block');
		}
	}
	if($('#select_'+id+'_second_area').css('display')=='none' && $('#select_'+id+'_third_area').css('display')=='none'){
		$('#'+id+'_back').css('display', 'none');
	}
}

//shar
function shar(config){
  if($('#shar_box').length){
	 unsetaction();
	 $('#light').css({'display':'none','height':''});
	 $('#dark').css('display', 'none');
	 $('#shar_box').remove();
	}else{
		setaction("shar()");
		$('#light').css({'display':'block','height':'50px'});
		$('#dark').css('display', 'block');
		$('#light').off().on('click', function() {shar();});
		$('#dark').off().on('click', function() {shar();});		 
		if(BRO=='qq' || BRO=='uc'){
		  var classname='llq';
		  var ins ='<div id="immwashar"></div>';
		}else{
		  var classname='llq';
		  var ins ='<div class="imui_sheet b_f size_16 tc showsheet"><ul class="immwashar_list cl"><li><a href="javascript:copyurl();">复制链接发送给好友</a></li><li><a href="javascript:shar()" class="cc">取消</a></li></ul></div>';
		}
		
		$('body').append('<div class="shar_'+classname+'" id="shar_box">'+ins+'</div>');
		if(BRO=='qq' || BRO=='uc'){
			$(document).on('click', '.immwashar_list li', function() {
				$('#light').css({'display':'none','height':''});
				$('#dark').css('display', 'none');
				$('#shar_box').remove();
			});			
		  var share_obj = new immwashar('immwashar',config);

		}
	}
}

function copyurl(){
	
	popup.close();
	$('#shar_box').remove();
  var url=window.location.href;
	if(ios){
		
		var copyDOM = document.querySelector('#url');
		
		var range = document.createRange();
		
		range.selectNode(copyDOM);        
		window.getSelection().addRange(range);       
		var successful = document.execCommand('copy');
		if(!successful){
			popup.open('您的手机不支持复制链接功能，请通过浏览器自带分享功能进行分享','alert');
		}else{
			window.getSelection().removeAllRanges(); 
			notice('网址已复制到剪切板');
		}
	}else{
		var save = function(e){
			e.clipboardData.setData('text/plain', url);
			e.preventDefault();
		}
		document.addEventListener('copy', save);
		document.execCommand('copy');
		document.removeEventListener('copy',save);
		notice('网址已复制到剪切板');
	}
	unsetaction();
}

function loading(type){
	if(!type){
		var type='comm';
	}
	var loadinghtml='<div class="imui_loading_toast imui_loading_toast_'+type+'"><div class="imui_toast"><div class="imui_loading"><div class="imui_loading_leaf imui_loading_leaf_0"></div><div class="imui_loading_leaf imui_loading_leaf_1"></div><div class="imui_loading_leaf imui_loading_leaf_2"></div><div class="imui_loading_leaf imui_loading_leaf_3"></div><div class="imui_loading_leaf imui_loading_leaf_4"></div><div class="imui_loading_leaf imui_loading_leaf_5"></div><div class="imui_loading_leaf imui_loading_leaf_6"></div><div class="imui_loading_leaf imui_loading_leaf_7"></div><div class="imui_loading_leaf imui_loading_leaf_8"></div><div class="imui_loading_leaf imui_loading_leaf_9"></div><div class="imui_loading_leaf imui_loading_leaf_10"></div><div class="imui_loading_leaf imui_loading_leaf_11"></div></div><p class="imui_toast_content">数据加载中</p></div></div>';
	popup.open(loadinghtml,'loading_'+type);
}

		
function closeload(){
	unsetaction();
	$('.body_main').remove();
	$('.main_sign').removeClass('main_hide main_sign main_move').addClass('body_main');
	popup.close();
}


/*callpage*/
function callpage(id){
	if($('#'+id+'_area').length){
		unsetaction();
		var main = $('#'+id+'_area').html();
		$('#'+id+'_area').remove();
		$('#'+id).html(main);
	}else{
		setaction("callpage('"+id+"')");
		var main = $('#'+id).html();
		$('#'+id).empty();
		$('#windowarea').append('<div id="'+id+'_area" class="showpagearea showpage_show">'+ main +'</div>');
	}
}
/*opentop*/
function opentop(id){
	if($('#'+id+'_area').length){
		unsetaction();
		var main = $('#'+id+'_area').html();
		$('#light').css({'display':'none','height':'','opacity':''});
		$('#dark').css('display', 'none');
		$('#'+id+'_area').remove();
		$('.body_main #'+id).html(main);
	}else{
		setaction("opentop('"+id+"')");
		var main = $('.body_main #'+id).html();
		$('.body_main #'+id).empty();
		$('#windowarea').append('<div id="'+id+'_area" class="imui_toparea open">'+ main +'</div>');
		$('#light').css({'display':'block','height':'50px','opacity':'0'});
		$('#dark').css('display', 'block');
		$('#light').off().on('click', function() {opentop(id);});
		$('#dark').off().on('click', function() {opentop(id);});
		$(document).on('click', '.imui_toparea a', function() {
			unsetaction();
			var main = $('#'+id+'_area').html();
			$('#light').css({'display':'none','height':'','opacity':''});
			$('#dark').css('display', 'none');
			$('#'+id+'_area').remove();
			$('.body_main #'+id).html(main);
		});
	}
}

function showheader(){
	if($('body').attr('id')=='noheader'){
		$('body').attr("id","");
	}else{
		$('body').attr("id","noheader");
	}
}
//openflickr
function openflickr(){
	if($('#imui_flickr').length){
		unsetaction();
		var main = $('#imui_flickr').html();
		$('#imui_flickr').remove();
		$('.body_main .imui_flickr').html(main);
		$('#light').css({'display':'none','height':''});
	}else{
		setaction("openflickr()");
		var main = $('.body_main .imui_flickr').html();
		$('.body_main .imui_flickr').empty();
		$('#windowarea').append('<div id="imui_flickr" class="imui_flickr b_f size_16">'+ main +'</div>');
		$('#light').css({'display':'block','height':'100%'});
		$('#light').off().on('click', function() {openflickr();});

	  $(document).on('click', '.imui_flickr a', function() {
			var main = $('#imui_flickr').html();
			$('#imui_flickr').remove();
			$('.body_main .imui_flickr').html(main);
			$('#light').css({'display':'none','height':''});
		});
	}
}

function needlogin(){
	popup.open('<div class="tip"><dt class="tc">您需要先登录才能继续本操作</dt><dd class="flex_box bo_t"><a href="member.php?mod=logging&action=login" class="cc flex bo_r showpage" id="member" type="getpage">登录</a><a href="javascript:;" class="flex" onclick="popup.close();">关闭</a></dd></div>','html');
}
function openpop(html){
	popup.open('<div class="tip">'+html+'</div>','html');
}

function closepop(){
	popup.close();
	unsetaction();
}
function loginout(siteid,ref,tab){
	loading();
	$.ajax({
		type:'GET',
		url:'/waplogout.aspx?siteid='+siteid+'&isGO=OK',
		dataType:'html',
	})
	.success(function(s) {
		popup.open('<div class="tip"><dt class="tc">您已成功退出登录</dt><dd class="flex_box bo_t"><a href="'+ref+'" class="cc flex gettab" tab="'+tab+'">继续</a></dd></div>','html');
	})
	.error(function() {
		window.location.href = obj.attr('href');
		popup.close();
	});	
}
/*cis_setcookie*/
function cis_setcookie(cookieName, cookieValue, seconds, path, domain, secure) {
	var cp = 'immwa';
	if(cookieValue == '' || seconds < 0) {
		cookieValue = '';
		seconds = -2592000;
	}
	if(seconds) {
		var expires = new Date();
		expires.setTime(expires.getTime() + seconds * 1000);
	}
	domain = !domain ? cookiedomain : domain;
	path = !path ? cookiepath : path;
	document.cookie = escape(cp + '_'+cookieName) + '=' + escape(cookieValue)
		+ (expires ? '; expires=' + expires.toGMTString() : '')
		+ (path ? '; path=' + path : '/')
		+ (domain ? '; domain=' + domain : '')
		+ (secure ? '; secure' : '');
}

function AutoScroll(obj){
	$(obj).find("ul:first").animate({
		marginTop:"-20px"
	},100,function(){
		$(this).css({marginTop:"0px"}).find("li:first").appendTo(this);
	});
}


						


(function($) {
	$.fn.scrollLoading = function(options) {
		var defaults = {
			attr: "data-url",
			container: $('.body_main'),
			callback: $.noop
		};
		
		var params = $.extend({}, defaults, options || {});
		params.cache = [];
		$(this).each(function() {
			var node = this.nodeName.toLowerCase(), url = $(this).attr(params["attr"]);
			//重组
			var data = {
				obj: $(this),
				tag: node,
				url: url
			};
			params.cache.push(data);
		});
		
		var callback = function(call) {
			if ($.isFunction(params.callback)) {
				params.callback.call(call.get(0));
			}
		};
		//动态显示数据
		var imloading = function() {
			var contHeight = params.container.height();
			var contop = params.container.offset().top;


			$.each(params.cache, function(i, data) {
				var o = data.obj, tag = data.tag, url = data.url, post, posb;
        
				if (o) {
					post = o.offset().top - contop, post + o.height();
					if (o.is(':visible') && (post < contHeight) || (posb <= contHeight)) {
						
						if (url) {
							//在浏览器窗口内
							if (tag === "img") {
								//图片，改变src
								o.removeClass('si').addClass('sp');
								callback(o.attr("src", url));		
							} else {
								o.load(url, {}, function() {
									callback(o);
								});
							}		
						} else {
							// 无地址，直接触发回调
							callback(o);
						}
						data.obj = null;	
					}
				}
			});	
		};
		
		//事件触发
		//加载完毕即执行
		imloading();
		//滚动执行
		params.container.bind("scroll", imloading);
	};
})(jQuery);

$(function() {
	$(".body_main .si").scrollLoading();	
});

function scrollmain(){
	var before = $('.body_main').scrollTop();
	if($('.imui_header').css('display')=='none' && $('#noheader').length==0){
		var hideheader=true;
	}	
	$('.body_main')[0].addEventListener("scroll", function(){
		var after = $('.body_main').scrollTop();

		if (before<after) {
			if($('.desk_ico').css('display')!='none'){
				$('.desk_ico').fadeOut();
			}
			if($('.imui_quick').css('display')!='none'){
				$('.imui_quick').fadeOut();
			}
			if(hideheader && $('.imui_header').css('display')=='none' && $('.body_main').scrollTop()>120){
				$('.imui_header').fadeIn();
			}
			before = after;
		};
		if (before>after) {
			if($('.desk_ico').css('display')=='none'){
				$('.desk_ico').fadeIn();
			}
			if($('.imui_quick').css('display')=='none'){
				$('.imui_quick').fadeIn();
			}
			if($('.imui_header').css('display')!='none' && hideheader==true  && $('.body_main').scrollTop()<120){
				$('.imui_header').fadeOut();
			}
			before = after;
		};
	});
}

function openhelp(step){
	var cookie=getcookie('help'+step);
	if(cookie){
		return false;
	}
	var ico=$('.imui_icon_post ').text();
	var hp1='<div class="find_help" id="help"><div id="help_btn"><a href="javascript:openhelp(2)" class="cf b_w">我知道了</a></div><div id="help_text">点击这里可以发现更多功能</div><div id="help_sign"></div><div id="help_ico" class="b_c">'+ico+'</div></div>';
	var hp2='<div class="quick_help" id="help"><div id="help_btn"><a href="javascript:openhelp()" class="cf b_w">我知道了</a></div><div id="help_text">点击这里展开快捷按钮</div><div id="help_sign"></div><div id="help_ico"></div></div>';
	var hp3='<div class="lmove_help" id="help"><div id="help_btn"><a href="javascript:openhelp(4)" class="cf b_w">我知道了</a></div><div id="help_text">从左侧边缘向右滑动可以返回上一页</div><div id="help_ico"></div></div>';
	var hp4='<div class="rmove_help" id="help"><div id="help_btn"><a href="javascript:openhelp()" class="cf b_w">我知道了</a></div><div id="help_text">从右侧边缘向作滑动可以打开侧边栏</div><div id="help_ico"></div></div>';
	var hp5='<div class="dmove_help" id="help"><div id="help_btn"><a href="javascript:openhelp()" class="cf b_w">我知道了</a></div><div id="help_text">下拉可以刷新网页</div><div id="help_ico"></div></div>';
	if(step==1){
		$('#mongolia').show();
		$('#windowarea').append(hp1);
		setcookie('help1', '1', 10368000);
	}else if(step==2){
		$('.find_help').remove();
		$('#windowarea').append(hp2);
		setcookie('help2', '2', 10368000);
	}else if(step==3){
		$('#mongolia').show();
		$('#windowarea').append(hp3);
		setcookie('help3', '3', 10368000);
	}else if(step==4){
		$('.lmove_help').remove();
		$('#windowarea').append(hp4);
		setcookie('help4', '4', 10368000);
	}else if(step==5){
		$('#mongolia').show();
		$('#windowarea').append(hp5);
		setcookie('help5', '5', 10368000);
	}else if(!step){
		$('#mongolia').hide();
		$('#windowarea').empty();
	}
}