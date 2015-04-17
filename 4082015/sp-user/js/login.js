$(function(){
	$('#loginbtn').on('click',function(){
		var i = $('#login_id').val();
		var p =  $('#login_pass').val();
		var h = Math.floor( Math.random() * 100000 );
		var c = 0;
		if($("#checkbox:checked").val()){
			c = 1;
		}
		if(i == "" || p == ""){
			$("#login_msg").html('<font style="color:#ff0000;">ＩＤまたはパスワードが入力されていません</font><br>');
			$('#login_id').focus();
		}
		var boylogin = $.ajax({
			url : './ajax/boy_login_ajax.php?h=' + h,
			type : 'post',
			data : {
				'user_id':i,
				'password':p,
				'mode2':'login',
				'save':c
			}
		});
		$.when(boylogin)
			.done(function(response){
				da = jQuery.parseJSON(response);
				if(da.success == "false"){
					$("#login_msg").html('<font style="color:#ff0000;">' + da.msg + '</font><br>');
					return false;
				}
				window.location.reload();
				return false;
			});
	});
});
