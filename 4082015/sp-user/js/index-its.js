$(function(){
	var page_max;
	var pos = 0;
	var max_cnt;
	var total_cnt = 0;
	var disp_cnt = 1;
	var node;
	var LMode = 'tile';
	var OMode = 0; 
	/*------------------------------------*/
	/* ���C���X���C�_�[
	/*------------------------------------*/
	$('#slider').bxSlider({
		  auto: true,
		  pager: true,
	});
	/*------------------------------------*/
	/*�X�N���[��
	/*-------------------------------------*/
	$(window).bottom({proximity: 0.05});
	$(window).bind("bottom", function() {
		 var obj = $(this);
		 if (!obj.data('loading')) {
		 	obj.data('loading', true);
		 	disp_cnt++;
		 	if(LMode=='tile'){
		 		makeTile(1);
		 	}
		 	else{
		 		makeList(1);
		 	}
		 	obj.data('loading', false);
		 }
	}); 
	/*------------------------------------*/
	/* �^�u�ؑ�
	/*------------------------------------*/
	// �p�t�H�[�}�[�Ǎ� ���X�g�\��
	$('#tabmenu_list').click(function(){
		$('.tabmenu li').removeClass('active');
		$(this).addClass('active');
		onlineList_load('list', OMode);
	});
	// �p�t�H�[�}�[�Ǎ� �^�C���\��
	$('#tabmenu_tile').click(function(){
		$('.tabmenu li').removeClass('active');
		$(this).addClass('active');
		onlineList_load('tile', OMode);
	});
	/*------------------------------------*/
	//�@�I�����C���{�^���ؑ�
	/*------------------------------------*/
	$('#btn_online').on('click', function(){
		// �I���̏ꍇ
		if($(this).hasClass('on')){
			$(this).removeClass('on');
			$('img', this).attr('src', $('img', this).attr('src').replace('_on.', '_off.'));
			onlineList_load(LMode, 0);
		// �I�t�̏ꍇ
		}else{
			$(this).addClass('on');
			$('img', this).attr('src', $('img', this).attr('src').replace('_off.', '_on.'));
			onlineList_load(LMode, 1);
		}
	});
	/*------------------------------------*/
	//�@�R�����g�S���\��
	/*------------------------------------*/
	$(document).on('click', '.comment_wrap', function(){
		var scope = $(this);
		$('.more', scope).hide();
		$('.comment.trancate', scope).hide();
		$('.comment.all', scope).show();
	});
	/*------------------------------------*/
	//�@�f�[�^���[�h
	/*------------------------------------*/
	function onlineList_load(l, o){
		//$('#tabbox_pf').html('<span class="loading on" style="margin-left:45%"><img src="image/icon/loader.gif" class="loader" style="width:40px;"></span>');
		$('#loding').css('display','block');
		LMode = l;
		OMode = o;
		pos = 0;
		total_cnt = 0;
		disp_cnt = 1;
		var h = Math.floor( Math.random() * 100000 );
		var girldata = $.ajax({
			url : './ajax/online_list-its.php?h=' + h,
			type : 'post',
			data : {
				'l':LMode,
				'o':OMode
			}
		});
		$.when(girldata)
			.done(function(response){
				var allNode = jQuery.parseJSON(response);
				if(allNode.onairNode == "notNode"){
					$('#loding').css('display','none');
					$('#tabbox_pf').html('<ul id="pafo_big">�I���G�A�[���̃p�t�H�[�}�[�����܂���B</ul>'); 
				}
				else{
					node = allNode.onairNode;
					total_cnt = allNode.onairNodeCont;
					if(LMode=='tile'){
					 	page_max = 18;
						makeTile(0);
					}
					else{
						page_max = 6;
						makeList(0);
					}
				}
			});
	}
	/*------------------------------------*/
	//�@�^�C��
	/*------------------------------------*/
	function makeTile(d){
		var max_cnt = disp_cnt * page_max;
		if(max_cnt > total_cnt){
			max_cnt = total_cnt;
		}
		var html = "";
		if(d == 0){
			html += '<ul id="pafo_big">';
		}
		for(var i = pos; i < max_cnt; i++){
			html += prTile(node[i]);
		}
		
		if(i >= total_cnt){
			$('#loding').css('display','none');
		}
		
		pos = max_cnt;
		if(d == 0){
			html += '</ul>';
		}
		if(d == 0){
			$('#tabbox_pf').html(html);
		}
		else{
			$('#pafo_big').append(html);
		}
	}
	/*------------------------------------*/
	//�@���X�g
	/*------------------------------------*/
	function makeList(d){
		var max_cnt = disp_cnt * page_max;
		if(max_cnt > total_cnt){
			max_cnt = total_cnt;
		}
		var html = '';
		for(var i = pos; i < max_cnt; i++){
			html += prList(node[i]);
		}
		
		if(i >= total_cnt){
			$('#loding').css('display','none');
		}
		
		pos = max_cnt;
		if(d == 0){
			$('#tabbox_pf').html(html);
		}
		else{
			$('#tabbox_pf').append(html);
		}
	}
	onlineList_load(LMode, OMode);
});
