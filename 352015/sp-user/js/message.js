
var fromId;
var fromType;
var toId;
var toType;
var contactId;
var img;
var messageDetail;
var messageAjax;
var hash;
var touchStart;
var currentDate;

$(function(){

	var href = location.pathname.substring(1);
	var pagename = href.substr(href.lastIndexOf('/') + 1).replace('.php','');
	if(pagename != 'detail'){
		$('.panel_menu').find('a').click(function() {
			if ($(this).attr('href') == 'javascript:void(0)') {
				location.href = $(this).attr('rel');
			}
		});
	}	
	
	var isSafari = Object.prototype.toString.call(window.HTMLElement).indexOf('Constructor') > 0;
	var height = $('#panel_menu').height();
	var isClicked = false;
	touchStart = false;
	
	fromId 		= $('#from-id').val();
	fromType 	= $('#from-type').val();
	toId 		= $('#to-id').val();
	toType 		= $('#to-type').val();
	contactId 	= $('#contact-id').val();
	img 		= $('#image-profile').val();
	hash 		= $('#hash').val();
	
	messageDetail = $('#message_detail_area');
	
	$(document).on('click', '#message_textarea', function() {
		if (detectmob()) {
			$('#btn_emoji').removeClass('on');
			$('#emoji_panel').hide();
		}
	});
	
	// Fix mobile floating toolbar when input is focused
	
	$(document).on('touchend', '#message_textarea', function() {
		$('#message_textarea').removeAttr('readonly');
	});
	
	if(/iPhone|iPod|iPad/.test(window.navigator.platform)&& isSafari){
		$(document).on('click', '#message_textarea', function(){
			$(document).on('touchend', '#btn_submit_message', function(e) {
				e.preventDefault();
				$('#btn_submit_message').click();
			});   
		});
	}

	// delete message list item button
	$(document).on('click', '.func_del_message_list_item', function(e){
		e.preventDefault();
		e.stopPropagation();
	
		if(confirm(' このチャットルームを削除してもよろしいですか？')){
			 window.location = $(this).attr('href');
		}
	});

	// block contact list item button
	/*$(document).on('click', '.func_block_contact_list_item', function(e){
		e.preventDefault();
		e.stopPropagation();
		
		if(confirm('この子からのメールを受信拒否にします。よろしいですか？')){
			// execute
		}
	});*/

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
		if (!$(this).hasClass('disabled')) {
			var type = $('#from-type').val();
			var contact_id = $('#contact-id').val();
			var to_id	=	encodeURIComponent($('#to-id').val());
			var from_id =	encodeURIComponent($('#from-id').val());
			if($(this).hasClass('on')){
				$(this).removeClass('on');
				$.post('/ajax/message/contact_fave.php',{unfavorite:contact_id,type:type,to_id:to_id,from_id:from_id},function(data){
					
				});
				
			}else{
				$(this).addClass('on');
				$.post('/ajax/message/contact_fave.php',{favorite:contact_id,type:type,to_id:to_id,from_id:from_id},function(data){
					
				});
			}
		}
	});

	$('.btn_block').on('click', function(e){
		e.preventDefault();
		e.stopPropagation();
		if (!$(this).hasClass('disabled')) {
			var type = $('#from-type').val();
			var contact_id = $('#contact-id').val();
			var to_id	=	encodeURIComponent($('#to-id').val());
			var from_id =	encodeURIComponent($('#from-id').val());
			if($(this).hasClass('on')){
				if(confirm('拒否解除します。よろしいですか？')){
					$(this).removeClass('on')
					$.post('/ajax/message/contact_block.php',{unblock:contact_id,type:type,to_id:to_id,from_id:from_id},function(data){
						
					});
				}
			}else{
				if(confirm('このパフォーマーを受信拒否します。よろしいですか？')){
					$(this).addClass('on');
					$.post('/ajax/message/contact_block.php',{block:contact_id,type:type,to_id:to_id,from_id:from_id},function(data){
						
					});
				}
			}
		}
	});
	
	// block button list
	$('.btn_blocklist').on('click', function(e){
		e.preventDefault();
		var type = 1;
		var contact_id = $(this).data("id");
		var cf = confirm('このパフォーマーを受信拒否します。よろしいですか？');

		if(cf){
				$.post('/ajax/message/contact_block.php',{block:contact_id,type:type},function(data){
						$('.list_item'+contact_id).remove();

						if($(".message_list > .message_list_item").length == 0){
							location.href = location.pathname;
						}
				});

				return false;
		}

	});
	// load more
	//10 is the records per page 
	var page = 0;
	var messages = $('#total').val();
	messages = parseInt(messages) - 10;
	var totalPages = (messages/10);
	totalPages 	= parseInt(totalPages);
	var test 	= $('.append-data').find('#last-date').val();
	var date    = $('.append-data').find("#last-date").attr('value');
	var last_id	= $('.message_detail_item').first().attr('message-id');
	$(document).on('click','.btn_more',function(){
	  page++;
	  var from_id = encodeURIComponent($('#from-id').val());
	  var to_id = encodeURIComponent($('#to-id').val());
	  var last_date = $('#last-date').val();
	  var from_type = $('#from-type').val();
	  var to_type   = $('#to-type').val();
	  var image 	= $('#image-profile').val();
	  var fulldate	= $('#lastfulldate').val();	
	  
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
				var resultMessage = data.substr(23);
				if (data.trim() !== 'fail') {
					//var messageDetail = $('#message_detail_area');
					$('#pop-message').hide();
					currentDate = data.substr(0,23);
					messageDetail.html(resultMessage);
					showLastMessage();
					date = lastdate;
					resultMessage = '';
				}
				$('.spinner-loading').hide();
				
			});
		
		
		
	});

	// photo button
	$('#btn_photo').on('click', function(e){
		e.preventDefault();
		var isSafari = Object.prototype.toString.call(window.HTMLElement).indexOf('Constructor') > 0;
		var isNexus = /^.*Android.*Nexus(((?:(?!Mobile))|(?:(\s(7).+))).)*$/i;
		if((/iPhone|iPod|iPad/.test(window.navigator.platform)&& isSafari) || isNexus.test(navigator.userAgent)){
			if (!isDisableSend) {
				$('#file').trigger('click');
			}
		}else{
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
		}
		
	});

	// emoji button
	$('#btn_emoji').on('click', function(e){
		e.preventDefault();
		if (!isDisableSend) {
			if($('#emoji_panel').is(':visible')){
				$(this).removeClass('on');
				$('#emoji_panel').slideUp(200);
				
				if(detectmob()){
					$('#message_textarea').removeAttr('readonly');
				}
				
				$(".message_wrap .message_footer").animate({
					'margin-top':'0'
				  }, 200, function() {});
			}else{
				$(this).addClass('on');
				$('#emoji_panel').slideDown(200);
				// photo reset
				$('#btn_photo').removeClass('on');
				$('#photo_panel').hide();
				
				if(detectmob()){
					$('#message_textarea').attr('readonly', 'true');
				}
				
				$(".message_wrap .message_footer").animate({
					'margin-top':'-160px'
				  }, 200, function() {});
			}
		} else {
			alert('相手側の都合により、メール送信できません。');
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
	/*$(document).on('click', '#btn_photo', function(e){
		e.preventDefault();
		$('#input_photo').click();
	});*/
	// message list edit
	$(document).on('click', '.message_list--edit .message_list_item .thumb,.message_list_item .col_center , .message_list--edit .message_list_item .message_data', function(e){
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

	/**
	 * resize detect mobile
	 */
	var top = $('.message_header--detail').outerHeight();
	var bottom = $('.message_footer').outerHeight();
	$('.message_detail_area').css({"top":top+'px',"bottom":bottom+'px'});
	if(detectmob()){
		$('.message_detail_area').on('click touch',function(){});
		$(window).on("orientationchange",function(event){
			$('.message_detail_area').css({"top":top+'px',"bottom":bottom+'px'});
		});
	}
	
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
	
	// emoji is added to body
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
		if (detectmob()) {
			document.activeElement.blur();
			$('#message_textarea').blur();
		}
		return false;
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

	// message sort menu
	/*$(document).on('click', '#message_sort_menu a', function(e){
		e.preventDefault();
		var scope = $('#message_sort_menu');
		$('li', scope).removeClass('on');
		$(this).closest('li').addClass('on');
	});*/
	
//	select photo
	//choose file selected.
	 $('#sel_file').click(function() {

		 if (!isDisableSend) {
			 $('#file').trigger("click");
		 } else {
			 alert('相手側の都合により、メール送信できません。');
		 }

	 });
	 
	 //take photo selected
	 $('#sel_cam').click(function() {
		 if (!isDisableSend) {
			$('#cam').trigger("click");
		 } else {
			 alert('相手側の都合により、メール送信できません。');
		 }
	 });
	 
	 
	 //form validation in messsage
	 $('#file, #cam').change(function() {
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
			if (!touchStart) {
				touchStart = true;
				createMessage(body, '');
			}
		} else {
			$('.spinner-loading').fadeIn(200);
		}
	});

	   
	setMessageInputArea();
	
	$('.bubble .desc').each(function() {
		var message = $(this).html();
		$(this).html(linkify(message));
	});
	 
	$(document).on('keyup',function(evt) {
	    if (evt.keyCode == 27) {
	    	if(messageAjax && messageAjax.readystate != 4){
	    		location.reload();
	    	}
	    	$('.spinner-loading').hide();
	    }
	});

	//link on its own tab
//	/$('.btn_message_list')
});

/**
 * Create a message via ajax, returns with the inserted message
 * @param String body of message.
 * @param FormData data of image to post.
 */
function createMessage(body, imgData) {
	$('.spinner-loading').show();
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
			var result = data.substr(0,1);
			var resultMessage = data.substr(1);
			if (0 == result) {
				$('#pop-message p').html(resultMessage);
				$('#pop-message').fadeIn(500);				
			} else {
				//var messageDetail = $('#message_detail_area');
				$('#pop-message').hide();
				messageDetail.append(resultMessage);
				
				if (imgData == '') {
					$('#message_textarea').val('');
				}else{
					showLastMessage();
				}
				
				if (!detectmob()) {
					$('#message_textarea').focus();
				}
				if (touchStart) {
					touchStart = false;
				}
			
				showLastMessage();
			
				
				hidePanels();
				checkPanelMenu();
			}
			$('.spinner-loading').hide();
		}
	});
}

/**
 * show display last message
 */
function showLastMessage(){
	$('img:last-child').on('load',function(){
		messageDetail.scrollTop(messageDetail.prop("scrollHeight"));
	});
	messageDetail.scrollTop(messageDetail.prop("scrollHeight"));
}


function checkPanelMenu() {
	if ($('.btn_block').hasClass('disabled')) {
		$('.btn_block').removeClass('disabled');
	}
	if ($('.btn_favorite').hasClass('disabled')) {
		$('.btn_favorite').removeClass('disabled');
	}
}

function hidePanels() {
	if ($('#photo_panel').is(':visible')) {
		$('#btn_photo').removeClass('on');
		$('#photo_panel').slideUp(200);
	}
	if ($('#emoji_panel').is(':visible')) {
		$('#btn_emoji').removeClass('on');
		$('#emoji_panel').slideUp(200);
	}
}

//window.onorientationchange = getDeviceOrientation;
function getDeviceOrientation() {
	
    
	var currentDegree = window.orientation;
	var degree = '';
	switch (currentDegree) {
    	case 180: 
    		degree = 90; //ok
    		break; 
        case -90:  
    		degree = 180; //
            // Landscape (Clockwise)
            break;  
        case 90: 
        	degree = 0; //ok
        	break;
        default:
        	degree = 270;
	}
	$.post('/ajax/message/store_orientation.php', {degree:degree }, function(data) {
		alert(data);
		
	});
	console.log('orientation : ' + window.orientation);
}

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
	}
}
var isDisableSend = false;

/**
* Detects if using mobile
*/
function detectmob() {
	if (navigator.userAgent.match(/Android/i)
			|| navigator.userAgent.match(/webOS/i)
			|| navigator.userAgent.match(/iPhone/i)
			|| navigator.userAgent.match(/iPad/i)
			|| navigator.userAgent.match(/iPod/i)
			|| navigator.userAgent.match(/BlackBerry/i)
			|| navigator.userAgent.match(/Windows Phone/i)) {
		return true;
	} else {
		return false;
	}
}