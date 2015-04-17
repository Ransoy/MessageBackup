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
	/* メインスライダー
	/*------------------------------------*/
	$('#slider').bxSlider({
		  auto: true,
		  pager: true,
	});
	/*------------------------------------*/
	/*スクロール
	/*-------------------------------------*/
	//$(window).bottom({proximity: 350});
	$('#more_view').click("bottom", function() {
		 var obj = $(this);
		 if (!obj.data('loading')) {
			$('#loding').css('display','block');
			$('#more_view').css('display','none');
			obj.data('loading', true);
		 	disp_cnt++;
		 	if(LMode=='tile'){
		 		makeTile(1);
		 	}
		 	else{
		 		makeList(1);
		 	}
			$('#loding').css('display','none');
		 	obj.data('loading', false);
		 }
	}); 
	/*------------------------------------*/
	/* タブ切替
	/*------------------------------------*/
	// パフォーマー読込 リスト表示
	$('#tabmenu_list').click(function(){
		$('#tabbox_pf').html('');
		$('.tabmenu li').removeClass('active');
		$(this).addClass('active');
		onlineList_load('list', OMode);
	});
	// パフォーマー読込 タイル表示
	$('#tabmenu_tile').click(function(){
		$('#tabbox_pf').html('');
		$('.tabmenu li').removeClass('active');
		$(this).addClass('active');
		onlineList_load('tile', OMode);
	});
	/*------------------------------------*/
	//　オンラインボタン切替
	/*------------------------------------*/
	$('#btn_online').on('click', function(){
		// オンの場合
		if($(this).hasClass('on')){
			$(this).removeClass('on');
			$('img', this).attr('src', $('img', this).attr('src').replace('_on.', '_off.'));
			onlineList_load(LMode, 0);
		// オフの場合
		}else{
			$(this).addClass('on');
			$('img', this).attr('src', $('img', this).attr('src').replace('_off.', '_on.'));
			onlineList_load(LMode, 1);
		}
	});
	/*------------------------------------*/
	//　更新ボタン
	/*------------------------------------*/
	$('#btn_koshin').on('click', function(){
		onlineList_load(LMode, OMode);
	});
	/*------------------------------------*/
	//　コメント全文表示
	/*------------------------------------*/
	$(document).on('click', '.comment_wrap', function(){
		var scope = $(this);
		$('.more', scope).hide();
		$('.comment.trancate', scope).hide();
		$('.comment.all', scope).show();
	});
	/*------------------------------------*/
	//　データロード
	/*------------------------------------*/
	function onlineList_load(l, o){
		//$('#tabbox_pf').html('<span class="loading on" style="margin-left:45%"><img src="image/icon/loader.gif" class="loader" style="width:40px;"></span>');
		$('#loding').css('display','block');
		$('#more_view').css('display','none');
		LMode = l;
		OMode = o;
		pos = 0;
		total_cnt = 0;
		disp_cnt = 1;
		var h = Math.floor( Math.random() * 100000 );
		var girldata = $.ajax({
			url : './ajax/online_list_meeting.php?h=' + h,
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
					$('#more_view').css('display','none');
					$('#tabbox_pf').html('<ul id="pafo_big">オンエアー中のパフォーマーがいません。</ul>'); 
				}
				else{
					node = allNode.onairNode;
					total_cnt = allNode.onairNodeCont;
					if(LMode=='tile'){
					 	page_max = 24;
						makeTile(0);
					}
					else{
						page_max = 18;
						makeList(0);
					}
					$('#loding').css('display','none');
				}
			});
	}
	/*------------------------------------*/
	//　タイル
	/*------------------------------------*/
	function makeTile(d){
		//var max_cnt = disp_cnt * page_max;
		var max_cnt = total_cnt;
/*
		if(max_cnt > total_cnt || disp_cnt > 1){
			max_cnt = total_cnt;
		}
*/
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
		if(max_cnt >= total_cnt){
			$('#more_view').css('display','none');
		}else{
			$('#more_view').css('display','block');
		}
	}
	/*------------------------------------*/
	//　リスト
	/*------------------------------------*/
	function makeList(d){
		//var max_cnt = disp_cnt * page_max;
		var max_cnt = total_cnt;
/*
		if(max_cnt > total_cnt || disp_cnt > 1){
			max_cnt = total_cnt;
		}
*/
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
		if(max_cnt >= total_cnt){
			$('#more_view').css('display','none');
		}else{
			$('#more_view').css('display','block');
		}
	}
	onlineList_load(LMode, OMode);
});
