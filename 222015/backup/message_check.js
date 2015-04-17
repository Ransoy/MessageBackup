/**
 * 
 */
$(document).ready(function() {
	var intervalCheckMessage;
	$(window).focus(function() {
		var latestDate = $('.postdate').last().attr('full');
		var checkMessage  = function() {
			$.post('/ajax/message/message_check.php', {
				to_id			:	toId,
				from_id			:	fromId,
				img				:	img,
				to_type			:	toType,
				lastest_date	:	latestDate
			}, function(data) {
				if (data.trim() !== "fail") {
					messageDetail.append(data);
					messageDetail.scrollTop(messageDetail.prop("scrollHeight"));
				}
			});
		}
	
		intervalCheckMessage = setInterval(checkMessage, 800);
	});
	
	$(window).blur(function() {
	    clearInterval(intervalCheckMessage);
	    intervalCheckMessage = 0;
	});
});