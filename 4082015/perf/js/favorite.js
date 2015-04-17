var Favo_url = "http://www.macherie.tv/performer/";
var Favo_name = "【マシェリ】パフォーマ管理画面";

//	Firefox
if(navigator.userAgent.indexOf("Firefox") > -1){
	//document.write('<a href="javascript:window.sidebar.addPanel(\''+ Favo_name + '\', \'' + Favo_url + '\', \'\')" style="color: #4169e1; border: 1px solid #4169e1; padding: 3px; text-decoration: none;">お気に入りに追加</a>');
	document.write('<img src="' + Favo_url + 'img/banner/bookmark.gif' + '" style="cursor:pointer;" onclick="window.sidebar.addPanel(\''+Favo_name+'\',\''+Favo_url+'\',\'\');" onmouseover="this.src=\'' +  Favo_url + 'img/banner/bookmark_on.gif\';' + '" onmouseout="this.src=\'' + Favo_url + 'img/banner/bookmark.gif' + '\';">');
}
// IE
else if(navigator.userAgent.indexOf("MSIE") > -1){
	//document.write('<a href="javascript:window.external.addFavorite(\''+ Favo_url + '\', \'' + Favo_name + '\')" style="color: #4169e1; border: 1px solid #4169e1; padding: 3px; text-decoration: none;">お気に入りに追加</a>');
	document.write('<img src="' + Favo_url + 'img/banner/bookmark.gif' + '" style="cursor:pointer;" onclick="window.external.AddFavorite(\'' + Favo_url + '\',\'' + Favo_name + '\')" onmouseover="this.src=\'' +  Favo_url + 'img/banner/bookmark_on.gif\';' + '" onmouseout="this.src=\'' + Favo_url + 'img/banner/bookmark.gif' + '\';">');
}
// Opera
else if(navigator.userAgent.indexOf("Opera") > -1){
	//document.write('<a href="'+Favo_url+'" rel="sidebar" title="'+Favo_name+'" style="color: #4169e1; border: 1px solid #4169e1; padding: 3px; text-decoration: none;">お気に入りに追加</a>');
	document.write('<a href="'+Favo_url+'" rel="sidebar" title="'+Favo_name+'"><img src="' + Favo_url + 'img/banner/bookmark.gif" alt="お気に入りに登録"' + ' onmouseover="this.src=\'' +  Favo_url + 'img/banner/bookmark_on.gif\';' + '" onmouseout="this.src=\'' + Favo_url + 'img/banner/bookmark.gif' + '\';"' + '/></a>');
}

//	Netscape
else if(navigator.userAgent.indexOf("Netscape") > -1){
	//document.write('<a href="javascript:window.sidebar.addPanel(\''+ Favo_name + '\', \'' + Favo_url + '\', \'\')" style="color: #4169e1; border: 1px solid #4169e1; padding: 3px; text-decoration: none;">お気に入りに追加</a>');
	document.write('<img src="' + Favo_url + 'img/banner/bookmark.gif' + '" style="cursor:pointer;" onclick="window.sidebar.addPanel(\''+Favo_name+'\',\''+Favo_url+'\',\'\');" onmouseover="this.src=\'' +  Favo_url + 'img/banner/bookmark_on.gif\';' + '" onmouseout="this.src=\'' + Favo_url + 'img/banner/bookmark.gif' + '\';">');
}