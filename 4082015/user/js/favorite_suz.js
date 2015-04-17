//=======================
//
//	Site:Macherie
//	Build:2007/4/1
//	Author:S.takaya
//
//=======================

//	ADD Favorite (ie,firefox,opera,netscape)
//=========================================

var Favo_url = "http://www.macherie.tv/";
var Favo_name = "ライブチャット マシェリ";

//	Firefox
if(navigator.userAgent.indexOf("Firefox") > -1){
	document.write('<img src="./images/face/head/flower_favorite.gif" style="cursor:pointer;" onclick="window.sidebar.addPanel(\''+Favo_name+'\',\''+Favo_url+'\',\'\');"><br />');
}
// IE
else if(navigator.userAgent.indexOf("MSIE") > -1){
	document.write('<img src="./images/face/head/flower_favorite.gif" style="cursor:pointer;" onclick="window.external.AddFavorite(\''+Favo_url+'\',\''+Favo_name+'\')"><br />');
}
// Opera
else if(navigator.userAgent.indexOf("Opera") > -1){
	document.write('<a href="'+Favo_url+'" rel="sidebar" title="'+Favo_name+'"><img src="./images/face/head/flower_favorite.gif" alt="お気に入りに登録" /></a><br />');
}

//	Netscape
else if(navigator.userAgent.indexOf("Netscape") > -1){
	document.write('<img src="./images/face/head/flower_favorite.gif" style="cursor:pointer;" onclick="window.sidebar.addPanel(\''+Favo_name+'\',\''+Favo_url+'\',\'\');"><br />');
}
//	Google Chrome
else if(navigator.userAgent.indexOf("Chrome") > -1){
//	document.write('<a href="javascript:window.external.addFavorite(\''+Favo_url+'\',\''+Favo_name+'\')"><img src="./images/face/head/flower_favorite.gif" alt="お気に入りに登録" /></a><br />');
//	document.write('<a href="https://www.macherie.tv/registrationfree.php"><img src="./images/face/head/flower_regist.gif" alt="今だけ!!まずは無料体験" /></a>');
}

// Other
else{
	//document.write('<a href="https://www.macherie.tv/registrationfree.php"><img src="./images/face/head/flower.gif" alt="今だけ!!まずは無料体験" /></a>');
}