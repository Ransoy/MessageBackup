$(function() {
	// カルーセルリスト複製
	var carouselCloneP = $('.carousel-m-contents').clone();
	var carouselCloneA = $('.carousel-m-contents').clone();
	
	// カルーセルリストを前後に表示、クラス名追加
	carouselCloneP.prependTo('.carousel-m-slide').addClass('first');
	carouselCloneA.appendTo('.carousel-m-slide').addClass('last');
	
	// メインのカルーセルリストにクラス名追加
	$('.carousel-m-contents').eq(1).addClass('main');
	// activeクラス初期位置設定
	$('.main').find('.carousel-m-banner').eq(0).addClass('active');
	
	// activeクラスのindex
	var activeIndexM = $('.active').index();
	
	// バナー1枚の幅
	var bannerWidth = $('.carousel-m-img').width();
	
	// バナーの枚数
	var banners = $('.main').children('li').length;
	
	// バナー全体の幅
	var bannersLength = bannerWidth * banners;
	
	// パフォーマーリスト数
	var pfIndexM = $('.active .pf-list-wrapper-m').find('ul').length;	

	
	/*** CSS追加 ***/
	// .carousel-m-base
	// バナー中央寄せ
	var bannerWidthHalf = -bannerWidth / 2;
	$('.carousel-m-base').css({
		'width' : bannerWidth,
		'margin-left' : bannerWidthHalf,
	});
	
	// .carousel-m-slide
	$('.carousel-m-slide').css({
		'width' : bannersLength,
	});
	
	// .carousel-m-contents
	$('.carousel-m-contents').css({
		'width' : bannersLength,
	});
	
	// .carousel-m-banner
	$('.carousel-m-banner').css({
		'width' : bannerWidth,
	});
	
	// . curtain
	// 幕
	$('.curtain').css({
		'width' : bannerWidth,
	});
	
	
	// カルーセルの動き
	function carouselM() {
		pfIndexM = $('.active .pf-list-wrapper-m').find('ul').length;

		var d = new $.Deferred;
		// ポジション値取得
		var carouselPositionF = parseInt($('.carousel-m-contents').first().css('left'));
		var carouselPositionM = parseInt($('.carousel-m-contents').eq(1).css('left'));
		var carouselPositionL = parseInt($('.carousel-m-contents').last().css('left'));
		
		// ポジションleft値取得
		var leftF = Math.ceil(carouselPositionF);
		var leftM = Math.ceil(carouselPositionM);
		var leftL = Math.ceil(carouselPositionL);
		
		// バナーリセット位置（1枚動いたら一番後ろにつく）
		//if(banners == 2) {
			//var stop = -(bannerWidth * (banners + 1));
		//}else {
			var stop = -(bannerWidth * banners) - bannerWidth;
		//}
		
		// 最初のカルーセルリスト
		$('.carousel-m-contents').first().animate({
			left : leftF + (-bannerWidth),
		}, 1000, function() {
			if(leftF == stop) {
				$(this).css('left', bannersLength * 2 - bannerWidth * 2);
			}
			
			d.resolve();
		});
		
		// メインのカルーセルリスト
		$('.carousel-m-contents').eq(1).animate({
			left: leftM + (-bannerWidth),
		}, 1000, function() {
			if(leftM == stop) {
				$(this).css('left', bannersLength * 2 - bannerWidth * 2);
			}
			
			d.resolve();

		});
		
		// 最後のカルーセルリスト
		$('.carousel-m-contents').last().animate({
			left: leftL + (-bannerWidth),
		}, 1000, function() {
			if(leftL == stop) {
				$(this).css('left', bannersLength * 2 - bannerWidth * 2);
			}
			
			d.resolve();
		});
		
		return d.promise();
	} // carouselM() 
	
	
	// オプションの動き
	function optionM() {
		var d = new $.Deferred;
		var a = false;
		// パフォーマーリスト数
		var pfIndexliM = $('.active .pf-list-wrapper-m').find('li').length;
		
		// ステータスバーアニメーション
		$('.active .event-data-wrapper').delay(300).animate({
			top : 0,
		}, 300);
		
		if(pfIndexliM == 0) {
			$('.active .pf-list-wrapper-m').css('background', 'none');
		}
		
		// パフォーマーアニメーション
		if(pfIndexM != 1) {	// パフォーマーリストが1つでない場合

			// 最初の一枚フェードインさせる
			$('.active .pf-list-wrapper-m ul').eq(0).fadeIn();
			$('.active .pf-list-wrapper-m').eq(0).delay(500).animate({	// 最初の1枚のアニメーション
				top : 40,
			}, 800, function() {
				$(this).find('ul').eq(0).delay(3000).fadeOut('slow', function() {
					// 2枚目以降のアニメーション
					for(var i=1; i<pfIndexM; i++) {
						var showPf = $('.active .pf-list-wrapper-m ul').eq(i).data('showPf');
						//var showDelay = $('.active .pf-list-wrapper-m ul').eq(i).data('showDelay');
						
						if(i != pfIndexM-1) {	// 最後の1枚以外のアニメーション
							$('.active .pf-list-wrapper-m ul').delay(showPf).eq(i).fadeIn('slow').delay(3000).fadeOut('slow');
						}else {	// 最後の1枚のアニメーション
							if($('.active .pf-list-wrapper-m ul').eq(i).hasClass('dataupcoming') && a == false){
								$('.active .pf-list-wrapper-m .costumelives').hide();
								$('.active .pf-list-wrapper-m .costumeupcoming').show();
								a = true;
							}
							
							$('.active .pf-list-wrapper-m ul').delay(showPf).eq(i).fadeIn('slow', function() {
								// パフォーマー枠隠す
								$('.active .pf-list-wrapper-m').delay(3000).animate({
									top : 200,
								}, 800, function() {
									$('.active .pf-list-wrapper-m ul').fadeOut();
								});
								
								// ステータスバー隠す
								$('.active .event-data-wrapper').delay(3700).animate({
									top : -40,
								}, 300, function() {
									
									if(activeIndexM+1 == banners) {	// それぞれ(.first .main .last)の最後のバナーの場合
										// .first .main .lastにactiveクラス移動 
										if($('.main .carousel-m-banner').hasClass('active')) {
											// 現在のactiveクラスを削除
											$('.main .active').removeClass('active');
											// .lastカルーセルリストの最初にactiveクラス追加
											$('.last .carousel-m-banner').eq(0).addClass('active');
											// activeIndexM初期化
											activeIndexM = 0;
										}else if($('.last .carousel-m-banner').hasClass('active')) {
											// 現在のactiveクラスを削除
											$('.last .active').removeClass('active');
											// .firstカルーセルリストの最初にactiveクラス追加
											$('.first .carousel-m-banner').eq(0).addClass('active');
											// activeIndexM初期化
											activeIndexM = 0;
										}else {
											// 現在のactiveクラスを削除
											$('.first .active').removeClass('active');
											// .mainカルーセルリストの最初にactiveクラス追加
											$('.main .carousel-m-banner').eq(0).addClass('active');
											// activeIndexM初期化
											activeIndexM = 0;
										}
									}else {
										// activeIndexMインクリメント
										activeIndexM += 1;
										
										if($('.main .carousel-m-banner').hasClass('active')) {
											// 現在のactiveクラスを削除
											$('.active').removeClass('active');
											// 次のバナーにactiveクラス追加
											$('.main .carousel-m-banner').eq(activeIndexM).addClass('active');
										}else if($('.last .carousel-m-banner').hasClass('active')) {
											// 現在のactiveクラスを削除
											$('.active').removeClass('active');
											// 次のバナーにactiveクラス追加
											$('.last .carousel-m-banner').eq(activeIndexM).addClass('active');											
										}else {
											// 現在のactiveクラスを削除
											$('.active').removeClass('active');
											// 次のバナーにactiveクラス追加
											$('.first .carousel-m-banner').eq(activeIndexM).addClass('active');											
										}	

									}
									
									d.resolve();
									playM();
									
								});
							});
						}	
					}
				});
			});
		}else {	// パフォーマーリストが1枚の場合
			
			if($('.active .pf-list-wrapper-m ul').eq(0).hasClass('dataupcoming') && a == false){
				$('.active .pf-list-wrapper-m .costumelives').hide();
				$('.active .pf-list-wrapper-m .costumeupcoming').show();
				a = true;
			}else{
				$('.active .pf-list-wrapper-m .costumelives').show();
				$('.active .pf-list-wrapper-m .costumeupcoming').hide();
			}
			$('.active .pf-list-wrapper-m ul').eq(0).fadeIn();
			$('.active .pf-list-wrapper-m').eq(0).delay(500).animate({	// 最初の1枚のアニメーション
				top : 40,
			}, 800, function() {
				var showPf = $('.active .pf-list-wrapper-m ul').data('showPf');
				//var showDelay = $('.active .pf-list-wrapper-m ul').data('showDelay');
				
				$('.active .pf-list-wrapper-m ul').delay(showPf).fadeIn('slow', function() {
					// パフォーマー枠隠す
					$('.active .pf-list-wrapper-m').delay(3000).animate({
						top : 200,
					}, 800, function() {
						$('.active .pf-list-wrapper-m ul').fadeOut();
					});
					
					// ステータスバー隠す
					$('.active .event-data-wrapper').delay(3700).animate({
						top : -40,
					}, 300, function() {
						
						if(activeIndexM+1 == banners) {	// それぞれ(.first .main .last)の最後のバナーの場合
							// .first .main .lastにactiveクラス移動 
							if($('.main .carousel-m-banner').hasClass('active')) {
								// 現在のactiveクラスを削除
								$('.main .active').removeClass('active');
								// .lastカルーセルリストの最初にactiveクラス追加
								$('.last .carousel-m-banner').eq(0).addClass('active');
								// activeIndexM初期化
								activeIndexM = 0;
							}else if($('.last .carousel-m-banner').hasClass('active')) {
								// 現在のactiveクラスを削除
								$('.last .active').removeClass('active');
								// .firstカルーセルリストの最初にactiveクラス追加
								$('.first .carousel-m-banner').eq(0).addClass('active');
								// activeIndexM初期化
								activeIndexM = 0;
							}else {
								// 現在のactiveクラスを削除
								$('.first .active').removeClass('active');
								// .mainカルーセルリストの最初にactiveクラス追加
								$('.main .carousel-m-banner').eq(0).addClass('active');
								// activeIndexM初期化
								activeIndexM = 0;
							}
						}else {
							// activeIndexMインクリメント
							activeIndexM += 1;
							
							if($('.main .carousel-m-banner').hasClass('active')) {
								// 現在のactiveクラスを削除
								$('.active').removeClass('active');
								// 次のバナーにactiveクラス追加
								$('.main .carousel-m-banner').eq(activeIndexM).addClass('active');
							}else if($('.last .carousel-m-banner').hasClass('active')) {
								// 現在のactiveクラスを削除
								$('.active').removeClass('active');
								// 次のバナーにactiveクラス追加
								$('.last .carousel-m-banner').eq(activeIndexM).addClass('active');											
							}else {
								// 現在のactiveクラスを削除
								$('.active').removeClass('active');
								// 次のバナーにactiveクラス追加
								$('.first .carousel-m-banner').eq(activeIndexM).addClass('active');											
							}	
		
						}
						
						d.resolve();
						playM();
						
					});
				});
			});


		}
		
		return d.promise();
	}	// optionM()
	
	//optionM();
	
	function link() {
		$('.carousel-m-banner').click(function() {
			var url = $('.active').data('url');
		
			if(url!='' || url=='undefined'){				
				window.open(url);
			}
			//return false;
		});
	}
	
	/*$('.thumb a').click(function() {
		$('.carousel-m-contents').die('click', link);
	});*/
	
	link();

	
	
	// ループ実行関数
	function playM() {
		optionM().then(carouselM);//.then(playM);
	}
	
	playM();
	
});