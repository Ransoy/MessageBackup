

(function($) {
  $.ipop_variety = function() {
    var wx, wy;    // ウインドウの左上座標

    // ウインドウの座標を画面中央にする。
    wx = $(document).scrollLeft() + ($(window).width() - $('.popup-variety').outerWidth()) / 2;
    if (wx < 0) wx = 0;
    wy = $(document).scrollTop() + ($(window).height() - $('.popup-variety').outerHeight()) / 2;
    if (wy < 0) wy = 0;

    // ポップアップウインドウを表示する。
    $('.popup-variety').css({top: wy, left: wx}).fadeIn(100);

    // 閉じるボタンを押したとき
    $('.popup-variety-title_close').click(function() {$('.popup-variety').fadeOut(50);});
    $('.popup-variety-title_closebt').click(function() {$('.popup-variety').fadeOut(50);});

    $('.popup-variety-title_comentarea_time').mousedown(function(e) {
      return false;
    });

    // タイトルバーをドラッグしたとき
    $('.popup-variety').mousedown(function(e) {
      var mx = e.pageX;
      var my = e.pageY;
      $(document).on('mousemove.ipop_variety', function(e) {
        wx += e.pageX - mx;
        wy += e.pageY - my;
        $('.popup-variety').css({top: wy, left: wx});
        mx = e.pageX;
        my = e.pageY;
        return false;
      }).one('mouseup', function(e) {
        $(document).off('mousemove.ipop_variety');
      });
      return false;
    });
  }
})(jQuery);

