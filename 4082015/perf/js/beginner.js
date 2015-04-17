var disp_flg = 1;
function menuDisplay(){
	var element = document.getElementById("contain");
	var element_close = document.getElementById("close_btn");
	var element_open = document.getElementById("open_btn");
	document.cookie = "disp_flg="+disp_flg+";expires=Tue, 1-Jan-2020 00:00:00 GMT;";
	if(disp_flg == 1){
		element.style.display = 'none';
		element_close.style.display = 'none';
		element_open.style.display = 'block';
		disp_flg = 0;
	}else{
		element.style.display = 'block';
		element_close.style.display = 'block';
		element_open.style.display = 'none';
		disp_flg = 1;
	}
}
function menuDisplay2(){
	str = document.cookie;
	if(str.indexOf('disp_flg=1;',0) != -1){
		document.getElementById("contain").style.display = 'none';
		document.getElementById("close_btn").style.display = 'none';
		document.getElementById("open_btn").style.display = 'block';
		disp_flg = 0;
	}
}
function pageChange(page_num){
	for(i=1;i<=6;i++){
		if(i==page_num){
			document.getElementById("page"+i).style.display = 'block';
		}else{
			document.getElementById("page"+i).style.display = 'none';
		}
	}
}
jQuery.event.add(window, "load", function(){
	menuDisplay2();
});