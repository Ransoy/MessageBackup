/*
*
* 共通スクリプト
*
*/

$(function(){
	
	var sidr_flg = 0;
	
	/*------------------------------------*/
	/* 共通タッチイベント
	/*------------------------------------*/
	/*
	$('[src*="_off."]').on('touchstart', function(){
		$(this).attr("src",$(this).attr("src").replace("_off.", "_on."));
	}).on('touchend', function(){
		$(this).attr("src",$(this).attr("src").replace("_on.", "_off."));
	});
	*/
	
	/*
	$(document).on('touchstart','#simple-menu', function(){
		$('#simple-menu').attr("src",$('#simple-menu').attr("src").replace("_off.", "_on."));
	}).on('touchend', '#menu_close_area,#simple-menu', function(){
		$('#simple-menu').attr("src",$('#simple-menu').attr("src").replace("_on.", "_off."));
	});
	*/
	
	/*
	$(document).on('touchstart', '[src*="_off."]',function(){
		$(this).attr("src",$(this).attr("src").replace("_off.", "_on."));
	}).on('touchend', '[src*="_off."]',function(){
		$(this).attr("src",$(this).attr("src").replace("_on.", "_off."));
	});
	*/
	
	
	$(document).on('touchstart','.h_menu', function(){
		$(this).attr("src",$(this).attr("src").replace("_off.", "_on."));
	}).on('touchend', '.h_menu', function(){
		$(this).attr("src",$(this).attr("src").replace("_on.", "_off."));
	});
	
	
	/*------------------------------------*/
	/* サイドメニュー表示切替
	/*------------------------------------*/
	
	$('#simple-menu').sidr({
		side: 'right',
		onOpen: function(){
			sidr_flg = 0;
			$('#icon_login img').attr('src', 'image/icon/close_off.png');
			$('#icon_menu img').attr('src', 'image/icon/close_off.png');
			$('#menu_close_area').toggle();
			$('#regist_footer_area').toggle();
			$('#main').css({ position: 'fixed' });
			$('#main').css({ top: '0' });
			var a = $('#sidr').width();
			$('#main').animate({left:"-"+a+"px"},180);
		},
		onClose: function() {
			sidr_flg = 1;
			$('#main').css({ top: '0' });
			$('#main').css({ left: '0' });
			$('#main').css({ position: 'static' });
			setTimeout(function(){
			$('#icon_login img').attr('src', 'image/icon/login_off.png');
			$('#icon_menu img').attr('src', 'image/icon/menu_off.png');
			$('#menu_close_area').toggle();
			$('#regist_footer_area').toggle();
			}, 300);
		}
	});
	
	$('#simple-menu-close').sidr({
		side: 'right',
		onOpen: function() {
			sidr_flg = 0;
			$('#icon_login img').attr('src', 'image/icon/close_off.png');
			$('#icon_menu img').attr('src', 'image/icon/close_off.png');
			$('#menu_close_area').toggle();
			$('#regist_footer_area').toggle();
			$('#main').css({ position: 'fixed' });
			$('#main').css({ top: '0' });
			var a = $('#sidr').width();
			$('#main').animate({left:"-"+a+"px"},180);
		},
		onClose: function() {
			sidr_flg = 1;
			$('#main').css({ top: '0' });
			$('#main').css({ left: '0' });
			$('#main').css({ position: 'static' });
			setTimeout(function(){
			$('#icon_login img').attr('src', 'image/icon/login_off.png');
			$('#icon_menu img').attr('src', 'image/icon/menu_off.png');
			$('#menu_close_area').toggle();
			$('#regist_footer_area').toggle();
			}, 300);
		}
	});
	
	$('#header_logo').on('click',function(){
		if(sidr_flg == 0){
			location = "./";
		}
		sidr_flg = 0;
		
	});
	
	/*------------------------------------*/
	/* トップへ戻るボタン
	/*------------------------------------*/
	// scroll top
	$(window).scroll(function(){
		if($(this).scrollTop() > 300) {
			$('#scroll_top').fadeIn();
		}else if($(this).scrollTop() < 300){
			$('#scroll_top').fadeOut();
		}
	});	
	if($('#regist_footer_area').size() > 0){
		var fix_bottom_h = $('#regist_footer_area .regist_button').height();
		var fix_bottom_pos = $('#regist_footer_area .regist_button').position();
		$('#scroll_top').css({ bottom: parseInt(fix_bottom_h) + 5 + 'px' });
	}
	
	/*---------------------------------*/
	/*ログイン
	/*---------------------------------*/
	$('#loginbtn').on('click',function(){
		var i = $('#login_id').val();
		var p =  $('#login_pass').val();
		var h = Math.floor( Math.random() * 100000 );
		
		var c = 0;
		if($("#checkbox:checked").val()){
			c = 1;
		}
		
		if(i == "" || p == ""){
			$("#login_msg").html('<font style="color:#ff0000;">ＩＤまたはパスワードが入力されていません</font><br>');
			$('#login_id').focus();
			return;
		}
		
		var boylogin = $.ajax({
			url : './ajax/boy_login_ajax.php?h=' + h,
			type : 'post',
			data : {
				'user_id':i,
				'password':p,
				'mode2':'login',
				'save':c
			}
		});
		
		$.when(boylogin)
			.done(function(response){
				da = jQuery.parseJSON(response);
				
				if(da.success == "false"){
					$("#login_msg").html('<font style="color:#ff0000;">' + da.msg + '</font><br>');
					$('#login_id').focus();
					return false;
				}
				
				
				window.location.reload();
				return false;
			});
		
	});
	
});
/*
var box = $("#menu_close_area")[0];
box.addEventListener("touchstart", touchHandler, false);
function touchHandler(e){}
*/


$('#menu_close_area').toggle();

