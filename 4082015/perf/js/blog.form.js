$(function() {
	// enable buttons only when the document is ready
	$('button').removeAttr('disabled');

	/* EMOJI PANEL */
	// emoji button toggles emoji panel
	$('#btn_emoji').on('click', function(e) {
		e.preventDefault();
		if ($('#emoji_panel').is(':visible')) {
			$(this).removeClass('on');
			$('#emoji_panel').hide();
		} else {
			$(this).addClass('on');
			$('#emoji_panel').fadeIn(200);
		}
	});

	// emoji tab
	$(document).on('click', '.emoji_tab_menu li', function(e) {
		e.preventDefault();
		var target_tab = $('#' + $(this).attr('data-target'));
		var tabs = $('.emoji_tab');
		var tab_menu = $('.emoji_tab_menu li');
		if (!target_tab.is(':visible')) {
			tab_menu.removeClass('on');
			$(this).addClass('on');
			tabs.hide();
			target_tab.show();
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

	// removes references to the image
	$('#image_del_btn').on('click', function(e) {
		e.preventDefault();
		clearImage();
	});

	// shows a preview whenever an image is selected
	$('#image').change(function() {
		var fileInput = $(this).get(0);
		if (isImageUpload(fileInput)) {
			previewImage(fileInput);
			// update new image flag
			$('#is_new_img').val('1');
		}
		else {
			$(this).val('');
		}
	});

	// submits form
	$('#blog_submit').on('click', function(e) {
		e.preventDefault();
		$('.blog_form_wrap form').submit();
	});

	// deletes an article after a confirmation
	$('.func_delete_article').click(function(e) {
		if (!confirm('–{“–‚Éíœ‚µ‚Ä‚æ‚ë‚µ‚¢‚Å‚·‚©H')) {
			return false;
		}
	});
	
	// changes blog theme participation
	$(document).on('change', 'input[name=part]', function() {
		$('#blog_theme_id').val($('input[name=part]:checked').val());
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
	
	if (input.files && input.files[0]) {
		var reader = new FileReader();

		reader.onload = function(e) {
			$('.img_attach').html('<img src="' + e.target.result + '"/>');
		}

		reader.readAsDataURL(input.files[0]);
	}
}

function clearImage() {
	$('#image').val('');
	$('#img_path').val('');
	$('#is_new_img').val('0');

	$('.img_attach').html('');
}


/**
 * Checks if the selected file is an image.
 * @param input Input element.
 */
function isImageUpload(input) {
	var ext = input.value.match(/([^.]*)$/)[1];
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

