/*
 * @Title： Macherie girls page JS
 * @Description：パフォーマー管理画面のスクリプト関係まとめ
 * @Copyright  Innetwork
 * @Last Edit：
 *        2010.10.16：Jquery対応と取り合えずまとめ　@author satodate
 */
$(function()
{
	// Tabメニュー
	$('#multipleInformation').tabs(
	{
			remove:[2],
			show: function(ui) {
				doIframe();
			},
			select: function(event, ui){
				nTabElement = document.getElementById("femaleNews");
				if(nTabElement){
					switch (ui.index){
					case 0:
					case 1:
						$('#campaign_news').show();
						break;
					case 2:
						$('#campaign_news').hide();
						break;
					}
				}
			}

	});

	// 初心者の方へ
	$('#MainBlock .cmnSection h2 span.btn').live("click", function(){
		menuDisplay();
	});
	menuDisplay2();

	$('#MainBlock .cmnSection h2 span.btn_open').live("click", function(){
		menuOpen( $(this).attr("value") );
	});

	$('#MainBlock .cmnSection h2 span.btn_close').live("click", function(){
		menuOpen( $(this).attr("value") );
	});

	menuInitial();

	// プルダウンメニュー
	$("#Navigation dd").PullDownMenu();

	// New Window
	$(".newWindow").live("click", function()
	{
		if($(this).attr("href").indexOf("\/mail\/mailbox_receive.php") != -1){
			$("#mail_icon").hide();
			$("#mail_icon_mini1").hide();
			$("#mail_icon_mini2").hide();
		}
		window.open($(this).attr("href"), "opwin", "scrollbars=yes, toolbar=no,resizable=yes");
		return false;
	});

	// 2SHOT Window Open
	$("#shot2Connection a,#event2shot a,#map2Shot").live("click", function()
	{
		if(680 <= getWindowHeight())
		{
			window.open($(this).attr("href"), "chatwin", "width=700,height=680,scrollbars=no, toolbar=no");
		}else{
			window.open($(this).attr("href"), "chatwin", "width=700,height=680,scrollbars=yes, toolbar=no");
		}
		return false;
	});

	// Party Window Open
	$("#partyConnection a,#eventParty a,#mapParty").live("click", function()
	{
		if(560 <= getWindowHeight())
		{
			window.open($(this).attr("href"), "chatwin", "width=780,height=560,scrollbars=no, toolbar=no");
		}else{
			window.open($(this).attr("href"), "chatwin", "width=780,height=560,scrollbars=yes, toolbar=no");
		}
		return false;
	});

	$("#mikeDirect").load("http://" + location.hostname + "/performer/mikeView.php",{user_id:$('#user_id').val()},
		function(responseText, status, XMLHttpRequest) {
			//alert("location.hostname:" + location.hostname);
			if(responseText == "1"){
				$("#mikeDirect").html('<img id="mikeOn" src="/performer/img/banner/mic_on.png" style="cursor:pointer;" onmouseover="this.src=\'/performer/img/banner/mic_on_on.png\'" onmouseout="this.src=\'/performer/img/banner/mic_on.png\'">');
				$("#pIcon").load("http://" + location.hostname + "/performer/pIconView.php",{user_id:$('#user_id').val()});
			} else if(responseText == "0"){
				$("#mikeDirect").html('<img id="mikeOff" src="/performer/img/banner/mic_off.png" style="cursor:pointer;" onmouseover="this.src=\'/performer/img/banner/mic_off_on.png\'" onmouseout="this.src=\'/performer/img/banner/mic_off.png\'">');
				$("#pIcon").load("http://" + location.hostname + "/performer/pIconView.php",{user_id:$('#user_id').val()});
			} else {
				$("#mikeDirect").html('<center><font color="red">マウス状態判定不能</font></center>');
			}
	});

	$("#mikeOn").live("click", function()
	{
		mikeDirect_edit( "0" );
	});

	$("#mikeOff").live("click", function()
	{
		mikeDirect_edit( "1" );
	});

	$("#contain1 form").submit(function(){
		buttonNumber = $(this).attr("value");
		document.cookie = "disp_flg" + buttonNumber + "=1";
	});

	$("#pwdException form").submit(function(){
		document.cookie = "referer_excepthon_flg=0";
	});

	pIcontimerID = setInterval('pIconUpdate()',1000*60*10);

	nTabElement = document.getElementById("femaleNews");
	if(nTabElement){
		nTabtimerID = setInterval('nTabUpdate(nTabElement)',1000*60*30);
	} else {
		anTabElement = document.getElementById("afemaleNews");
		if(anTabElement){
			anTabtimerID = setInterval('anTabUpdate(anTabElement)',1000*60*30);
		}
	}
	
	$(document).on('click', '.disable', function(e){
		e.preventDefautl();
		return false;
	});

});



//プルダウンメニュー
$.fn.PullDownMenu = function()
{
	$(this).bind('mouseover',function(){
		$(this).find(".menuChild").show();
	});

	$(this).bind('mouseout',function(){
		$(this).find(".menuChild").hide();
	});
};

function mikeDirect_edit(mic_flg){

	$("#mikeDirect").load("http://" + location.hostname + "/performer/mikeView.php",{user_id:$('#user_id').val(), mic:mic_flg},
		function(responseText, status, XMLHttpRequest) {
			if(responseText == "1"){
				$("#mikeDirect").html('<img id="mikeOn" src="/performer/img/banner/mic_on.png" style="cursor:pointer;" onmouseover="this.src=\'/performer/img/banner/mic_on_on.png\'" onmouseout="this.src=\'/performer/img/banner/mic_on.png\'">');
				$("#pIcon").load("http://" + location.hostname + "/performer/pIconView.php",{user_id:$('#user_id').val()});
			} else if(responseText == "0"){
				$("#mikeDirect").html('<img id="mikeOff" src="/performer/img/banner/mic_off.png" style="cursor:pointer;" onmouseover="this.src=\'/performer/img/banner/mic_off_on.png\'" onmouseout="this.src=\'/performer/img/banner/mic_off.png\'">');
				$("#pIcon").load("http://" + location.hostname + "/performer/pIconView.php",{user_id:$('#user_id').val()});
			} else {
				$("#mikeDirect").html('<center><font color="red">マウス状態判定不能</font></center>');
			}
	});

}

function pIconUpdate(){

	$("#pIcon").load("http://" + location.hostname + "/performer/pIconView.php",{user_id:$('#user_id').val()});

}

function nTabUpdate(nTabElement){

	nTabElement.src='./news.php';

}

function anTabUpdate(anTabElement){

	anTabElement.src='./news_agent.php';

}

function send_chat(value)
{
	window.open("", "chatwin", "width=700,height=680,scrollbars=no, toolbar=no");
	document.F1.con_mode.value = value;
	document.F1.target = "chatwin";
	document.F1.submit();
}


// Window Height Check
function getWindowHeight ()
{
    if( window.innerHeight )
	{
        return window.innerHeight;
    }
	else if( document.documentElement && document.documentElement.clientHeight )
	{
        return document.documentElement.clientHeight;
    }
	else if ( document.body && document.body.clientHeight )
	{
        return document.body.clientHeight;
    }
}

var disp_flg = 1;

function menuDisplay()
{
	var element = document.getElementById("contain");
	var element_close = document.getElementById("close_btn");
	var element_open = document.getElementById("open_btn");

	document.cookie = "disp_flg="+disp_flg+";expires=Tue, 1-Jan-2020 00:00:00 GMT;";
	if(disp_flg == 1){
		element.style.display = 'none';
		element_close.style.display = 'none';
		element_open.style.display = 'block';
		disp_flg = 0;
	}else{
		element.style.display = 'block';
		element_close.style.display = 'block';
		element_open.style.display = 'none';
		disp_flg = 1;
	}
}
function menuDisplay2()
{
	var element = document.getElementById("contain");
	var element_close = document.getElementById("close_btn");
	var element_open = document.getElementById("open_btn");

	if(element)
	{
		str = document.cookie;
		if(str.indexOf('disp_flg=1;',0) != -1){
			element.style.display = 'none';
			element_close.style.display = 'none';
			element_open.style.display = 'block';
			disp_flg = 0;
		}

	}
}

var disp_array = new Array(1,0,0,0,1,0,0,0);

function menuOpen(buttonNumber)
{
	var element = document.getElementById("contain" + buttonNumber);

	if(disp_array[buttonNumber - 1] == 1){
		element.style.display = 'none';
		$('#click_button' + buttonNumber).removeClass("btn_close").addClass("btn_open");
		disp_array[buttonNumber - 1] = 0;
	}else{
		element.style.display = 'block';
		$('#click_button' + buttonNumber).removeClass("btn_open").addClass("btn_close");
		disp_array[buttonNumber - 1] = 1;
	}
	document.cookie = "disp_flg"+buttonNumber  +"="+disp_array[buttonNumber - 1];
}

function menuInitial()
{
	var element;
	var element_click;
	var excepthon_flg = false;

	for(elementC = 1; elementC <= disp_array.length; elementC++ ){

		element = document.getElementById("contain" + elementC);
		element_click = document.getElementById("click_button" + elementC);

		if(element)
		{
			element_click.style.display = 'block';

			str1 = document.referrer;
			str2 = document.cookie;
			if( (str1.lastIndexOf('operator_edit_prof_anonname.php') != -1) ||
			    ( str1.lastIndexOf('operator_edit_myinfo.php') != -1 && str2.indexOf('referer_excepthon_flg=2;', 0) != -1 ) ||
			    (str2.indexOf('referer_excepthon_flg=1;', 0)  != -1) ){
				if(str2.indexOf('disp_flg' + elementC + '=0;', 0) != -1){
					element.style.display = 'none';
					$('#click_button' + elementC).addClass("btn_open");
					disp_array[elementC - 1] = 0;
				} else {
					$('#click_button' + elementC).addClass("btn_close");
					disp_array[elementC - 1] = 1;
				}
				document.cookie = "referer_excepthon_flg=2";
			} else {
				if(elementC != 1 && elementC != 5){
					element.style.display = 'none';
					$('#click_button' + elementC).addClass("btn_open");
					disp_array[elementC - 1] = 0;
				} else {
					$('#click_button' + elementC).addClass("btn_close");
					if(elementC == 5){
						excepthon_flg = true;
					}
				}
			}
		}

	}

	if(excepthon_flg){
		document.cookie = "referer_excepthon_flg=1";
	}

}

function allNavi_manage(){

	var element;

	element = document.getElementById("all_navi");

	if(element.style.display == 'none'){
		element.style.display = 'block';
	} else {
		element.style.display = 'none';
	}

}

function pageChange(page_num)
{
	for(i=1;i<=6;i++){
		if(i==page_num){
			document.getElementById("page"+i).style.display = 'block';
		}else{
			document.getElementById("page"+i).style.display = 'none';
		}
	}
}
function logoutF()
{
	document.logoutArea.submit();
}

function doIframe()
{

	o = document.getElementsByTagName('iframe');

	for(i=0;i<o.length;i++)
	{
		if (/\bautoHeight\b/.test(o[i].className))
		{
			setHeight(o[i]);
			addEvent(o[i],'load', doIframe);
		}
	}
}
function setHeight(e)
{
	if(e){
		if(e.contentDocument){
			if (navigator.userAgent.indexOf("Trident/4.0") != -1)
			{
				e.height = e.contentWindow.document.body.scrollHeight + 30;
			}
			else
			{
				if(e.contentDocument.body == null)
				{
					setTimeout("setHeight()",1000);
				}
				else
				{
					e.height = e.contentDocument.body.offsetHeight + 30;
				}
			}
		} else {
			if(e.contentWindow.document.body != null){
				e.height = e.contentWindow.document.body.scrollHeight;
			}
		}
	}
}
function addEvent(obj, evType, fn)
{
	if(obj.addEventListener)
	{
	obj.addEventListener(evType, fn,false);
	return true;
	} else if (obj.attachEvent){
	var r = obj.attachEvent("on"+evType, fn);
	return r;
	} else {
	return false;
	}
}

function mailload() {
	//dummy = parseInt(new Date() / 1000);
	//dummy = Math.floor(Math.random() * 10000);
	$.get("/performer/ajax/new_mail_check.php", { id: Math.random()},
		function(data){
			if(data == 1){
				$("#mail_icon").show();
				$("#mail_icon_mini1").show();
				$("#mail_icon_mini2").show();
			}else{
				$("#mail_icon").hide();
				$("#mail_icon_mini1").hide();
				$("#mail_icon_mini2").hide();
			}
  		}
	);
}
mailload();
timer1=setInterval("mailload()",60000);