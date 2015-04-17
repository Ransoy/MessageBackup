$(function(){

	$('.prev_bulletin_frame').load(function(e) {
		var frame = this;
		setTimeout(function() {
			var height = frame.contentWindow.document.body.scrollHeight + 80;
			frame.style.height = height + "px";
		}, 5000);
	});

	$('#nav_preview_denko > ul > li > a').click(function(e){
		if(!$(this).hasClass('active')){
			$('#nav_preview_denko > ul > li > a').removeClass('active');
			$(this).addClass('active');

			var id = $(this).data('val');
			$('.frame_div').hide();
			$('#filtered-frame').attr('src', 'performer_denko.php?flv1='+id);
			$('#filtered-frame').show();
			$('#back').show();
		}

		e.preventDefault();
	});

	$('#back').click(function(e){
		$('#nav_preview_denko > ul > li > a').removeClass('active');
		$('#filtered-frame').hide();
		$('#back').hide();
		$('.frame_div').show();
	});

});//end onready
