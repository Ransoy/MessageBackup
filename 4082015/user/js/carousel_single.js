$(function() {
	// 画像index取得
	var carouselIndex = $('.carousel-contents').index();
	// activeクラスindex取得
	var activeIndex = $('.active').index();
	// パフォーマーindex取得
	var pfIndex = $('.active').find( '.pf-list-wrapper ul').length;
	var a = false;
	// オプション動き 最初
	function option() {
		var d = new $.Deferred;
		var showPf = $('.active').find('.pf-list-wrapper ul').eq(0).data('showPf'); 
		
		// ステータスバー
		$('.event-data-wrapper').delay(300).animate({
			top: 0,
		}, 300);
		
		// パフォーマー
		var showDelay = $('.active').find('.pf-list-wrapper').eq(0).data('showDelay');

		$('.active').find('.pf-list-wrapper').eq(0).delay(showDelay).animate({
			top: 40,
		}, 800);
		
		$('.active').find('.pf-list-wrapper ul').eq(0).fadeIn().delay(3000).fadeOut('slow', function() { d.resolve(); });
		if($('.active .pf-list-wrapper ul').eq(0).hasClass('datalive') && a == true){
			$('.active').find('.pf-list-wrapper .costumelives').show();
			$('.active').find('.pf-list-wrapper .costumeupcoming').hide();
			a = false;
		}
		return d.promise();
	}
	
	// オプション動き 二番目以降
	function option2() {
		var d = new $.Deferred;
		
		// パフォーマー		
		for(var i=1; i<pfIndex; i++) {
			var showPf = $('.active').find('.pf-list-wrapper ul').eq(i).data('showPf');
			
			if(i  != pfIndex - 1) {
				$('.active').find('.pf-list-wrapper ul').delay(showPf).eq(i).fadeIn('slow').delay(3000).fadeOut('slow');
				
				
			}else {
				if($('.active').find('.pf-list-wrapper ul').eq(i).hasClass('dataupcoming') && a == false){
					$('.active').find('.pf-list-wrapper .costumelives').hide();
					$('.active').find('.pf-list-wrapper .costumeupcoming').show();
					a = true;
				}
				
				$('.active').find('.pf-list-wrapper ul').delay(showPf).eq(i).fadeIn('slow').delay(3000).fadeOut('slow',  function() {
					
					d.resolve();
					play();
				});
			}
		}			
		
		return d.promise();
	}
	
	function option3() {
		var showPf = $('.active').find('.pf-list-wrapper ul').eq(0).data('showPf'); 
		
		// ステータスバー
		$('.event-data-wrapper').delay(300).animate({
			top: 0,
		}, 300);
		
		// パフォーマー
		var showDelay = $('.active').find('.pf-list-wrapper').eq(0).data('showDelay');

		$('.active').find('.pf-list-wrapper').eq(0).delay(showDelay).animate({
			top: 40,
		}, 800);
	}
		
	function link() {
		$('.carousel-contents').click(function() {
			var url = $('.active').data('url');
			
			if(url!='' || url=='undefined' ){
				window.open(url);
			}
			
					
			
			//return false;
		});
	}
	
	$('.thumb a').click(function() {
		$('.carousel-contents').die('click', link);
	});
	
	link();
	
	function play() {
		option().then(option2);
	}
	
	//option();
	if(pfIndex == 1) {
		option3();
	}else if(pfIndex >= 2) {
		play();
	}
});