

var visualeditor = function(c) {
    this.config = {
        source: 'source',
        visual: 'visual',
        textarea: 'textarea'
    };
    for (attr in c) {
        this.config[attr] = c[attr];
    }
    this.init();
};
visualeditor.prototype = {
    init: function() {
        var _this = this;
        var status = this.getCookie('status');
        this.bind(window, 'load', function() {
            _this.btnSource = document.getElementById('btn-' + _this.config.source);
            _this.btnVisual = document.getElementById('btn-' + _this.config.visual);
            _this.boxSource = document.getElementById('box-' + _this.config.source);
            _this.boxVisual = document.getElementById('box-' + _this.config.visual);
            _this.textarea = document.getElementById(_this.config.textarea);
			
            _this.bind(_this.btnSource, 'click', function() {
                _this.boxVisual.style.display = 'none';
                _this.boxSource.style.display = 'block';
				_this.setCookie('status' , _this.boxSource.id);
            });
            _this.bind(_this.btnVisual, 'click', function() {
                _this.boxVisual.style.display = 'block';
                _this.boxSource.style.display = 'none';
                if (!_this.btnVisual.btn) {
                    _this.generate();
                }
				_this.setCookie('status' , _this.boxVisual.id);
            });
			
			if(status){
				document.getElementById(status).style.display = 'block';
				if(status == 'box-' + _this.config.visual){
					_this.generate();
				}
			}
        });
		
		if(status == 'box-' + _this.config.visual){
			document.getElementById('visualeditor-style').innerHTML += '#box-' + _this.config.source + '{display:none;}';
		}
    
    },
    generate: function() {
        var sHtml = this.textarea.value;
        var aConfig = sHtml.match(/(\[([^\]]+)\][\s\S]*?\[\/\2\])|(<!--#([\s\S]*?)-->)/g);
        var str = '';
        
        if (aConfig) {
            for (var i = 0; i < aConfig.length; i++) {
                str += aConfig[i];
            }
            
            sHtml = str;
            
            var visualHTml = sHtml.replace(/\[([^\]]+)\]([\s\S]*?)\[\/\1\]/g, function($1, $2, $3) {
                var reBr = /\n/;
                var text = '';
                if (reBr.test($3)) {
                    text = '<div class="visualeditor-list visualeditor-editor visualeditor-even"><textarea spellcheck="false" onchange="change(editor,this , \'' + $2 + '\')">' + $3 + '</textarea></div>';
                } else {
                    text = '<div class="visualeditor-list visualeditor-editor visualeditor-odd"><textarea spellcheck="false" onkeydown="checkEnter(event)"  onchange="change(editor, this, \'' + $2 + '\')">' + $3 + '</textarea></div>';
                }
                return text;
            });
            visualHTml = visualHTml.replace(/<!--#([\s\S]*?)-->/g, function($1, $2) {
                var sHtml = '<div class="visualeditor-list visualeditor-title">' + $2 + '</div>';
                return sHtml;
            });
            
            this.boxVisual.innerHTML = visualHTml;
        	this.btnVisual.btn = true;
        }
    },
    bind: function(obj, type, fn) {
        if (obj.addEventListener) {
            obj.addEventListener(type, fn, false);
        } else if (obj.attachEvent) {
            obj.attachEvent('on' + type, fn);
        } else {
            obj['on' + type] = fn;
        }
    },
    setCookie: function(name, value) { //设置cookie
        var Days = 30;
        var exp = new Date();
        exp.setTime(exp.getTime() + Days * 24 * 60 * 60 * 1000);
        document.cookie = name + "=" + escape(value) + ";expires=" + exp.toGMTString() + ";path=/";
    },
    getCookie: function(name) { //获取cookie
        var arr, reg = new RegExp("(^| )" + name + "=([^;]*)(;|$)");
        if (arr = document.cookie.match(reg))
            return unescape(arr[2]);
        else
            return null;
    }
};
function checkEnter(e) 
{
    var et = e || window.event;
    var keycode = et.charCode || et.keyCode;
    if (keycode == 13) 
    {
        if (window.event)
            window.event.returnValue = false;
        else
            e.preventDefault(); //for firefox
    }
}
function change(_this, obj, tagName) {
    
    if (obj.value.indexOf('[/' + tagName + ']') > -1) {
        alert('不能含有关键字,[/' + tagName + ']');
        obj.style.border = '1px solid red';
    } else if (obj.value.indexOf('[' + tagName + ']') > -1) {
        alert('不能含有关键字,[' + tagName + ']');
        obj.style.border = '1px solid red';
    } else {
        var strRe = '(\\[' + tagName + '\\])([\\s\\S]*?)(\\[\\/' + tagName + '\\])';
        var reTag = new RegExp(strRe, 'g');
        var sHtml = obj.value.replace(/\$/g, '$$$$');
        var value = '$1' + sHtml + '$3';
        _this.textarea.value = _this.textarea.value.replace(reTag, value);
        obj.style.border = '';
    }
}
