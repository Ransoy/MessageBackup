
var popTimerID_ex;

/* マイク注意ポップアップ */
function popDisp(id,t){
	var divObj=$("div.popWin"+id);
	if(t){
		divObj.css({
			top:$(t).offset().top+8,
			left:$(t).offset().left-64
		});
	}
	// アニメーション
	divObj.stop().animate( {"opacity":"toggle"},{duration:500, easing:'swing'} );
	// 数秒後に自動的に閉じる処理
	autoClose();

	return false;
}

/* 10秒後に自動的に非表示になる */
function autoClose(){
	clearTimeout( popTimerID_ex );
	var divObj=$("div.popWindow");
	popTimerID_ex = setTimeout( function(){
		divObj.stop().css({opacity:1}).animate({opacity:"hide"});
	},10000 );
}
