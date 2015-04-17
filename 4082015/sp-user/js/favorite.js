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
		 sobj = $(this);
		 
		 if (!sobj.data('loading')) {
		 	sobj.data('loading', true);
		 	
		 	disp_cnt++;
		 	if(LMode=='tile'){
		 		
		 		if(cnt == d_cnt){
		 			pos = pos + d_cnt;
		 			if(pos < total_cnt){
						init(1);
		 				favoriteList_load(LMode, OMode);
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
		 				favoriteList_load(LMode, OMode);
		 			}
		 		}
		 		else{
		 			makeList(1);
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
		init(0);
		if(sobj != ""){
			sobj.data('loading', false);
		}
		favoriteList_load('list', OMode);
	});
	// パフォーマー読込 タイル表示
	$('#tabmenu_tile').click(function(){
		girldata.abort();
		$('.tabmenu li').removeClass('active');
		$(this).addClass('active');
		init(0);
		if(sobj != ""){
			sobj.data('loading', false);
		}
		favoriteList_load('tile', OMode);
	});
	/*------------------------------------*/
	//　オンラインボタン切替
	/*------------------------------------*/
	$('#btn_online').on('click', function(){
		girldata.abort();
		init(0);
		if(sobj != ""){
			sobj.data('loading', false);
		}
		// オフの場合
		if($(this).hasClass('on')){
			$(this).removeClass('on');
			$('img', this).attr('src', $('img', this).attr('src').replace('_on.', '_off.'));
			favoriteList_load(LMode, 0);
		// オンの場合
		}
		else{
			$(this).addClass('on');
			$('img', this).attr('src', $('img', this).attr('src').replace('_off.', '_on.'));
			favoriteList_load(LMode, 1);
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
	
	function favoriteList_load(l, o){
		$('#loding').css('display','block');
		LMode = l;
		OMode = o;
		var h = Math.floor( Math.random() * 100000 );
		girldata = $.ajax({
			url : './ajax/favorite_list.php?h=' + h,
			type : 'post',
			data : {
				'l':LMode,
				'o':OMode,
				'pos':pos
			}
		});
		$.when(girldata)
			.done(function(response){
				var allNode = "";
				allNode = jQuery.parseJSON(response);
				if(allNode.onairNode == "notNode"){
					$('#loding').css('display','none');
					$('#tabbox_pf').html('');
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
				}
				if(sobj != ""){
					sobj.data('loading', false);
				}
				return false;
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
		return false;
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
		return false;
	}
	
	init(0);
	favoriteList_load(LMode, OMode);
});
