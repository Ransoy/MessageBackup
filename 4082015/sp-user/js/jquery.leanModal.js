// leanModal v1.1 by Ray Stone - http://finelysliced.com.au
// Dual licensed under the MIT and GPL

(function($){
	$.fn.extend({
		leanModal:function(options){
			var defaults={
				top:100,
				overlay:0.5,
				closeButton:null
			};
			var overlay=$("<div id='lean_overlay'></div>");
			$("body").append(overlay);
			options=$.extend(defaults,options);
			return this.each(function(){
				var o=options;
				$(this).click(function(e){
					
					if($(this).attr("href").match(/^#/)){
						var modal_id=$(this).attr("href");
					}else{
						var modal_id='#common_modal';
						var data_type = $(this).attr('data-type');
						switch(data_type){
							case 'ajax' :
								$.ajax({
									type: 'GET',
									url: $(this).attr('href'),
									success: function(data){
										$('#common_modal #modal_content').html(data);
									}
								});
								break;
							case 'iframe' :
								var iframe = '<iframe src="' + $(this).attr('href') + '" width="' + $(this).attr('data-width') + '" height="' +  $(this).attr('data-height') + '"></iframe>';
								$('#common_modal #modal_content').html(iframe);
								break;
							case 'image' :
								var url = $(this).attr('href');
								var img = $('<img style="background: url(' + url + '); width: 100%; position: absolute; margin: auto; background-position:center center; background-repeat:no-repeat; -o-background-size: cover; -moz-background-size: contain; -webkit-background-size: contain; background-size: contain;" src="./image/skeleton.png">');
								$('#common_modal #modal_content').html(img);
								break;
						}
					}
					
					$("#lean_overlay").click(function(){
						close_modal(modal_id);
					});
					$(o.closeButton).click(function(){
						close_modal(modal_id);
					});
					var modal_height=$(modal_id).outerHeight();
					var modal_width=$(modal_id).outerWidth();
					$("#lean_overlay").css({"display":"block",opacity:0});
					$("#lean_overlay").fadeTo(200,o.overlay);
					$(modal_id).css({"display":"block","position":"absolute","opacity":0,"z-index":11000,"left":50+"%","margin-left":-(modal_width/2)+"px","top":o.top + $(document).scrollTop() + "px"});
					$(modal_id).fadeTo(200,1);
					e.preventDefault();
				});
			});
			function close_modal(modal_id){
				$("#lean_overlay").fadeOut(200);
				$(modal_id).css({"display":"none"});
			}
		}
	})
})(jQuery);
