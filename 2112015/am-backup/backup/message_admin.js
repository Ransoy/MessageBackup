var message;
var performer_id;
var male_id;
var type;
var page;
var sort;
var select;
var is_check;
var start_date;
var end_date;
var postUpdate;
var performer_type;
var male_type;
var dataArray = []; //id value

function initialize() {
	message 		= '';
	performer_id 	= '';
	male_id 		= '';
	type 			= 'body';
	page 			= 1;
	sort 			= 'DESC';
	select 			= 'all';
	is_check 		= 0;
	start_date 		= '';
	end_date		= '';
	performer_type  = '';
	male_type 		= '';
	dataArray 		= [];
}
$(function() {
	initialize();
	processList();
	$('#btn-reset').click(function() {
		initialize();
		$('#admin-screen-form')[0].reset();
		$('#message').html(loader);
		processList();
	});
	var loader = '<tr><td colspan="6">' + $('#loader').html() + '</td></tr>';
	$('#btn-search').click(function(e) {
		e.preventDefault();
		validateDate(0);
		if (!validateDate(3)) {
			alert('日付不完全。');
			return;
		} else {
			$('#message').html(loader);
			page = 1;
			processList();
		}
		
	});
	
	$('.select-message select').change(function() {
		select = $(this).val();
		if (select == 'image') {
			$('#message-keyword').val('');
		} 
		page = 1;
		//processList();
	});
	
	$('.sort select').change(function() {
		sort = ($(this).val() == '1') ? 'DESC' : 'ASC';
		//processList();
	});
	
	$('.is-check select').change(function() {
		is_check = $(this).val();
		page = 1;
		//processList();
	});
	
	$('#performer-type').change(function() {
		changeType('performer', $(this));
	});
	$('#male-type').change(function() {
		changeType('user', $(this));
	});
	
	/*$('#start-year, #start-month, #start-day, #end-year, #end-month, #end-day').change(function() {
		
	});*/

	/*
	 * check/uncheck value 
	 */
	$('#chck-all').on('click',function(e) {
		
		var i = 0,a = 0;
		var rowId;
		var arrtemp = [];
		
		if($(this).prop('checked') && $('.chck_s').size() > 0 ) {
			
			$('.chck_s').prop('checked', true);
			
			if(dataArray.length > 0){
				var x = IsReadChecked();
				while(i < x.length){
					dataArray.push(parseInt(x[i]));
					i++;
				}
			}else{
				var x = IsReadChecked().toString();
				dataArray = JSON.parse("[" + x + "]");
			}
			
		}else{
			
			while(i < dataArray.length){
				rowId = '#'+dataArray[i]+' .act_col > .chck_s';
				if($(rowId).is(':checked')){
					$(rowId).prop('checked', false);
					arrtemp.push(dataArray[i]);
				}
				i++;
			}
			while(a < arrtemp.length){
				dataArray.splice(dataArray.indexOf(arrtemp[a]), 1);
				a++;
			}
			
		}

	});
	
	/*
	 * check/uncheck individual data
	 * set to append to dataArray value
	 */
	$(document).on('click','.chck_s',function(e) {
		
		var val = parseInt($(this).val());
		
		if($(this).prop('checked')) {
			dataArray.push(val);
		}else{
			dataArray.splice(dataArray.indexOf(val), 1);
		}
		
	});
	
	/*
	 * send value to read in specific message
	 */
	$(document).on('click','td > .chk_btn',function(e){
		
		var value = this.value;
		
		if(value != ''){
			$.post('/ajax/message/admin_ajax.php',{action:'chk_btn',dataID:value},function(data){
				
				if(data == true){
					$('#'+value).remove();
				}

				emptyCheck();
				
			});
		}else{
			alert('There is no data has been checked');
		}
	
		
	});
	

	
	/*
	 * send value by batch
	 * to read all message
	 */
	$('.chk_all').on('click',function(e){
		
		var value = dataArray;
		var i = 0;
		
		if(value != ''){
			
			$.post('/ajax/message/admin_ajax.php',{action:'chk_all',dataID:value},function(data){
				if(data == true){
					
					while(i < value.length){
						
						$('#'+value[i]).remove();
						i++;
						
					}
					$('#chck-all').prop('checked',false);
				}
				dataArray = [];
				emptyCheck();
				
			});	
		}else{
			alert('There is no data has been checked');
		}
		
		
	});
	
	/*
	 * Function delete this message 
	 * both user/performer
	 */
	$('.del_btn').on('click',function(e){
		
		var value = IsReadChecked();
		var i = 0;
		
		if(value != ''){
			
			$.post('/ajax/message/admin_ajax.php',{action:'delete',dataID:value},function(data){

				if(data == true){
					
					while(i < value.length){
						
						$('#'+value[i]).remove();
						
						i++;
					}
					emptyCheck();
				}
				
			});
			
		}else{
			
			alert('There is no data has been checked');
			
		}
	});
	
	/*
	 * Function delete this message by batch
	 * both user/performer
	 */
	$('.del_all').on('click',function(e){
		
		var value = dataArray;
		var i = 0;
		
		if(value != ''){
			if(confirm('チェックしたメール履歴を削除します。よろしいですか？')){
				$.post('/ajax/message/admin_ajax.php',{action:'del_all',dataID:value},function(data){

					if(data == true){
						
						while(i < value.length){
							
							$('#'+value[i]).remove();
							
							i++;
						}
						dataArray = [];
						emptyCheck();
					}
					
				});
			}
		}else{
			
			alert('There is no data has been checked');
			
		}
	});
	
	$('#start-month' , '#start-year').change(function(){
			var month = $(this).val();
			var year = $('#start-year').val();
			var i = 1;
			var days = '';
			
			if(year && month){
				days = daysInMonth(month,year);
				$('#start-day').html('');
				$html = '<option value selected>日</option>';
				while(i <= days){
					$html += '<option value='+i+'>'+i+'</option>';
					i++;
				}
				$('#start-day').html($html);
			}
				
	});
	
	$('#end-month' , '#start-year').change(function(){
			var month = $(this).val();
			var year = $('#end-year').val();
			var i = 1;
			var days = '';
			
			if(year && month){
				days = daysInMonth(month,year);
				$('#end-day').html('');
				$html = '<option value selected>日</option>';
				while(i <= days){
					$html += '<option value='+i+'>'+i+'</option>';
					i++;
				}
				$('#end-day').html($html);
			}
				
	});
	
});


$(document).on('keypress keyup change', '.search_performer, .search_male, #message-keyword', function(e) {
	page = 1;
	//processList();
});


$(document).on('click', '.del_btn', function() {
	var id = $(this).val();
	if(confirm('チェックしたメール履歴を削除します。よろしいですか？')){
		$.post('../ajax/message/admin_ajax.php', {action:'delete', dataID:id}, function(data) {
			if(data) {
				$('tr#'+id).fadeOut(500).remove();
				if($('table tbody  tr').length == 1){
					window.location.reload();
				}
			}
		});
	}
});

/*
 * Get days in month
 * @param month - set month numerical 01-12
 * @param year - set year
 * @return numerical value
 */
function daysInMonth(month,year) {
    return new Date(year, month, 0).getDate();
}


/*
 * check if empty table row
 * then reload if equal to 0
 */
function emptyCheck(){
	
	if($('table tbody  tr').length == 1 || dataArray.length == 0){
		
		window.location.reload();
		
	}
	
}

/*
 * retrieve all value that has
 * been check
 */
function IsReadChecked(){
	
	var values = $('.act_col > input:checkbox:checked.chck_s').map(function () {
		  return this.value;
		}).get();
	
	return values;
}

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

$(document).on('click', '#click_log', function(e) {
	page = 1;
	pointLoglist(page);
});

$(document).on('click', '.page_num', function(e) {
	pointLoglist($(this).data('val'));
});

$(document).on('click', '.prev_num', function(e) {
	pointLoglist($(this).data('val'));
});
	
$(document).on('click', '.next_num', function(e) {
	pointLoglist($(this).data('val'));
});

function pointLoglist(pageId){
	validateDate(1);
	page = pageId;
	var male_id = $('#male_id').val();
	var female_id = $('#female_id').val();
	
	if (performer_id != '') {
		performer_id = encodeURIComponent(female_id);
	} else if (male_id != '') {
		male_id = encodeURIComponent(male_id);
	}
	if(postUpdate && postUpdate.readystate != 4){
		postUpdate.abort();
	}
	
	postUpdate = $.post('./message_point_list.php', {
		
		male_id			:	male_id,
		female_id		:	female_id,
		start_date		:	start_date,
		page		:	page,
		end_date		:	end_date,
		action: 'submit'
		
	}, function(data){
			$('#list_log').html(data);
	
	});
}	
	
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
	postUpdate = $.post('./message_admin_detail.php', {
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

function validateDate(process) {
	var startYear = $('#start-year').val();
	var startMonth = $('#start-month').val();
	var startDay = $('#start-day').val();
	
	var endYear = $('#end-year').val();
	var endMonth = $('#end-month').val();
	var endDay = $('#end-day').val();
	console.log(startYear +'|'+startMonth+'|'+startDay+'|'+endYear +'|'+endMonth+'|'+endDay);
	if (process == 3) {
		if (startYear != '' || startMonth != '' || startDay != '' || endYear != '' || endMonth != '' || endDay != '') {
			if (start_date == '' || end_date == '') {
				
				console.log('error0');
				return false;
			}
			console.log('error1');
		} 
		console.log('error2');
		return true;
	} else {
		if (
			(startYear != '' && startMonth != '' && startDay != '') &&
			(endYear != '' && endMonth != '' && endDay != '') 
		) {
			
			start_date = startYear + '-' + startMonth + '-' +startDay;
			end_date = endYear + '-' + endMonth + '-' + endDay;
			if (process == 1) {
				page = 1;
				processList();
			}
		} else if (start_date != '' || end_date != '') {
			end_date = '';
			start_date = '';
			if (process == 1) {
				page = 1;
				processList();
			}
		} else {
			start_date = '';
			end_date = '';
		}
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
	//processList();
}


/*
 * Set dataArray checkbox to true
 * return void
 */
function loadCheck(){
	
	var i = 0;
	var rowId;
	var thisCheck = 0;
	
	while(i < dataArray.length){
		
		rowId = '#'+dataArray[i]+' .act_col > .chck_s';
		
		$(rowId).prop('checked', true);
		if($(rowId).is(':checked')){
			thisCheck++;
		}
		
		i++;
	}

	if(thisCheck > 0){
		$('#chck-all').prop('checked',true);
	}else{
		$('#chck-all').prop('checked',false);
	}

	
}