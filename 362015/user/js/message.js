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
var hash;
var currentDate;
$(function() {
	
	fromId 		= $('#from-id').val();
	fromType 	= $('#from-type').val();
	toId 		= $('#to-id').val();
	toType 		= $('#to-type').val();
	contactId 	= $('#contact-id').val();
	img 		= $('#image-profile').val();
	hash 		= $('#hash').val();
	
	messageDetail = $('#message_detail_area');
	
	setMessageInputArea();
	// delete message list item button
	$(document).on('click', '.func_del_message_list_item', function(e){
		e.preventDefault();
		e.stopPropagation();
		
		if(confirm('この子とのやりとりを削除してよろしいですか？')){
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

	// favorite button
	$('.btn_favorite').on('click', function(e){
		e.preventDefault();
		if (!$(this).hasClass('disable')) {
			var type = $('#from-type').val();
			var contact_id = $('#contact-id').val();
			var from_id = encodeURIComponent($('#from-id').val());
			var to_id	= encodeURIComponent($('#to-id').val());
			if($(this).hasClass('on')){
				$(this).removeClass('on');
				$.post('/ajax/message/contact_fave.php',{unfavorite:contact_id,type:type,from_id:from_id,to_id:to_id},function(data){
					
				});
			}else{
				$(this).addClass('on');
				$.post('/ajax/message/contact_fave.php',{favorite:contact_id,type:type,from_id:from_id,to_id:to_id},function(data){
					
				});
			}
		}
	});

	// block button
	$('.btn_block').on('click', function(e){
		e.preventDefault();
		if (!$(this).hasClass('disable')) {
			var type = $('#from-type').val();
			var contact_id = $('#contact-id').val();
			var from_id = encodeURIComponent($('#from-id').val());
			var to_id	= encodeURIComponent($('#to-id').val());
			if($(this).hasClass('on')){
				if(confirm('拒否解除します。よろしいですか？')){
					$(this).removeClass('on');
					$.post('/ajax/message/contact_block.php',{unblock:contact_id,type:type,from_id:from_id,to_id:to_id},function(data){
						
					});
				}
			}else{
				if(confirm('このパフォーマーを受信拒否します。よろしいですか？')){
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
		var from_id = $('#from-id').val();
		var to_id 		= $('#to-id').val();
		var last_date 	= $('#last-date').val();
		var from_type 	= $('#from-type').val();
		var to_type	  	= $('#to-type').val();
		var image     	= $('#image-profile').val();
		var fulldate  	= $('#lastfulldate').val();
		to_id			= encodeURIComponent(to_id);
		from_id			= encodeURIComponent(from_id);
		
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
					currentDate = data.substr(0,19);
					$('#pop-message').hide();
					resultMessage = resultMessage.replace(/<br>/g,'');
					messageDetail.html(resultMessage);
					messageDetail.scrollTop(messageDetail.prop("scrollHeight"));
					date = lastdate;
					resultMessage = '';
				}
				$('.spinner-loading').hide();
				
			});
		
		
		
	});

	/*$('#input_photo').change(function() {	
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
	});*/
	
	$('#input_photo').change(function() {	
		var fileInput = $(this).get(0);
		if (validateImage(fileInput) && !isDisableSend) {
			previewImage(fileInput);
		}
	});
	//var latestDate = '';
	$('#btn_submit_message').click(function(e) {
		var body = encodeURIComponent($('#message_textarea').val());
		var fileInput = ($('#input_photo').val() == '') ? '' : 	new FormData($('#form-image')[0]);
		if ((fileInput == '' && body == '') || isDisableSend) {
			e.preventDefault();
			$('#message_textarea').focus();
		} else if (window.FormData != undefined) {
			e.preventDefault();
			createMessage(body, fileInput);
		} else {
			$('.spinner-loading').fadeIn(200);
		}
	});
	

	// emoji button
	$('#btn_emoji').on('click', function(e){
		e.preventDefault();
		if(!isDisableSend){
			if($('#emoji_panel').is(':visible')){
				$(this).removeClass('on');
				$('#emoji_panel').hide();
			}else{
				$(this).addClass('on');
				$('#emoji_panel').fadeIn(200);
			}
		}else{
			 alert('相手側の都合により、メール送信できません。');
		}
	});
	
	//photo button
	/*$('#btn_photo').click(function(e){
		e.preventDefault();
		if(!isDisableSend){
			//$('#input_photo').trigger('click');
			$('#input_photo').fadeTo('fast',0);

		}else{
			 alert('相手側の都合により、メール送信できません。');
		}
	});*/
	
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
	
	/* RFC/BUG on ie 9 problem */
	var isMsgClick = false;
	$(document).on('click', '#message_textarea', function() {
		isMsgClick = true;
	});
	$(document).on('change', '#message_textarea', function() {
		isMsgClick = false;
	});
	function isIE() {
	  var myNav = navigator.userAgent.toLowerCase();
	  return (myNav.indexOf('msie') != -1) ? parseInt(myNav.split('msie')[1]) : false;
	}
	$(document).on('click', '.emoji_tab li a', function(e) {
		  e.preventDefault();
		  var emoji = '[i:' + $(this).attr('href') + ']';
		  if (isIE() <= 9 && !isMsgClick) {
			  $('#message_textarea').focus();
			  $('#message_textarea').val($('#message_textarea').val());
		  }
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
			 alert('相手側の都合により、メール送信できません。');
		}
				
	});

	
	// message list edit
	$(document).on('click', '.message_list--edit .message_list_item .col_left, .col_center, .message_list--edit .message_list_item .col_center', function(e){
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
		if(confirm('チェックした連絡先を拒否解除します。よろしいですか？')){
			// execute
			$('.message_list_form').submit();
		}
	});

	$(document).on('keyup',function(evt) {
	    if (evt.keyCode == 27) {
	    	if(messageAjax && messageAjax.readystate != 4){
	    		//messageAjax.abort();
	    		location.reload();
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
	
	
	$('.reload_window').click(function(e) {

		window.location.replace();
	});

	
	$('#message_textarea').keydown(function (e) {
		  
		  if (e.ctrlKey && e.keyCode == 13) {
			  $(this).val(function(i,val){
		            return val + "\n";
		      });
		  }else if(e.keyCode == 13){
			  e.preventDefault();
			  $('#btn_submit_message').click();
		  }
		 
	});
	
	
	$('#img_prev_remove').click(function() {
		resetImage();
	});
	
	
	
});

/**
 * Reset prev image 
 */
function resetImage() {
	$('#img_prev').hide();
	$('#btn_photo').show();
	$('#form-image')[0].reset();
}

/**
 * remove disabled on panel menu
 */
function checkPanelMenu() {
	if ($('.btn_block').hasClass('disable')) {
		$('.btn_block').removeClass('disable');
	}
	if ($('.btn_favorite').hasClass('disable')) {
		$('.btn_favorite').removeClass('disable');
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
	} else if (body != '') { 
		formData = imgData;
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
				$('#pop-message').hide();
				$('#message_textarea').val('');
				$('#message_textarea').focus();
				messageDetail.append(resultMessage);
				messageDetail.scrollTop(messageDetail.prop("scrollHeight"));
				changePoint(fromType, fromId);
				checkPanelMenu();
				resetImage();
			}
			$('.spinner-loading').hide();
		}
	});
}

/**
 * Change Point
 */

function changePoint(fromType, fromId) {
	$.post('/ajax/message/get_point.php', {from_type:fromType, from_id:fromId}, function(data) {
		$('.point_right').find('a').html(data+" pt");
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
   $('#btn_refresh').attr('disabled',  'disabled');
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
   alert('ファイルサイズの上限を超えています。');
  }
 } else {
  alert('ファイルは画像ではありません。');
 }
 $('#file').val('');
 return false;
}

/**
 * Shows a preview of an input image if any. Otherwise, it shows previously uploaded image if any.
 * @param input Input element.
 */
function previewImage(input) {
	if (input.files && input.files[0]) {
		var reader = new FileReader();
	
		reader.onload = function(e) {
			$('#img_prev').find('img').attr('src', e.target.result);
		}
	
		reader.readAsDataURL(input.files[0]);
		$('#img_prev').show();
		$('#btn_photo').hide();
	}
}

var isDisableSend = false;
