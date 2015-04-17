function func_ans(No){
	ele=document.getElementById("ansdiv"+No);
	title= document.getElementById("adiv"+No);
	if(ele.style.display == "none"){
		ele.style.display = "block";
		title.style.color="blue";
	}else if(ele.style.display == "block"){
		ele.style.display = "none";
		title.style.color="#B40101";
	}
}
function MM_openBrWindow(theURL,winName,features) {
	window.open(theURL,winName,features);
}
function MM_Submit(){
	if(!document.SF.email.value.match(/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/)){
		alert("メールアドレスが正しくありません。\nご確認のうえ、もう一度入力してください。\n");
	}else{
		document.SF.submit();
	}
}