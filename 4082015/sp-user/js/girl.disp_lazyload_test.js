function prTile(girl){
	var html = '';

	//------------------------------
	//�I���G�A�[���
	onairSt = '<div class="online"><a href="#"><img src="image/icon/performer_online.png"></a></div>';
	if(girl.st=="2st" || girl.st=="pat"){
		//�ҋ@��
		onairSt = '<div class="online"><img src="image/icon/performer_online.png"></div>';
	}
	else if(girl.st=="2sc" || girl.st=="pac"){
		//�`���b�g��
		onairSt = '<div class="twoshot"><img src="image/icon/performer_2shot.png"></div>';
	}
	else{
		//�I�t���C��
		onairSt = '';
	}

	//------------------------------
	//�}�C�N
	micIcon = '<div class="mike"></div>';
	if(girl.vo==1){
		micIcon = '<div class="mike"><img src="image/icon/performer_mike.png"></div>';
	}

	//------------------------------
	//�V�l
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
	// html += '<a href="shicho.php?id=' + girl.hs + ru + '"><img                                                                                     src="./image/skeleton.png" style="background: url(http://c.macherie.tv/imgs/op/320x240/'+girl.ph+'); background-position:center center; background-repeat:no-repeat; -o-background-size: contain; -moz-background-size: contain; -webkit-background-size: contain; background-size: contain;"></a>';
	html +=    '<a href="shicho.php?id=' + girl.hs + ru + '"><img class="lazy" data-background="http://c.macherie.tv/imgs/op/320x240/' + girl.ph + '" src="./image/skeleton.png"                                                                    style="background-position:center center; background-repeat:no-repeat; -o-background-size: contain; -moz-background-size: contain; -webkit-background-size: contain; background-size: contain;" alt="' + girl.cn + '"></a>';
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
	//�I���G�A�[���
	onairSt = '<div class="icon"><img src="image/icon/view_detail_online.png"></div>';
	if(girl.st=='2st' || girl.st=='pat'){
		//�ҋ@��
		onairSt = '<div class="icon"><img src="image/icon/view_detail_online.png"></div>';
	}
	else if(girl.st=='2sc' || girl.st=='pac'){
		//�`���b�g��
		onairSt = '<div class="icon"><img src="image/icon/view_detail_2shot.png"></div>';
	}
	else{
		//�I�t���C��
		onairSt = '';
	}
	//------------------------------
	//�}�C�N
	micIcon = '';
	if(girl.vo==1){
		micIcon = '<div class="mike"><img src="image/icon/performer_mike.png"></div>';
	}
	//------------------------------
	//�V�l
	debut = '';
	if(girl.nw==2){
		debut = '<div class="icon"><img src="image/icon/view_detail_debut.png"></div>';
	}

	var ru = "";
	if(girl.ru != ""){
		ru = '&ru='+girl.ru;
	}

	//HTML
	html += '<ul id="pafo_detail">';
	html += '<li class="detail_img">';
	// html += '<a href="shicho.php?id=' + girl.hs + ru + '"><img                                                                                     src="./image/skeleton.png" style="background: url(http://c.macherie.tv/imgs/op/320x240/'+girl.ph+'); background-position:center center; background-repeat:no-repeat; -o-background-size: contain; -moz-background-size: contain; -webkit-background-size: contain; background-size: contain;"></a>';
	html +=    '<a href="shicho.php?id=' + girl.hs + ru + '"><img class="lazy" data-background="http://c.macherie.tv/imgs/op/320x240/' + girl.ph + '" src="./image/skeleton.png"                                                                    style="background-position:center center; background-repeat:no-repeat; -o-background-size: contain; -moz-background-size: contain; -webkit-background-size: contain; background-size: contain;" alt="' + girl.cn + '"></a>';
	html += debut;
	html += onairSt;
	html += micIcon;
	html += '</li>';
	html += '<li class="status">';
	html += '<p class="name">'+girl.cn+'</p>';
	html += '<p class="age age_area">'+girl.ag+'</p>';
	html += '<p class="age_area">'+girl.ar+'</p>';
	html += '<p class="status_detail">�g���F<b>'+girl.hi+'</b></p>';
	html += '<p class="status_detail">�J�b�v�F<b>'+girl.bu+'</b></p>';
	html += '<p class="status_detail">�̌^�F<b>'+girl.ty+'</b></p>';
	html += '<p class="status_detail">���t�F<b>'+girl.bt+'</b></p>';
	html += '<div class="comment_wrap cf" onclick="">';

	if(girl.moto == 1){
		html += '<p class="comment trancate">'+girl.cm+'</p>';
		html += '<span class="more">�����Ɠǂ�</span>';
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
