var event_areaTag = document.getElementById("event_areaTag");
var event_areaRoom = document.getElementById("event_areaRoom");
var first_areaTag = document.getElementById("first_areaTag");
var first_areaRoom = document.getElementById("first_areaRoom");
var second_areaTag = document.getElementById("second_areaTag");
var second_areaRoom = document.getElementById("second_areaRoom");
var new_areaTag = document.getElementById("new_areaTag");
var new_areaRoom = document.getElementById("new_areaRoom");

var docStr = "";
var ReLoadFLG = false;

/* GirlsDisplay
-------------------------------------*/
function reloadPage(selectId,n_select){
	if(ReLoadFLG == false){

		ReLoadFLG = true;
		if(selectId != ''){
			docStr = document.getElementById(selectId).value;
		}else{
			docStr = n_select;
		}

		var url = 'http://' + location.hostname + '/ajax_online.php';

		setTag(docStr);
		document.cookie="number="+docStr;

		var current_url = 'http://' + location.hostname + '/imacherie_online.php';

		if(current_url == location.href){
			wval='true';
		} else {
			wval='false';
		}

		new Ajax.Request(url, { method: 'post', postBody: $H({m:docStr,w:wval}).toQueryString(), onComplete: dispReData });
	}
}

function dispReData(node){
//alert(node.responseText);
	setNode(node.responseText);
	setTimeout(function(){ReLoadFLG = false;},800);
}

/* NodeProcess
-------------------------------------*/
function setNode(allNode){

	event_areaTag.style.display = "none";
	first_areaTag.style.display = "none";
	second_areaTag.style.display = "none";
	new_areaTag.style.display = "none";
	event_areaRoom.style.display = "none";
	first_areaRoom.style.display = "none";
	second_areaRoom.style.display = "none";
	new_areaRoom.style.display = "none";
	event_areaRoom.innerHTML = "";
	first_areaRoom.innerHTML = "";
	second_areaRoom.innerHTML = "";
	new_areaRoom.innerHTML = "";
	var areaData = eval("("+allNode+")");

	//var img = "http://c.macherie.tv/c/m/images/common/tag/";
	var img = "http://192.168.1.105/c/m/images/common/tag/";

	switch(docStr){
		case "0": $("tag2").src = img + 'tag_2shot.gif';break;
		case "1": $("tag2").src = img + 'tag_2shot.gif';break;
		case "2": $("tag2").src = img + 'tag_2shot.gif';break;
		default: $("tag2").src = img + 'tag_locate9.gif';
	}

	// --eventRoom
	if( areaData.eventNode !== "notNode" && areaData.eventNode != "showNode"){
		//$("m_event").options[docStr].selected = true;
		event_areaTag.style.display = "";
		event_areaRoom.style.display = "";
		for(var i=0; i<areaData.eventNode.length; i++){
			event_areaRoom.appendChild(girlsNode(areaData.eventNode[i]));
		}
	} /* else if( areaData.eventNode == "showNode" ){
		event_areaTag.style.display = "";
		event_areaRoom.style.display = "";
		event_areaRoom.appendChild(document.createElement("p"));
		event_areaRoom.lastChild.appendChild(document.createTextNode("オンライン中のパフォーマーがいません"));
	} */

	// --firstRoom
	if((areaData.firstNode !== "notNode")){
		$("m_first").options[docStr].selected = true;
		first_areaTag.style.display = "";
		first_areaRoom.style.display = "";
		for(var i=0; i<areaData.firstNode.length; i++){
			first_areaRoom.appendChild(girlsNode(areaData.firstNode[i]));
		}
	}else{
		if(docStr != 0){
			$("m_first").options[docStr].selected = true;
			first_areaTag.style.display = "";
			first_areaRoom.style.display = "";
			first_areaRoom.appendChild(document.createElement("p"));
			first_areaRoom.lastChild.appendChild(document.createTextNode("オンライン中のパフォーマーがいません"));
		}
	}

	// --secondRoom
	if((areaData.secondNode !== "notNode")&&(docStr !== 0)){
		$("m_second").options[docStr].selected = true;
		second_areaTag.style.display = "";
		second_areaRoom.style.display = "";
		for(var i=0; i<areaData.secondNode.length; i++){
			second_areaRoom.appendChild(girlsNode(areaData.secondNode[i]));
		}
	}else{
		if(docStr == 0){
			$("m_second").options[docStr].selected = true;
			second_areaTag.style.display = "";
			second_areaRoom.style.display = "";
			second_areaRoom.appendChild(document.createElement("p"));
			second_areaRoom.lastChild.appendChild(document.createTextNode("オンライン中のパフォーマーがいません"));
		}
	}

	//newRoomBlock
	if((areaData.NewFace !== "notNode")){
		new_areaTag.style.display = "";
		new_areaRoom.style.display = "";
		for(var i=0; i<areaData.NewFace.length; i++){
			new_areaRoom.appendChild(girlsNode(areaData.NewFace[i]));
		}
	}else{
		new_areaTag.style.display = "";
		new_areaRoom.style.display = "";
		new_areaRoom.appendChild(document.createElement("p"));
		new_areaRoom.lastChild.appendChild(document.createTextNode("オンライン中のパフォーマーがいません"));
	}
}



/*	GirlsNode 
------------------------------------*/
function girlsNode(node){
	var ul    = document.createElement("ul");
	var tag   = document.createElement("li");
	var pic   = document.createElement("li");
	var cname = document.createElement("li");

	ul.appendChild(tag);
	ul.appendChild(pic);
	ul.appendChild(cname);

	ul.className  = node.cs + " gB";
	tag.className = "tag";
	tag.appendChild(document.createElement("img"));
	tag.lastChild.src = "http://192.168.1.105/c/d/images/common/g/" + node.st + ".gif";

	if(node.cf == 1){
		tag.appendChild(document.createElement("img"));
		tag.lastChild.src = "http://192.168.1.105/c/d/images/common/g/new.gif";
	}else if(node.cf == 2){
		tag.appendChild(document.createElement("img"));
		tag.lastChild.src = "http://192.168.1.105/c/d/images/common/g/debut.gif";
	}else if(node.cf == 3){
		tag.appendChild(document.createElement("img"));
		tag.lastChild.src = "http://192.168.1.105/c/d/images/common/g/fine.gif";
	}else if(node.cf == 4){
		tag.appendChild(document.createElement("img"));
		tag.lastChild.src = "http://192.168.1.105/c/d/images/common/g/fine_new.gif";
	}else if(node.cf == 5){
		tag.appendChild(document.createElement("img"));
		tag.lastChild.src = "http://192.168.1.105/c/d/images/common/g/fine_debut.gif";
	}else if(node.cf == 8){
		tag.appendChild(document.createElement("img"));
		tag.lastChild.src = "http://192.168.1.105/c/d/images/common/g/checkmark.gif";
	}else if(node.cf == 7){
		tag.appendChild(document.createElement("img"));
		tag.lastChild.src = "http://192.168.1.105/c/d/images/common/g/fine_check.gif";
	}

	//この辺りで画像を表示する
	pic.className = "pic";
	pic.style = "text-align:center;";
	if(node.cs != "cm"){
		//pic.style.backgroundImage = "url(http://192.168.1.105/imgs/op/120x90/"+node.ph+")";
		pic.appendChild(document.createElement("a"));
		pic.lastChild.href = "javascript:cO('"+node.hs+"');";
		pic.lastChild.appendChild(document.createElement("img"));
		pic.lastChild.lastChild.src   = "http://192.168.1.105/imgs/op/120x90/"+node.ph;
		//pic.lastChild.lastChild.style = "width:120px;height:90px;";
		//pic.lastChild.lastChild.width  = "120";
		//pic.lastChild.lastChild.height = "90";
	} else {
		pic.style.backgroundImage = "url(http://192.168.1.105/imgs/cm/120x90/"+node.ph+")";
		pic.appendChild(document.createElement("a"));
		pic.lastChild.href = "javascript:cO('"+node.hs+"');";
		pic.lastChild.appendChild(document.createElement("img"));
		pic.lastChild.lastChild.src = "http://192.168.1.105/c/d/images/common/g/b.gif";
	}
	
	
	
	

	cname.className = "name";
	cname.appendChild(document.createElement("a"));
	if(node.cs != "cm"){
		cname.lastChild.href = "javascript:pO('"+node.hs+"');";
	} else {
		cname.lastChild.href = "javascript:cO('"+node.hs+"');";
	}
	cname.lastChild.innerHTML = node.cn;

	cname.appendChild(document.createElement("div"));
	cname.lastChild.className = "mail";
	cname.lastChild.appendChild(document.createElement("a"));
	if(node.cs != "cm"){
		cname.lastChild.lastChild.href = "javascript:mO('"+node.hs+"')";
	} else {
		cname.lastChild.lastChild.href = "javascript:cO('"+node.hs+"')";
	}
	cname.lastChild.lastChild.appendChild(document.createElement("img"));
	cname.lastChild.lastChild.lastChild.src = "http://192.168.1.105/c/d/images/common/g/thumb01mail.gif";

	if(node.vo == 1){
		cname.lastChild.appendChild(document.createElement("img"));
		cname.lastChild.lastChild.src = "http://192.168.1.105/c/d/images/common/g/thumbvoice.gif";
	}
	return ul;
}


/*	OptionSelected
-------------------------------------*/
function setTag(docStr){
	var img = 'http://192.168.1.105/c/m/images/common/tag/';
	switch(docStr){
		case "0": $("gTag").src = img + 'tag_party.gif';break;
		case "1": $("gTag").src = img + 'tag_online.gif';break;
		case "2": $("gTag").src = img + 'tag_party.gif';break;
		case "3": $("gTag").src = img + 'tag_locate1.gif';break;
		case "4": $("gTag").src = img + 'tag_locate2.gif';break;
		case "5": $("gTag").src = img + 'tag_locate3.gif';break;
		case "6": $("gTag").src = img + 'tag_locate4.gif';break;
		case "7": $("gTag").src = img + 'tag_locate5.gif';break;
		case "8": $("gTag").src = img + 'tag_locate6.gif';break;
		case "9": $("gTag").src = img + 'tag_locate7.gif';break;
		case "10": $("gTag").src = img + 'tag_locate8.gif';break;
		default: $("gTag").src = img + 'tag_online.gif';
	}
}
/* Cookie
-------------------------------------*/
function getCookieV(){
	loadData = "";
	page = "number=";
	cookie = document.cookie+";";
	pageTop = cookie.indexOf(page);

	if(pageTop!=-1){
	pageEnd = cookie.indexOf(";",pageTop);
	loadData = unescape(cookie.substring(pageTop+page.length,pageEnd));
	}
	if(loadData == "") loadData="0";
	return loadData;
}
function firstPageLoad(){
	var cllkieVal = getCookieV();
	reloadPage('',cllkieVal);
}




window.onload = firstPageLoad;
