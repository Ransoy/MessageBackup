/*
 * blog page script
 */

$(document).on("pagebeforeload",function(){
	
});
document.getElementsByClassName('btn--logout').innerHTML = '���b�Z�[�W';
$(function(){
	// disable link
	$(document).on('click', 'a.disable', function(e){
		e.preventDefault();
	});

	// function like
	$(document).on('click', '.func_like', function(e){
		e.preventDefault();	
		// �����ˏ���
	});

	// delete article
	$(document).on('click', '.func_delete_article', function(e){
		if(!window.confirm('�{���ɍ폜���Ă�낵���ł����H')){
			// �폜����
			e.preventDefault();
		}
	});

	// delete comment
	$(document).on('click', '.func_delete_comment', function(e){
		e.preventDefault();
		if(window.confirm('�{���ɍ폜���Ă�낵���ł����H')){
			// �폜����
		}
	});

	// swipe and show functions buttons
	$('.func_swipe').swipe({
		swipeLeft: function(event, direction, distance, duration, fingerCount){
			$(this).animate({ marginLeft: '-14rem' }, 200);
		},
		swipeRight: function(event, direction, distance, duration, fingerCount){
			$(this).animate({ marginLeft: '0rem' }, 200);
		}
	});
	
	$('.log--cont').show();
	$('.log--cont').css('text-align', 'center');
	$('.btn--logout').show();
	$('.btn--logout').html('���b�Z�[�W');
	$('.btn--logout').attr('href', '/sp/performer/message/inbox.php');
});