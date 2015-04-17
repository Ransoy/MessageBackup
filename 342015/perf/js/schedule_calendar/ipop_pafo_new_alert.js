

(function($) {
  $.ipop_pafo_new_alert = function() {
    var wx, wy;    // ウインドウの左上座標

    // ウインドウの座標を画面中央にする。
    wx = $(document).scrollLeft() + ($(window).width() - $('.popup-pafo_new_alert').outerWidth()) / 2;
    if (wx < 0) wx = 0;
    wy = $(document).scrollTop() + ($(window).height() - $('.popup-pafo_new_alert').outerHeight()) / 2;
    if (wy < 0) wy = 0;

    // ポップアップウインドウを表示する。
    $('.popup-pafo_new_alert').css({top: wy, left: wx}).fadeIn(100);

    // 閉じるボタンを押したとき
    $('.popup-pafo_new_alert-title_cancellbt').click(function() {$('.popup-pafo_new_alert').fadeOut(50);});

    // マウスムーブを無視させる
    $('.popup-pafo_new_alert-title_comentarea_time').mousedown(function(e) {
      return false;
    });

    // タイトルバーをドラッグしたとき
    $('.popup-pafo_new_alert').mousedown(function(e) {
      var mx = e.pageX;
      var my = e.pageY;
      $(document).on('mousemove.ipop_pafo_new_alert', function(e) {
        wx += e.pageX - mx;
        wy += e.pageY - my;
        $('.popup-pafo_new_alert').css({top: wy, left: wx});
        mx = e.pageX;
        my = e.pageY;
        return false;
      }).one('mouseup', function(e) {
        $(document).off('mousemove.ipop_pafo_new_alert');
      });
      return false;
    });
  }
})(jQuery);


