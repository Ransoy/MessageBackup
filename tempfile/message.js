/*
 * blog page script
 */
 
$(function(){
	// delete message list item button
	$(document).on('click', '.func_del_message_list_item', function(e){
		e.preventDefault();
		e.stopPropagation();
		
		if(confirm('この子とのやりとりを削除してよろしいですか？')){
			// execute
		}
	});

	// block contact list item button
	$(document).on('click', '.func_block_contact_list_item', function(e){
		e.preventDefault();
		e.stopPropagation();
		
		if(confirm('この子からのメールを受信拒否にします。よろしいですか？')){
			// execute
		}
	});

	// disable link
	$(document).on('click', 'a.disable', function(e){
		e.preventDefault();
	});

	// table row bigger link
	$(document).on('click', 'tr.b_link_tr', function(e){
		window.location = $('a', this).attr('href');
	});

	// switch panel menu
	$('#switch_panel_menu').on('click', function(e){
		e.preventDefault();
		if($('#panel_menu').is(':visible')){
			$(this).removeClass('on');
			$('#panel_menu').fadeOut(200);
		}else{
			$(this).addClass('on');
			$('#panel_menu').fadeIn(200);
		}
	});

	// favorite button
	$('.btn_favorite').on('click', function(e){
		e.preventDefault();
		if($(this).hasClass('on')){
			$(this).removeClass('on');
			$.post('ajaxController.php',{unfavorite:$('.user-id').val()},function(data){
				
			});
		}else{
			$(this).addClass('on');
			$.post('ajaxController.php',{favorite:$('.user-id').val()},function(data){
				
			});
		}
	});

	// block button
	$('.btn_block').on('click', function(e){
		e.preventDefault();
		if($(this).hasClass('on')){
			if(confirm('拒否解除します。よろしいですか？')){
				$(this).removeClass('on')
				$.post('ajaxController.php',{unblock:$('.user-id').val()},function(data){
					;
				});
			}
		}else{
			if(confirm('このパフォーマーを受信拒否します。よろしいですか？')){
				$(this).addClass('on');
				$.post('ajaxController.php',{block:$('.user-id').val()},function(data){
					
				});
			}
		}
	});

	// photo button
	$('#btn_photo').on('click', function(e){
		e.preventDefault();
		if($('#photo_panel').is(':visible')){
			$(this).removeClass('on');
			$('#photo_panel').slideUp(200);
		}else{
			$(this).addClass('on');
			$('#photo_panel').slideDown(200);
			// emoji reset
			$('#btn_emoji').removeClass('on');
			$('#emoji_panel').hide();
		}
	});

	// emoji button
	$('#btn_emoji').on('click', function(e){
		e.preventDefault();
		if($('#emoji_panel').is(':visible')){
			$(this).removeClass('on');
			$('#emoji_panel').slideUp(200);
		}else{
			$(this).addClass('on');
			$('#emoji_panel').slideDown(200);
			// photo reset
			$('#btn_photo').removeClass('on');
			$('#photo_panel').hide();
		}
	});
	
	// emoji tab
	$(document).on('click', '.emoji_tab_menu li', function(e){
		e.preventDefault();
		var target_tab = $('#' + $(this).attr('data-target'));
		var tabs = $('.emoji_tab');
		var tab_menu = $('.emoji_tab_menu li');
		if(!target_tab.is(':visible')){
			tab_menu.removeClass('on');
			$(this).addClass('on');
			tabs.hide();
			target_tab.show();
		}
	});

	// photo button
	$(document).on('click', '#btn_photo', function(e){
		e.preventDefault();
		$('#input_photo').click();
	});

	// message list edit
	$(document).on('click', '.message_list--edit .message_list_item .thumb, .message_list--edit .message_list_item .message_data', function(e){
		e.preventDefault();
		e.stopPropagation();
		var scope = $(this).closest('.message_list_item');
		
		if($(':checkbox', scope).prop('checked') === true){
			$(':checkbox', scope).prop('checked', false);
			scope.removeClass('on');

			// disable button >>>
			if($('.message_list--edit input:checked').size() == 0){
				$('[id^=exe]').addClass('disable');	
			}
			// <<<
		}else{
			$(':checkbox', scope).prop('checked', true);
			scope.addClass('on');

			// active button >>>
			$('[id^=exe]').removeClass('disable');
			// <<<
		}
	});

	$(document).on('change', '.message_list--edit .message_list_item :checkbox', function(e){
		if($(this).prop('checked') === true){
			$(this).closest('.message_list_item').addClass('on');
			
			// active button >>>
			$('[id^=exe]').removeClass('disable');
			// <<<
		}else{
			$(this).closest('.message_list_item').removeClass('on');

			// disable button >>>
			if($('.message_list--edit input:checked').size() == 0){
				$('[id^=exe]').addClass('disable');	
			}
			// <<<
		}
	});

	// delete message list 
	$(document).on('click', '#exe_delete_message_list', function(e){
		e.preventDefault();
		if($(this).hasClass('disable')){
			return false;
		}
		if(confirm('チェックしたメール履歴を削除します。よろしいですか？')){
			// execute
		}
	});

	// block contact list 
	$(document).on('click', '#exe_block_contact_list', function(e){
		e.preventDefault();
		if($(this).hasClass('disable')){
			return false;
		}
		if(confirm('チェックした連絡先を受信拒否します。よろしいですか？')){
			// execute
		}
	});

	// cancel block list 
	$(document).on('click', '#exe_cancel_block_list', function(e){
		e.preventDefault();
		if($(this).hasClass('disable')){
			return false;
		}
		if(confirm('チェックした連絡先を拒否解除します。よろしいですか？')){
			// execute
		}
	});

	// message sort menu
	/*$(document).on('click', '#message_sort_menu a', function(e){
		e.preventDefault();
		var scope = $('#message_sort_menu');
		$('li', scope).removeClass('on');
		$(this).closest('li').addClass('on');
	});*/

});