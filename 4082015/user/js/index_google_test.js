function reloadPage(t,v){
	var data = "";
	if(v != undefined){
		var data = "select="+v;
	}
	$.ajax({
		url: "/index_google_test.php",
		type: "post",
		data: data+"&mode=xxnode",
		success: function(request){
			$("#girls-area").html(request);
		}
	});
}
$(document).ready(function(){$("body").bind("contextmenu",function(e){return false;});});
