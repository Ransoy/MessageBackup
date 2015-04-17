$(function(){

	$.get('article_box.php',function(data){
		$('.load_data').html(data);
		$('a[rel*=leanModal]').leanModal();
	});
})