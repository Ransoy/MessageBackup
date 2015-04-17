function prTile(girl){
	var html = '';
	
	//------------------------------
	//オンエアー状態
	onairSt = '<div class="online"><a href="#"><img src="image/icon/performer_online.png"></a></div>';
	if(girl.st=="2st" || girl.st=="pat"){
		//待機中				
		onairSt = '<div class="online-banner banner-style">Online</div>';
		if(girl.ma == 1) {
			onairSt = '<div class="status-position">'+onairSt+'<div class="machiawase-banner banner-style">待ち合わせ</div></div><!-- / .status-position -->';
		}else {
			onairSt = '<div class="status-position">'+onairSt+'</div><!-- / .status-position -->';
		}
		onairSt = onairSt+'</div><!-- / .status-wrap --></div>';
	}
	else if(girl.st=="2sc" || girl.st=="pac"){
		//チャット中
		onairSt = '<div class="status-position"><div class="chat-banner banner-style">2shot</div></div>';
	}
	else{
		//オフライン
		onairSt = '';
	}
	
	//------------------------------
	//マイク
	micIcon = '<div class="mike"></div>';
	if(girl.vo==1){
		micIcon = '<div class="mike"><img src="image/icon/performer_mike.png"></div>';
	}
	
	//------------------------------
	//新人
	debut = '';
	if(girl.nw==2){
		debut = '<div class="debut"><img src="image/icon/performer_debut.png"></div>';
	}
	
	var ru = "";
	if(girl.ru != ""){
		ru = '&ru='+girl.ru;
	}
	
	html += '<li class="pafo_big_img">';
	html += '<div class="image_area">';
	html += '<div class="status-wrap"><a href="shicho.php?id=' + girl.hs + ru + '"><img src="./image/skeleton.png" style="background: url(http://c.macherie.tv/imgs/op/320x240/'+girl.ph+'); background-position:center center; background-repeat:no-repeat; -o-background-size: contain; -moz-background-size: contain; -webkit-background-size: contain; background-size: contain;"></a>';
	html += onairSt;
	html += '</div>';
	html += debut;
	html += '<p class="area"><em>'+girl.ar+'<em></p><p class="age"><em>'+girl.ag+'<em></p>';
	html += micIcon;
	html += '</li>';
	
	return html;
}

function prList(girl){
	var html = '';
	
	//------------------------------
	//オンエアー状態
	onairSt = '<div class="icon"><img src="image/icon/view_detail_online.png"></div>';
	if(girl.st=='2st' || girl.st=='pat'){
		//待機中
		onairSt = '<div class="online-banner banner-style">Online</div>';
	}
	else if(girl.st=='2sc' || girl.st=='pac'){
		//チャット中
		onairSt = '<div class="chat-banner　banner-style">2shot</div>'
	}
	else{
		//オフライン
		onairSt = '';
	}
	//------------------------------
	//マイク
	micIcon = '';
	if(girl.vo==1){
		micIcon = '<div class="mike-icon"><img src="image/icon/performer_mike.png"></div>';
	}
	//------------------------------
	//新人
	debut = '';
	if(girl.nw==2){
		debut = '<div class="debut-banner"><img src="image/icon/performer_debut.png"></div>';
	}
	
	//
	machiawase = '';
	if(girl.ma==1){
		// 待ち合わせ
		machiawase = '<div class="machiawase-banner banner-style">待ち合わせ</div>';
	}
	
	
	var ru = "";
	if(girl.ru != ""){
		ru = '&ru='+girl.ru;
	}
	
	//HTML
	html += '<ul id="pafo_detail">';
	html += '<li class="detail_img">';
	html += '<a href="shicho.php?id=' + girl.hs + ru +'"><img src="./image/skeleton.png" style="background: url(http://c.macherie.tv/imgs/op/320x240/'+girl.ph+'); background-position:center center; background-repeat:no-repeat; -o-background-size: contain; -moz-background-size: contain; -webkit-background-size: contain; background-size: contain;"></a>';
	html += debut;
	html += onairSt;
	html += machiawase;
	html += micIcon;
	html += '</li>';
	html += '<li class="status">';
	html += '<p class="name">'+girl.cn+'</p>';
	html += '<p class="age age_area">'+girl.ag+'</p>';
	html += '<p class="age_area">'+girl.ar+'</p>';
	html += '<p class="status_detail">身長：<b>'+girl.hi+'</b></p>';
	html += '<p class="status_detail">カップ：<b>'+girl.bu+'</b></p>';
	html += '<p class="status_detail">体型：<b>'+girl.ty+'</b></p>';
	html += '<p class="status_detail">血液：<b>'+girl.bt+'</b></p>';
	html += '<div class="comment_wrap cf" onclick="">';
	
	if(girl.moto == 1){
		html += '<p class="comment trancate">'+girl.cm+'</p>';
		html += '<span class="more">もっと読む</span>';
		html += '<p class="comment all">'+girl.cm_more+'</p>';
	}
	else{
		html += '<p class="comment trancate">'+girl.cm_more+'</p>';
		html += '<p class="comment all">'+girl.cm_more+'</p>';
	}
	html += '</div>';
	html += '</li>';
	html += '<li class="line"></li>';
	html += '</ul>';
	
	return html;
}
