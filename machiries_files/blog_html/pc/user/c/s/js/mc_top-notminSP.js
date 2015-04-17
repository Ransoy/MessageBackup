function makeLeaf(node,showarea){
	var retval='';
	if(node!='notNode' && node!='showNode'){
		$.each(node,function(){
			retval+='<ul class="'+this.cs+' gB">';
			retval+='<li class="tag"><img src="http://c.macherie.tv/c/d/images/common/g/'+this.st+'.gif" alt="'+this.cn+'" width="89" height="27" />';
			switch(this.cf){
				case "1":retval+='<img src="http://c.macherie.tv/c/d/images/common/g/new.gif" width="27" height="27" />';break;
				case "2":retval+='<img src="http://c.macherie.tv/c/d/images/common/g/debut.gif" width="27" height="27" />';break;
				case "3":retval+='<img src="http://c.macherie.tv/c/d/images/common/g/fine.gif" width="27" height="27" />';break;
				case "4":retval+='<img src="http://c.macherie.tv/c/d/images/common/g/fine_new.gif" width="27" height="27" />';break;
				case "5":retval+='<img src="http://c.macherie.tv/c/d/images/common/g/fine_debut.gif" width="27" height="27" />';break;
				case "7":retval+='<img src="http://c.macherie.tv/c/d/images/common/g/fine_check.gif" width="27" height="27" />';break;
				case "8":retval+='<img src="http://c.macherie.tv/c/d/images/common/g/checkmark.gif" width="27" height="27" />';break;
			}
			retval+='</li>';
			if(this.cs!="cm"){
				if(this.ph == "/imgs/op/jyunbi.gif"){ //pre-loading image
//					$('<img/>')[0].src = 'http://p.macherie.tv'+this.ph;
					$('<img/>')[0].src = 'http://mache-dev.vjsol.jp/'+this.ph;
				} else {
//					$('<img/>')[0].src = 'http://p.macherie.tv/imgs/op/120x90/'+this.ph;
					$('<img/>')[0].src = 'http://mache-dev.vjsol.jp/imgs/op/120x90/'+this.ph;
				}
//				retval+='<li class="pic" style="background-image:url(http://p.macherie.tv/imgs/op/120x90/'+this.ph+');">';

// �p�t�H�ʐ^���掿�Ή�
				if( this.clip == "1" ){
					retval+='<li class="pic" style="text-align:center;">';
					retval+='<div class="tochat" alt="'+this.cn+'" value="'+this.hs+'">';
					retval+='<img src="http://mache-dev.vjsol.jp/imgs/op/120x90/'+this.ph+'" style="width:120px;height:90px;" alt="'+ this.cn +'" />';
					retval+='</div>';
					retval+='</li>';
				}else{
					retval+='<li class="pic" style="background-image:url(http://mache-dev.vjsol.jp/imgs/op/120x90/'+this.ph+');">';

//retval+= '<div style="text-align:left; margin-left:5px; margin-top:5px; position:relative; font-size:11px;">';
//retval+= '<span style="position: absolute;font-weight:bold;color:#FFF;left:-1px;">0�l�Q��</span>';
//retval+= '<span style="position: absolute;font-weight:bold;color:#FFF;left:1px;">0�l�Q��</span>';
//retval+= '<span style="position: absolute;font-weight:bold;color:#FFF;top:-1px;">0�l�Q��</span>';
//retval+= '<span style="position: absolute;font-weight:bold;color:#FFF;top:1px;">0�l�Q��</span>';
//retval+= '<span style="position: absolute;font-weight:bold;">0�l�Q��</span>';
//retval+= '</div>';

					retval+='<div class="tochat" alt="'+this.cn+'" value="'+this.hs+'"></div>';
					retval+='</li>';
				}

				retval+='<li class="name"><a href="javascript:pO(\''+this.hs+'\');">'+this.cn+'</a>';
				retval+='<div class="mail">';
				if(this.vo==1){
					retval+='<img src="http://c.macherie.tv/c/d/images/common/g/thumbvoice.gif" alt="voice" width="17" height="17" />';
				}
				retval+='<a href="javascript:mO(\''+this.hs+'\');"><img src="http://c.macherie.tv/c/d/images/common/g/thumb01mail.gif" alt="Mail" width="17" height="17" /></a></div>';
			}else{
				$('<img/>')[0].src = 'http://p.macherie.tv/imgs/cm/120x90/'+this.ph;//pre-loading image
				retval+='<li class="pic" style="background-image:url(http://p.macherie.tv/imgs/cm/120x90/'+this.ph+');">';
				retval+='<div class="tochat" alt="'+this.cn+'" value="'+this.hs+'"></div></li>';
				retval+='<li class="name"><a href="javascript:cO(\''+this.hs+'\');">'+this.cn+'</a>';
				retval+='<div class="mail">';
				if(this.vo==1){
					retval+='<img src="http://c.macherie.tv/c/d/images/common/g/thumbvoice.gif" alt="voice" width="17" height="17" />';
				}
				retval+='<a href="javascript:cO(\''+this.hs+'\');"><img src="http://c.macherie.tv/c/d/images/common/g/thumb01mail.gif" alt="Mail" width="17" height="17" /></a></div>';
			}
			retval+='</li></ul>';
		});
	} else {
		retval+='<div><p>�I�����C�����̃p�t�H�[�}�[�����܂���</p>	</div>';
	}

	$(showarea).html(retval);
}

// ============================================================================
//
// ������makeLeaf()���ꕔ���ς��܂���
//
// ============================================================================
function makeLeafAddDebut(node,showarea){
	var retval='';

	var id_cnt = 0;

	var now_html = $(showarea).html();	// �����_��HTML

	if(node!='notNode' && node!='showNode'){
		$.each(node,function(){

			id_cnt += 1;
			retval+='<ul class="'+this.cs+' gB">';
			retval+='<li class="tag"><img src="http://c.macherie.tv/c/d/images/common/g/'+this.st+'.gif" alt="'+this.cn+'" width="89" height="27" />';
			switch(this.cf){
				case "1":retval+='<img src="http://c.macherie.tv/c/d/images/common/g/new.gif" width="27" height="27" />';break;
				case "2":retval+='<img src="http://c.macherie.tv/c/d/images/common/g/debut.gif" width="27" height="27" />';break;
				case "3":retval+='<img src="http://c.macherie.tv/c/d/images/common/g/fine.gif" width="27" height="27" />';break;
				case "4":retval+='<img src="http://c.macherie.tv/c/d/images/common/g/fine_new.gif" width="27" height="27" />';break;
				case "5":retval+='<img src="http://c.macherie.tv/c/d/images/common/g/fine_debut.gif" width="27" height="27" />';break;
				case "7":retval+='<img src="http://c.macherie.tv/c/d/images/common/g/fine_check.gif" width="27" height="27" />';break;
				case "8":retval+='<img src="http://c.macherie.tv/c/d/images/common/g/checkmark.gif" width="27" height="27" />';break;
			}
			retval+='</li>';
			if(this.cs!="cm"){
				if(this.ph == "/imgs/op/jyunbi.gif"){ //pre-loading image
//					$('<img/>')[0].src = 'http://p.macherie.tv'+this.ph;
					$('<img/>')[0].src = 'http://mache-dev.vjsol.jp/'+this.ph;
				} else {
//					$('<img/>')[0].src = 'http://p.macherie.tv/imgs/op/120x90/'+this.ph;
					$('<img/>')[0].src = 'http://mache-dev.vjsol.jp/imgs/op/120x90/'+this.ph;
				}
//				retval+='<li class="pic" style="background-image:url(http://p.macherie.tv/imgs/op/120x90/'+this.ph+');">';
				retval+='<li class="pic" style="background-image:url(http://mache-dev.vjsol.jp/imgs/op/120x90/'+this.ph+');">';
				retval+='<div class="tochat" alt="'+this.cn+'" value="'+this.hs+'"></div></li>';
				retval+='<li class="name"><a href="javascript:pO(\''+this.hs+'\');">'+this.cn+'</a>';
				retval+='<div class="mail">';
				if(this.vo==1){
					retval+='<img src="http://c.macherie.tv/c/d/images/common/g/thumbvoice.gif" alt="voice" width="17" height="17" />';
				}
				retval+='<a href="javascript:mO(\''+this.hs+'\');"><img src="http://c.macherie.tv/c/d/images/common/g/thumb01mail.gif" alt="Mail" width="17" height="17" /></a></div>';
			}else{
				$('<img/>')[0].src = 'http://p.macherie.tv/imgs/cm/120x90/'+this.ph;//pre-loading image
				retval+='<li class="pic" style="background-image:url(http://p.macherie.tv/imgs/cm/120x90/'+this.ph+');">';
				retval+='<div class="tochat" alt="'+this.cn+'" value="'+this.hs+'"></div></li>';
				retval+='<li class="name"><a href="javascript:cO(\''+this.hs+'\');">'+this.cn+'</a>';
				retval+='<div class="mail">';
				if(this.vo==1){
					retval+='<img src="http://c.macherie.tv/c/d/images/common/g/thumbvoice.gif" alt="voice" width="17" height="17" />';
				}
				retval+='<a href="javascript:cO(\''+this.hs+'\');"><img src="http://c.macherie.tv/c/d/images/common/g/thumb01mail.gif" alt="Mail" width="17" height="17" /></a></div>';
			}
			retval+='</li></ul>';
		});
	} else {
		retval+='<div><p>�I�����C�����̃p�t�H�[�}�[�����܂���</p>	</div>';
	}

	retval = now_html + retval;

	$(showarea).html(retval);
}

function reloadGirl(mval){

	var url;
	var url_array = ['http://' + location.hostname + '/',
	                 'http://' + location.hostname + '/index.php',
	                 'https://' + location.hostname + '/',
	                 'https://' + location.hostname + '/index.php'];

	if( location.href.match(/^https:/) ){
		url = 'https://' + location.hostname;
	} else {
		url = 'http://' + location.hostname;
	}

	document.cookie="number="+mval;

	if( $.inArray(location.href, url_array) != -1 ){
		wval = 'true';
	} else {
		wval = 'false';
	}

	$.post(url + '/xxnode-neo.php',{m:mval,w:wval},function(d){
		if(mval=="0"){
			if(d.partyNode!="notNode"){
				makeLeaf(d.partyNode,'#partyArea');
				$('#partySelect').attr("style","");
				$('#partyArea').attr("style","");
			}else{
				$('#partySelect').attr("style","display:none");
				$('#partyArea').attr("style","display:none");
			}
		}else{
			$('#partyArea').empty();
			$('#partySelect').attr("style","display:none");
			$('#partyArea').attr("style","display:none");
		}
		//if(d.eventNode != "notNode" || d.eventNode == "showNode"){
		if(d.eventNode != "notNode" && d.eventNode != "showNode"){
			makeLeaf(d.eventNode,"#eventArea");
			$('#eventSelect').attr("style","");
			$('#eventArea').attr("style","");
		} else {
			$('#eventArea').empty();
			$('#eventSelect').attr("style","display:none");
			$('#eventArea').attr("style","display:none");
		}
		switch(mval){
			case "0":$('#tag_first').css("background-position","0 -910px");break;//online
			case "1":$('#tag_first').css("background-position","0 -910px");break;//online
			case "2":$('#tag_first').css("background-position","0 -1001px");break;//party
			case "3":$('#tag_first').css("background-position","0 -91px");break;//loc1
			case "4":$('#tag_first').css("background-position","0 -182px");break;//loc2
			case "5":$('#tag_first').css("background-position","0 -273px");break;//loc3
			case "6":$('#tag_first').css("background-position","0 -364px");break;//loc4
			case "7":$('#tag_first').css("background-position","0 -455px");break;//loc5
			case "8":$('#tag_first').css("background-position","0 -546px");break;//loc6
			case "9":$('#tag_first').css("background-position","0 -637px");break;//loc7
			case "10":$('#tag_first').css("background-position","0 -728px");break;//loc8
			default:$('#tag_first').css("background-position","0 -910px");break;//online
		}
		makeLeaf(d.firstNode,'#firstArea');
		if(d.secondNode!="notNode"){
			switch(mval){
				case "0":$('#tag_second').css("background-position","0 -910px");break;//online
				case "1":$('#tag_second').css("background-position","0 0px");break;//2shot
				case "2":$('#tag_second').css("background-position","0 0px");break;//2shot
				default:$('#tag_second').css("background-position","0 -819px");break;//loc9
			}
			$('#secondSelect').attr("style","");
			$('#secondArea').attr("style","");
			makeLeaf(d.secondNode,'#secondArea');
		}else{
			$('#secondArea').empty();
			$('#secondSelect').attr("style","display:none");
			$('#secondArea').attr("style","display:none");
		}
	},'json');
}

function reloadDebut(){

	var url;

	if( location.href.match(/^https:/) ){
		url = 'https://' + location.hostname;
	} else {
		url = 'http://' + location.hostname;
	}

	$.post(url + '/xxnodedebut20_neo.php',function(d){
		makeLeaf(d.NewFace,'#newFace');
	},'json');

}

// ====================================================================
// reloadDebut()�̋@�\�ɉ����A�S�}�V�F��������id���X�g���擾����
// ====================================================================
function reloadDebut2(){

	var url;

	if( location.href.match(/^https:/) ){
		url = 'https://' + location.hostname;
	} else {
		url = 'http://' + location.hostname;
	}

	// �ʐM -----------------------------------------------------------

	$.ajax({
			url: '/xxnodedebutList_neo.php',		// �ʐM��
			type: 'POST',							// GET or POST
			dataType: 'html',						// ����f�[�^�̎��
			success: function(data_from_ajax){		// �ʐM�������Ɏ��s

				var list = data_from_ajax;
				var pp 	 = 1;
				var list_cnt = list.split( ',' ).length - 1;

				var post_data = { page : pp, all : 0 };
				$.ajax({
						url: '/xxnodedebutAll_neo.php',			// �ʐM��
						type: 'POST',							// GET or POST
						dataType: 'json',						// ����f�[�^�̎��
						data: post_data,						// �n���f�[�^
						success: function(dd){					// �ʐM�������Ɏ��s

							// ���݂̃y�[�W�������Z�b�g
							$("div#newface_id_list_cnt").html( list_cnt );
							$("div#newface_current_page").html( "1" );

							// �擾�������̂�HTML�ɒǉ����邾��
							makeLeaf(dd.NewFace,'#newFace' );

							// �����ƌ���{�^��
							// �S�Ă̐V�l��\���������ǂ����𔻒肷��
							if( list_cnt <= 20 ){
								btnDebutMoreFin();
							}else{
								btnDebutMoreInitial();
							}

						},
						error: function(xhr, status, err){		// �ʐM���s���Ɏ��s
						}
				});

			},
			error: function(xhr, status, err){		// �ʐM���s���Ɏ��s
			}
	});
}

// ====================================================================
// �����ƌ���{�^���̏����ݒ�
// ====================================================================
function btnDebutMoreInitial(){

	// �����ƌ���{�^���I�u�W�F�N�g
	var img = $("li#btn_debut_more img");

	// 
	img.attr( "src", "http://c.macherie.tv/c/m/images/roombar/btn_debut_more.png" );
	img.css( "cursor", "auto" );

	// mouse�֘A���\�b�h����
	img.unbind("mouseover").unbind("mouseout");

	img.mouseover( function(){
		// �J�[�\�������������Ƃ�
		img.attr( "src", "http://c.macherie.tv/c/m/images/roombar/btn_debut_more_ov.png" );
		img.css( "cursor", "pointer" );
	}).mouseout(function(){
		// �J�[�\�����O�ꂽ�Ƃ�
		img.attr( "src", "http://c.macherie.tv/c/m/images/roombar/btn_debut_more.png" );
		img.css( "cursor", "auto" );
	});

	// mouser�N���b�N�C�x���g
	img.die( 'click' );

	img.live( 'click', function(){
		// �c��S�����\��
		addDebutSP();
	});
}

// ====================================================================
// �����ƌ���{�^���̏I���ݒ�
// ====================================================================
function btnDebutMoreFin(){

	// �����ƌ���{�^���I�u�W�F�N�g
	var img = $("li#btn_debut_more img");

	// fin�摜��ݒ�
	img.attr( "src", "http://c.macherie.tv/c/m/images/roombar/btn_debut_more_fin.png" );
	img.css( "cursor", "auto" );

	// mouse�֘A���\�b�h����
	img.unbind("mouseover").unbind("mouseout");

	img.mouseover( function(){
		// �J�[�\�������������Ƃ�
		img.attr( "src", "http://c.macherie.tv/c/m/images/roombar/btn_debut_more_fin_ov.png" );
		img.css( "cursor", "pointer" );
	}).mouseout(function(){
		// �J�[�\�����O�ꂽ�Ƃ�
		img.attr( "src", "http://c.macherie.tv/c/m/images/roombar/btn_debut_more_fin.png" );
		img.css( "cursor", "auto" );
	});

	// �N���b�N�C�x���g�j��
	img.die( "click" )

	img.live( 'click', function(){
		location.href = "#tpos";
	});

}

// ====================================================================
// �V����20�l���̍̂ꂽ�ă}�V�F�����q�̃f�[�^��\��
// ====================================================================
/*
function addDebut(){

	var url;

	if( location.href.match(/^https:/) ){
		url = 'https://' + location.hostname;
	} else {
		url = 'http://' + location.hostname;
	}

	// �ʐM -----------------------------------------------------------

	var p 	 = $("div#newface_current_page").html();		// �y�[�W��
	var list_cnt = $("div#newface_id_list_cnt").html() - 1; // id���X�g��

	// id���X�g��
	var cc = new Number( list_cnt );

	// ���݂̃y�[�W���X�V
	var pp = new Number( p );
	pp += 1;
	$("div#newface_current_page").html( pp );

	var post_data = { page : pp, all : 0 };
	$.ajax({
			url: '/xxnodedebutAll_neo.php',			// �ʐM��
			type: 'POST',							// GET or POST
			dataType: 'json',						// ����f�[�^�̎��
			data: post_data,						// �n���f�[�^
			success: function(data_from_ajax){		// �ʐM�������Ɏ��s

				makeLeafAddDebut(data_from_ajax.NewFace,'#newFace' );

				// �S���\�������ꍇ
				if( cc <= pp*20 ){
					btnDebutMoreFin();
				}else{
//					img.attr( "src", "http://c.macherie.tv/c/m/images/roombar/btn_debut_more.png" );
//					img.css( "cursor", "auto" );
				}

			},
			error: function(xhr, status, err){		// �ʐM���s���Ɏ��s
				btnDebutMoreInitial();
			}
	});
}
*/

// ====================================================================
// �c��S�����̍̂ꂽ�ă}�V�F�����q�̃f�[�^��\��
// ====================================================================
function addDebutSP(){

	var url;

	if( location.href.match(/^https:/) ){
		url = 'https://' + location.hostname;
	} else {
		url = 'http://' + location.hostname;
	}

	// �ʐM -----------------------------------------------------------

	var p 	 = $("div#newface_current_page").html();		// �y�[�W��
	var list_cnt = $("div#newface_id_list_cnt").html() - 1; // id���X�g��

	// id���X�g��
	var cc = new Number( list_cnt );

	// ���݂̃y�[�W���X�V
	var pp = new Number( p );
	pp += 1;
	$("div#newface_current_page").html( pp );

	var post_data = { page : pp, all : 1 };			// all=1 �ŁA�y�[�W���֌W�Ȃ��S���R�[�h�擾
	$.ajax({
			url: '/xxnodedebutAll_neoSP.php',		// �ʐM��
			type: 'POST',							// GET or POST
			dataType: 'json',						// ����f�[�^�̎��
			data: post_data,						// �n���f�[�^
			success: function(data_from_ajax){		// �ʐM�������Ɏ��s
				// �S���\�������ꍇ
				btnDebutMoreFin();
				// �c��S���\������
				makeLeafAddDebut(data_from_ajax.NewFace,'#newFace' );
			},
			error: function(xhr, status, err){		// �ʐM���s���Ɏ��s
				btnDebutMoreInitial();
			}
	});
}

// ====================================================================
// ��ʂɕ\�����̍̂ꂽ�ă}�V�F�����q�̃f�[�^�X�V
// ====================================================================
/*
function updateDebut(){

	var url;

	if( location.href.match(/^https:/) ){
		url = 'https://' + location.hostname;
	} else {
		url = 'http://' + location.hostname;
	}

	// �ʐM -----------------------------------------------------------

	var p 	 = $("div#newface_current_page").html();	// �y�[�W��

	var post_data = { page : p, all : 1 };			// all=1 �ŁA����page���܂ł̑S���R�[�h���擾
	$.ajax({
			url: '/xxnodedebutAll_neo.php',			// �ʐM��
			type: 'POST',							// GET or POST
			dataType: 'json',						// ����f�[�^�̎��
			data: post_data,						// �n���f�[�^
			success: function(data_from_ajax){		// �ʐM�������Ɏ��s
				makeLeaf(data_from_ajax.NewFace,'#newFace' );
			},
			error: function(xhr, status, err){		// �ʐM���s���Ɏ��s
			}
	});

}
*/

function updateDebutSP(){

	var url;

	if( location.href.match(/^https:/) ){
		url = 'https://' + location.hostname;
	} else {
		url = 'http://' + location.hostname;
	}

	// �ʐM -----------------------------------------------------------

	var p 	 = $("div#newface_current_page").html();// �y�[�W��

	var post_data = { page : p, all : 0 };			// all=1 �ŁA�y�[�W���֌W�Ȃ��S���R�[�h�擾
	$.ajax({
			url: '/xxnodedebutAll_neoSP.php',		// �ʐM��
			type: 'POST',							// GET or POST
			dataType: 'json',						// ����f�[�^�̎��
			data: post_data,						// �n���f�[�^
			success: function(data_from_ajax){		// �ʐM�������Ɏ��s
				makeLeaf(data_from_ajax.NewFace,'#newFace' );
			},
			error: function(xhr, status, err){		// �ʐM���s���Ɏ��s
			}
	});

}

// �̂ꂽ�ă}�V�F�����q�i�I�����C���̂݁j�p
function updateDebutOnline(){

	var url;

	if( location.href.match(/^https:/) ){
		url = 'https://' + location.hostname;
	} else {
		url = 'http://' + location.hostname;
	}

	// �ʐM -----------------------------------------------------------

	var p 	 = $("div#newface_current_page").html();// �y�[�W��

	var post_data = { page : p, all : 0 };			// all=1 �ŁA�y�[�W���֌W�Ȃ��S���R�[�h�擾
	$.ajax({
			url: '/xxnodedebutAll_neoOnline.php',	// �ʐM��
			type: 'POST',							// GET or POST
			dataType: 'json',						// ����f�[�^�̎��
			data: post_data,						// �n���f�[�^
			success: function(data_from_ajax){		// �ʐM�������Ɏ��s
				makeLeaf(data_from_ajax.NewFaceOnline,'#newFaceOnline' );
			},
			error: function(xhr, status, err){		// �ʐM���s���Ɏ��s
			}
	});

}

function updateDebutAll(){

	var url;

	if( location.href.match(/^https:/) ){
		url = 'https://' + location.hostname;
	} else {
		url = 'http://' + location.hostname;
	}

	// �ʐM -----------------------------------------------------------

	var p 	 = $("div#newface_current_page").html();// �y�[�W��

	var post_data = { page : p, all : 0 };			// all=1 �ŁA�y�[�W���֌W�Ȃ��S���R�[�h�擾
	$.ajax({
			url: '/xxnodedebutAll_all.php',		// �ʐM��
			type: 'POST',							// GET or POST
			dataType: 'json',						// ����f�[�^�̎��
			data: post_data,						// �n���f�[�^
			success: function(data_from_ajax){		// �ʐM�������Ɏ��s
				makeLeaf(data_from_ajax.NewFace,'#newFace' );
			},
			error: function(xhr, status, err){		// �ʐM���s���Ɏ��s
			}
	});

}

/* �摜�̃v�����[�h�i��ǂ݁j */
function preLoadImages(){
	for( var i = 0, I = arguments.length; i < I; ++i){
    	new Image().src = arguments[i];
	}
}

function pO(a){window.open("profile/profile.php?sid="+a,"Profile","width=714,height=536,titlebar=1,status=0,scrollbars=1");}
function mO(a){window.open("webmail/write.php?sid="+a,"memberWebmail","titlebar=0,status=0,scrollbars=yes,resizable=yes");}
function cO(a){window.open("chat/shicho.php?id="+a,"memberChat");}
function mail(cd){
	if( location.protocol == 'https:' ){
		var url = 'http://' + location.hostname;
		switch (cd){
		case 1:		//Normal
		case 2:		//World
			url += '/webmail/index.php';
			break;
		case 3:		//Biglobe
			url += '/biglobe/webmail/index.php';
			break;
		case 4:		//�y�V
			url += '/rakuten/webmail/index.php';
			break;
		case 5:		//Cinema
			url += '/cinema/webmail/index.php';
			break;
		}
	} else {
		var url = 'webmail/index.php';
	}
	window.open(url,"memberWebmail","titlebar=0,status=0,scrollbars=yes,resizable=yes");
}

$("#girlsBlock div.tochat").live('click',function(){window.open("chat/shicho.php?id="+$(this).attr("value"),"memberChat");});
$(".select").live('change',function(){reloadGirl($(this).val());$('.select').val($(this).val());});
// �I�����C�����p�t�H�[�}�[�G���A �� �Y���G���A�݂̂̍X�V
//$(".btn_reload").live('click',function(){reloadGirl($('#m').val());reloadDebut();});
$(".btn_reload").live('click',function(){reloadGirl($('#m').val());});
$(".btn_reload_w").live('click',function(){reloadGirl(0);});
// �̂ꂽ�ă}�V�F�����q�G���A
//$(".btn_reload_debut").live('click',function(){updateDebut();});
$(".btn_reload_debut").live('click',function(){updateDebutSP();});
// �̂ꂽ�ă}�V�F�����q�i�I�����C���j�G���A
$(".btn_reload_debut_online").live('click',function(){updateDebutOnline();});
// �̂ꂽ�ă}�V�F�����q�i�S���j�G���A
$(".btn_reload_debut_all").live('click',function(){updateDebutAll();});
