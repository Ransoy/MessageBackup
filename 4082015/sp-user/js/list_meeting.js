$(function(){
	
	var page_max;
	var pos;
	var cnt;
	var max_cnt;
	var total_cnt;
	var disp_cnt;
	var node;
	var LMode = 'tile';
	var OMode = 0;
	var btn;
	var d_cnt;
	var sobj = "";
	var girldata = "";
	
	$('select').attr('class', 'w_100');
	$('#search_icon').css('display',' none');
	$('#tabmenu_pf').css('display',' none');
	
	function init(m){
		if(m == 0){
			pos = 0;
		}
		cnt = 0;
		max_cnt = 0;
		disp_cnt = 1;
		node = "";
	}
	
	/*------------------------------------*/
	/*スクロール
	/*-------------------------------------*/
	$(window).bottom({proximity: 350});
	$(window).bind("bottom", function() {
		 if(btn == 1){
			 sobj = $(this);
			 
			 if (!sobj.data('loading')) {
			 	sobj.data('loading', true);
			 	disp_cnt++;
			 	if(LMode=='tile'){
			 		if(cnt == d_cnt){
			 			pos = pos + d_cnt;
			 			if(pos < total_cnt){
			 				init(1);
			 				searchList_load(LMode, OMode);
			 			}
			 		}
			 		else{
			 			makeTile(1);
			 		}
			 	}
			 	else{
			 		if(cnt == d_cnt){
			 			pos = pos + d_cnt;
			 			if(pos < total_cnt){
							init(1);
			 				searchList_load(LMode, OMode);
			 			}
			 		}
			 		else{
			 			makeList(1);
			 		}
			 	}
			 }
		 }
	}); 
	
	/*------------------------------------*/
	/* タブ切替
	/*------------------------------------*/	
	// パフォーマー読込 リスト表示
	$('#tabmenu_list').click(function(){
		girldata.abort();
		$('.tabmenu li').removeClass('active');
		$(this).addClass('active');
		if(btn == 1){
			init(0);
			if(sobj != ""){
				sobj.data('loading', false);
			}
			searchList_load('list', OMode);
		}
		else{
			 LMode = 'list';
		}
	});
	// パフォーマー読込 タイル表示
	$('#tabmenu_tile').click(function(){
		girldata.abort();
		$('.tabmenu li').removeClass('active');
		$(this).addClass('active');
		if(btn == 1){
			init(0);
			if(sobj != ""){
				sobj.data('loading', false);
			}
			searchList_load('tile', OMode);
		}
		else{
			 LMode = 'tile';
		}
	});
	/*------------------------------------*/
	//　オンラインボタン切替
	/*------------------------------------*/
	$('#btn_online').on('click', function(){
		girldata.abort();
		init(0);
		if(btn == 1){
			$('#se_success').removeClass('on');
			$('#se_loading').addClass('on');
			if(sobj != ""){
				sobj.data('loading', false);
			}
		}
		// オフの場合
		if($(this).hasClass('on')){
			$(this).removeClass('on');
			$('img', this).attr('src', $('img', this).attr('src').replace('_on.', '_off.'));
			if(btn == 1){
				searchList_load(LMode, 0);
			}
			else{
				OMode = 0;
			}
		// オンの場合
		}
		else{
			$(this).addClass('on');
			$('img', this).attr('src', $('img', this).attr('src').replace('_off.', '_on.'));
			if(btn == 1){
				searchList_load(LMode, 1);
			}
			else{
				OMode = 1;
			}
		}
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
	//検索画面へ
	/*------------------------------------*/
	$('.re_search_btn').on('click',function(){
		$('#loding').css('display','none');
		
		$('.go_top').css('display','block');
		$('.re_list').css('display',' none');
		
		$('#btn_search').css('display',' none');
		$('#btn_online').css('display',' none');
		$('#tabmenu_pf').css('display',' none');
		
		var bo = $('#btn_online');
		bo.removeClass('on');
		$('img', bo).attr('src', $('img', bo).attr('src').replace('_on.', '_off.'));
		
		if(OMode == 0){
			 $('input[name=onair]').prop('checked',  false);
		}
		else{
			 $('input[name=onair]').prop('checked', true);
		}
		
		btn = 0;
		init(0);
		//$('#nick_name').val('');
		//$("select[name='age']").val(99);
		//$("select[name='area']").val(99);
		//$("select[name='job']").val(99);
		//$("select[name='category']").val(99);
		//$("select[name='type']").val(99);
		//$("select[name='appear_time']").val(99);
		//$('#newface').attr("checked", false);
		//$('#mic').attr("checked", false);
		$('#se_loading').removeClass('on');
		$('#se_success').removeClass('on');
		$('#tabbox_pf').html('').css('display','none');
		$('#serch_box').css('display','block');
		
	});
	/*---------------------------------------*/
	//検索ボタン
	/*---------------------------------------*/
	$('#search_btn').on('click',function(){
		$('#se_loading').addClass('on');
		$('html,body').scrollTop(0);
		$('#search_icon').css('display',' block');
		$('#tabmenu_pf').css('display',' block');
		
		$('#btn_search').css('display',' block');
		$('#btn_online').css('display',' block');
		
		$('.go_top').css('display',' none');
		$('.re_list').css('display',' block');
		
		btn = 1;
		d_cnt = 0;
		total_cnt = 0;
		init(0);
		if($('#onair').prop('checked')){
			OMode = 1;
		}
		else{
			OMode = 0;
		}
		searchList_load(LMode, OMode);
	});
	
	function searchList_load(l, o){
		$('#loding').css('display','block');
		LMode = l;
		OMode = o;
		var nick_name = encodeURI($('#nick_name').val());
		var age = $('#age').val();
		var area = $('#area').val();
		var job = $('#job').val();
		var category = $('#category').val();
		var type = $('#type').val();
		var appear_time = $('#appear_time').val();
		var newface = 0;
		if($('#newface').prop('checked')){
			newface = 1;
		}
		var mic = 99;
		if($('#mic').prop('checked')){
			mic = 1;
		}
		
		var h = Math.floor( Math.random() * 100000 );
		girldata = $.ajax({
			url : './ajax/search_list_meeting.php?h=' + h,
			type : 'post',
			data : {
				'l':LMode,
				'o':OMode,
				'nick_name':nick_name,
				'age':age,
				'area':area,
				'job':job,
				'category':category,
				'type':type,
				'appear_time':appear_time,
				'newface':newface,
				'mic':mic,
				'pos':pos
			}
		});
		$.when(girldata)
			.done(function(response){
				var allNode = "";
				allNode = jQuery.parseJSON(response);
				if(allNode.onairNode == "notNode"){
					$('#loding').css('display','none');
					$('#tabbox_pf').html('<ul id="pafo_big">該当する検索結果はありません。</ul>');
					$('#tabbox_pf').css('display','block');
				}
				else{
					node = allNode.onairNode;
					d_cnt = allNode.onairNodeCont;
					total_cnt = allNode.totalCont;
					if(LMode=='tile'){
					 	page_max = 18;
					 	if(pos == 0){
							makeTile(0);
						}
						else{
							makeTile(1);
						}
					}
					else{
						page_max = 6;
					 	if(pos == 0){
							makeList(0);
						}
						else{
							makeList(1);
						}
					}
					$('#se_success').html(total_cnt+"人ヒットしました").addClass('on');
				}
				$('#se_loading').removeClass('on');
				$('#serch_box').css('display','none');
				if(sobj != ""){
					sobj.data('loading', false);
				}
				
				var bo = $('#btn_online');
				if(OMode != 1){
					bo.removeClass('on');
					$('img', bo).attr('src', $('img', bo).attr('src').replace('_on.', '_off.'));
				}
				else{
					bo.addClass('on');
					$('img', bo).attr('src', $('img', bo).attr('src').replace('_off.', '_on.'));
				}
			});
	}
	
	/*-------------------------------*/
	//　タイル
	/*-------------------------------*/
	function makeTile(d){
		max_cnt = disp_cnt * page_max;
		if(max_cnt > d_cnt){
			max_cnt = d_cnt;
		}
		var html = "";
		if(d == 0){
			html += '<ul id="pafo_big">';
		}
		for(var i = cnt; i < max_cnt; i++){
			html += prTile(node[i]);
		}
		
		if(i >= total_cnt){
			$('#loding').css('display','none');
		}
		
		cnt = i;
		if(d == 0){
			html += '</ul>';
		}
		if(d == 0){
			$('#tabbox_pf').html(html);
		}
		else{
			$('#pafo_big').append(html);
		}
		$('#tabbox_pf').css('display','block');
		if(d == 1){
			sobj.data('loading', false);
		}
	}
	/*-------------------------------*/
	//　リスト
	/*-------------------------------*/
	function makeList(d){
		max_cnt = disp_cnt * page_max;
		if(max_cnt > d_cnt){
			max_cnt = d_cnt;
		}
		var html = '';
		
		for(var i = cnt; i < max_cnt; i++){
			html += prList(node[i]);
		}
		
		if(i >= total_cnt){
			$('#loding').css('display','none');
		}
		
		cnt = i;
		if(d == 0){
			$('#tabbox_pf').html(html);
		}
		else{
			$('#tabbox_pf').append(html);
		}
		$('#tabbox_pf').css('display','block');
		if(d == 1){
			sobj.data('loading', false);
		}
		
	}
	
	//$('#search_btn').trigger("click");
});
