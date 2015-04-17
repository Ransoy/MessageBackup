
var timer;
var item;//入力項目
var nod   = 0;//item の個数
var place = "";
var ref;//referer



//保存のインターバルを設定
function setIvl(IVL){
	var i = parseInt(IVL);

	clearInterval(timer);
	if(i>0){
		//timer = setInterval(setSaveTime, i);
		//timer = setInterval(saveCookie(item), i);
		timer = setInterval(function(){saveCookie(item);}, i);
	}
}


//cookie に保存する
function saveCookie(ARR){
//alert($(ARR[i]).val());
	for(var i=0;i<ARR.length;i++){
	//alert(ARR[i]);
	//alert($(ARR[i]).val());
		$.cookie(ARR[i],$(ARR[i]).val());
	}
}


function test(ARR){
	for(var i=0;i<ARR.length;i++){
		alert($(ARR[i]).val());
	}
}

//cookie に保存されているものをフォームにセットする
function setCookie(ARR){
	var cookies = $.cookie();

	for(var i=0;i<ARR.length;i++){
		$(ARR[i]).val($.cookie(ARR[i]));
	}
}


//何が cookie に保存されているかを確認する
function getCookie(ARR){
	var cookies = $.cookie();
	var str     = "";

	for(var i=0;i<ARR.length;i++){
		var content = "";
		
		if(cookies[ARR[i]]){
			var content = cookies[ARR[i]];
		}

		str += content + "\n\n\n";
	}

	alert(str);
}


function beforeSend(){
	
	deleteCookie(item);
	exclude = true;
	//document.f1.submit();

	//exclude = false;
}

var exclude = false;//「送信」は除外するためのフラグ
var str     = '送信がまだ完了していません。このまま移動しますか？';

window.onbeforeunload = function(e){

	//saveCookie(item);
	//setSaveTime(item);

	if(!exclude){
		saveCookie(item);

		var e = e || window.event;

		// For IE and Firefox prior to version 4
		if(e){ e.returnValue = str; }

		// For Safari
		return str;
	}
};



//cookie を削除
function deleteCookie(ARR){
	for(var i=0;i<ARR.length;i++){
		/*if($.removeCookie(ARR[i])){
			alert(ARR[i]);
		}else{
			alert("ありませんでした");
		}*/
		$.removeCookie(ARR[i]);
	}
}



//保存した時間を表示する
function setSaveTime(ITEM){
	var DD   = new Date();
	var Hour = DD.getHours();
	var Min  = DD.getMinutes();
	var Sec  = DD.getSeconds();
	var time = Hour + "時" + Min + "分" + Sec +"秒 に保存しました";

	saveCookie(ITEM);
	//$(place).html(time);
}


function makeIdName(ITEM){
	var id;
	var noi = ITEM.length;

	if(noi){
		for(var i;i<noi;i++){
			id = "#" + item[i];
			$.cookie(item[i],$(id).val());
		}
	}
}


$(function(){

	//cookie を確認し、保存されていたら復元するかを確認する
	var cookies = $.cookie();
	var word    = "";
	var count   = 0;

	//自動保存
	setIvl(360000);

	for(var i in item){
//alert(item[i]);
		if(cookies[item[i]] && cookies[item[i]].length>0){ count++; }
	}

	if(ref && count>0){
		word = "保存されている内容を復元しますか？";
		if(confirm(word)){
			setCookie(item);
		}else{
			deleteCookie(item);
		}
	}

});


