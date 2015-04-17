function surroundHTML(openTag){
	target = 'comment';
	var elm = document.getElementById(target);
	if(openTag.indexOf("img") != -1 || openTag.indexOf("br") != -1 ){
		closeTag = '';
	}else{
		var closeTag = openTag;
		while(!(closeTag.indexOf("=") == -1 && closeTag.indexOf(" ") == -1)){
			closeTag = closeTag.slice(0, closeTag.indexOf("="));
			closeTag = closeTag.slice(0, closeTag.indexOf(" "));
		}
		closeTag = closeTag.replace(/<|>/g	,'');
		closeTag = '</'+closeTag+'>';
	}
	if (document.selection){
		elm.focus();
		var rng = document.selection.createRange();
		var str = rng.text;
		rng.text = openTag + str + closeTag;
		if (openTag != ''){
			rng.moveStart("character", (-1 * closeTag.length));
			rng.moveEnd("character", (-1 * closeTag.length));
		}
		rng.select();
	}else if ((elm.selectionEnd - elm.selectionStart) >= 0){
		var startPos = elm.selectionStart;
		var endPos   = elm.selectionEnd;
		var newPos = elm.selectionEnd + openTag.length + closeTag.length;
		elm.value = elm.value.substring(0, startPos)
		+ openTag
		+ elm.value.substring(startPos, endPos)
		+ closeTag
		+ elm.value.substring(endPos, elm.value.length);
		if (openTag != ''){
		elm.selectionStart = endPos + openTag.length;
		elm.selectionEnd = elm.selectionStart;
		}else{
		elm.selectionStart = endPos + closeTag.length;
		elm.selectionEnd = elm.selectionStart;
		}
	}else{
		elm.value += openTag + closeTag;
	}
	elm.focus();
	if (target != 'sidebar_text'){
		writePreview();
	}
}

function surroundHTMLele(opentag, ele, closetag) {
	opentag = "<"+opentag+'"'+ele+'"'+">";
	surroundHTML(opentag);
}

function surroundHTMLhref(){
	var result = window.prompt('リンク先のURLを入力してください。');
	var subject = window.prompt('リンクの文字を入力してください。');
	opentag = '<a href="'+result+'"'+' target="_blank">'+subject;
	
	if(result && subject){
		surroundHTML(opentag);
	}
}
function disp(tar){
	if(!tar){
		imageTable.style.display = "none";
		colorTable.style.display = "none";
		unobserve('body','mousedown',function(){disp();});
		return false;
	}
	if(tar.style.display=="none"){
		imageTable.style.display = "none";
		colorTable.style.display = "none";
		tar.style.display = "block";
	}else if(tar.style.display=="block"){
		imageTable.style.display = "none";
		colorTable.style.display = "none";
		tar.style.display = "none";
	}
	observe('body','mousedown',function(){disp();});
}
function writePreview(){
	var src='comment';
	var target='sample';
	var value = document.getElementById(src).value;
	value= value.replace(/\r\n/g,'<br />');
	value= value.replace(/\n/g,'<br />');
	document.getElementById(target).innerHTML = value;
}

function dispColor(color){
	dispcolor.style.backgroundColor = color;
}

function observe(target_id, type, listener) {
	var target = document.getElementById(target_id);
	if (target.addEventListener) target.addEventListener(type, listener, false);
	else target.attachEvent('on' + type, function() { listener.call(target, window.event); });
}
function unobserve(target_id, type, listener) {
	var target = document.getElementById(target_id);
	if (target.removeEventListener) target.removeEventListener(type, listener, false);
	else target.detachEvent('on' + type, function() { listener.call(target, window.event); });
}


function inittextarea(){
	var target = document.getElementById('comment');
	target.style.width="500px";
	target.style.height="300px";
}
var imageTable = null;
function initpicarea(){
	imageTable = document.getElementById('imageTable');
}
var dispcolor = null;
var colorTable = null;
function initcolorarea(){
	dispcolor = document.getElementById('dispcolor');
	colorTable = document.getElementById('colorTable');
}

function init(){
	writePreview();
	inittextarea();
	initpicarea();
	initcolorarea();
	observe('comment','keyup',function(){writePreview();} );
}