/**
 * 
 */

$(document).ready(function() {
	var intervalCheckMessage;
	currentDate = $('#current-date-time').val();
	//$(window).focus(function() {
		
	var checkMessage  = function() {
		var latestDate = $('.postdate').last().attr('full');
		$.post('/ajax/message/message_check.php', {
			to_id			:	toId,
			from_id			:	fromId,
			img				:	img,
			to_type			:	toType,
			lastest_date	:	latestDate,
			from_date		: 	currentDate
		}, function(data) {
			if (data.trim() !== "fail") {
				currentDate = data.substr(0,23);
				var message = data.substr(23);
				messageDetail.append(message);
				$('html, body').scrollTop(messageDetail.prop("scrollHeight"));
			}
		});
		checkUnReadMessage();
	}
	
	intervalCheckMessage = setInterval(checkMessage, 10000);
	//});
	/*
	$(window).blur(function() {
	    clearInterval(intervalCheckMessage);
	    intervalCheckMessage = 0;
	});*/
});

function checkUnReadMessage() {
	$('.not-read').each(function() {
		var messageId = $(this).parent().attr('message-id');
		var elem = $(this);
		var time = elem.html();
		$.post('/ajax/message/message_read.php', {
			message_id : messageId
		}, function(data) {
			if (data.trim() == 'true') {
				var read = '<p>Šù“Ç</p>';
				elem.html(read + time);
				elem.removeClass('not-read');
			}
			//console.log(data);
			
		});
	});
}