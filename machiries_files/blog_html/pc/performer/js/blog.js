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

	// delete article
	$(document).on('click', '.func_delete_article', function(e){
		e.preventDefault();
		if(window.confirm('本当に削除してよろしいですか？')){
			// 削除処理
		}
	});

	// delete comment
	$(document).on('click', '.func_delete_comment', function(e){
		e.preventDefault();
		if(window.confirm('本当に削除してよろしいですか？')){
			// 削除処理
		}
	});
});