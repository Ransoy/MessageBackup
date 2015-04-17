

(function($) {
  $.ipop_performer = function() {
    var wx, wy;    // ウインドウの左上座標

    // ウインドウの座標を画面中央にする。
    wx = $(document).scrollLeft() + ($(window).width() - $('.popup-performer').outerWidth()) / 2;
    if (wx < 0) wx = 0;
    wy = $(document).scrollTop() + ($(window).height() - $('.popup-performer').outerHeight()) / 2;
    if (wy < 0) wy = 0;

    // ポップアップウインドウを表示する。
    $('.popup-performer').css({top: wy, left: wx}).fadeIn(100);

    // 閉じるボタンを押したとき
    $('.popup-performer-title_close').click(function() {$('.popup-performer').fadeOut(50);});
    $('.popup-performer-title_closebt').click(function() {$('.popup-performer').fadeOut(50);});

    $('.popup-performer-title_comentarea_time').mousedown(function(e) {
      return false;
    });

    // タイトルバーをドラッグしたとき
    $('.popup-performer').mousedown(function(e) {
      var mx = e.pageX;
      var my = e.pageY;
      $(document).on('mousemove.ipop_performer', function(e) {
        wx += e.pageX - mx;
        wy += e.pageY - my;
        $('.popup-performer').css({top: wy, left: wx});
        mx = e.pageX;
        my = e.pageY;
        return false;
      }).one('mouseup', function(e) {
        $(document).off('mousemove.ipop_performer');
      });
      return false;
    });
  }
})(jQuery);

