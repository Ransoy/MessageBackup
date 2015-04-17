$(function(){
	// modal
	$('a[rel=leanModal]').leanModal();

	//show hide emoji
	$('#btn_emoji').click(function(e){
		if($('#emoji_panel').is(':visible')){
			$(this).removeClass('on');
			$('#emoji_panel').hide();
		}else{
			$(this).addClass('on');
			$('#emoji_panel').fadeIn(200);
		}
		e.preventDefault();
	});

	// close emoji panel
	$('#close_emoji_panel').on('click', function(e){
		e.preventDefault();
		$('#emoji_panel').hide();
		$('#btn_emoji').removeClass('on');
	});

	// removes references to the image
	$('#image_del_btn').click(function() {
		$('#image').val('');
		$('#img_path').val('');
		$('#is_new_img').val('0');
		$('.img_attach > canvas').hide();
		$('#cam').val('');
		$('#file').val('');
		
		if ($('.img_attach > img').length > 0) {
			$('.img_attach > img').remove();
		}
		
	});

	//trigger for ios
	/*$('#ios').click(function(){
		$('#cam').trigger('click');
	});*/
	
	$('.btn_attach').click(function(e){
		e.preventDefault();
		$('#file').trigger('click');
		return false;
	});

	// emoji tab
	$(document).on('click', '.emoji_tab_menu li', function(e){
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

	// emoji flick
	$('.emoji_tab ul').on({
		'touchstart': function(e) {
			this.touchX = event.changedTouches[0].pageX;
			this.slideX = $(this).position().left;
		},
		'touchmove': function(e) {
			e.preventDefault();
			this.slideX = this.slideX - (this.touchX - event.changedTouches[0].pageX );
			$(this).css({left:this.slideX});
			this.accel = (event.changedTouches[0].pageX - this.touchX) * 5;
			this.touchX = event.changedTouches[0].pageX;
		},
		'touchend': function(e) {
			this.slideX += this.accel
			$(this).animate({left : this.slideX },200,'linear');
			this.accel = 0;
			w = - ( $(this).width() - $(this).parent("#flame").width() );
			if (this.slideX > 0) {
			   this.slideX = 0;
			   $(this).animate({left:"0px"},500);
			}
			if (this.slideX < w) {
			   this.slideX = w;
			   $(this).animate({left:"-600px"},500);
			}
		}
	});

	// emoji is added to body
	$(document).on('click', '.emoji_tab li a', function(e) {
		e.preventDefault();
		var emoji = '[i:' + $(this).attr('href') + ']';

		$('#body').selection('replace', {
			text : emoji,
			caret : 'end'
		});

		return false;
	});

	// image file input is not visible but is triggered by #image_button
	$('#image_btn').on('click', function(e) {
		e.preventDefault();
		$('#image').click();
	});

	//camera
	$('#cam').change(function() {
		var fileInput = $(this).get(0);
		if (isImageUpload(fileInput)) {
			previewImage(fileInput);
			// update new image flag
			$('#is_new_img').val('1');
			$('#modal_dialog_attach').hide();
			$('#file').val('');
			$('#lean_overlay').hide();
			
			if ($('.img_attach > img').length > 0) {
				$('.img_attach > img').remove();
			}
		} 
		else {
			$(this).val('');
		}
	});

	//file 
	$('#file').change(function() {
		var fileInput = $(this).get(0);
		if (isImageUpload(fileInput)) {
			previewImage(fileInput);
			// update new image flag
			$('#is_new_img').val('1');
			$('#modal_dialog_attach').hide();
			$('#cam').val('');
			$('#lean_overlay').hide();
			
			if ($('.img_attach > img').length > 0) {
				$('.img_attach > img').remove();
			}
		}
		else {
			$(this).val('');
		}
	});

	// submits form
	$('#blog_submit').on('click', function(e) {
		$('.form_wrap--blog form').submit();
	});

	//choose file selected.
	$('#sel_file').click(function(){
		$('#file').trigger("click");
	});
	
	//take photo selected
	$('#sel_cam').click(function(){
		$('#cam').trigger("click");
	});
});

/**
 * Shows a preview of an input image if any. Otherwise, it shows previously uploaded image if any.
 * @param input Input element.
 */
function previewImage(input) {
  $('#image_size_error').hide();
  var imgSize = input.files[0].size / 1024 / 1024;
  if (imgSize > 8) {
    $('#image_size_error').show();
    clearImage();
    return;
  }
  
	var reader = new FileReader();

	if (input.files && input.files[0]) {
		// if 1 ios
		//if ($('#sp_type').val() == 1) {
			reader.onload = function(event) {
				$('.img_attach').html('<img src="' + event.target.result + '"/>');
			}
			reader.readAsDataURL(input.files[0]);
			return true;
		//}
			
			var orientation,exif;
			reader.onloadend = function(event) {
			    var contents = event.target.result;
			    exif= EXIF.readFromBinaryFile(contents);
			    orientation = exif.Orientation;
			};
		    reader.readAsArrayBuffer(input.files[0]);
		    
			var height,width;
		    var img = new Image();
		    var url = window.URL ? window.URL : window.webkitURL;
		    img.src = url.createObjectURL(input.files[0]);
		    
		    img.onload = function() {
			    width = img.width;
			    height = img.height;
	    
			   var canvas = $('.img_attach > canvas')[0];
				$('.img_attach > canvas').show();
					canvas.width = width;
					canvas.height= height;
			   var ctx = canvas.getContext("2d");

			   //portrait upside down
			   if (orientation == 8 ) {
				   canvas.width = height;
				   canvas.height = width;
			       ctx.setTransform(0, -1, 1, 0, 0, width);
			       ctx.drawImage(img, 0, 0, width, height);
			   }
			   else if (orientation == 6) {
				   canvas.width = height;
				   canvas.height = width;
			       ctx.setTransform(0, 1, -1, 0, height, 0);
			       ctx.drawImage(img, 0, 0, width, height);
			   }
			   //portrait
			   else if (orientation == 3) {
				   canvas.width = width;
				   canvas.height = height;
				   ctx.translate(width, height);
		           ctx.rotate(Math.PI);
			       ctx.drawImage(img, 0, 0, width, height);
			   } 
			   else {
			       ctx.setTransform(1, 0, 0, 1, 0, 0);
			       ctx.drawImage(img, 0, 0, width, height);
			   }
		    }
	} 
	return true;
}

function clearImage() {
  $('#image').val('');
  $('#img_path').val('');
  $('#is_new_img').val('0');
  $('.img_attach > canvas').hide();
  $('#cam').val('');
  $('#file').val('');
}

/**
 * Checks if the selected file is an image.
 * @param input Input element.
 */
function isImageUpload(input) {
	var ext = input.value.match(/\.(.+)$/)[1];
	ext = ext.toLowerCase();
	switch (ext) {
		case 'jpg':
		case 'jpeg':
		case 'bmp':
		case 'png':
		case 'gif':
			return true;
		default:
			this.value = '';
	}

	return false;
}