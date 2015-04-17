
//
//	Site:Macherie
//	Build:2007/4/1
//	Author:S.takaya
//
//-----------------------------------

/* frame
-------------------------------------*/

/* ChatWindowOpen
-------------------------------------*/
function cO(id){
	var url = 'chat/shicho.php?id=' + id;
	window.open(url,"memberChat");
}
/* MobileChatWindowOpen
-------------------------------------*/
function MO(id){
	var url = 'chat/shicho_mobile.php?id=' + id;
	window.open(url,"memberChat");
}
/* ProfileWindowOpen
-------------------------------------*/
function pO(id){
	var url = 'profile/profile.php?sid=' + id;
	window.open(url,"Profile","width=714,height=536,titlebar=1,status=0,scrollbars=1");
}
/*	3Paty
-------------------------------------*/
function uO(id){
	var url = 'chat/wakusei_shityo_top.php?id=' + id;
	window.open(url,"memberChat","width=700,height=695,titlebar=1,status=0,scrollbars=no");
}
/* MailWindowOpen
-------------------------------------*/
function mO(id){
	var url = 'webmail/write.php?sid=' +id;
	window.open(url,"memberWebmail","titlebar=0,status=0,scrollbars=yes,resizable=yes");
}
/* MailWindowOpen2
-------------------------------------*/
function mail(cd){
	if( location.protocol == 'https:' ){
		var url = 'http://' + location.hostname;
		switch (cd){
			case 1:		//Normal
			case 2:		//World
				url += '/webmail/index.php';
				break;
			case 3:		//Biglobe
				url += '/biglobe/webmail/index.php';
				break;
			case 4:		//楽天
				url += '/rakuten/webmail/index.php';
				break;
			case 5:		//Cinema
				url += '/cinema/webmail/index.php';
				break;
		}
	} else {
		var url = 'webmail/index.php';
	}
	window.open(url,"memberWebmail","titlebar=0,status=0,scrollbars=yes,resizable=yes");
}


function checkRule(){
	if(document.registration.agree[1].checked){
		alert("登録をするには規約に同意する必要があります");
		return false;
	}
}

function newtargetblank(href){
	if(href != "" && typeof(href) != 'undefined'){
		window.open(href);
	}
	return false;
}
