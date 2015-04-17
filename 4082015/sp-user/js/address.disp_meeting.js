function addressDisp(address){
	var html = '';
	
	var s = "OFFLINE";
	var st = "type_offline";
	if(address.stat=="2st" || address.stat=="pat"){
		//�`���b�g��
		s = '2SHOT';
		st = 'type_2shot';
	}
	else if(address.stat=="2sc" || address.stat=="pac"){
		//�ҋ@��
		s = 'Online';
		st = 'type_online';
		if(address.ma == 1) {
			ma = '�҂����킹';
			mat = 'type_online_machiawase';
		}
	}
	
	html += '<tr>';
	html += '<td class="td_checkbox"><input type="checkbox" value="' + address.hash +'" name="fid[]"></td>';
	html += '<td>';
	html += '<a href="mail_new_creation_meeting.php?sid=' + address.hash + '">';
	html += '<p class="mail_content_ttl">';
	html += '<span class="img_profile">';
	html += '<img width="49" height="37" src="./image/skeleton.png" style="background: url(http://p.macherie.tv/' + address.img + '); background-position:center center; background-repeat:no-repeat; -o-background-size: contain; -moz-background-size: contain; -webkit-background-size: contain; background-size: contain;">';
	html += '</span>';
	html += '<span class="name">' + address.nick_name + '</span>';
	if(address.okini == 1){
		html += '<span class="icon_type del_fava" fid="' + address.hash + '">&nbsp;<img src="image/icon/favorite_heart_on.png" alt="�n�[�g" ></span>';
	}
	else{
		//html += '<span class="icon_type add_fava" fid="' + address.hash + '">&nbsp;<img src="image/icon/favorite_heart_off.png" alt="�n�[�g" ></span>';
		html += '<span class="icon_type">&nbsp;</span>';
	}
	html += '</p>';
	html += '<span class="' + st + '">' + s +'</span>';
	if(address.ma == 1) {
		html += '&nbsp;&nbsp;<span class="' + mat + '">' + ma +'</span>';
	}
	html += '</a>';
	html += '</td>';
	html += '<td class="next_arrow"><img src="image/icon/header_next_arrow.png" class=""></td>';
	html += '</tr>';
	
	return html;
}

$(function(){
	var go_pg = 0;
	var nick_name = "";
	var sort_type = 0;
	var refinement_type = 0;
	var fid = [];
	var menu_flg = "close";
	var refine_flg = "close";
	
	$('.popup_delete').hide();
	$('.popup_block').hide();
	
	$(document).on('click','.contents :checkbox',function() {
		if($(".address_list :checked").length > 0) {
			$(".popup_delete").show();
			$('.popup_block').show();
			$(".delete").hide();
			$(".block").hide();
		} else {
			$(".popup_delete").hide();
			$('.popup_block').hide();
			$(".delete").show();
			$(".block").show();
		}
	});
	
	//�폜
	$('.popup_delete').on('click',function(event){
		$('#mode').val('del');
		event.preventDefault();
		var docHeight = $(document).height();
		var scrollTop = $(window).scrollTop();
        $('.overlay_bg').show().css({'height' : docHeight});
        $('.overlay_content').css({'top': scrollTop+150+'px'});
		$('.ttl2').text("�폜");
		$('.text_top').text("�I�����ꂽ�p�t�H�[�}�[���폜���܂��B");
    });
	
	//��M����
	$('.popup_block').click(function(event){
		$('#mode').val('deny');
		event.preventDefault();
		var docHeight = $(document).height();
		var scrollTop = $(window).scrollTop();
		$('.overlay_bg').show().css({'height' : docHeight});
		$('.overlay_content').css({'top': scrollTop+100+'px'});
		$('.ttl2').text("��M����");
		$('.text_top').text("����A���̃p�t�H�[�}�[����̃��[������M���ۂ��܂��B");
    });
	
	// ��M���ۉ���
	$(document).on('click','.rejection',function(e){
		e.preventDefault();
		if(confirm("��M���ېݒ���������܂����H")){
			$('#mode').val('ref');
			
			refid = $(this).attr('uid');
			
			mailMake();
			
		 }else{
			 return;
		}
	});
	
	//���C�ɓ������
	$(document).on('click','.del_fava',function(e){
		e.preventDefault();
		$('#mode').val('back_address');
		
		fid = [];
		fid.push($(this).attr('fid'));
		
		mailMake();
	});
	
	//���C�ɓ���ǉ�
	$(document).on('click','.add_fava',function(e){
		e.preventDefault();
		$('#mode').val('upd_fav');
		
		fid = [];
		fid.push($(this).attr('fid'));
		
		mailMake();
	});
	
	//���ёւ�
	$('.mail_sort').on('click',function(){
		
		$('.mail_sort').removeClass('on');
		
		$(this).addClass('on');
		$('#mail_sort_type').val($(this).attr('data-type'));
		
	});
	
	//�i�荞��
	$('.mail_refinement').on('click',function(){
		$('.mail_refinement').removeClass('on');
		
		$(this).addClass('on');
		$('#mail_refinement_type').val($(this).attr('data-type'));
	});
	
	//����
	$('.search_start').on('click',function(){
		go_pg = 0;
		
		nick_name = $('#search_name').val();
		sort_type = $('#mail_sort_type').val();
		refinement_type = $('#mail_refinement_type').val();
		
		mailMake();
		
	});
	
	//�L�����Z���{�^��
	$('.cancel_btn').click(function(){
		$('#mode').val('');
		$('.overlay_bg').hide();
	});
	
	//�n�j�{�^��
	$('.ok_btn').on('click',function(){
		
		fid = [];
		
		$("[name='fid[]']:checked").each(function(){
			fid.push(this.value);
		});
		
		mailMake();

		/* �`�F�b�N�{�b�N�X�E�{�^�����Z�b�g >>> */
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
		var address = $.ajax({
			url : './ajax/mail_new_ajax_meeting.php?h=' + h,
			type : 'post',
			data : {
				'pg':go_pg,
				'mode':mode,
				'st':sort_type,
				'hash':fid,
				'nk':nk,
				'rt':refinement_type 
			}
		});
		
		$.when(address)
			.done(function(response){
				var data = jQuery.parseJSON(response);
				
				if(data.addressNodeCont == 0){
					
				}
				
				var html = "";
				for(var i = 0; i < data.addressNodeCont; i++){
					html += addressDisp(data.addressNode[i]);
				}
				
				if(data.page_val == 0){
					$('#top_pg_back').css('visibility','hidden');
					$('#bottom_pg_back').css('visibility','hidden');
				}
				else{
					$('#top_pg_back').css('visibility','visible');
					$('#bottom_pg_back').css('visibility','visible');
				}
				
				if(data.totalCont == data.pg_to){
					$('#top_pg_next').css('visibility','hidden');
					$('#bottom_pg_next').css('visibility','hidden');
				}
				else{
					$('#top_pg_next').css('visibility','visible');
					$('#bottom_pg_next').css('visibility','visible');
				}
				
				$('#address_disp').html(html);
				$('#pager_top').html("�S"+ data.totalCont + "����<br />" + data.pg_from + "�`" + data.pg_to + "���\��");
				$('#pager_bottom').html("�S"+ data.totalCont + "����" + data.pg_from + "�`" + data.pg_to + "���\��");
				
				if(mode == "upd_fav"){
					alert("���C�ɓ���ɒǉ����܂����B");
				}
				else if(mode == "back_address"){
					alert("���C�ɓ�����������܂����B");
				}
				
				
				$('#mode').val('');
		});
	}
	
	$('#top_pg_next,#bottom_pg_next').on('click',function(){
		
		go_pg = parseInt(go_pg) + 1;
		 mailMake();
		
	});
	
	$('#top_pg_back,#bottom_pg_back').on('click',function(){
		
		if(go_pg > 0){
			go_pg = parseInt(go_pg) - 1;
		}
		else{
			go_pg = 0;
		}
		mailMake();
	});
	
	mailMake();
	
});
