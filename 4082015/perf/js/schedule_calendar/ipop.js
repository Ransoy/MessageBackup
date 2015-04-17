
(function($) {
  $.ipop = function(open_id,ipop_name) {
    var wx, wy;    // ウインドウの左上座標

	//他ウィンドウを閉じる
	if($('.popup-member').css('display') == 'block'){
		$('.popup-member').fadeOut(50);
	}
	if($('.popup-performer').css('display') == 'block'){
		$('.popup-performer').fadeOut(50);
	}
	if($('.popup-pafo_edit').css('display') == 'block'){
		$('.popup-pafo_edit').fadeOut(50);
	}
	if($('.popup-pafo_new').css('display') == 'block'){
		$('.popup-pafo_new').fadeOut(50);
	}
    // ウインドウの座標を画面中央にする。
    wx = $(document).scrollLeft() + ($(window).width() - $(open_id).outerWidth()) / 2;
    if (wx < 0) wx = 0;
    wy = $(document).scrollTop() + ($(window).height() - $(open_id).outerHeight()) / 2;
    if (wy < 0) wy = 0;

    // ポップアップウインドウを表示する。
    $(open_id).css({top: wy, left: wx}).fadeIn(100);

    // 閉じるボタンを押したとき
	var close_name = open_id + '-title_close';
	var close_bt   = open_id + '-title_closebt';
    $(close_name).click(function() {$(open_id).fadeOut(50);});
    $(close_bt).click(function() {$(open_id).fadeOut(50);});

	// 閉じるボタンを押したとき
	var close_name = open_id + '-title_close';
	var close_bt   = open_id + '-title_closebt';
	$(close_name).click(function() {$(open_id).fadeOut(50);});
	$(close_bt).click(function() {$(open_id).fadeOut(50);});

	// タイトルバーをドラッグしたとき
	var un_drag = open_id + '-undrag';
	$(open_id).draggable({
		cursor: 'move',
		containment: 'document',
		cancel: un_drag,
		scroll: false
	});

  }
})(jQuery);

