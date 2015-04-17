/*
 * blog page script
 */
 
$(function(){
	// disable link
	$(document).on('click', 'a.disable', function(e){
		e.preventDefault();
	});

	// function like
	$(document).on('click', '.func_like', function(e){
		e.preventDefault();	
		// いいね処理
	});
});