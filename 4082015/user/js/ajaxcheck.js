//-----------------------------------
//
//	Site:Macherie
//	Build:20080411
//	Author:waka
//
//-----------------------------------
/* Setting
-------------------------------------*/
var url ="firstpageRight_mobi_check_nick.php";


check_nick= function (){
	chatname = nickname.value;
	if(jstrlen(chatname) <= 14){
		val=EscapeEUCJP(chatname);
		new Ajax.Request(url, { method: 'post', postBody: $H({"nick_name":val}).toQueryString(), onComplete: dispData });
	}else {
		cresult.innerHTML="���`���b�g�l�[���̕������I�[�o�[�ł��B";
		cresult.style.color="red";

	}
}

function jstrlen(str,   len, i) {
   len = 0;
   str = escape(str);
   for (i = 0; i < str.length; i++, len++) {
      if (str.charAt(i) == "%") {
         if (str.charAt(++i) == "u") {
            i += 3;
            len++;
         }
         i++;
      }
   }
   return len;
}


function dispData(node){
	switch(node.responseText){
		case 'true':
			if(chatname=="")break;
			cresult.innerHTML="���`���b�g�l�[�����d�����Ă��܂��B\""+chatname+"\"�͗��p�ł��܂���B";
			cresult.style.color="red";
		break;
		case 'false':
			cresult.innerHTML="��\""+chatname+"\"�͗��p�ł��܂��B";
			cresult.style.color="green";
		break;
		case 'none':
			cresult.innerHTML="���`���b�g�l�[������͂��Ă�������";
			cresult.style.color="red";
		break;
		case 'dberror':
			cresult.innerHTML="";
			cresult.style.color="red";
		break;
		case 'default':
		break;
	}
}

function onload_body(){
	nickname=document.getElementById('nick_name');
	if(nickname) nickname.onchange=check_nick;
	cresult = document.getElementById("nick_check_result");
}
