/**
 *
 * �g�b�v�y�[�W�I�����C���ꗗ�擾
 *
 */

var LMode = 'tile';
var OMode = 0;
function onlineList_load(l, o){
	LMode = l;
	OMode = o;
	$.ajax({
		type: 'POST',
		url: './ajax/online_list.php?l='+LMode+'&o='+OMode,
		cache:false,
		success: function(html){
			var allNode = JSON.parse(html);
			if(allNode.onairNode=="notNode"){
				$('.tabbox').html('<ul id="pafo_big">�I���G�A�[���̃p�t�H�[�}�[�����܂���B</ul>');
			}else{
				if(LMode=='tile'){
					makeTile(allNode.onairNode);
				}else{
					makeList(allNode.onairNode);
				}
			}
		},
		beforeSend: function(){
		},
		error: function(){
		},
		complete :function(){
		}
	});
}



function makeTile(node){

	var html = '<ul id="pafo_big">';
	for (var i=0; i<node.length; i++) {
		//------------------------------
		//�I���G�A�[���
		onairSt = '<div class="online"><a href="#"><img src="image/icon/performer_online.png"></a></div>';
		if(node[i].st=='2st' || node[i].st=='pat'){
			//�ҋ@��
			onairSt = '<div class="online"><img src="image/icon/performer_online.png"></div>';
		}else if(node[i].st=='2sc' || node[i].st=='pac'){
			//�`���b�g��
			onairSt = '<div class="twoshot"><img src="image/icon/performer_2shot.png"></div>';
		}else{
			//�I�t���C��
		}

		//------------------------------
		//�}�C�N
		micIcon = '<div class="mike"></div>';
		if(node[i].vo==1){
			micIcon = '<div class="mike"><img src="image/icon/performer_mike.png"></div>';
		}

		//------------------------------
		//�V�l
		debut = '';
		if(node[i].nw==2){
			debut = '<div class="debut"><img src="image/icon/performer_debut.png"></div>';
		}

		//HTML
		html += '<li class="pafo_big_img">';
		html += '<div class="image_area">';
		html += '<a href="#"><img src="/sp_design/image/skeleton.png"  style="background: url(/imgs/op/120x90/'+node[i].ph+'); background-position:center center; background-repeat:no-repeat; -o-background-size: contain; -moz-background-size: contain; -webkit-background-size: contain; background-size: contain;"></a>';
		html += onairSt;
		html += '</div>';
		html += debut;
		html += '<p class="area"><em>'+node[i].ar+'<em></p><p class="age"><em>'+node[i].ag+'<em></p>';
		html += micIcon;
		html += '</li>';
	}
	html += '</ul>';
	$('.tabbox').html(html);
}



function makeList(node){

	var html = '';
	for (var i=0; i<node.length; i++) {
		//------------------------------
		//�I���G�A�[���
		onairSt = '<div class="icon"><img src="image/icon/view_detail_online.png"></div>';
		if(node[i].st=='2st' || node[i].st=='pat'){
			//�ҋ@��
			onairSt = '<div class="icon"><img src="image/icon/view_detail_online.png"></div>';
		}else if(node[i].st=='2sc' || node[i].st=='pac'){
			//�`���b�g��
			onairSt = '<div class="icon"><img src="image/icon/view_detail_2shot.png"></div>';
		}else{
			//�I�t���C��
		}

		//------------------------------
		//�}�C�N
		micIcon = '';
		if(node[i].vo==1){
			micIcon = '<div class="mike"><img src="image/icon/performer_mike.png"></div>';
		}

		//------------------------------
		//�V�l
		debut = '';
		if(node[i].nw==2){
			debut = '<div class="icon"><img src="image/icon/view_detail_debut.png"></div>';
		}

		//HTML
		html += '<ul id="pafo_detail">';
		html += '<li class="detail_img">';
		html += '<a href="#"><img src="/sp_design/image/skeleton.png"  style="background: url(/imgs/op/120x90/'+node[i].ph+'); background-position:center center; background-repeat:no-repeat; -o-background-size: contain; -moz-background-size: contain; -webkit-background-size: contain; background-size: contain;"></a>';
		html += debut;
		html += onairSt;
		html += micIcon;
		html += '</li>';
		html += '<li class="status">';
		html += '<p class="name">'+node[i].cn+'</p>';
		html += '<p class="age age_area">'+node[i].ag+'</p>';
		html += '<p class="age_area">'+node[i].ar+'</p>';
		html += '<p class="status_detail">�g���F<b>'+node[i].hi+'</b></p>';
		html += '<p class="status_detail">�J�b�v�F<b>'+node[i].bu+'</b></p>';
		html += '<p class="status_detail">�̌^�F<b>'+node[i].ty+'</b></p>';
		html += '<p class="status_detail">���t�F<b>'+node[i].bt+'</b></p>';

		html += '<div class="comment_wrap cf" onclick="">';
		html += '<p class="comment trancate">'+node[i].cm+'</p>';
		html += '<span class="more">�����Ɠǂ�</span>';
		html += '<p class="comment all">'+node[i].cm_more+'</p>';
		html += '</div>';
		html += '</li>';
		html += '<li class="line"></li>';
		html += '</ul>';
	}
	$('.tabbox').html(html);
}
