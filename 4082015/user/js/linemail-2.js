var dataArray = []; //id value

$(function() {


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
				emptyCheck();
				dataArray = [];
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
	
	
	$('.del_all').on('click',function(e){
		
		var value = IsReadChecked();
		var i = 0;
		
		if(value != ''){
			
			$.post('/ajax/message/admin_ajax.php',{action:'del_all',dataID:value},function(data){

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
	 * check if empty table row
	 * then reload if equal to 0
	 */
	function emptyCheck(){
		
		if($('table tbody  tr').length == 1){
			
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

	
});

/**
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