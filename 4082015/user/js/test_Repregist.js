var Rep = new Array();
function addEventListener_regist(target, type, listener) {
    if (target.addEventListener) target.addEventListener(type, listener, false);
    else if (target.attachEvent) target.attachEvent('on' + type, function() { listener.call(target, window.event); });
    else target['on' + type] = function(e) { listener.call(target, e || window.event); };
}
function checkRep(script_target,uid,nid,eid,oid){
	Rep['error'] = document.getElementById(eid);
	Rep['ok'] = document.getElementById(oid);
	if(uid){
		Rep['user_id'] = document.getElementById(uid).value;
		robj = Rep['user_id'].match(new RegExp('[0-9a-zA-Z]+'));
		if(robj != Rep['user_id']){
			Rep['ok'].style.display = 'none';
			Rep['error'].style.display = 'block';
			Rep['error'].innerHTML = '���O�C��ID�Ɏg�p�ł��Ȃ��������܂܂�Ă��܂��B<br/>���p�p�����̂ݎg�p�ł��܂��B';
			return false;
		}
		if(Rep['user_id'].length < 4 || Rep['user_id'].length > 12){
			Rep['ok'].style.display = 'none';
			Rep['error'].style.display = 'block';
			Rep['error'].innerHTML = '��������4�����ȏ�12�����ȓ��ł��肢�������܂��B';
			return false;
		}
	}else{
		Rep['user_id'] = '';
	}
	if(nid){
		Rep['nick_name'] = document.getElementById(nid).value;
		robj = Rep['nick_name'].match(new RegExp('\"'));
		if(robj == '"'){
			Rep['ok'].style.display = 'none';
			Rep['error'].style.display = 'block';
			Rep['error'].innerHTML = '�`���b�g�l�[���Ɏg�p�ł��Ȃ������u"�v���܂܂�Ă��܂��B';
			return false;
		}
	}else{
		Rep['nick_name'] = '';
	}
	Rep['nick_name'] = encodeURI(Rep['nick_name']);
	Rep['nick_name'] = Rep['nick_name'].replace(/%/g, " ");
	Rep['parameters'] = 'user_id='+Rep['user_id']+'&nick_name='+Rep['nick_name'];

	var act = new Ajax.Request(
		script_target,
		{
			"method": "get",
			"parameters": Rep['parameters'],
			onSuccess: function(request){},
			onComplete: function(request){
				if(Rep['user_id'] != "" && Rep['nick_name'] != ""){
					txt = "ID�E�`���b�g�l�[��";
				}else{
					if(Rep['user_id'] != "") txt = "ID";
					if(Rep['nick_name'] != "") txt = "�`���b�g�l�[��";
				}
				var key = request.responseText;
				if(key == '2'){
					Rep['ok'].style.display = 'none';
					Rep['error'].style.display = 'block';
					Rep['error'].innerHTML = '���͂��ꂽ'+txt+'�͂��łɓo�^����Ă��܂��B';
				}else if(key == '1'){
					Rep['ok'].style.display = 'none';
					Rep['error'].style.display = 'block';
					Rep['error'].innerHTML = '���͂��ꂽ'+txt+'�͂����p�ł��܂���B';
				}else if(key == '0'){
					Rep['ok'].style.display = 'block';
					Rep['error'].style.display = 'none';
					Rep['ok'].innerHTML = '���͂��ꂽ'+txt+'�͎g�p�ł��܂�';
				}
			},
			onFailure: function(request){
				//alert('�ǂݍ��݂Ɏ��s���܂���!');
			},
			onException: function(request){
				//alert('�ǂݍ��ݒ��ɃG���[����!');
			}
		}
	);
}
