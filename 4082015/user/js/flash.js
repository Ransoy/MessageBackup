//
//	Site:Macherie
//	Build:2007/4/1
//	Author:S.takaya
//
//-----------------------------------

/* SetPropety
-------------------------------------*/

var flaWidth = "100%";
var flaHeight = "200px";
var flaData = "images/top/top.swf";

/* FlashWrite
-------------------------------------*/

var FlashTag =
'	<object width="' + flaWidth + '" height="' + flaHeight + '" data="' + flaData + '" type="application/x-shockwave-flash">' +
'		<param name="movie" value="' + flaData + '">' +
'		<param name="quality" value="high">' +
'	</object>';
FlashWrite();

function FlashWrite(){
	var fla = document.getElementById("topFlash");
	if(fla){
		fla.style.width = flaWidth;
		fla.style.height = flaHeight;
		fla.innerHTML = FlashTag;
		return;
	}
	setTimeout(FlashWrite,1);
}