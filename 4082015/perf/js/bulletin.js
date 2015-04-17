// Wait until the DOM has loaded before querying the document
$(function() {

  var interval = 60000;
  var loadingHtml = '<div style="text-align:center; margin:50px auto;"><img src="/performer/img/loading.gif"></div>';

  $('.tabs ul').each(function() {
    var $active, $content, $links = $(this).find('a');
    $active = $($links.filter('[href="'+location.hash+'"]')[0] || $links[0]);
    $active.addClass('active');

    $content = $($active[0].hash);
    $links.not($active).each(function () {
      $(this.hash).hide();
    });
    
    $content.html(loadingHtml);
    $.get(
      'bulletin_list.php', 
      { is : 1 },
      function(data) {
        $content.html(data);
      }
    );

    $(this).on('click', 'a', function(e) {
      $active = $(this).closest('ul').find('a.active');
      $active.removeClass('active');
      $content.hide();

      $active = $(this);
      $content = $(this.hash);

      $content.html(loadingHtml);
      $.get(
        'bulletin_list.php', 
        { is : this.hash.substr(this.hash.length - 1) },
        function(data) {
          $content.html(data);
        }
      );

      $active.addClass('active');
      $content.show();

      clearInterval(time);
      time = setInterval(function() {
        reloadList('bulletin_list.php');
      }, interval);

      e.preventDefault();
    });
  });

  $('.tabs').on('click', '.btn > a', function(e) {
    var $active = $('.tabs ul li a.active');
    var $content = $($active.get(0).hash);

    $active.removeClass('active');
    $content.hide();

    $active = $('.tabs ul li a[href="' + this.hash + '"]');
    $content = $(this.hash);

    $content.html(loadingHtml);
    $('.tabs').get(0).scrollIntoView();
    // reload list
    $.get(
      'bulletin_list.php', 
      { is : this.hash.substr(this.hash.length - 1) },
      function(data) {
        $content.html(data);
      }
    );

    $active.addClass('active');
    $content.show();

    clearInterval(time);
    time = setInterval(function(){
      reloadList('bulletin_list.php');
    }, interval);

    e.preventDefault();
  });

  $('.tabs').on('click', '.show_detail', function(e) {

    clearInterval(time);

    var seqno = $(this).data('seqno');
    var $content = $(this).closest('div');
    var tabId = $content.attr('id');
    var is = tabId.substr(tabId.length - 1);
    $.get(
      'bulletin_detail.php', 
      { seqno : seqno, is : is },
      function(data) {
        $content.contents().hide();
        $content.append(data);
        /*$content.html(data);*/
        $('.tabs').get(0).scrollIntoView();
      }
    );
  });

  $('.tabs').on('click', '.back_to_list', function(e) {
    // start my timer
    time = setInterval(function() {
      reloadList('bulletin_list.php');
    }, interval);

    var $content = $(this).parent().parent('div');
    $content.children(':visible').remove();
    $content.contents().show();
    $('.tabs').get(0).scrollIntoView();
  });

 /**
  * PAGINATION
  */
  $('.tabs').on('click', '.page_item', function(e) {
    e.preventDefault();
    var url = $(this).attr('href'); 

    var $content = $(this).closest('div').parent();
    var tabId = $content.attr('id');
    var is = tabId.substr(tabId.length - 1);
    
    $content.html(loadingHtml);
    $('.tabs').get(0).scrollIntoView();
    $.get(
      url,
      { is : is },
      function(data) {
        $content.html(data);
      }
    );

    clearInterval(time);
    time = setInterval(function() {
      reloadList(url);
    }, interval);

    return false;
  });

  var time = setInterval(function() {
    reloadList('bulletin_list.php');
  }, interval);

});

// Refreshes bulletin list
function reloadList(url){
  var $active = $('.tabs ul li a.active');
  var hash = $active.get(0).hash;
  var $content = $(hash);
  $.get(
    // 'bulletin_list.php'
    url,
    { is : hash.substr(hash.length - 1) },
    function(data) {
      $content.html(data);
    }
  );
}