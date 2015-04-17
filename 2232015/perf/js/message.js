/*
 * performer message page script
 */
var fromId;
var fromType;
var toId;
var toType;
var contactId;
var img;
var messageDetail;
var messageAjax;
var hash;
 
$(function() {
	
	fromId 		= $('#from-id').val();
	fromType 	= $('#from-type').val();
	toId 		= $('#to-id').val();
	toType 		= $('#to-type').val();
	contactId 	= $('#contact-id').val();
	img 		= $('#image-profile').val();
	messageDetail = $('#message_detail_area');
	hash 		= $('#hash').val();
	
	// delete message list item button
	$(document).on('click', '.func_del_message_list_item', function(e){
		e.preventDefault();
		e.stopPropagation();
	
		if(confirm('この子とのやりとりを削除してよろしいですか？')){
			 window.location = $(this).attr('href');
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
		if(e.preventDefault) {
			e.preventDefault();
		} else {
			e.returnValue = false;
		}
	});

	// table row bigger link
	$(document).on('click', 'tr.b_link_tr', function(e){
		window.location = $('a', this).attr('href');
	});
	$(document).on('click', '.bl-bigger', function() {
		window.location = $(this).attr('href');
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


	// block button
	$('.btn_block').on('click', function(e){
		e.preventDefault();
		if (!$(this).hasClass('disable')) {
			var type = 2;
			var contact_id = $('#contact-id').val();
			var from_id	= encodeURIComponent($('#from-id').val());
			var to_id	= encodeURIComponent($('#to-id').val());
			if($(this).hasClass('on')){
				if(confirm('拒否解除します。よろしいですか？')){
					$(this).removeClass('on')
					$.post('/ajax/message/contact_block.php',{unblock:contact_id,type:type,from_id:from_id,to_id:to_id},function(data){
						
					});
				}
			}else{
				if(confirm('このユーザーを受信拒否します。よろしいですか？')){
					$(this).addClass('on');
					$.post('/ajax/message/contact_block.php',{block:contact_id,type:type,from_id:from_id,to_id:to_id},function(data){
						
					});
				}
			}
		}
	});
	
	


	// load more
	//10 is the records per page 
	var page = 0;
	var messages = $('#total').val();
	messages = parseInt(messages) - 10;
	var totalPages = (messages/10);
	totalPages 	= parseInt(totalPages);
	var date	= $('.append-data').find("#last-date").attr('value');
	var last_id	= $('.message_detail_item').first().attr('message-id');
	
	$(document).on('click','.btn_more',function(){
		page++;
		var from_id   	= encodeURIComponent($('#from-id').val());
		var to_id 	  	= encodeURIComponent($('#to-id').val());
		var last_date 	= $('#last-date').val();
		var from_type 	= $('#from-type').val();
		var to_type	  	= $('#to-type').val();
		var image 	  	= $('#image-profile').val();
		var fulldate  	= $('#lastfulldate').val();
		
		$.post('/ajax/message/load_more.php',
				{page:page,
				from_id:from_id,
				to_id:to_id,
				last_date:last_date,
				from_type:from_type,
				to_type:to_type,
				image:image,
				fulldate:fulldate,
				last_id : last_id
				},
				function(data){
					var responseDate  = $(data).find(".postdate").last().attr('value');
					var lastDate = $(data).find(".postdate").eq(0).attr('value');
					var body = linkify(data);
					if(responseDate == date){
							$('.postdate').eq(0).hide();
					}
					date = lastDate;
					$('.append-data').prepend(body);
					var temp = (page > totalPages) ?$('.btn_more').hide(): '' ;
					
			
				});
		
		
		
	});

	//refresh button
	$('#btn_refresh').click(function(e){
		e.preventDefault();
		var lastdate = $('.append-data').find("#last-date").attr('value');
		page = 0;
		var isLoading = true;
		setTimeout(function() {
			if (isLoading) {
				$('.spinner-loading').show();
			}
		}, 500);
		
		var latestDate = $('.postdate').last().attr('full');
		
		$.post(
			'/ajax/message/message_refresh.php',
			{
			from_id: fromId,
			from_type: fromType,
			to_id: toId,
			to_type: toType,
			img: img,
			hash: hash,
			contact_id: contactId,
			latest_date: latestDate	
			},
			function(data){
				isLoading = false;
				var resultMessage = data.substr(1);
				if (data.trim() !== 'fail') {
					//var messageDetail = $('#message_detail_area');
					$('#pop-message').hide();
					messageDetail.html(resultMessage);
					messageDetail.scrollTop(messageDetail.prop("scrollHeight"));
					date = lastdate;
					resultMessage = '';
				}
				$('.spinner-loading').hide();
				
			});
		
		
		
	});

	//photo button
	$('#btn_photo').click(function(e){
		e.preventDefault();
		//if (!isDisableSend) {
			$('#input_photo').trigger('click');
		//}
		//$('#input_photo').fadeTo('fast',0);
		
	});

	// photo button
	/*$('#btn_photo').on('click', function(e){
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
	});*/

	// emoji button
	$('#btn_emoji').on('click', function(e){
		e.preventDefault();
		if (!isDisableSend) {
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

	$(document).on('click', '.emoji_tab li a', function(e) {
		  e.preventDefault();
		  var emoji = '[i:' + $(this).attr('href') + ']';
		  $('#message_textarea').selection('replace', {
		   text : emoji,
		   caret : 'end'
		  });
	
		  return false;
	 });
	
	// photo button
	/*$(document).on('click', '#btn_photo', function(){
		//e.preventDefault();
		//alert('testing');
		$('#input_photo').trigger('click');
	});*/
	
	

	// message list edit
	$(document).on('click', '.message_list--edit .message_list_item .thumb,.col_left, .col_center , .message_list--edit .message_list_item .message_data', function(e){
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
			$('.message_list_form').submit();
		}
	});

	// block contact list 
	$(document).on('click', '#exe_block_contact_list', function(e){
		e.preventDefault();
		if($(this).hasClass('disable')){
			return false;
		}
		if(confirm('チェックした連絡先を受信拒否します。よろしいですか？')){
			$('.message_list_form').submit();
		}
	});

	// cancel block list 
	$(document).on('click', '#exe_cancel_block_list', function(e){
		e.preventDefault();
		if($(this).hasClass('disable')){
			return false;
		}
		if(confirm('チェックした連絡先を拒否解除します。よろしいですか？')){
			$('.message_list_form').submit();
		}
	});

	$(document).on('keyup',function(evt) {
	    if (evt.keyCode == 27) {
	    	if(messageAjax && messageAjax.readystate != 4){
	    		messageAjax.abort();
	        }
	    	$('.spinner-loading').hide();
	    }
	});
	// message sort menu
	/*$(document).on('click', '#message_sort_menu a', function(e){
		e.preventDefault();
		var scope = $('#message_sort_menu');
		$('li', scope).removeClass('on');
		$(this).closest('li').addClass('on');
	});*/
	
	$('#input_photo').change(function() {
			
		var fileInput = $(this).get(0);
		if (validateImage(fileInput) && !isDisableSend) {
			if (window.FormData != undefined) {
				var imgData = new FormData($('#form-image')[0]);
				createMessage('', imgData);
			} else {
				$('#form-image').submit();
				$('.spinner-loading').fadeIn(200);
			}
		}
		$('#form-image')[0].reset();
	});
	
	$('#btn_submit_message').click(function(e) {
		var body = encodeURIComponent($('#message_textarea').val());
		
		if (body == '' || isDisableSend) {
			e.preventDefault();
			$('#message_textarea').focus();
		} else if (window.FormData != undefined) {
			e.preventDefault();
			createMessage(body, '');
		} else {
			$('.spinner-loading').fadeIn(200);
		}
	});
	
	setMessageInputArea();
	
	$('.bubble .desc').each(function() {
		var message = $(this).html();
		$(this).html(linkify(message));
	});
	
});

/**
 * remove disabled on panel menu
 */
function checkPanelMenu() {
	if ($('.btn_block').hasClass('disable')) {
		$('.btn_block').removeClass('disable');
	}
}


/**
 * Create a message via ajax, returns with the inserted message
 * @param String body of message.
 * @param FormData data of image to post.
 */
function createMessage(body, imgData) {
	var isLoading = true;
	setTimeout(function() {
		if (isLoading) {
			$('.spinner-loading').show();
		}
	}, 500);
	
	var formData;
	if (imgData == '') {
		formData = new FormData();
		formData.append('message_body', body);
	} else {
		formData = imgData;
	}
	
	var latestDate = $('.postdate').last().attr('full');
	
	formData.append('from_id', fromId);
	formData.append('from_type', fromType);
	formData.append('to_id', toId);
	formData.append('to_type', toType);
	formData.append('contact_id', contactId);
	formData.append('latest_date', latestDate);
		
	messageAjax = $.ajax({
		type	: 'POST',
		url		: '/ajax/message/send_message.php',
		data	: formData,
		processData: false,  // tell jQuery not to process the data
		contentType: false,
		success	: function(data) {
			isLoading = false;
			var result = data.substr(0,1);
			var resultMessage = data.substr(1);
			if (0 == result) {
				$('#pop-message p').html(resultMessage);
				$('#pop-message').fadeIn(500);
			} else {
				var messageDetail = $('#message_detail_area');
				$('#pop-message').hide();
				messageDetail.append(resultMessage);
				messageDetail.scrollTop(messageDetail.prop("scrollHeight"));
				if (imgData == '') {
					$('#message_textarea').val('');
				}
				$('#message_textarea').focus();
				checkPanelMenu();
			}
			$('.spinner-loading').hide();
		}
	});
}

	
/**
 * Check if the message has a url and replace it with a link
 * @param String inputText
 * @returns return the message
 */
function linkify(inputText) {
    var replacedText, replacePattern1, replacePattern2, replacePattern3;

    //URLs starting with http://, https://, or ftp://
    replacePattern1 = /(\b(https?|ftp):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/gim;
    replacedText = inputText.replace(replacePattern1, '<a class="message-link" href="$1" target="_blank">$1</a>');

    //URLs starting with "www." (without // before it, or it'd re-link the ones done above).
    replacePattern2 = /(^|[^\/])(www\.[\S]+(\b|$))/gim;
    replacedText = replacedText.replace(replacePattern2, '$1<a class="message-link" href="http://$2" target="_blank">$2</a>');

    //Change email addresses to mailto:: links.
    replacePattern3 = /(([a-zA-Z0-9\-\_\.])+@[a-zA-Z\_]+?(?:[a-zA-Z]{2,6})+)/gim;
    replacedText = replacedText.replace(replacePattern3, '<a class="message-link" href="mailto:$1">$1</a>');

    return replacedText;
}
	
function validateImage(input){
	if (isImageUpload(input)) {
		if (imageSizeValidate(input)) {
			return true;
		} else {
			alert('ファイルサイズの上限を超えています。');
		}
	} else {
		alert('ファイルは画像ではありません。');
	}
	$('#file').val('');
	return false;
}



/**
 * Checks if the selected file is an image.
 * @param input Input element.
 */
function isImageUpload(input) {
	var ext = input.value.match(/([^.]*)$/)[1];
	switch (ext) {
		case 'jpg':
		case 'JPG':
		case 'jpeg':
		case 'JPEG':
		case 'bmp':
		case 'BMP':
		case 'png':
		case 'PNG':
		case 'gif':
		case 'GIF':
			return true;
		default:
			this.value = '';
	}
	return false;
}

/**
* Checks if image size does not exceed the size limit
* @param input Input element.
*/
function imageSizeValidate(input) {
	if(input.hasOwnProperty('files')) {
		var imgSize = input.files[0].size / 1024 / 1024;
		return (imgSize > 8) ? false: true;
	}
	return true;
}

/**
 * Determine wethere the message input area must be disabled or not
 **/
function setMessageInputArea(){
	var canSend = $('#can-send').val();
	if(canSend == 0) {
		isDisableSend = true;
		$('#message_textarea').attr('disabled','disabled');
		$('#btn_submit_message').attr('disabled',  'disabled');
		$('#btn_refresh').attr('disabled',  'disabled');
		$('#form-image').attr('disabled', 'disabled');
		$('#input_photo').attr('disabled', 'disabled');
	}
}



var isDisableSend = false;