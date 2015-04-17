function mailDeleteDisp(mail){
	var html = '';
	
	var s = "";
	var st = "";
	if(mail.stat=="2st" || mail.stat=="pat"){
		//チャット中
		s = '2SHOT';
		st = 'type_2shot';
	}
	else if(mail.stat=="2sc" || mail.stat=="pac"){
		//待機中
		s = 'Online';
		st = 'type_online';
	}
	
	var rd = '<input name="jusin_mail_id[]" type="checkbox" class="sel_cb" value="' + mail.mail_id + '" uid="'+ mail.hash +'">';
	var hr = 'mail_trash.php?mail_id=' + mail.mail_id;
	if(mail.mail_type == 'mailmag'){
		rd = '<input name="mailmag_mail_id[]" type="checkbox" class="sel_cb" value="' + mail.mail_id + '" uid="'+ mail.hash +'">';
		hr = 'mail_trash.php?mailx_id=' + mail.mail_id;
	}
	else if(mail.mail_type == 'sosin'){
		rd = '<input name="sosin_mail_id[]" type="checkbox" class="sel_cb" value="' + mail.mail_id + '" uid="'+ mail.hash +'">';
		hr = 'mail_trash.php?mail_id=' + mail.mail_id;
	}
	
	
	if(mail.jyoutai == 1){
		html += '<tr class="news">';
	}
	else{
		html += '<tr>';
	}
	html += '<td class="td_checkbox">';
	html += rd;
	html += '</td>';
	html += '<td>';
	html += '<dl>';
	html += '<a href="' + hr + '&tmpl=' + mail.mail_type +'">';
	html += '<dt><img width="49" height="37" src="./image/skeleton.png" style="background: url(http://p.macherie.tv/' + mail.img + '); background-position:center center; background-repeat:no-repeat; -o-background-size: contain; -moz-background-size: contain; -webkit-background-size: contain; background-size: contain;"></dt>';
	html += '<dd>';
	html += '<span class="name">'+ mail.nick_name +'</span>';
	if(mail.okini == 1){
		html += '<span class="icon_type"><img src="image/icon/favorite_heart_on.png" alt="ハート" ></span>';
	}
	else{
		html += '<span class="icon_type"></span>';
	}
	html += '<span class="' + st +'">' + s + '</span>';
	html += '<span class="mail_content">'+ mail.subject + '</span>';
	html += '<em>';
	html += '<span class="time">' + mail.cre_date +'</span>';
	if(mail.kyohi == 1){
		html += '<span class="rejection" uid="'+ mail.hash +'">受信拒否</span>';
	}
	html += '</em>';
	html += '</dd>';
	html += '</a>';
	html += '</dl>';
	html += '</td>';
	html += '</tr>';
	
	return html;
}

$(function(){
	var manu_flg = "close";
	var go_pg = 0;
	var refid;
	var m_chk = [];
	var s_chk = [];
	var x_chk = [];
	
	$(".btn_delete_dark_100").hide();
	$('.popup_delete').hide();
	$('.popup_undelete').hide();
	
	//check all
	$('#all').on('change', function(){
    	$('.sel_cb').prop('checked', this.checked);
		if($(".mail_list :checked").length > 0) {
			$(".btn_delete_gray_100").hide();
			$(".btn_delete_dark_100").show();
		} else {
			$(".btn_delete_dark_100").hide();
			$(".btn_delete_gray_100").show();
		}
	});
	
	$(document).on('click','.contents :checkbox',function() {
		/*
		if($(".contents :checked").length > 0) {
			$(".btn_delete_gray_100").hide();
			$(".btn_delete_dark_100").show();
		} else {
			$(".btn_delete_dark_100").hide();
			$(".btn_delete_gray_100").show();
		}
		*/
		
		//$('.popup_delete').hide();
		//$('.popup_undelete').hide();
		if($(".contents :checked").length > 0) {
			$(".popup_delete").show();
			$('.popup_undelete').show();
			$(".delete").hide();
			$(".undelete").hide();
		} else {
			
			$(".popup_delete").hide();
			$('.popup_undelete').hide();
			$(".delete").show();
			$(".undelete").show();
		}

		
	});
	
	// 受信拒否解除
	$(document).on('click','.rejection',function(e){
		e.preventDefault();
		if(confirm("受信拒否設定を解除しますか？")){
			$('#mode').val('ref');
			refid = $(this).attr('uid');
			mailMake();
		 }
		 else{
			 return;
		}
	});
	
	//削除
	$('.popup_delete').on('click',function(event){
		$('#mode').val('del');
		event.preventDefault();
		var docHeight = $(document).height();
		var scrollTop = $(window).scrollTop();
		$('.overlay_bg').show().css({'height' : docHeight});
		$('.overlay_content').css({'top': scrollTop+150+'px'});
		$('#ttl').text("削除");
		$('.text_top').text("選択されたメッセージを削除します。");
	});
	
	
	//元に戻す 2014-07-03
	$('.popup_undelete').click(function(event){
		$('#mode').val('del');
		event.preventDefault();
		var docHeight = $(document).height();
		var scrollTop = $(window).scrollTop();
        	$('.overlay_bg').show().css({'height' : docHeight});
        	$('.overlay_content').css({'top': scrollTop+100+'px'});
		$('.ttl').text("元に戻す");
		$('.text_top').text("選択されたメッセージを受信メールに戻しても");
    	});
	//end 2014-07-03//
	
	//キャンセルボタン
	$('.cancel_btn').on('click',function(){
		$('#mode').val('');
		$('.overlay_bg').hide();
	});
	
	//ＯＫボタン
	$('.ok_btn').on('click',function(){
		
		m_chk = [];
		s_chk = [];
		x_chk = [];
		
		if($('#mode').val() == "del"){
			$("[name='jusin_mail_id[]']:checked").each(function(){
				m_chk.push(this.value);
			});
			
			$("[name='sosin_mail_id[]']:checked").each(function(){
				s_chk.push(this.value);
			});
			
			$("[name='mailmag_mail_id[]']:checked").each(function(){
				x_chk.push(this.value);
			});
		}

		mailMake();

		/* チェックボックス・ボタンリセット >>> */
		$('#all').prop("checked", false);
		$(".popup_delete").hide();
		$('.popup_block').hide();
		$(".delete").show();
		$(".block").show();
		/* <<< */
		
		$('.overlay_bg').hide();
	});
	
	//メール表示
	function mailMake(){
		
		var mode = $('#mode').val();
		
		var h = Math.floor( Math.random() * 100000 );
		var mail = $.ajax({
			url : './ajax/mail_delete_ajax.php?h=' + h,
			type : 'post',
			data : {
				'pg':go_pg,
				'mode':mode,
				'm':m_chk,
				's':s_chk,
				'mx':x_chk,
				'refid':refid 
			}
		});
		
		$.when(mail)
			.done(function(response){
				var data = jQuery.parseJSON(response);
				
				if(data.mailNodeCont == 0){
					
				}
				
				var html = "";
				for(var i = 0; i < data.mailNodeCont; i++){
					html += mailDeleteDisp(data.mailNode[i]);
				}
				
				if(data.page_val == 0){
					$('#pg_back').css('visibility','hidden');
				}
				else{
					$('#pg_back').css('visibility','visible');
				}
				
				if(data.totalCont == data.pg_to){
					$('#pg_next').css('visibility','hidden');
				}
				else{
					$('#pg_next').css('visibility','visible');
				}
				
				$('#mail_disp').html(html);
				$('#pager_top').html("全"+ data.totalCont + "件中<br />" + data.pg_from + "～" + data.pg_to + "件表示");
				$('#pager_bottom').html("全"+ data.totalCont + "件中" + data.pg_from + "～" + data.pg_to + "件表示");
				
				$('#mode').val('');
		});
	}
	
	$('#pg_next').on('click',function(){
		
		if($('#all:checked').val()){
			$('#all').prop("checked", false);
		}
		
		go_pg = parseInt(go_pg) + 1;
		 mailMake();
		
	});
	
	$('#pg_back').on('click',function(){
		
		if($('#all:checked').val()){
			$('#all').prop("checked", false);
		}
		
		if(go_pg > 0){
			go_pg = parseInt(go_pg) - 1;
		}
		else{
			go_pg = 0;
		}
		mailMake();
	})
	
	mailMake();
	
});
