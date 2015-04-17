

(function($) {
  $.ipop_pafo_edit = function() {
    var wx, wy;    // ウインドウの左上座標

    // ウインドウの座標を画面中央にする。
    wx = $(document).scrollLeft() + ($(window).width() - $('.popup-pafo_edit').outerWidth()) / 2;
    if (wx < 0) wx = 0;
    wy = $(document).scrollTop() + ($(window).height() - $('.popup-pafo_edit').outerHeight()) / 2;
    if (wy < 0) wy = 0;

    // ポップアップウインドウを表示する。
    $('.popup-pafo_edit').css({top: wy, left: wx}).fadeIn(100);

    // 閉じるボタンを押したとき
    $('.popup-pafo_edit-title_close').click(function() {$('.popup-pafo_edit').fadeOut(50);});

    // マウスムーブを無視させる
    $('.popup-pafo_edit-title_comentarea_time').mousedown(function(e) {
      return false;
    });

    // タイトルバーをドラッグしたとき
    $('.popup-pafo_edit').mousedown(function(e) {
      var mx = e.pageX;
      var my = e.pageY;
      $(document).on('mousemove.ipop_pafo_edit', function(e) {
        wx += e.pageX - mx;
        wy += e.pageY - my;
        $('.popup-pafo_edit').css({top: wy, left: wx});
        mx = e.pageX;
        my = e.pageY;
        return false;
      }).one('mouseup', function(e) {
        $(document).off('mousemove.ipop_pafo_edit');
      });
      return false;
    });
  }
})(jQuery);


