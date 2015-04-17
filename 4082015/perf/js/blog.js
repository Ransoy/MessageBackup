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

		var isLikedClass = 'cnt_like--magenta';
		
		var likeLink = $(this);
		
		// if already liked, do nothing
		if (likeLink.hasClass(isLikedClass)) return;
		
		var likeCountTag = likeLink.find('span.cnt');
		var likeCount = parseInt(likeCountTag.text());
		
		var blogArticleId = likeLink.data('id');
		var userType = likeLink.data('utype');
		var userId   = likeLink.data('uid');
		//var action   = (likeLink.hasClass(isLikedClass)) ? 'del' : 'add';
		var action   = 'add';
		
		$.post(
			'update_article_like.php',
			{ blog_article_id : blogArticleId,
				user_type : userType,
				user_id   : userId,
				action    : action },
			function(data) {
				if ('success' == data) {
					if ('del' == action) {
						likeLink.removeClass(isLikedClass);
						likeCountTag.text(likeCount - 1);
					}
					else {
						likeLink.addClass(isLikedClass);
						likeCountTag.text(likeCount + 1);
					}
				} 
			}
		);
	});

	// delete article
	$(document).on('click', '.func_article_delete', function(e){
		e.preventDefault();
		if(window.confirm('本当に削除してよろしいですか？')){
			var id=$(this).attr('data-val');
			$.get('delete_article.php',{id:id},function(res){
				if(res == 'ok'){
					window.location = "./index.php";
				}else{
					alert("Error occured!");
				}
			});
		}
	});
	
	// delete comment
	$(document).on('click', '.func_delete_comment', function(e){
		e.preventDefault();
		if(window.confirm('本当に削除してよろしいですか？')){
		}
	});
	
	// load more comments
	$(document).on('click', '.load_more > a', function(e){
		e.preventDefault();
		var loadMoreLink  = $(this);
		var blogArticleId = loadMoreLink.data('id');
		var page = loadMoreLink.data('page');
		$.post(
			'get_article_comment.php',
			{
				blog_article_id : blogArticleId,
				page : page,
				is_own : $('#is_own').val()
			},
			function(data) {
				$('.comment_list').html(data);
				// 削除ポップアップ
			$('.popbox').popbox({
			    'open'          : '.open',
			    'box'           : '.box',
			    'arrow'         : '.arrow',
			    'arrow-border'  : '.arrow-border',
			    'close'         : '.close'
			});
			
			// 削除成功アラート
			$('.delete_yes').click(function() {
				var id = $(this).data('val');
				var comment = $(this).closest('.comment_item');
				var count = $('.cnt_comment > a > span.cnt');
				//ajax delete
				$.post('delete_comment.php',{id:id},function(data){
					if (data == '1') {
						count.html(parseInt(count.html())-1);
						comment.remove();
						//display confirmation
						swal({
							title:'ブログコメント削除',
							text:'コメントを削除しました',
							confirmButtonText:'OK',
						});
					}
					else {
						//error write in console
						console.log(data);
					}
				});
			});
			}
		);
	});
	//for pager in notification of the performer
	$(document).on('click', '.blog_notice_list_pager a', function(e) {
		e.preventDefault();
		var currUser  = $(this).data('uid');
		var notifPage = $(this).attr('href');

		if (0 < notifPage) {
			$.post(
				'./get_article_notif.php',
				{ user_id : currUser, page : notifPage },
				function(data) {
					$('.blog_notice_list').html(data);
				}
			);
		}
		
		
	});
	
	
});