function mailHistoryDisp(mail){
	var html = '';
	
	var rd = '<input name="mail_id[]" type="checkbox" class="sel_cb" value="' + mail.mail_id +'">';
	var hr = 'mail_detail.php?mail_id=' + mail.mail_id + '&id=' + mail.id;
	var ms = '受信　';
	if(mail.mail_stat == 2){
		rd = '<input name="mails_id[]" type="checkbox" class="sel_cb" value="' + mail.mail_id +'">';
		hr = 'send_detail.php?mail_id=' + mail.mail_id + '&id=' + mail.id;
		ms = '送信　';
	}
	
//	if(mail.jyoutai == 1){
//		html += '<tr class="news">';
//	}
//	else{
		html += '<tr>';
//	}
	html += '<td class="td_checkbox">';
	html += rd;
	html += '</td>';
	html += '<td>';
	html += '<a href="' + hr + '">';
	html += '<p class="mail_title">' + mail.subject + '</p>';
	html += '</a>';
	html += '<p><span class="time">' + ms + mail.cre_date + '</span></p>';
	html += '</td>';
	html += '<td class="td_right"><img src="image/icon/header_next_arrow.png" class="next_arrow"></td>';
	html += '</tr>';
	
	return html;
}
