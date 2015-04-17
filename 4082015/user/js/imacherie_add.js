//XMLHttpRequestオブジェクト生成
var conTargetId
function createHttpRequestXX(){
	if(window.ActiveXObject){
		try {
			return new ActiveXObject("Msxml2.XMLHTTP");
		} catch (e) {
			try {
				return new ActiveXObject("Microsoft.XMLHTTP");
			} catch (e2) {
				return null;
			}
		}
	} else if(window.XMLHttpRequest){
		return new XMLHttpRequest();
	} else {
		return null;
	}
}
function saveCookiesDB(data ,method ,contentsId){
	var httpoj = createHttpRequestXX();
	//open メソッド
	fileName='./imacherie_save.php';
	httpoj.open(method ,fileName ,true );

	httpoj.setRequestHeader("content-type", "application/x-www-form-urlencoded;charset=UTF-8");
	var body = method == 'post' ? (data) : null;
	httpoj.send(body);
	conTargetId = contentsId;
	httpoj.onreadystatechange = function(){
		if(httpoj.readyState==4){
			document.getElementById('contentBtn_'+conTargetId).innerHTML = "追加済み";
			if(document.getElementById('s_contentBtn')){
				document.getElementById('s_contentBtn').innerHTML = "追加済み";
			}
		}
	}
}
function createFeed(contentsId){
	var tabId = document.F1.tab_id.value;
	var insStr = "mode=insert&contents_id="+contentsId+"&tab_id="+tabId;
	saveCookiesDB(insStr ,'post',contentsId);
}
function saveThemeDB(data ,method ,contentsId){
	var httpoj = createHttpRequestXX();
	//open メソッド
	fileName='./imacherie_theme.php';
	httpoj.open(method ,fileName ,true );

	httpoj.setRequestHeader("content-type", "application/x-www-form-urlencoded;charset=UTF-8");
	var body = method == 'post' ? (data) : null;
	httpoj.send(body);
	conTargetId = contentsId;
	httpoj.onreadystatechange = function(){
	}
}
function createTheme(t_id,t_img,t_color){
	var tabId = document.F1.tab_id.value;
	var insStr = "mode=add_theme&theme_id="+t_id+"&tab_id="+tabId;

	document.getElementById('member').style.backgroundImage='url(/imgs/imacherie/theme/'+t_img+')';
	document.getElementById('member').style.backgroundColor=t_color;
	document.getElementById('contentBtn_'+t_id).innerHTML = "　追加済み";
	old_Id =document.F_theme.now_id.value;
	old_img=document.F_theme.now_img.value;
	old_col=document.F_theme.now_col.value;
	btn_str="<input type='button' value='追加する' onclick=\"createTheme('"+old_Id+"','"+old_img+"','"+old_col+"');\" />";
	if(document.getElementById('contentBtn_'+old_Id)){
		document.getElementById('contentBtn_'+old_Id).innerHTML = btn_str;
	}
	document.F_theme.now_id.value=t_id;
	document.F_theme.now_img.value=t_img;
	document.F_theme.now_col.value=t_color;

	saveThemeDB(insStr ,'post',t_id);
}

