
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
	var url = '/chat/shicho.php?id=' + id;
	window.open(url,"memberChat");
	//window.open(url,"Shicho","width=992,height=700,titlebar=1,status=0,scrollbars=no");

	//var url = '/chat/shityo_top.php?id=' + id;
	//window.open(url,"memberChat","width=700,height=695,titlebar=1,status=0,scrollbars=no");
}
/* MobileChatWindowOpen
-------------------------------------*/
function MO(id){
	var url = '/chat/shicho_mobile.php?id=' + id;
	window.open(url,"memberChat");
}
/* ProfileWindowOpen
-------------------------------------*/
function pO(id){
	var url = '/profile/profile.php?sid=' + id;
	window.open(url,"Profile","width=714,height=536,titlebar=1,status=0,scrollbars=1");
	//window.open(url,"Profile","width=710,height=605,titlebar=1,status=0,scrollbars=1");
}
/*	3Paty
-------------------------------------*/
function uO(id){
	var url = '/chat/wakusei_shityo_top.php?id=' + id;
	window.open(url,"memberChat","width=700,height=695,titlebar=1,status=0,scrollbars=no");
}
/* MailWindowOpen
-------------------------------------*/
function mO(id){
	var url = '/webmail/write.php?sid=' +id;
	window.open(url,"memberWebmail","titlebar=0,status=0,scrollbars=yes,resizable=yes");
}
/* MailWindowOpen2
-------------------------------------*/
function mail(){
	if( location.protocol == 'https:' ){
		var url = 'http://' + location.hostname  +  '/webmail/index.php';
	} else {
		var url = '/webmail/index.php';
	}
	window.open(url,"memberWebmail","titlebar=0,status=0,scrollbars=yes,resizable=yes");
}


function checkRule(){
	if(document.registration.agree[1].checked){
		alert("“o˜^‚ð‚·‚é‚É‚Í‹K–ñ‚É“¯ˆÓ‚·‚é•K—v‚ª‚ ‚è‚Ü‚·");
		return false;
	}
}

function newtargetblank(href){
	if(href != "" && typeof(href) != 'undefined'){
		window.open(href);
	}
	return false;
}

function chat_waiting(screen_type) {
	if(timer1_flag == 1) {
		timer1_flag = 0;
		var hash = $('#f_hash').val();
		var h = Math.floor( Math.random() * 100000 );
		var url = "/chat/shityo_machiawase_ajax_meeting.php?h="+h;
		var machiawase_flg = $('#machiawase_flg').val();
	    $.post(url, {'id':hash, 'machiawase_flg':machiawase_flg })
	    	.done(function(response) {
	    		timer1_flag = 1;
	    		var res = jQuery.parseJSON(response);  
				if(res.machiawase_flg == 1) {              
					$('#overlay_content').css('z-index', '13');
			        if (!document.getElementById("overlay_content")) { 		  	
				        $('#'+screen_type).prepend("<div id='overlay_content'></div>");
				  	}  		  	
				}
				else if(res.machiawase_flg == '') {
					$('#overlay_content').hide();
					clearInterval(Timer1);
			        clearInterval(Timer2);
				}		
				else {	
				    $('#overlay_content').css('z-index', '-13');
				}  
	    });
	}
}

function performer_video() { 
    m_overlay_timer--; 
    if(m_overlay_timer == -1) { 
        clearInterval(Timer2);
        $('#overlay_content').hide();
        clearInterval(Timer1);
        clearInterval(Timer2);
    } 
}
