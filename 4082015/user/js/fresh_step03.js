function go_back(){
  document.F1.mode.value = "back";
  document.F1.submit();
}
function observe(target_name, type, listener) {
	var target = document.getElementById(target_name);
	
	if(target){
		if (target.addEventListener) target.addEventListener(type, listener, false);
		else target.attachEvent('on' + type, function() { listener.call(target, window.event); });
	}
}
function accountBG(){
	var account = document.getElementById('account');
	var accountV;
	
	if(account){
		accountV = account.value;
		if(accountV.length > 7 || !accountV.match(/^[0-9]+$/)){
			account.style.backgroundColor = 'red';
			alert("口座番号の形式が不正です\n口座番号は7桁以下の半角数字です");
		} else {
			account.style.backgroundColor = 'white';
		}
	}
}
var out=0;
function check(){
    var iCount;
    var sTemp, st_val;
	var av, bPosition;
    st_val=document.F1.meigi.value;
	if(st_val!=""){
	    for (iCount=0;iCount < st_val.length;iCount++){
	        sTemp = escape(st_val.charAt(iCount));
	        if (sTemp.length < 4){
				out=-1;
				break;
	        }
	    }
	    if(out!=0)alert("口座名義に全角以外の文字が含まれています。");
	    //全角スペースが含まれているかチェック
		bPosition = st_val.indexOf("　");
	    //if(st_val.match(/　/) ){
		if(bPosition == -1 || bPosition == 0 || bPosition == st_val.length - 1){
			out=-1;
	        alert("口座名義の姓と名の間に全角スペースを入れてください。");
		}
		if(!st_val.match(/^[ァ-ン　]+$/)){
			out=-1;
			alert("口座名義は全角カタカナで入力してください");
		}
	}
	av=document.F1.account.value;
	if(av.length > 7 || !av.match(/^[0-9]+$/)){
		out=-1;
		alert("口座番号の形式が不正です\n口座番号は7桁以下の半角数字です");
	}
	if(out!=0){
		out=0;
		return false;
	}
}

function remoteStart(){
	remote = window.open('/operator/edit_info/select_bank/search_bank.php','payment','width=470,height=700,scrollbars=yes');
}