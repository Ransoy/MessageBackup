
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
var imgData = null;
var msgPlaceholder;
$(function(){
	
	var isIos = /iPhone|iPod|iPad/.test(window.navigator.platform);
	var isAppAndroid = forAppWebviewAccess.isWebviewAccess() && forAppWebviewAccess.ua.isAndroid();
	var is_safari = navigator.userAgent.indexOf("Safari") > -1;
	var event = isIos ? 'touchstart' : 'click';
	
	msgPlaceholder = $('#message_textarea').attr('placeholder');
	var href = location.pathname.substring(1);
	var pagename = href.substr(href.lastIndexOf('/') + 1).replace('.php','');
	if(pagename != 'detail'){
		$('.panel_menu').find('a').on(event, function() {
			if ($(this).attr('href') == 'javascript:void(0)') {
				location.href = $(this).attr('rel');
			}
		});
	}	
	
	//var isSafari = Object.prototype.toString.call(window.HTMLElement).indexOf('Constructor') > 0;
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
	
	$(document).on(event, '#message_textarea', function() {
		if (detectmob()) {
			$('#btn_emoji').removeClass('on');
			$('#emoji_panel').hide();
		}
	});
	
	// Fix mobile floating toolbar when input is focused
	
	$(document).on('touchend', '#message_textarea', function() {
		$('#message_textarea').removeAttr('readonly');
	});
	
	/*if(/iPhone|iPod|iPad/.test(window.navigator.platform)){
		$(document).on(event, '#message_textarea', function(){
			$(document).on('touchend', '#btn_submit_message', function(e) {
				e.preventDefault();
				$('#btn_submit_message').click();
			});   
		});
	}*/

	// delete message list item button
	$(document).on(event, '.func_del_message_list_item', function(e){
		e.preventDefault();
		e.stopPropagation();
	
		if(confirm(' このチャットルームを削除してもよろしいですか？')){
			var id = $(this).attr('mid');
			var type = $(this).attr('mtype');
			var elem = this;
			
			
			$.post('/ajax/message/delete_message.php', {id:id, type:type}, function(data) {
				if (data.trim() === 'success') {
					$(elem).parents().eq(3).fadeOut(200, function() {
						$(this).remove();
						if($(".message_list > .message_list_item").length == 0){
							var page = $('#page').val();
							page = (page == 1) ? page : page--;
							$('#page').val(page);
							$('#search').click();
						}
					});
				}
			});
			 //window.location = $(this).attr('href');
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

	$(document).ready(function(){
        var UA = navigator.userAgent.toLowerCase();
        if(UA.indexOf("chrome") >=0 && UA.indexOf("Mobile") < 0 && UA.indexOf("Android") < 0){ 
            var links = $(document.head).find("link");
            $.each(links, function(indx, elm){
                var href = $(elm).attr("href");

                if (href.toLowerCase().indexOf("portal") >=0){
                    $(elm).attr("href", href + "?v=" + Date.now());
                }
            });
        }
	} ); 
	
	// disable link
	$(document).on(event, 'a.disable', function(e){
		if(e.preventDefault) {
			e.preventDefault();
		} else { // for IE debugs
			e.returnValue = false;
		}
	});

	// table row bigger link
	$(document).on(event, 'tr.b_link_tr', function(e){
		window.location = $('a', this).attr('href');
	});

	// switch panel menu
	$('#switch_panel_menu').on(event, function(e){
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
	$('.btn_favorite').on(event, function(e){
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

	$('.btn_block').on(event, function(e){
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
	$('.btn_blocklist').on(event, function(e){
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
	$(document).on(event,'.btn_more',function(){
	  page++;
	  var last_id	= $('.message_detail_item').first().attr('message-id');
	  var from_id 	= encodeURIComponent($('#from-id').val());
	  var to_id 	= encodeURIComponent($('#to-id').val());
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
	    	if(data.trim() !==  'fail'){
			     var responseDate  = $(data).find(".postdate").last().attr('value');
			     var lastDate = $(data).find(".postdate").eq(0).attr('value');
			     var body = linkify(data);
			     if(responseDate == date){
			       $('.postdate').eq(0).hide();
			     }
			     date = lastDate;
			     $('.append-data').prepend(body);
			     var temp = (page > totalPages) ?$('.btn_more').hide(): '' ;
	    	}
	   
	    });
	  
	  
	  
	 });
	
	//refresh button
	$('#btn_refresh').on(event, function(e){
		e.preventDefault();
		var lastdate = $('.append-data').find("#last-date").attr('value');
		page = 0;
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
				var resultMessage = data.substr(19);
				if (data.trim() !== 'fail') {
					//var messageDetail = $('#message_detail_area');
					$('#pop-message').hide();
					currentDate = data.substr(0,19);
					checkNotifyDisp();
					messageDetail.html(resultMessage);
					showLastMessage();
					date = lastdate;
					resultMessage = '';
				}
				
			});
		
		
		
	});
	
	// emoji button
	$('#btn_emoji').on(event, function(e){
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
	
	$('#btn_photo').on(event, function(){
		if (!isDisableSend) {
			if (isAppAndroid) {
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
				return;
			}
			$('#file').click();
		}
	});
	
	// emoji tab
	$(document).on(event, '.emoji_tab_menu li', function(e){
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
	$(document).on(event, '.message_list--edit .message_list_item .thumb,.message_list_item .col_center , .message_list--edit .message_list_item .message_data', function(e){
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
		$('.message_detail_area').on('click touch',function(){
			$('#btn_emoji').removeClass('on');
			$('#emoji_panel').hide();
		});
		$(window).on("orientationchange",function(event){
			$('.message_detail_area').css({"top":top+'px',"bottom":bottom+'px'});
		});
	}
	
	/* RFC/BUG on ie 9 problem */
	var isMsgClick = false;
	$(document).on(event, '#message_textarea', function() {
		isMsgClick = true;
	});
	$(document).on('change', '#message_textarea', function() {
		isMsgClick = false;
	});
	
	// emoji is added to body
	$(document).on(event, '.emoji_tab li', function(e) {
		e.preventDefault();
		var emoji = '[i:' + $(this).attr('emoji') + ']';
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
	$(document).on(event, '#exe_delete_message_list', function(e){
		e.preventDefault();
		if($(this).hasClass('disable')){
			return false;
		}
		if(confirm('チェックしたメール履歴を削除します。よろしいですか？')){
			$('.message_list_form').submit();
		}
	});

	// block contact list 
	$(document).on(event, '#exe_block_contact_list', function(e){
		e.preventDefault();
		if($(this).hasClass('disable')){
			return false;
		}
		if(confirm('チェックした連絡先を受信拒否します。よろしいですか？')){
			$('.message_list_form').submit();
		}
	});

	// cancel block list 
	$(document).on(event, '#exe_cancel_block_list', function(e){
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
	 $('#sel_file').on(event, function() {

		 if (!isDisableSend) {
			 $('#file').trigger("click");
		 } else {
			 alert('相手側の都合により、メール送信できません。');
		 }

	 });
	 
	 //take photo selected
	 $('#sel_cam').on(event, function() {
		 if (!isDisableSend) {
			$('#cam').trigger("click");
		 } else {
			 alert('相手側の都合により、メール送信できません。');
		 }
	 });
	 
	 
	 //form validation in messsage
	 $('#file, #cam').change(function() {
		var fileInput = $(this).get(0); 
		if ($(this).val() != '' && validateImage(fileInput) && !isDisableSend) {
			previewImage(fileInput);
			if($('#photo_panel').is(':visible')){
				$('#btn_photo').removeClass('on');
				$('#photo_panel').slideUp(200);
			}
 		}
	});

	$('#btn_submit_message').on(event, function(e) {
		if (touchStart) {
			e.preventDefault();
		} else {
			touchStart = true;
			
			var body = encodeURIComponent($('#message_textarea').val());
			var fileInput = ($('#file').val() == '' && $('#cam').val() == '') ? false : true;
			
			if (window.FormData == undefined) {
				$('.spinner-loading').fadeIn(200);
			} else if ((!fileInput && body == '' && imgData == null) || isDisableSend) {
				e.preventDefault();
				touchStart = false;
				$('#message_textarea').focus();
			}else {
				e.preventDefault();
				fileInput = (imgData == null) ? imgInput() : imgData;
				createMessage(body, fileInput, fName);
			}
		}
	});
	var fName = '';
	function imgInput() {
		var img = '';
		if ($('#file').val() != '') {
			img = $('#file').get(0);
			fName = 'imageFile';
		} else if ($('#cam').val() != '') {
			img = $('#cam').get(0);
			fName = 'imageCam';
		}
		return img;
	}
	   
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


	$('#message_textarea').keyup(function(e) {
		
		if(!detectmob()){
			 if (e.ctrlKey && e.keyCode == 13) {
				  $(this).val(function(i,val){
			            return val + "\n";
			      });
			 }else if(e.keyCode == 13){
				  e.preventDefault();
				  $('#btn_submit_message').click();
			 }
		}
		if ($(this).val() != '') {
			$(this).attr('placeholder', '');
		} else {
			$(this).attr('placeholder', msgPlaceholder);
		}
	});
	
	
	$('#img_prev_remove').on(event, function() {
		resetImage();
	});
	
	
	/*$('.bd_message').on('touchmove', function(e){
			e.preventDefault();
	});*/
	/*document.body.addEventListener("ontouchstart", function(event) {
		  if(document.getElementById("main").scrollTop > 0) return;
		  event.preventDefault();
		  event.stopPropagation();
		}, false);*/
	
});

/* Reset prev image */
function resetImage() {
	$('#img_prev').hide();
	$('#btn_photo').show();
	$('#img_prev').find('img').attr('src', '');
	$('#file').val('');
	$('#cam').val('');
	
	if(isIE() <= 10){
		$fileInput = $('#file');
		$fileInput.replaceWith($fileInput = $fileInput.clone(true));
		$camInput = $('#cam');
		$camInput.replaceWith($camInput = $camInput.clone(true));
		imgData = null;
	}
}

/**
 * Create a message via ajax, returns with the inserted message
 * @param String body of message.
 * @param FormData data of image to post.
 */
function createMessage(body, fData, fileName) {
	if (messageAjax && messageAjax.readystate != 4) {
		return;
	}
	
	$('.spinner-loading').show();
	var formData = (imgData == null) ? new FormData() : imgData;
	if (fData == '') {
		formData.append('message_body', body);
	} else if (body != ''){
		if (imgData == null) {
			formData.append(fileName, fData.files[0]);
		}
		formData.append('message_body', body);
	} else {
		if (imgData == null) {
			formData.append(fileName, fData.files[0]);
		}
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
				$('#pop-message').fadeIn(500,function(){
					checkNotifyDisp();
					showLastMessage();
				});				
			} else {
				$('#pop-message').hide();
				$('#message_textarea').val('');
				$('#message_textarea').attr('placeholder', msgPlaceholder);
				messageDetail.append(resultMessage);
				showLastMessage();
				checkPanelMenu();
				resetImage();
				hidePanels();
				if (!detectmob()) {
					$('#message_textarea').focus();
				}
			}
			if (touchStart) {
				touchStart = false;
			}
			messageAjax = null;
			$('.spinner-loading').hide();
		}
	});
}
$(window).on('resize', function() {
	checkNotifyDisp();
}); 


checkNotifyDisp();

function checkNotifyDisp(){
	var hh_notify = 0;
	if($('.notice_msg').is(':visible')) {
		hh_notify = $('.notice_msg').outerHeight();
		hh_notify = hh_notify + 24;
	    $('.message_wrap .message_detail_area').css({'padding-bottom':hh_notify + 'px'});
	}
}

/* show display last message */
function showLastMessage(){
	$('img.resized:last-child').on('load',function(){
		messageDetail.scrollTop(messageDetail.prop("scrollHeight"));
	});
	messageDetail.scrollTop(messageDetail.prop("scrollHeight"));
}


function isIE() {
  var myNav = navigator.userAgent.toLowerCase();
  return (myNav.indexOf('msie') != -1) ? parseInt(myNav.split('msie')[1]) : false;
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

/* Determine wethere the message input area must be disabled or not */
function setMessageInputArea(){
	var canSend = $('#can-send').val();
	if(canSend == 0) {
		isDisableSend = true;
		$('#message_textarea').attr('disabled','disabled');
		$('#btn_submit_message').attr('disabled',  'disabled');
		$('#btn_refresh').attr('disabled',  'disabled');
		//$('#form-image').attr('disabled', 'disabled');
		$('#message-form').attr('disabled', 'disabled');
	}
}
var isDisableSend = false;

/* Detects if using mobile */
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