//-----------------------------------
//
//	Site:Macherie
//	Build:2007/4/1
//	Author:S.takaya
//
//-----------------------------------

var val = "";

/* Setting
-------------------------------------*/
var reload = true;
var new_first=true;
var nodeFile ="xxnode.php";

var roomArea = document.getElementById("roomArea");
var partyArea = document.getElementById("partyArea");
var firstArea = document.getElementById("firstArea");
var secondArea = document.getElementById("secondArea");
var newFaceArea = document.getElementById("newFace");

/* Cookie
-------------------------------------*/
function getCookie(){
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
/* GirlsDisplay
-------------------------------------*/
function reloadPage(selectId){
	if(reload == true){
		$("girlsBlock").style.visibility = "hidden";
		$("roomSelect").style.display = "none";
		$("roomArea").style.display = "none";
		$("partySelect").style.display = "none";
		$("partyArea").style.display = "none";
		$("secondSelect").style.display = "none";
		reload = false;
		val = document.getElementById(selectId).value;
		$("m").options[val].selected = true;
		var url = nodeFile;
		setTag(val);
		document.cookie="number="+val;
		new Ajax.Request(url, { method: 'post', postBody: $H({m:val}).toQueryString(), onComplete: dispData });
	}
}

function reloadPageDebut(){
	var url = "/xxnodedebut.php"
	new Ajax.Request(url, { method: 'post', postBody:"", onComplete: dispData2 });
	
	var url = "/xxnodedebut20.php";
	new Ajax.Request(url, { method: 'post', postBody:"", onComplete: dispData3 });
}

function reloadPageDebut_all(){
	var url = "/xxnodedebut.php"
	new Ajax.Request(url, { method: 'post', postBody:"", onComplete: dispData2 });
}

function showAllDebut(){
	if($('toggle_debut').innerHTML=="デビューマークのパフォーマーを全て表示&gt;&gt;"){
		if(new_first==true){
			new_first = false;
			reloadPageDebut_all();
		}
		$('toggle_debut').innerHTML="表示を戻す&gt;&gt;";
		$('debut_reload').style.display='inline';
		$("aj_debut_norm").style.display = "none";
		$("aj_debut_rand").style.display='block';
		$('aj_debut_norm').style.visibility="hidden";
		$('aj_debut_rand').style.visibility="visible";
		
	}else{
		$('toggle_debut').innerHTML="デビューマークのパフォーマーを全て表示&gt;&gt;";
		$('debut_reload').style.display='none';
		$("aj_debut_rand").style.display = "none";
		$("aj_debut_norm").style.display='block';
		$('aj_debut_norm').style.visibility="visible";
		$('aj_debut_rand').style.visibility="hidden";
	}
}

function dispData2(node){

	var debutRandArea = document.getElementById("aj_debut_rand");
	debutRandArea.removeChild(debutRandArea.firstChild);

	var ddebut = eval("("+node.responseText+")");

	debutRandArea.appendChild(document.createElement("div"));

	if(ddebut.NewFace == "notNode"){
		debutRandArea.lastChild.appendChild(document.createElement("p"));
		debutRandArea.lastChild.lastChild.appendChild(document.createTextNode("オンライン中のパフォーマーがいません"));
	}else{
		for(var i=0; i<ddebut.NewFace.length; i++){
			debutRandArea.lastChild.appendChild(girlsNode(ddebut.NewFace[i]));
		}
	}
	
	debutRandArea.lastChild.innerHTML += '<div style="clear:both;display:block;margin-top:15px"></div><span id="debut_reload2"><a href="javascript:reloadPageDebut()" style="color:blue">更新する&gt;&gt;</a>　　　</span><a href="javascript:showAllDebut()" style="color:blue"><span id="toggle_debut2">表示を戻す&gt;&gt;</span></a>　　　<a href="#newface" style="color:blue">△上に戻る</a><div style="display:block;margin-top:10px"></div>';

}

function dispData3(node){

	var debutRandArea = document.getElementById("aj_debut_norm");
	debutRandArea.removeChild(debutRandArea.firstChild);

	var ddebut = eval("("+node.responseText+")");

	debutRandArea.appendChild(document.createElement("div"));

	if(ddebut.NewFace == "notNode"){
		debutRandArea.lastChild.appendChild(document.createElement("p"));
		debutRandArea.lastChild.lastChild.appendChild(document.createTextNode("オンライン中のパフォーマーがいません"));
	}else{
		for(var i=0; i<ddebut.NewFace.length; i++){
			debutRandArea.lastChild.appendChild(girlsNode(ddebut.NewFace[i]));
		}
	}
	
}

function dispData(node){
	setNode(node.responseText);
	setTimeout(function(){reload = true},500);
}

/* NodeProcess
-------------------------------------*/
function setNode(node){
	partyArea.removeChild(partyArea.firstChild);
	secondArea.removeChild(secondArea.firstChild);
	firstArea.removeChild(firstArea.firstChild);
	roomArea.removeChild(roomArea.firstChild);
	
	var data = eval("("+node+")");

	var img = "/images/face/tag/";
	switch(val){
		case "1": $("tag2").src = img + 'tag_2shot.gif';break
		case "2": $("tag2").src = img + 'tag_2shot.gif';break
		default: $("tag2").src = img + 'tag_locate9.gif';
	}

	//RoomBlock
	roomArea.appendChild(document.createElement("div"));
	if((data.eventNode !== "notNode")&&(val == 0)){
		$("m4").options[val].selected = true;
		$("roomSelect").style.display = "";
		$("roomArea").style.display = "";
		for(var i=0; i<data.eventNode.length; i++){
			roomArea.lastChild.appendChild(girlsNode(data.eventNode[i]));
		}
	}

	//PartyBlock
	partyArea.appendChild(document.createElement("div"));
	if((data.partyNode !== "notNode")&&(val == 0)){
		$("m3").options[val].selected = true;
		$("partySelect").style.display = "";
		$("partyArea").style.display = "";
		for(var i=0; i<data.partyNode.length; i++){
				partyArea.lastChild.appendChild(girlsNode(data.partyNode[i]));
		}
	}

	//FirstBlock
	if(firstArea.hasChildNodes()){
		firstArea.removeChild(firstArea.firstChild);
	}
	firstArea.appendChild(document.createElement("div"));
	if(data.firstNode == "notNode"){
		firstArea.lastChild.appendChild(document.createElement("p"));
		firstArea.lastChild.lastChild.appendChild(document.createTextNode("オンライン中のパフォーマーがいません"));
	}else{
		for(var i=0; i<data.firstNode.length; i++){
			firstArea.lastChild.appendChild(girlsNode(data.firstNode[i]));
		}
	}

	//SecondBlock
	secondArea.appendChild(document.createElement("div"));
	if((data.secondNode !== "notNode")&&(val !== 0)){
		secondArea.lastChild.className = "girlsView";
		$("m2").options[val].selected = true;
		$("secondSelect").style.display = "";
		for(var i=0; i<data.secondNode.length; i++){
			secondArea.lastChild.appendChild(girlsNode(data.secondNode[i]));
		}
	}
	$("girlsBlock").style.visibility = "visible";
}
/*	GirlsNode
------------------------------------*/
function girlsNode(node){
	var ul = document.createElement("ul");
	var tag = document.createElement("li");
	var pic = document.createElement("li");
	var cname = document.createElement("li");

	ul.appendChild(tag);
	ul.appendChild(pic);
	ul.appendChild(cname);
	
	ul.className = node.cs+" gB";
	tag.className = "tag";
	tag.appendChild(document.createElement("img"));
	tag.lastChild.src = "/images/face/g/" + node.st + ".gif";
	
	if(node.cf == 1){
		tag.appendChild(document.createElement("img"));
		tag.lastChild.src = "/images/face/g/new.gif";
	}else if(node.cf == 2){
		tag.appendChild(document.createElement("img"));
		tag.lastChild.src = "/images/face/g/debut.gif";
	}else if(node.cf == 3){
		tag.appendChild(document.createElement("img"));
		tag.lastChild.src = "/images/face/g/fine.gif";
	}else if(node.cf == 4){
		tag.appendChild(document.createElement("img"));
		tag.lastChild.src = "/images/face/g/fine_new.gif";
	}else if(node.cf == 5){
		tag.appendChild(document.createElement("img"));
		tag.lastChild.src = "/images/face/g/fine_debut.gif";
	}else if(node.cf == 8){
		tag.appendChild(document.createElement("img"));
		tag.lastChild.src = "/images/face/g/checkmark.gif";
	}else if(node.cf == 7){
		tag.appendChild(document.createElement("img"));
		tag.lastChild.src = "/images/face/g/fine_check.gif";
	}

	pic.className = "pic";
	if(node.cs != "cm"){
		pic.style.backgroundImage = "url(/imgs/op/120x90/"+node.ph+")";
	}else{
		pic.style.backgroundImage = "url(/imgs/cm/120x90/"+node.ph+")";
	}
	pic.appendChild(document.createElement("a"));
	pic.lastChild.href = "javascript:cO('"+node.hs+"');";
	pic.lastChild.appendChild(document.createElement("img"));
	pic.lastChild.lastChild.src = "/images/b.gif";
	
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
	cname.lastChild.lastChild.lastChild.src = "/images/xxsato/g/thumb01mail.gif";
	
	if(node.vo == 1){
		cname.lastChild.appendChild(document.createElement("img"));
		cname.lastChild.lastChild.src = "/images/xxsato/g/thumbvoice.gif";
	}
	return ul;
}
/*	OptionSelected
-------------------------------------*/
function setTag(val){
	var img = 'images/face/tag/';
	switch(val){
		case "0": $("gTag").src = img + 'tag_online.gif';break;
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