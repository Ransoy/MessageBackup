$(function(){
	// �����j���[
	$(document).on('click', '.hiraku', function(){
		if($('.menu_content').is(':hidden')){
			$('.menu_content_refine').hide();
			$('.ddmenu > p').removeClass('on');
			$('.menu_content').slideDown();	
			$(this).addClass('on');			
		}else{
			$('.menu_content').slideUp();
			$(this).removeClass('on');	
		}
	});

	// �E���j���[
	$(document).on('click', '.hiraku_refine', function(){
		if($('.menu_content_refine').is(':hidden')){
			$('.menu_content').hide();
			$('.ddmenu > p').removeClass('on');
			$('.menu_content_refine').slideDown();
			$(this).addClass('on');
		}else{
			$('.menu_content_refine').slideUp();
			$(this).removeClass('on');
		}
    	});

	// ����������
	$(document).on('click', '.search_start', function(){
		$('.menu_content').hide();
		$('.menu_content_refine').hide();
		$('.ddmenu > p').removeClass('on');
		
		// �ʒu�ړ�
		var pos = $('.ddmenu').position().top;
		$('html,body').animate({ scrollTop: pos }, 200);

	});
});
