function makeLeaf(node,showarea){
	var retval='';
	if(node!='notNode' && node!='showNode'){
		$.each(node,function(){

// ----------------------------------------------------------------------------
// 2013-07-19 もっと見るボタン機能しない件の対応
			// 一旦置換を元の文字に戻す
			this.cn = this.cn.replace( 'aabbcc', '\\' );
// ----------------------------------------------------------------------------
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
					$('<img/>')[0].src = 'http://p.macherie.tv'+this.ph;
				} else {
					$('<img/>')[0].src = 'http://p.macherie.tv/imgs/op/120x90/'+this.ph;
				}

// パフォ写真高画質対応
				if( this.cl == "1" ){
					retval+='<li class="pic" style="text-align:center;">';
					if (this.zn=='event' && this.st=='ph' && this.evcnt > 0) {
						var px='';
						if (this.evcnt>=10) {
							px = '90px';
						} else {
							px = '100px';
						}

						retval+= '<div style="position:relative; text-align:left; font-size:25px; top:60px; left:' + px + ';" id="div_' + this.ohs +'">';
						retval+= '<span style="position: absolute;font-weight:bold;color:#ff3f00;" class="event_counter" id="' + this.ohs +'">' + this.evcnt + '</span>';
						retval+= '</div>';
					}

					retval+='<div class="tochat" alt="'+this.cn+'" value="'+this.hs+'">';
//					retval+='<img src="http://p.macherie.tv/imgs/op/120x90/'+this.ph+'" style="width:120px;height:90px;" alt="'+ this.cn +'" />';
					retval+='<img src="http://p.macherie.tv/imgs/op/120x90/'+this.ph+'" style="height:90px;" alt="'+ this.cn +'" />';
					retval+='</div>';
					retval+='</li>';
				}else{
					retval+='<li class="pic" style="background-image:url(http://p.macherie.tv/imgs/op/120x90/'+this.ph+');">';

					if (this.zn=='event' && this.st=='ph' && this.evcnt > 0) {
						var px='';
						if (this.evcnt>=10) {
							px = '90px';
						} else {
							px = '100px';
						}

						retval+= '<div style="position:relative; text-align:left; font-size:25px; top:60px; left:' + px + ';" id="div_' + this.ohs +'">';
						retval+= '<span style="position: absolute;font-weight:bold;color:#ff3f00;" class="event_counter" id="' + this.ohs +'">' + this.evcnt + '</span>';
						retval+= '</div>';
					}

					retval+='<div class="tochat" alt="'+this.cn+'" value="'+this.hs+'"></div>';
					retval+='</li>';
				}

/*
				retval+='<li class="pic" style="background-image:url(http://p.macherie.tv/imgs/op/120x90/'+this.ph+');">';
				retval+='<div class="tochat" alt="'+this.cn+'" value="'+this.hs+'"></div></li>';
*/
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
		retval+='<div><p>オンライン中のパフォーマーがいません</p>	</div>';
	}
	$(showarea).html(retval);
}

// ============================================================================
//
// 既存のmakeLeaf()を一部改変しました
//
// ============================================================================
function makeLeafAddDebut(node,showarea){
	var retval='';

	var id_cnt = 0;

	var now_html = $(showarea).html();	// 現時点のHTML

	if(node!='notNode' && node!='showNode'){
		$.each(node,function(){

// ----------------------------------------------------------------------------
// 2013-07-19 もっと見るボタン機能しない件の対応
			// 一旦置換を元の文字に戻す
			this.cn = this.cn.replace( 'aabbcc', '\\' );
// ----------------------------------------------------------------------------
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
					$('<img/>')[0].src = 'http://p.macherie.tv'+this.ph;
				} else {
					$('<img/>')[0].src = 'http://p.macherie.tv/imgs/op/120x90/'+this.ph;
				}

// パフォ写真高画質対応
				if( this.cl == "1" ){
					retval+='<li class="pic" style="text-align:center;">';
					retval+='<div class="tochat" alt="'+this.cn+'" value="'+this.hs+'">';
//					retval+='<img src="http://p.macherie.tv/imgs/op/120x90/'+this.ph+'" style="width:120px;height:90px;" alt="'+ this.cn +'" />';
					retval+='<img src="http://p.macherie.tv/imgs/op/120x90/'+this.ph+'" style="height:90px;" alt="'+ this.cn +'" />';
					retval+='</div>';
					retval+='</li>';
				}else{
					retval+='<li class="pic" style="background-image:url(http://p.macherie.tv/imgs/op/120x90/'+this.ph+');">';
					retval+='<div class="tochat" alt="'+this.cn+'" value="'+this.hs+'"></div>';
					retval+='</li>';
				}
/*
				retval+='<li class="pic" style="background-image:url(http://p.macherie.tv/imgs/op/120x90/'+this.ph+');">';
				retval+='<div class="tochat" alt="'+this.cn+'" value="'+this.hs+'"></div></li>';
*/
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
		retval+='<div><p>オンライン中のパフォーマーがいません</p>	</div>';
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
// reloadDebut()の機能に加え、全マシェリっこのidリストを取得する
// ====================================================================
function reloadDebut2(){

	var url;

	if( location.href.match(/^https:/) ){
		url = 'https://' + location.hostname;
	} else {
		url = 'http://' + location.hostname;
	}

	// 通信 -----------------------------------------------------------

	$.ajax({
			url: '/xxnodedebutList_neo.php',		// 通信先
			type: 'POST',							// GET or POST
			dataType: 'html',						// 受取るデータの種類
			success: function(data_from_ajax){		// 通信成功時に実行

				var list = data_from_ajax;
				var pp 	 = 1;
				var list_cnt = list.split( ',' ).length - 1;

				var post_data = { page : pp, all : 0 };
				$.ajax({
						url: '/xxnodedebutAll_neo.php',			// 通信先
						type: 'POST',							// GET or POST
						dataType: 'json',						// 受取るデータの種類
						data: post_data,						// 渡すデータ
						success: function(dd){					// 通信成功時に実行

							// 現在のページ数をリセット
							$("div#newface_id_list_cnt").html( list_cnt );
							$("div#newface_current_page").html( "1" );

							// 取得したものをHTMLに追加するだけ
							makeLeaf(dd.NewFace,'#newFace' );

							// もっと見るボタン
							// 全ての新人を表示したかどうかを判定する
							if( list_cnt <= 20 ){
								btnDebutMoreFin();
							}else{
								btnDebutMoreInitial();
							}

						},
						error: function(xhr, status, err){		// 通信失敗時に実行
						}
				});

			},
			error: function(xhr, status, err){		// 通信失敗時に実行
			}
	});
}

// ====================================================================
// もっと見るボタンの初期設定
// ====================================================================
function btnDebutMoreInitial(ATIME){

	// もっと見るボタンオブジェクト
	var img = $("li#btn_debut_more img");

	// 
	img.attr( "src", "http://c.macherie.tv/c/m/images/roombar/btn_debut_more.png" );
	img.css( "cursor", "auto" );

	// mouse関連メソッド解除
	img.unbind("mouseover").unbind("mouseout");

	img.mouseover( function(){
		// カーソルが当たったとき
		img.attr( "src", "http://c.macherie.tv/c/m/images/roombar/btn_debut_more_ov.png" );
		img.css( "cursor", "pointer" );
	}).mouseout(function(){
		// カーソルが外れたとき
		img.attr( "src", "http://c.macherie.tv/c/m/images/roombar/btn_debut_more.png" );
		img.css( "cursor", "auto" );
	});

	// mouserクリックイベント
	img.die( 'click' );

	img.live( 'click', function(){
//		addDebut(ATIME);
		addDebutSP(ATIME);
	});
}

// ====================================================================
// もっと見るボタンの終了設定
// ====================================================================
function btnDebutMoreFin(){

	// もっと見るボタンオブジェクト
	var img = $("li#btn_debut_more img");

	// fin画像を設定
	img.attr( "src", "http://c.macherie.tv/c/m/images/roombar/btn_debut_more_fin.png" );
	img.css( "cursor", "auto" );

	// mouse関連メソッド解除
	img.unbind("mouseover").unbind("mouseout");

	img.mouseover( function(){
		// カーソルが当たったとき
		img.attr( "src", "http://c.macherie.tv/c/m/images/roombar/btn_debut_more_fin_ov.png" );
		img.css( "cursor", "pointer" );
	}).mouseout(function(){
		// カーソルが外れたとき
		img.attr( "src", "http://c.macherie.tv/c/m/images/roombar/btn_debut_more_fin.png" );
		img.css( "cursor", "auto" );
	});

	// クリックイベント破棄
	img.die( "click" )

	img.live( 'click', function(){
		location.href = "#tpos";
	});

}

// ====================================================================
// 新たに20人分の採れたてマシェリっ子のデータを表示
// ====================================================================
/*
function addDebut(ATIME){
//alert(ATIME);
	var url;

	if( location.href.match(/^https:/) ){
		url = 'https://' + location.hostname;
	} else {
		url = 'http://' + location.hostname;
	}

	// 通信 -----------------------------------------------------------

	var p 	 = $("div#newface_current_page").html();		// ページ数
	var list_cnt = $("div#newface_id_list_cnt").html() - 1; // idリスト数

	// idリスト数
	var cc = new Number( list_cnt );

	// 現在のページを更新
	var pp = new Number( p );
	pp += 1;
	$("div#newface_current_page").html( pp );

	var post_data = { page : pp, all : 0, atime : ATIME };
	$.ajax({
			url: '/xxnodedebutAll_neo.php',			// 通信先
			type: 'POST',							// GET or POST
			dataType: 'json',						// 受取るデータの種類
			data: post_data,						// 渡すデータ
			success: function(data_from_ajax){		// 通信成功時に実行

				makeLeafAddDebut(data_from_ajax.NewFace,'#newFace' );

				// 全件表示した場合
				if( cc <= pp*20 ){
					btnDebutMoreFin();
				}else{
//					img.attr( "src", "http://c.macherie.tv/c/m/images/roombar/btn_debut_more.png" );
//					img.css( "cursor", "auto" );
				}

			},
			error: function(xhr, status, err){		// 通信失敗時に実行
				btnDebutMoreInitial();
			}
	});
}
*/

// ====================================================================
// 残り全員分の採れたてマシェリっ子のデータを表示
// ====================================================================
function addDebutSP(ATIME){

	var url;

	if( location.href.match(/^https:/) ){
		url = 'https://' + location.hostname;
	} else {
		url = 'http://' + location.hostname;
	}

	// 通信 -----------------------------------------------------------

	var p 	 = $("div#newface_current_page").html();		// ページ数
	var list_cnt = $("div#newface_id_list_cnt").html() - 1; // idリスト数

	// idリスト数
	var cc = new Number( list_cnt );

	// 現在のページを更新
	var pp = new Number( p );
	pp += 1;
	$("div#newface_current_page").html( pp );

// ----------------------------------------------------------------------------
// 2013-07-19 もっと見るボタン機能しない件の対応
	var post_data = { page : pp, all : 1, atime : ATIME };	// all=1 で、ページ数関係なく全レコード取得
	$.ajax({
			url: '/xxnodedebutAll_neoSP.php',		// 通信先
			type: 'POST',							// GET or POST
			dataType: 'json',						// 受取るデータの種類
			data: post_data,						// 渡すデータ
			success: function(data_from_ajax){		// 通信成功時に実行
				// 全件表示した場合
				btnDebutMoreFin();
				// 残り全部表示する
				makeLeafAddDebut(data_from_ajax.NewFace,'#newFace' );
			},
			error: function(xhr, status, err){		// 通信失敗時に実行
				// 禁止文字が含まれているがためにparse処理でエラーが起きてる可能性大！
				// 一旦文字置換してあげる
				try{
					var first = xhr.responseText.replace( /\\/g, 'aabbcc' );
					makeLeafAddDebut( jQuery.parseJSON( first ).NewFace,'#newFace' );
					// 全件表示した場合
					btnDebutMoreFin();
				}catch( e ){
				}
			}
	});
// ----------------------------------------------------------------------------
}

// ====================================================================
// 画面に表示中の採れたてマシェリっ子のデータ更新
// ====================================================================
/*
function updateDebut(){

	var url;

	if( location.href.match(/^https:/) ){
		url = 'https://' + location.hostname;
	} else {
		url = 'http://' + location.hostname;
	}

	// 通信 -----------------------------------------------------------

	var p 	 = $("div#newface_current_page").html();	// ページ数

	//var post_data = { page : p, all : 1 };			// all=1 で、そのpage数までの全レコードを取得
	var post_data = { page : p, all : 1, atime : aTime };
	$.ajax({
			url: '/xxnodedebutAll_neo.php',			// 通信先
			type: 'POST',							// GET or POST
			dataType: 'json',						// 受取るデータの種類
			data: post_data,						// 渡すデータ
			success: function(data_from_ajax){		// 通信成功時に実行
				makeLeaf(data_from_ajax.NewFace,'#newFace' );
			},
			error: function(xhr, status, err){		// 通信失敗時に実行
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

	// 通信 -----------------------------------------------------------

	var p 	 = $("div#newface_current_page").html();// ページ数

//	var post_data = { page : p, all : 0 };			// all=1 で、ページ数関係なく全レコード取得
	var post_data = { page : p, all : 0, atime : aTime };
	$.ajax({
			url: '/xxnodedebutAll_neoSP.php',		// 通信先
			type: 'POST',							// GET or POST
			dataType: 'json',						// 受取るデータの種類
			data: post_data,						// 渡すデータ
			success: function(data_from_ajax){		// 通信成功時に実行
				makeLeaf(data_from_ajax.NewFace,'#newFace' );
			},
			error: function(xhr, status, err){		// 通信失敗時に実行
// ----------------------------------------------------------------------------
// 2013-07-19 もっと見るボタン機能しない件の対応
				// 禁止文字が含まれているがためにparse処理でエラーが起きてる可能性大！
				// 一旦文字置換してあげる
				try{
					var first = xhr.responseText.replace( /\\/g, 'aabbcc' );
					makeLeaf( jQuery.parseJSON( first ).NewFace,'#newFace' );
				}catch( e ){
				}
// ----------------------------------------------------------------------------
			}
	});

}

// 採れたてマシェリっ子（オンラインのみ）用
function updateDebutOnline(){

	var url;

	if( location.href.match(/^https:/) ){
		url = 'https://' + location.hostname;
	} else {
		url = 'http://' + location.hostname;
	}

	// 通信 -----------------------------------------------------------

	var p 	 = $("div#newface_current_page").html();// ページ数

	var post_data = { page : p, all : 0 };			// all=1 で、ページ数関係なく全レコード取得
	$.ajax({
			url: '/xxnodedebutAll_neoOnline.php',	// 通信先
			type: 'POST',							// GET or POST
			dataType: 'json',						// 受取るデータの種類
			data: post_data,						// 渡すデータ
			success: function(data_from_ajax){		// 通信成功時に実行
				makeLeaf(data_from_ajax.NewFaceOnline,'#newFaceOnline' );
			},
			error: function(xhr, status, err){		// 通信失敗時に実行
			}
	});

}

// 採れたてマシェリっ子（全員）用
function updateDebutAll(){

	var url;

	if( location.href.match(/^https:/) ){
		url = 'https://' + location.hostname;
	} else {
		url = 'http://' + location.hostname;
	}

	// 通信 -----------------------------------------------------------

	var p 	 = $("div#newface_current_page").html();// ページ数

	var post_data = { page : p, all : 0 };			// all=1 で、ページ数関係なく全レコード取得
	$.ajax({
			url: '/xxnodedebutAll_all.php',		// 通信先
			type: 'POST',							// GET or POST
			dataType: 'json',						// 受取るデータの種類
			data: post_data,						// 渡すデータ
			success: function(data_from_ajax){		// 通信成功時に実行
				makeLeaf(data_from_ajax.NewFace,'#newFace' );
			},
			error: function(xhr, status, err){		// 通信失敗時に実行
			}
	});

}


/* 画像のプリロード（先読み） */
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
		case 4:		//楽天
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
// オンライン中パフォーマーエリア ⇒ 該当エリアのみの更新
//$(".btn_reload").live('click',function(){reloadGirl($('#m').val());reloadDebut();});
$(".btn_reload").live('click',function(){reloadGirl($('#m').val());});
$(".btn_reload_w").live('click',function(){reloadGirl(0);});
// 採れたてマシェリっ子エリア
//$(".btn_reload_debut").live('click',function(){updateDebut();});
$(".btn_reload_debut").live('click',function(){updateDebutSP();});
// 採れたてマシェリっ子（オンライン）エリア
$(".btn_reload_debut_online").live('click',function(){updateDebutOnline();});
// 採れたてマシェリっ子（全員）エリア
$(".btn_reload_debut_all").live('click',function(){updateDebutAll();});

$(function(){
    setInterval(function(){
        var hs = '';

        $.ajax({
            type: "GET",
            url: "/party-count.php",
            cache: false,
            success: function(res){
                var data = res.split(",");
                var item = '';
                var party_count_arr = {};

                for (i=0; i<data.length; i++) {
                    item = data[i].split("=");
                    party_count_arr[item[0]] = item[1];
                }

                var selcts = $(".event_counter");
                for (var i=0; i<selcts.length ; i++){
                    hs = $(selcts[i]).attr('id');
                    //コンテンツ表示
                    if (party_count_arr[hs] >= 10) {
                        $("#div_" + hs).css("left", "90px");
                    } else {
                        $("#div_" + hs).css("left", "100px");
                    }
                    $("#" + hs).text(party_count_arr[hs]);
                }

            }
        });
    },30000);
    // オンライン出演者自動更新の動作
    var active_tab = true;  // アクティブタブのみ、自動更新を有効にする
    var auto_reload_flg = $.cookie("auto_reload_flg");
	if ( auto_reload_flg == undefined ){
		$.cookie("auto_reload_flg",0, { path:"/", expires: 60 }); //デフォルトはOFFにする
		auto_reload_flg = 0;
	}
	if ( auto_reload_flg == 1 ){
		// update checkbox
		$(".btn_reload_switch img").attr("src", "/c/m/images/onoffswitch/on.png");
	}else{
		$(".btn_reload_switch img").attr("src", "/c/m/images/onoffswitch/off.png");
	}
	setInterval(function() {   //calls click event after a certain time
	   auto_reload_flg = $.cookie("auto_reload_flg");
	   if ( auto_reload_flg == 1 && active_tab == true ){
	   		reloadGirl($('#m').val());
	   }
	}, 60000);

	$(".btn_reload_switch").click(function(){
		auto_reload_flg = $.cookie("auto_reload_flg");
		if (auto_reload_flg == 1) {
			$(".btn_reload_switch img").attr("src", "/c/m/images/onoffswitch/off.png");
			//auto_reload_flg = false;
			$.cookie("auto_reload_flg",0,{ path:"/", expires: 60 });//---trueの時はfalseに
		}else{
			$(".btn_reload_switch img").attr("src", "/c/m/images/onoffswitch/on.png");
			//auto_reload_flg = true;
			$.cookie("auto_reload_flg",1,{ path:"/", expires: 60 });//---falseの時はtrueに
		}
	});
/*
	$(window).on("blur focus", function(e) {
	    var prevType = $(this).data("prevType");
	    if (prevType != e.type) {   //  reduce double fire issues
	        switch (e.type) {
	            case "blur":
	                active_tab = false;
	                break;
	            case "focus":
	                active_tab = true;
	                break;
	        }
	    }

	    $(this).data("prevType", e.type);
	})
*/
});
