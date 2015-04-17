var message 		= '';
var performer_id 	= '';
var male_id 		= '';
var type 			= 'body';
var page 			= 1;
var sort 			= 'DESC';
var select 			= 'all';
var is_check 		= 0;
var start_date 		= '';
var end_date		= '';
var postUpdate;
var performer_type;
var male_type;

$(function() {
	
	processList();
	
	$('.select-message select').change(function() {
		select = $(this).val();
		if (select == 'image') {
			$('#message-keyword').val('');
		} 
		page = 1;
		processList();
	});
	
	$('.sort select').change(function() {
		sort = ($(this).val() == '1') ? 'DESC' : 'ASC';
		processList();
	});
	
	$('.is-check select').change(function() {
		is_check = $(this).val();
		page = 1;
		processList();
	});
	
	$('#performer-type').change(function() {
		changeType('performer', $(this));
	});
	$('#male-type').change(function() {
		changeType('user', $(this));
	});
	
	$('#start-year, #start-month, #start-day, #end-year, #end-month, #end-day').change(function() {
		validateDate();
	});
	
});


$(document).on('keypress keyup change', '.search_performer, .search_male, #message-keyword', function(e) {
	page = 1;
	processList();
});


$(document).on('click', '.del_btn', function() {
	var id = $(this).val();
	$.post('../ajax/message/admin_ajax.php', {action:'delete', dataID:id}, function(data) {
		if(data) {
			alert('success');
			$('tr#'+id).fadeOut(500);
		} else {
			alert('fail');
		}
	});
});


$(document).on('click', '.page', function() {
	page = $(this).html();
	processList();
});

$(document).on('click', '.prev', function() {
	page = parseFloat($('.current span').html()) - 1;
	if (page > 0) { // incase if user used inspect element
		processList();
	} else {
		alert("invalid page");
	}
});

$(document).on('click', '.next', function() {
	var lastPage = $('.blog_pager ul li').last().find('a, span').html();
	page = parseFloat($('.current span').html()) + 1;
	if (page <= parseFloat(lastPage)) { // incase if user used inspect element
		processList();
	} else {
		alert('invalid page');
	}
});

function updateList(){
	if	(message != '') {
		message = encodeURIComponent(message);
		//console.log(message);
	}
	if (performer_id != '') {
		performer_id = encodeURIComponent(performer_id);
	} else if (male_id != '') {
		male_id = encodeURIComponent(male_id);
	}
	if(postUpdate && postUpdate.readystate != 4){
		postUpdate.abort();
	}
	postUpdate = $.post('./linemail_list_detail.php', {
			is_check	:	is_check,
			male_id		:	male_id,
			performer_id:	performer_id,
			message		:	message,
			page		:	page,
			order		:	sort,
			select		:	select,
			male_type	:	male_type,
			end_date	:	end_date,
			start_date	: 	start_date,
			performer_type : performer_type
			
		}, function(data){
			$('#message').html(data);
			$('#message').focus();
			loadCheck();
			console.log('succes');
		}
	);
}

function processList() {
	message = $('#message-keyword').val();
	performer_id = $('.search_performer').val();
	male_id = $('.search_male').val();
	updateList();
}

function validateDate() {
	var startYear = $('#start-year').val();
	var startMonth = $('#start-month').val();
	var startDay = $('#start-day').val();
	
	var endYear = $('#end-year').val();
	var endMonth = $('#end-month').val();
	var endDay = $('#end-day').val();
	
	if (
		(startYear != '' && startMonth != '' && startDay != '') &&
		(endYear != '' && endMonth != '' && endDay != '') 
	) {
		
		start_date = startYear + '-' + startMonth + '-' +startDay;
		end_date = endYear + '-' + endMonth + '-' + endDay;
			
		
		page = 1;
		processList();
		
	} else if (start_date != '' || end_date != '') {
		end_date = '';
		start_date = '';
		page = 1;
		processList();
	} else {
		start_date = '';
		end_date = '';
	}
}

function changeType(user, elem){
	if (user == 'performer') {
		if (elem.val() == 0) {
			performer_type = 0;
			male_type = 0;
		} else if(elem.val() == 1) {
			performer_type = 1;
			male_type = 2;
		} else {
			performer_type = 2;
			male_type = 1;
		}
	} else {
		if (elem.val() == 0) {
			male_type = 0;
			performer_type = 0;
		} else if(elem.val() == 1) {
			male_type = 1;
			performer_type = 2;
		} else {
			male_type = 2;
			performer_type = 1;
		}
	}
	
	$('#male-type').val(male_type);
	$('#performer-type').val(performer_type);
	page = 1;
	processList();
}