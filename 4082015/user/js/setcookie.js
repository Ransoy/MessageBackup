
var timer;
var item;//���͍���
var nod   = 0;//item �̌�
var place = "";
var ref;//referer



//�ۑ��̃C���^�[�o����ݒ�
function setIvl(IVL){
	var i = parseInt(IVL);

	clearInterval(timer);
	if(i>0){
		//timer = setInterval(setSaveTime, i);
		//timer = setInterval(saveCookie(item), i);
		timer = setInterval(function(){saveCookie(item);}, i);
	}
}


//cookie �ɕۑ�����
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

//cookie �ɕۑ�����Ă�����̂��t�H�[���ɃZ�b�g����
function setCookie(ARR){
	var cookies = $.cookie();

	for(var i=0;i<ARR.length;i++){
		$(ARR[i]).val($.cookie(ARR[i]));
	}
}


//���� cookie �ɕۑ�����Ă��邩���m�F����
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

var exclude = false;//�u���M�v�͏��O���邽�߂̃t���O
var str     = '���M���܂��������Ă��܂���B���̂܂܈ړ����܂����H';

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



//cookie ���폜
function deleteCookie(ARR){
	for(var i=0;i<ARR.length;i++){
		/*if($.removeCookie(ARR[i])){
			alert(ARR[i]);
		}else{
			alert("����܂���ł���");
		}*/
		$.removeCookie(ARR[i]);
	}
}



//�ۑ��������Ԃ�\������
function setSaveTime(ITEM){
	var DD   = new Date();
	var Hour = DD.getHours();
	var Min  = DD.getMinutes();
	var Sec  = DD.getSeconds();
	var time = Hour + "��" + Min + "��" + Sec +"�b �ɕۑ����܂���";

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

	//cookie ���m�F���A�ۑ�����Ă����畜�����邩���m�F����
	var cookies = $.cookie();
	var word    = "";
	var count   = 0;

	//�����ۑ�
	setIvl(360000);

	for(var i in item){
//alert(item[i]);
		if(cookies[item[i]] && cookies[item[i]].length>0){ count++; }
	}

	if(ref && count>0){
		word = "�ۑ�����Ă�����e�𕜌����܂����H";
		if(confirm(word)){
			setCookie(item);
		}else{
			deleteCookie(item);
		}
	}

});


