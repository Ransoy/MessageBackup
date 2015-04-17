function sendDisp(mail){
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
	
	var rd = '<input name="mail_id[]" type="checkbox" class="sel_cb" value="' + mail.mail_id + '" uid="'+ mail.hash +'">';
	var hr = 'send_detail.php?mail_id=' + mail.mail_id;
//	if(mail.merumaga == 1){
//		rd = '<input name="mailx_id[]" type="checkbox" class="sel_cb" value="' + mail.mail_id + '" uid="'+ mail.hash +'">';
//		hr = 'mail_detail.php?mailx_id=' + mail.mail_id;
//	}
	
//	if(mail.jyoutai == 1){
//		html += '<tr class="news">';
//	}
//	else{
		html += '<tr>';
//	}
	html += '<td class="td_checkbox">';
	html += rd;
	html += '</td>';
	html += '<td>';
	html += '<dl>';
	html += '<a href="' + hr +'">';
	html += '<dt><img width="49" height="37" src="./image/skeleton.png" style="background: url(http://p.macherie.tv/' + mail.img + '); background-position:center center; background-repeat:no-repeat; -o-background-size: contain; -moz-background-size: contain; -webkit-background-size: contain; background-size: contain;"></dt>';
	html += '<dd>';
	html += '<span class="name">'+ mail.nick_name +'</span>';
	if(mail.okini == 1){
		html += '<span class="icon_type">&nbsp;<img src="image/icon/favorite_heart_on.png" alt="ハート" ></span>';
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
	var go_pg = 0;
	var mail_chk = [];
	var mailx_chk = [];
	var uid = [];
	var refid;
	var menu_flg = "close";
	var refine_flg = "close";
	var nick_name = "";
	var sort_type = 0;
	var refinement_type = 0;
	
	$('.popup_delete').hide();
	$('.popup_block').hide();
	
	//check all
	$('#all').on('change', function(){
    	$('.sel_cb').prop('checked', this.checked);
    	if($(".mail_list :checked").length > 0){
			$(".popup_delete").show();
			$('.popup_block').show();
			$(".delete").hide();
			$(".block").hide();
		}
		else{
			$(".popup_delete").hide();
			$('.popup_block').hide();
			$(".delete").show();
			$(".block").show();
		}
	});
	
	$(document).on('click','.mail_list :checkbox',function() {
		if($(".mail_list :checked").length > 0) {
			$(".popup_delete").show();
			$('.popup_block').show();
			$(".delete").hide();
			$(".block").hide();
		} else {
			if($('#all:checked').val()){
				$('#all').prop("checked", false);
			}
			$(".popup_delete").hide();
			$('.popup_block').hide();
			$(".delete").show();
			$(".block").show();
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
	
	//受信拒否
	$('.popup_block').on('click',function(event){
		$('#mode').val('deny');
		event.preventDefault();
		var docHeight = $(document).height();
		var scrollTop = $(window).scrollTop();
		$('.overlay_bg').show().css({'height' : docHeight});
		$('.overlay_content').css({'top': scrollTop+100+'px'});
		$('#ttl').text("受信拒否");
		$('.text_top').text("今後、このパフォーマーからのメールを受信拒否します。");
    });
	
	// 受信拒否解除
	$(document).on('click','.rejection',function(e){
		e.preventDefault();
		if(confirm("受信拒否設定を解除しますか？")){
			$('#mode').val('ref');
			
			refid = $(this).attr('uid');
			
			mailMake();
			
		 }else{
			 return;
		}
	});
	
	//並び替え
	$('.mail_sort').on('click',function(){
		
		$('.mail_sort').removeClass('on');
		
		$(this).addClass('on');
		$('#mail_sort_type').val($(this).attr('data-type'));
		
	});
	
	
	//絞り込み
	$('.mail_refinement').on('click',function(){
		$('.mail_refinement').removeClass('on');
		
		$(this).addClass('on');
		$('#mail_refinement_type').val($(this).attr('data-type'));
	});
	
	//検索
	$('.search_start').on('click',function(){
		go_pg = 0;
		
		nick_name = $('#search_name').val();
		sort_type = $('#mail_sort_type').val();
		refinement_type = $('#mail_refinement_type').val();
		
		mailMake();
		
	});
	
	
	//キャンセルボタン
	$('.cancel_btn').click(function(){
    	$('#mode').val('');
        $('.overlay_bg').hide();
	});
	
	//ＯＫボタン
	$('.ok_btn').on('click',function(){
		
		mail_chk = [];
		mailx_chk = [];
		uid = [];
		
		$("[name='mail_id[]']:checked").each(function(){
			if($('#mode').val() == "del"){
				mail_chk.push(this.value);
			}
			else if($('#mode').val() == "deny"){
				uid.push( $(this).attr('uid'));
			}
		});
		$("[name='mailx_id[]']:checked").each(function(){
			if($('#mode').val() == "del"){
				mailx_chk.push(this.value);
			}
			else if($('#mode').val() == "deny"){
				uid.push($(this).attr('uid'));
			}
		});
		
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
	
	function mailMake(){
		
		var id = "";
		var mode = $('#mode').val();
		var nk = encodeURI(nick_name);
		
		var h = Math.floor( Math.random() * 100000 );
		var mail = $.ajax({
			url : './ajax/send_ajax.php?h=' + h,
			type : 'post',
			data : {
				'id':id,
				'pg':go_pg,
				'mode':mode,
				'm':mail_chk,
				'mx':mailx_chk,
				'uid':uid,
				'refid':refid,
				'nk':nk,
				'st':sort_type,
				'rt':refinement_type
			}
		});
		
		$.when(mail)
			.done(function(response){
				var data = jQuery.parseJSON(response);
				
				if(data.mailNodeCont == 0){
					
				}
				
				var html = "";
				for(var i = 0; i < data.mailNodeCont; i++){
					html += sendDisp(data.mailNode[i],data.page_val,id);
				}
				
				if(data.page_val == 0){
					$('.pg_back').css('visibility','hidden');
				}
				else{
					$('.pg_back').css('visibility','visible');
				}
				
				if(data.totalCont == data.pg_to){
					$('.pg_next').css('visibility','hidden');
				}
				else{
					$('.pg_next').css('visibility','visible');
				}
				
				$('#mail_disp').html(html);
				$('#pager_top').html("全"+ data.totalCont + "件中<br />" + data.pg_from + "～" + data.pg_to + "件表示");
				$('#pager_bottom').html("全"+ data.totalCont + "件中" + data.pg_from + "～" + data.pg_to + "件表示");
				
				$('#mode').val('');
		});
	}
	
	$('.pg_next').on('click',function(){
		
		if($('#all:checked').val()){
			$('#all').prop("checked", false);
		}
		
		go_pg = parseInt(go_pg) + 1;
		 mailMake();
		
	});
	
	$('.pg_back').on('click',function(){
		
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
