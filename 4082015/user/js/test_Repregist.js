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
			Rep['error'].innerHTML = 'ログインIDに使用できない文字が含まれています。<br/>半角英数字のみ使用できます。';
			return false;
		}
		if(Rep['user_id'].length < 4 || Rep['user_id'].length > 12){
			Rep['ok'].style.display = 'none';
			Rep['error'].style.display = 'block';
			Rep['error'].innerHTML = '文字数は4文字以上12文字以内でお願いいたします。';
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
			Rep['error'].innerHTML = 'チャットネームに使用できない文字「"」が含まれています。';
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
					txt = "ID・チャットネーム";
				}else{
					if(Rep['user_id'] != "") txt = "ID";
					if(Rep['nick_name'] != "") txt = "チャットネーム";
				}
				var key = request.responseText;
				if(key == '2'){
					Rep['ok'].style.display = 'none';
					Rep['error'].style.display = 'block';
					Rep['error'].innerHTML = '入力された'+txt+'はすでに登録されています。';
				}else if(key == '1'){
					Rep['ok'].style.display = 'none';
					Rep['error'].style.display = 'block';
					Rep['error'].innerHTML = '入力された'+txt+'はご利用できません。';
				}else if(key == '0'){
					Rep['ok'].style.display = 'block';
					Rep['error'].style.display = 'none';
					Rep['ok'].innerHTML = '入力された'+txt+'は使用できます';
				}
			},
			onFailure: function(request){
				//alert('読み込みに失敗しました!');
			},
			onException: function(request){
				//alert('読み込み中にエラー発生!');
			}
		}
	);
}
