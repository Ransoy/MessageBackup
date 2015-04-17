

(function($) {
  $.ipop_mysc_new = function() {
    var wx, wy;    // ウインドウの左上座標

    // ウインドウの座標を画面中央にする。
    wx = $(document).scrollLeft() + ($(window).width() - $('.popup-mysc_new').outerWidth()) / 2;
    if (wx < 0) wx = 0;
    wy = $(document).scrollTop() + ($(window).height() - $('.popup-mysc_new').outerHeight()) / 2;
    if (wy < 0) wy = 0;

    // ポップアップウインドウを表示する。
    $('.popup-mysc_new').css({top: wy, left: wx}).fadeIn(100);

    // 閉じるボタンを押したとき
    $('.popup-mysc_new-title_close').click(function() {$('.popup-mysc_new').fadeOut(50);});

    // マウスムーブを無視させる
    $('.popup-mysc_new-title_comentarea_time').mousedown(function(e) {
      return false;
    });

    // タイトルバーをドラッグしたとき
    $('.popup-mysc_new').mousedown(function(e) {
      var mx = e.pageX;
      var my = e.pageY;
      $(document).on('mousemove.ipop_mysc_new', function(e) {
        wx += e.pageX - mx;
        wy += e.pageY - my;
        $('.popup-mysc_new').css({top: wy, left: wx});
        mx = e.pageX;
        my = e.pageY;
        return false;
      }).one('mouseup', function(e) {
        $(document).off('mousemove.ipop_mysc_new');
      });
      return false;
    });
  }
})(jQuery);


