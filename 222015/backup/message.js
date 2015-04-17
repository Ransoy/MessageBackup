
/*
* message common javascript
*
*/
var fromId;
var fromType;
var toId;
var toType;
var contactId;
var img;
var messageDetail;
var messageAjax;

$(function() {
	
	fromId 		= $('#from-id').val();
	fromType 	= $('#from-type').val();
	toId 		= $('#to-id').val();
	toType 		= $('#to-type').val();
	contactId 	= $('#contact-id').val();
	img 		= $('#image-profile').val();
	
	messageDetail = $('#message_detail_area');
	
	setMessageInputArea();
	// delete message list item button
	$(document).on('click', '.func_del_message_list_item', function(e){
		e.preventDefault();
		e.stopPropagation();
		
		if(confirm('���̎q�Ƃ̂��Ƃ���폜���Ă�낵���ł����H')){
			// execute
		}
	});

	// disable link
	$(document).on('click', 'a.disable', function(e){
		if(e.preventDefault) {
			e.preventDefault();
		} else { // for IE debugs
			e.returnValue = false;
		}
	});

	// table row bigger link
	$(document).on('click', 'tr.b_link_tr', function(e){
		window.location = $('a', this).attr('href');
	});

	// table td bigger link
	$(document).on('click', 'td.b_link_td', function(e){
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
		var type = $('#from-type').val();
		var contact_id = $('#contact-id').val();
		if($(this).hasClass('on')){
			$(this).removeClass('on');
			$.post('/ajax/message/contact_fave.php',{unfavorite:contact_id,type:type},function(data){
				
			});
		}else{
			$(this).addClass('on');
			$.post('/ajax/message/contact_fave.php',{favorite:contact_id,type:type},function(data){
				
			});
		}
	});

	// block button
	$('.btn_block').on('click', function(e){
		e.preventDefault();
		var type = $('#from-type').val();
		var contact_id = $('#contact-id').val();
		var from_id = encodeURIComponent($('#from-id').val());
		var to_id	= encodeURIComponent($('#to-id').val());
		if($(this).hasClass('on')){
			if(confirm('���ۉ������܂��B��낵���ł����H')){
				$(this).removeClass('on');
				$.post('/ajax/message/contact_block.php',{unblock:contact_id,type:type,from_id:from_id,to_id:to_id},function(data){
					
				});
			}
		}else{
			if(confirm('���̃p�t�H�[�}�[����M���ۂ��܂��B��낵���ł����H')){
				$(this).addClass('on');
				$.post('/ajax/message/contact_block.php',{block:contact_id,type:type},function(data){
					
				});
			}
		}
	});

	// load more
	//10 is the records per page 
	var page = 0;
	var messages = $('#total').val();
	messages = parseInt(messages) - 10;
	var totalPages = (messages/10);
	totalPages = parseInt(totalPages);
	var date		  = $('.append-data').find("#last-date").attr('value');
	
	$('.btn_more').click(function(){
		page++;
		var from_id = $('#from-id').val();
		var to_id 	=$('#to-id').val();
		var last_date = $('#last-date').val();
		var from_type = $('#from-type').val();
		var to_type	  = $('#to-type').val();
		var image     = $('#image-profile').val();
		var fulldate  = $('#lastfulldate').val();
		to_id= encodeURIComponent(to_id);
		from_id	= encodeURIComponent(from_id);
		
		$.post('/ajax/message/load_more.php',
				{page:page,
				from_id:from_id,
				to_id:to_id,
				last_date:last_date,
				from_type:from_type,
				to_type:to_type,
				image:image,
				fulldate:fulldate
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
	


	$('#input_photo').change(function() {	
		var fileInput = $(this).get(0);
		if (validateImage(fileInput) && !isDisableSend) {
			var imgData = new FormData($('#form-image')[0]);  
			createMessage('', imgData);
			//$('.spinner-loading').fadeIn(200);
			//$('#form-image').submit();
		}
	});
	
	//var latestDate = '';
	$('#btn_submit_message').click(function(e) {
		var body = encodeURIComponent($('#message_textarea').val());
		e.preventDefault();
		if (body == '' || isDisableSend) {
			$('#message_textarea').focus();
		} else {
			createMessage(body, '');
		}
	});
	

	// emoji button
	$('#btn_emoji').on('click', function(e){
		e.preventDefault();
		if($('#emoji_panel').is(':visible')){
			$(this).removeClass('on');
			$('#emoji_panel').hide();
		}else{
			$(this).addClass('on');
			$('#emoji_panel').fadeIn(200);
		}
	});
	
	//photo button
	$('#btn_photo').click(function(e){
		e.preventDefault();
		if(!isDisableSend){
			//$('#input_photo').trigger('click');
			$('#input_photo').fadeTo('fast',0);

		}else{
			 alert('���葤�̓s���ɂ��A���[�����M�ł��܂���B');
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
	$(document).on('click', '#btn_photo', function(e){
		e.preventDefault();

		if(!isDisableSend){
			$('#input_photo').trigger('click');
		}else{
			 alert('���葤�̓s���ɂ��A���[�����M�ł��܂���B');
		}
				
	});

	
	// message list edit
	$(document).on('click', '.message_list--edit .message_list_item .col_left, .message_list--edit .message_list_item .col_center', function(e){
		e.preventDefault();
		e.stopPropagation();
		var scope = $(this).closest('tr');
		
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
		if(confirm('�`�F�b�N�������[���������폜���܂��B��낵���ł����H')){
			$('.message_list_form').submit();
		}
	});

	// block contact list 
	$(document).on('click', '#exe_block_contact_list', function(e){
		e.preventDefault();
		if($(this).hasClass('disable')){
			return false;
		}
		if(confirm('�`�F�b�N�����A�������M���ۂ��܂��B��낵���ł����H')){
			// execute
			$('.message_list_form').submit();
		}
	});

	// cancel block list 
	$(document).on('click', '#exe_cancel_block_list', function(e){
		e.preventDefault();
		if($(this).hasClass('disable')){
			return false;
		}
		if(confirm('�`�F�b�N�����A��������ۉ������܂��B��낵���ł����H')){
			// execute
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
	
	$('.bubble .desc').each(function() {
		var message = $(this).html();
		$(this).html(linkify(message));
	});


});


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
				//var messageDetail = $('#message_detail_area');
				$('#pop-message').hide();
				messageDetail.append(resultMessage);
				messageDetail.scrollTop(messageDetail.prop("scrollHeight"));
				$('#message_textarea').val('').focus();
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
    replacedText = inputText.replace(replacePattern1, '<a href="$1" target="_blank">$1</a>');

    //URLs starting with "www." (without // before it, or it'd re-link the ones done above).
    replacePattern2 = /(^|[^\/])(www\.[\S]+(\b|$))/gim;
    replacedText = replacedText.replace(replacePattern2, '$1<a href="http://$2" target="_blank">$2</a>');

    //Change email addresses to mailto:: links.
    replacePattern3 = /(([a-zA-Z0-9\-\_\.])+@[a-zA-Z\_]+?(?:[a-zA-Z]{2,6})+)/gim;
    replacedText = replacedText.replace(replacePattern3, '<a href="mailto:$1">$1</a>');

    return replacedText;
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
  $('#form-image').attr('disabled', 'disabled');
 }
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
//validating if the file is an image or not
function validateImage(input){
 if (isImageUpload(input)) {
  if (imageSizeValidate(input)) {
   return true;
  } else {
   alert('File exceed the limit');
  }
 } else {
  alert('File is not image');
 }
 $('#file').val('');
 return false;
}

var isDisableSend = false;
