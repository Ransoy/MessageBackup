
(function($) {
  $.ipop = function() {
    var wx, wy;    // ウインドウの左上座標

    // ウインドウの座標を画面中央にする。
    wx = $(document).scrollLeft() + ($(window).width() - $('#ipop').outerWidth()) / 2;
    if (wx < 0) wx = 0;
    wy = $(document).scrollTop() + ($(window).height() - $('#ipop').outerHeight()) / 2;
    if (wy < 0) wy = 0;

    // ポップアップウインドウを表示する。
    $('#ipop').css({top: wy, left: wx}).fadeIn(100);

    // 閉じるボタンを押したとき
    $('#ipop_close').click(function() {$('#ipop').fadeOut(100);});

    // タイトルバーをドラッグしたとき
    $('#ipop_title').mousedown(function(e) {
      var mx = e.pageX;
      var my = e.pageY;
      $(document).on('mousemove.ipop', function(e) {
        wx += e.pageX - mx;
        wy += e.pageY - my;
        $('#ipop').css({top: wy, left: wx});
        mx = e.pageX;
        my = e.pageY;
        return false;
      }).one('mouseup', function(e) {
        $(document).off('mousemove.ipop');
      });
      return false;
    });
  }
})(jQuery);

