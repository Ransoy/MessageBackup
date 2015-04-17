$(function() {
  setupFlv1();

  $startY = $('#start_y');
  $endY   = $('#end_y');

  toggleTimeOthers('end', $endY.val());
  toggleTimeOthers('start', $startY.val());

  $startY.change(function() {
    toggleTimeOthers('start', this.value);
  });
  $endY.change(function() {
    toggleTimeOthers('end', this.value);
  });

  // flv1 'All' option
  $('input[name^=flv1][value=99]').prop('checked', areAllFlv1Checked());

  $('input[name^=flv1]').click(function() {
    if ('99' == this.value) {
      $('input[name^=flv1]').prop('checked', this.checked);
    }
    else if (false == this.checked) {
      $('input[name^=flv1][value=99]').prop('checked', false);
    }
    else if (areAllFlv1Checked()) {
      $('input[name^=flv1][value=99]').prop('checked', true);
    }
  });

  $('input[name^=flv1][value=99]').click(function() {
    $('input[name^=flv1]').prop('checked', this.checked);
  });

  showPreview();

  $('#preview').click(function() {
    showPreview();
  });

  $('input:submit').click(function() {
    $('form').submit();
    this.disabled = true;
  });
});

function setupFlv1() {
  // rearrange some checkboxes
  var $07flv1 = $('input[name^=flv1][value=7]').parent();
  $07flv1.insertAfter($('input[name^=flv1][value=2]').parent());
  $07flv1.after('<br>');

  var $18flv1 = $('input[name^=flv1][value=18]').parent();
  $18flv1.insertAfter($('input[name^=flv1][value=21]').parent());
  $18flv1.before('<br>');

  var $allflv1 = $('input[name^=flv1][value=99]').parent();
  $allflv1.insertBefore($('input[name^=flv1][value=1]').parent());
}

function areAllFlv1Checked() {
  return (9 <= $('input[name^=flv1]:checked').length);
}

/**
 * NOT USED since hour and minute options have been removed.
 * Kept for future reference.
 */
function populateTimeOptions() {
  for (t = 1; t < 24; t++) {
    var padT = (t < 10) ? '0' + t : t;
    hourOption = '<option value="' + padT + '">' + padT + '</option>';
    $('#start_h').append(hourOption);
    $('#end_h').append(hourOption);
  }

  for (t = 1; t < 60; t++) {
    var padT = (t < 10) ? '0' + t : t;
    minOption = '<option value="' + padT + '">' + padT + '</option>';
    $('#start_min').append(minOption);
    $('#end_min').append(minOption);
  }
}

/**
 * Show or hide other date time fields.
 * Resets date time fields to default values when hidden.
 * @param type "start" or "end"
 * @param isShow Hide if O. Show for any other value.
 */
function toggleTimeOthers(type, isShow) {
  var $otherDTFields = $('#' + type + '_others');
  if (0 == isShow) {
    $otherDTFields.find('select[name$=_y]').val('0');
    $otherDTFields.find('select[name$=_m]').val('1');
    $otherDTFields.find('select[name$=_d]').val('1');
    $otherDTFields.find('span[id$=_others]').hide();
    $otherDTFields.hide();
  }
  else $otherDTFields.show();
}

/**
 * Formats the date and time to yîNMåédì˙.
 * @param type "start" or "end"
 */
function formatDateTime(type) {
  var valY = $('#' + type + '_y').val();

  if (0 == valY) return '';

  var dateTime = valY + 'îN' +
    $('#' + type + '_m').val() + 'åé' +
    $('#' + type + '_d').val() + 'ì˙';

  var valH = $('#' + type + '_h').val();
  if ('' != valH)
    dateTime += ' ' + valH + ':' + $('#' + type + '_min').val();

  return dateTime;
}

/**
 * Shows a preview of an input image if any. Otherwise, it shows previously uploaded image if any.
 * @param input Input element.
 */
function previewImage(input) {
  if (input.files && input.files[0]) {
    var reader = new FileReader();

    reader.onload = function (e) {
      $('dt').html('<img class="img_prev" src="' + e.target.result + '">');
    }

    reader.readAsDataURL(input.files[0]);
  }
  else {
    // this must always be set in update
    var imgPath = $('#img_path').val();
    if (imgPath)
      $('dt').html('<img class="img_prev" src="' + imgPath + '">');
  }
}

function showPreview() {
  previewImage($('input[name=image]').get(0));

  $('.title').html($('#title').val().trim());

  var datetime = formatDateTime('start');
  if ('' != datetime) {
    var endDateTime = formatDateTime('end');
    if ('' != endDateTime) datetime += 'Å`' + endDateTime;
    $('.time').html(datetime)
  }
  else $('.time').html('');

  if ($('input[name^=is_male]').get(0).checked) $('.btn_blue').show();
  else  $('.btn_blue').hide();
  if ($('input[name^=is_female]').get(0).checked) $('.btn_red').show();
  else  $('.btn_red').hide();
  if ($('input[name^=is_macherie]').get(0).checked) $('.btn_yellow').show();
  else  $('.btn_yellow').hide();
  if ($('input[name^=is_new]').get(0).checked) $('.btn_news').show();
  else  $('.btn_news').hide();

  $('.content').html($('#short_msg').val().trim());
  $('.detail_cont').html($('#msg').val().trim());
}
