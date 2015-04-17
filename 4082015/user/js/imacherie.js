var columnParentBox;
var numberOfContainers=3;
var containerIMBoxIdName='main';
var containerChildIdName='IMContainerChild';
var contentsBoxIdName='imContentsBox';
var contentsDragIdName='imacherieTitle';
var contentsDIR='imacherie/';
var loadingStr='<div align="center"><br><br><img src="./imacherie/images/loading.gif" /><br>Now Loading...<br><br><br></div>';
var refreshBtn_src='images/i_member/btn_1.gif';
var hidenImage_src='images/i_member/btn_3.gif';
var blockImage_src='images/i_member/btn_2.gif';
var deletImage_src='images/i_member/btn_4.gif';
var refreshOve_src='images/i_member/btn_1.gif';
var hidenOver_src='images/i_member/btn_3.gif';
var blockOver_src='images/i_member/btn_2.gif';
var deletOver_src='images/i_member/btn_4.gif';
var scrollSpeed=4;
var objBorderWidth=1;
var autoScrollActive=false;
var dragBoxsArray=new Array();
var ajaxObjects=new Array();
var ajaxPHP='ajax_read.php';
ajaxPHP='';
var cntDivHType=1;
var mouse_x;
var mouse_y;
var el_x;
var el_y;
var rectangleDiv;
var goToMove=true;
var moveToCounter=-1;
var dragObject=false;
var dragObjNextS=false;
var dragObjParent=false;
var destinationObj=false;
var documentHeight=false;
var documentScrollHeight=false;
var opera=navigator.userAgent.toLowerCase().indexOf('opera')>=0?true:false;
var firefox=navigator.userAgent.indexOf("Firefox")>=0?true:false;
var CookieSP='#|#';
var cookieArray=new Array();
var ReLoadContentsFLG = false;
function saveCookies(){
contentsId=0;
var tmpUrlArray=new Array();
var saveStr="";
for(var no=1;no<=numberOfContainers;no++){
var parentObj=document.getElementById(containerIMBoxIdName+no);
var items=parentObj.getElementsByTagName('DIV');
if(items.length==0)continue;
var item=items[0];
var tmpItemArray=new Array();
while(item){
var contentsIndex=item.id.replace(/[^0-9]/g,'');
if(item.id!='rectangleDiv'){
tmpItemArray[tmpItemArray.length]=contentsIndex;
}
item=item.nextSibling;
}
var container_id=no;
for(var no2=tmpItemArray.length-1;no2>=0;no2--){
var contentsIndex=tmpItemArray[no2];
var title=dragBoxsArray[contentsIndex]['title'];
var url=dragBoxsArray[contentsIndex]['contUrl'];
var BoxHeight=dragBoxsArray[contentsIndex]['BoxHeight'];
var state=dragBoxsArray[contentsIndex]['boxState'];
var cntDispType=dragBoxsArray[contentsIndex]['dispType'];
var tabId=document.F1.tab_id.value;
var displayStat="1";
saveStr += contentsIndex+"="+container_id + CookieSP + state + CookieSP + contentsId + CookieSP + tabId + "&";
contentsId++;
}
}
saveStr+="mode=updConts";
saveCookiesDB(saveStr,'post');
}
function getTopPos(inputObj){
var returnValue=inputObj.offsetTop;
while((inputObj=inputObj.offsetParent)!=null){
if(inputObj.tagName!='HTML')returnValue+=inputObj.offsetTop;
}
return returnValue;
}
function getLeftPos(inputObj){
var returnValue=inputObj.offsetLeft;
while((inputObj=inputObj.offsetParent)!=null){
if(inputObj.tagName!='HTML')returnValue+=inputObj.offsetLeft;
}
return returnValue;
}
function autoScroll(direction,yPos){
if(document.documentElement.scrollHeight>documentScrollHeight&&direction>0)return;
if(opera)return;
window.scrollBy(0,direction);
if(!dragObject)return;

if(direction<0){
if(document.documentElement.scrollTop>0){
dragObject.style.top=(el_y-mouse_y+yPos+document.documentElement.scrollTop)+'px';
}else{
autoScrollActive=false;
}
}else{
if(yPos>(documentHeight-50)){
dragObject.style.top=(el_y-mouse_y+yPos+document.documentElement.scrollTop)+'px';
}else{
autoScrollActive=false;
}
}
if(autoScrollActive)setTimeout('autoScroll('+direction+','+yPos+')',5);
}
function initDragContainers(e){
moveToCounter=1;
if(document.all)e=event;
if(e.target) source=e.target;
else if(e.srcElement) source=e.srcElement;
if(source.nodeType==3)
source=source.parentNode;
if(source.tagName.toLowerCase()=='img'||source.tagName.toLowerCase()=='a'||source.tagName.toLowerCase()=='input'||source.tagName.toLowerCase()=='td'||source.tagName.toLowerCase()=='tr'||source.tagName.toLowerCase()=='table')return;
mouse_x=e.clientX;
mouse_y=e.clientY;
var numericId=this.id.replace(/[^0-9]/g,'');
el_x=getLeftPos(this.parentNode.parentNode.parentNode)/1;
el_y=getTopPos(this.parentNode.parentNode)/1-document.documentElement.scrollTop;
dragObject=this.parentNode.parentNode;
documentScrollHeight=document.documentElement.scrollHeight+100+dragObject.offsetHeight;
if(dragObject.nextSibling){
dragObjNextS=dragObject.nextSibling;
if(dragObjNextS.tagName!='DIV')dragObjNextS=dragObjNextS.nextSibling;
}
dragObjParent=dragBoxsArray[numericId]['parentObj'];
moveToCounter=0;
initDragContainersTimer();
if(firefox){
for(c_id in dragBoxsArray){
if(dragBoxsArray[c_id]['dispType']==0){
document.getElementById(containerChildIdName+c_id).style.overflow='hidden';
}else{
document.getElementById('contFramef'+c_id).style.display='block';
//document.getElementById('contFrame'+c_id).style.scrolling='hidden';
}
}
}
return false;
}
function initDragContainersTimer(){
if(moveToCounter>=0&&moveToCounter<10){
moveToCounter++;
setTimeout('initDragContainersTimer()',10);
return;
}
}
function moveDragableElement(e){
if(document.all)e=event;
if(moveToCounter<10)return;
if(document.all&&e.button!=1&&!opera){
if(navigator.userAgent.indexOf("Trident/4.0") == -1){
stop_dragDropElement();
return;
}
}
if(document.body!=dragObject.parentNode){
dragObject.style.width=(dragObject.offsetWidth-(objBorderWidth*2))+'px';
dragObject.style.position='absolute';
dragObject.style.filter='alpha(opacity=70)';
dragObject.style.opacity='0.7';
dragObject.parentNode.insertBefore(rectangleDiv,dragObject);
rectangleDiv.style.display='block';
document.body.appendChild(dragObject);
rectangleDiv.style.width=dragObject.style.width;
rectangleDiv.style.height=(dragObject.offsetHeight-(objBorderWidth*2))+'px';
}
if(e.clientY<50||e.clientY>(documentHeight-50)){
if(e.clientY<50&&!autoScrollActive){
autoScrollActive=true;
autoScroll((scrollSpeed*-1),e.clientY);
}
if(e.clientY>(documentHeight-50)&&document.documentElement.scrollHeight<=documentScrollHeight&&!autoScrollActive){
autoScrollActive=true;
autoScroll(scrollSpeed,e.clientY);
}
}else{
autoScrollActive=false;
}
var leftPos=e.clientX;
var topPos=e.clientY+document.documentElement.scrollTop;
dragObject.style.left=(e.clientX-mouse_x+el_x)+'px';
dragObject.style.top=(el_y-mouse_y+e.clientY+document.documentElement.scrollTop)+'px';
if(!goToMove)return;
goToMove=false;
destinationObj=false;
rectangleDiv.style.display='none';
var objFound=false;
var tmpParentArray=new Array();
if(!objFound){
for(var no=1;no<dragBoxsArray.length;no++){
if(!dragBoxsArray[no]){continue;}
if(dragBoxsArray[no]['obj']==dragObject)continue;
tmpParentArray[dragBoxsArray[no]['obj'].parentNode.id]=true;
if(!objFound){
var tmpX=getLeftPos(dragBoxsArray[no]['obj']);
var tmpY=getTopPos(dragBoxsArray[no]['obj']);
if(leftPos>tmpX&&leftPos<(tmpX+dragBoxsArray[no]['obj'].offsetWidth)
&& topPos>(tmpY-20)&&topPos<(tmpY+(dragBoxsArray[no]['obj'].offsetHeight/2))){
destinationObj=dragBoxsArray[no]['obj'];
destinationObj.parentNode.insertBefore(rectangleDiv,dragBoxsArray[no]['obj']);
rectangleDiv.style.display='block';
objFound=true;
break;
}
if(leftPos>tmpX&&leftPos<(tmpX+dragBoxsArray[no]['obj'].offsetWidth)
&& topPos>=(tmpY+(dragBoxsArray[no]['obj'].offsetHeight/2))&&topPos<(tmpY+dragBoxsArray[no]['obj'].offsetHeight)){
objFound=true;
if(dragBoxsArray[no]['obj'].nextSibling){
destinationObj=dragBoxsArray[no]['obj'].nextSibling;
if(!destinationObj.tagName)destinationObj=destinationObj.nextSibling;
if(destinationObj!=rectangleDiv)destinationObj.parentNode.insertBefore(rectangleDiv,destinationObj);
}else{
destinationObj=dragBoxsArray[no]['obj'].parentNode;
dragBoxsArray[no]['obj'].parentNode.appendChild(rectangleDiv);
}
rectangleDiv.style.display='block';
break;
}
if(!dragBoxsArray[no]['obj'].nextSibling&&leftPos>tmpX&&leftPos<(tmpX+dragBoxsArray[no]['obj'].offsetWidth)
&& topPos>topPos>(tmpY+(dragBoxsArray[no]['obj'].offsetHeight))){
destinationObj=dragBoxsArray[no]['obj'].parentNode;
dragBoxsArray[no]['obj'].parentNode.appendChild(rectangleDiv);
rectangleDiv.style.display='block';
objFound=true;
}
}
}
}
if(!objFound){
for(var no=1;no<=numberOfContainers;no++){
if(!objFound){
var obj=document.getElementById(containerIMBoxIdName + no);
var left=getLeftPos(obj)/1;
var width=obj.offsetWidth;
if(leftPos>left&&leftPos<(left+width)){
destinationObj=obj;
obj.appendChild(rectangleDiv);
rectangleDiv.style.display='block';
objFound=true;
}
}
}
}
setTimeout('goToMove=true',5);
}
function stop_dragDropElement(){
if(moveToCounter<10){
moveToCounter = -1
return;
}
moveToCounter = -1;
dragObject.style.filter='alpha(opacity=100)';
dragObject.style.opacity='1.0';
dragObject.style.position='static';
dragObject.style.width='100%';
dragObject.style.width=null;
var numericId=dragObject.id.replace(/[^0-9]/g,'');
if(destinationObj && destinationObj.id!=dragObject.id){
if(destinationObj.id.indexOf(containerIMBoxIdName)>=0){
destinationObj.appendChild(dragObject);
dragBoxsArray[numericId]['parentObj']=destinationObj;
}else{
destinationObj.parentNode.insertBefore(dragObject,destinationObj);
dragBoxsArray[numericId]['parentObj']=destinationObj.parentNode;
}
}else{
if(dragObjNextS){
dragObjParent.insertBefore(dragObject,dragObjNextS);
}else{
dragObjParent.appendChild(dragObject);
}
}
if(firefox){
for(c_id in dragBoxsArray){
if(dragBoxsArray[c_id]['dispType']==0){
document.getElementById(containerChildIdName+c_id).style.overflow='auto';
}else{
document.getElementById('contFramef'+c_id).style.display='none';
//document.getElementById('contFrame'+c_id).style.scrolling='auto';
}
}
}
autoScrollActive=false;
rectangleDiv.style.display='none';
dragObject=false;
dragObjNextS=false;
destinationObj=false;
setTimeout('saveCookies()',100);
documentHeight=document.documentElement.clientHeight;
}
var conTargetId
function createHttpRequestXX(){
if(window.ActiveXObject){
try {
return new ActiveXObject("Msxml2.XMLHTTP");
} catch (e) {
try {
return new ActiveXObject("Microsoft.XMLHTTP");
} catch (e2) {
return null;
}
}
} else if(window.XMLHttpRequest){
return new XMLHttpRequest();
} else {
return null;
}
}
function requestFileCont(data,method,fileName,async,targetId){
var httpoj=createHttpRequestXX();
if(method!='post'){
fileName+='?'+data;
}
if(ajaxPHP!=''){
httpoj.open(method,ajaxPHP+'?url='+escape(fileName),async);
}else{
httpoj.open(method,fileName,async);
}
httpoj.setRequestHeader("content-type","application/x-www-form-urlencoded;charset=UTF-8");
var body=method=='post' ? (data) : null;
httpoj.send(body);
conTargetId=targetId;
httpoj.onreadystatechange=function(){
if(httpoj.readyState==4){
//on_loaded(httpoj);
document.getElementById(containerChildIdName+conTargetId).innerHTML = httpoj.responseText;
}
}
}
function saveCookiesDB(data,method){
var httpoj=createHttpRequestXX();
fileName='./imacherie_save.php';
httpoj.open(method,fileName,true);
httpoj.setRequestHeader("content-type","application/x-www-form-urlencoded;charset=UTF-8");
var body=method=='post' ? (data) : null;
httpoj.send(body);
httpoj.onreadystatechange=function(){
if(httpoj.readyState==4){
}
}
}
function createAjaxObjeX(fileId,url,parm){
this.requestContentsId = containerChildIdName + fileId;
this.requestFile = contentsDIR + url;
this.method = "POST";
this.URLString = parm;
this.encodeURIString = true;
this.execute = false;
this.createAJAX=function(){
try{
this.xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
}catch (e){
try{
this.xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
}catch (err){
this.xmlhttp = null;
}
}
if(!this.xmlhttp&&typeof XMLHttpRequest!="undefined")
this.xmlhttp = new XMLHttpRequest();
if(!this.xmlhttp){
this.failed = true;
}
};
this.setVar=function(name,value){
if(this.URLString.length < 3){
this.URLString=name+"="+value;
}else{
this.URLString+="&"+name+"="+value;
}
}
this.encVar=function(name, value){
var varString=encodeURIComponent(name)+"="+encodeURIComponent(value);
return varString;
}
this.encodeURLString=function(string){
varArray=string.split('&');
for (i = 0;i<varArray.length;i++){
urlVars=varArray[i].split('=');
if (urlVars[0].indexOf('amp;')!=-1){
urlVars[0]=urlVars[0].substring(4);
}
varArray[i]=this.encVar(urlVars[0],urlVars[1]);
}
return varArray.join('&');
}
this.runResponse=function(){
eval(this.response);
}
this.runAJAX=function(urlstring){
if (this.xmlhttp) {
var self=this;
if(ajaxPHP!=''){
this.xmlhttp.open("GET",ajaxPHP+'?url='+escape(this.requestFile),true);
}else{
this.xmlhttp.open("GET",this.requestFile,true);
}
if(this.URLString!=""){
this.xmlhttp.send(this.URLString);
}else{
this.xmlhttp.send(null);
}
}
this.xmlhttp.onreadystatechange=function() {
if((self.xmlhttp.readyState==4)&&(self.xmlhttp.status==200)){
resdate=self.xmlhttp.responseText;
document.getElementById(self.requestContentsId).innerHTML=resdate;
}
};
};
this.createAJAX();
}
function showStatusMessage(boxIndex,msgStr){
document.getElementById(containerChildIdName+boxIndex).innerHTML=msgStr;
}
function createContentsFromCookie(){
var tmpArray=new Array();
for(var iiii=0;iiii<cookieArray.length;iiii++){
if(cookieArray[iiii]){
var items=cookieArray[iiii].split(CookieSP);
var index=items[2];
tmpArray[index]=true;
createContentsBox(items[0],items[1],items[2],items[3],items[4],items[5],items[6]);
}
}
}
function createContentsBox(contentsId,container_id, title, contUrl, BoxHeight, state, cntDispType){
if(!BoxHeight)BoxHeight='0';
if(numberOfContainers<container_id)container_id=1;
var tmpIndex=createCBox(contentsId,container_id,title,BoxHeight,contUrl,cntDispType);
dragBoxsArray[tmpIndex]['title']=title;
dragBoxsArray[tmpIndex]['contUrl']=contUrl;
dragBoxsArray[tmpIndex]['BoxHeight']=BoxHeight;
dragBoxsArray[tmpIndex]['state']=state;
dragBoxsArray[tmpIndex]['dispType']=cntDispType;
if(state==0){
showHideContents(false,document.getElementById('contentsDisp'+tmpIndex),true);
}
var tmpInterval=false;
dragBoxsArray[tmpIndex]['intervalObj']=tmpInterval;
if(cntDispType==0){
if(!document.getElementById(containerChildIdName+tmpIndex).innerHTML){
ajaxObjects[tmpIndex]=new createAjaxObjeX(tmpIndex,contUrl+'?c_id='+tmpIndex,'');
showStatusMessage(tmpIndex,loadingStr);
ajaxObjects[tmpIndex].runAJAX();
}
}
}
function createCBox(contentsIndex,container_id,title,BoxHeight,contUrl,cntDispType){
var maindiv=document.createElement('DIV');
maindiv.className=contentsBoxIdName;
maindiv.id=contentsBoxIdName+contentsIndex;
var indivdiv=document.createElement('DIV');
indivdiv.className='imMainBox';
maindiv.appendChild(indivdiv);
addDragBar(contentsIndex,indivdiv,title);
addContentContainer(contentsIndex,indivdiv,BoxHeight,contUrl,cntDispType);
var obj=document.getElementById(containerIMBoxIdName+container_id);
var subs=obj.getElementsByTagName('DIV');
if(subs.length>0){
obj.insertBefore(maindiv,subs[0]);
}else{
obj.appendChild(maindiv);
}
dragBoxsArray[contentsIndex]=new Array();
dragBoxsArray[contentsIndex]['obj']=maindiv;
dragBoxsArray[contentsIndex]['parentObj']=maindiv.parentNode;
dragBoxsArray[contentsIndex]['BoxHeight']=BoxHeight;
dragBoxsArray[contentsIndex]['boxState']=1;
return contentsIndex;
}
function addDragBar(contentsIndex,parentObj,title){
var div=document.createElement('DIV');
div.className=contentsDragIdName;
div.id=contentsDragIdName+contentsIndex;
div.onmousedown=initDragContainers;
var textSpan=document.createElement('P');
span_str=document.createTextNode(title);
textSpan.appendChild(span_str);
div.appendChild(textSpan);
parentObj.appendChild(div);	
var contMenu=document.createElement('UL');
div.appendChild(contMenu);
var contLi=document.createElement('LI');
contMenu.appendChild(contLi);
var image=document.createElement('IMG');
image.id='contentsRefresh'+contentsIndex;
image.src=refreshBtn_src;
image.onclick=refreshContents;
contLi.appendChild(image);
var contLi=document.createElement('LI');
contMenu.appendChild(contLi);
var image=document.createElement('IMG');
image.id='contentsDisp'+contentsIndex;
image.src=hidenImage_src;
image.onmousedown=showHideContents;
contLi.appendChild(image);
var contLi=document.createElement('LI');
contMenu.appendChild(contLi);
var image=document.createElement('IMG');
image.id='contentsCloseLink'+contentsIndex;
image.src=deletImage_src;
image.onmousedown=deletContents;
contLi.appendChild(image);
}
function addContentContainer(contentsIndex,parentObj,BoxHeight,url,cntDispType){
if(cntDispType==0){
var div=document.createElement('DIV');
div.className='contentbody';
if(opera)div.style.clear='none';
div.id=containerChildIdName+contentsIndex;
}else{
var contFrame=document.createElement('IFRAME');
contFrame.src=contentsDIR + url;
contFrame.name='contFrame'+contentsIndex;
contFrame.className='icIfram';
contFrame.frameborder='0';
contFrame.scrolling='auto';
if(firefox){
var contFramefilter=document.createElement('IMG');
contFramefilter.src='images/ifilter.gif';
contFramefilter.id='contFramef'+contentsIndex;
contFramefilter.style.top='-'+BoxHeight+'px';
contFramefilter.className='icIframf';
contFramefilter.style.display='none';
}
var div=document.createElement('DIV');
div.className='icBoxclass';
if(opera)div.style.clear='none';
div.id=containerChildIdName+contentsIndex;
div.appendChild(contFrame);
if(firefox){div.appendChild(contFramefilter);}
}
parentObj.appendChild(div);
if(BoxHeight&&BoxHeight/1>40){
if(cntDispType==0&&cntDivHType==1){
//div.style.height=BoxHeight+'px';
//div.setAttribute('BoxHeight',BoxHeight);
//div.BoxHeight=BoxHeight;
}else{
div.style.height=BoxHeight+'px';
div.setAttribute('BoxHeight',BoxHeight);
div.BoxHeight=BoxHeight;
}
if(cntDispType==0){div.style.overflow='auto';}
}
}
function deletContents(e,inputObj){
if(!inputObj)inputObj=this;
var numericId=inputObj.id.replace(/[^0-9]/g,'');
document.getElementById(contentsBoxIdName+numericId).style.display='none';
var tabId=document.F1.tab_id.value;
var delStr="mode=del&contents_id="+numericId+"&tab_id="+tabId;
saveCookiesDB(delStr,'post');
setTimeout('moveToCounter=-5',5);
}
function mouseover_deletContents(){
this.src=deletOver_src;
}
function mouseout_deletContents(){
this.src=deletImage_src;
}
function showHideContents(e,inputObj,flg){
if(document.all)e=event;
if(!inputObj)inputObj=this;
var numericId=inputObj.id.replace(/[^0-9]/g,'');
var obj=document.getElementById(containerChildIdName + numericId);
if(flg){
obj.style.display=inputObj.src.indexOf(hidenImage_src)>=0?'none':'block';
//obj.style.imContentsBox
document.getElementById(contentsBoxIdName+numericId).style.height=inputObj.src.indexOf(hidenImage_src)>=0?24+'px':290+'px';
inputObj.src=inputObj.src.indexOf(hidenImage_src)>=0?blockImage_src:hidenImage_src
}else{
obj.style.display=inputObj.src.indexOf(hidenOver_src)>=0?'none':'block';
document.getElementById(contentsBoxIdName+numericId).style.height=inputObj.src.indexOf(hidenImage_src)>=0?24+'px':290+'px';
inputObj.src=inputObj.src.indexOf(hidenOver_src)>=0?blockOver_src:hidenOver_src
}
dragBoxsArray[numericId]['boxState']=obj.style.display=='block'?1:0;
saveCookies();
}
function mouseover_showHideContents(e,inputObj){
if(document.all)e=event;
if(!inputObj)inputObj=this;
var numericId=inputObj.id.replace(/[^0-9]/g,'');
var obj=document.getElementById(containerChildIdName+numericId);
inputObj.src=dragBoxsArray[numericId]['boxState']==0?blockOver_src:hidenOver_src
}
function mouseout_showHideContents(e,inputObj){
if(document.all)e=event;
if(!inputObj)inputObj=this;
var numericId=inputObj.id.replace(/[^0-9]/g,'');
var obj=document.getElementById(containerChildIdName + numericId);
inputObj.src=dragBoxsArray[numericId]['boxState']==0?blockImage_src:hidenImage_src
}
function refreshContents(e,inputObj){
if(ReLoadContentsFLG == false){
ReLoadContentsFLG = true;
if(!inputObj)inputObj=this;
var numericId=inputObj.id.replace(/[^0-9]/g,'');
var url=dragBoxsArray[numericId]['contUrl'];
var cntDispType=dragBoxsArray[numericId]['dispType'];
if(cntDispType==0){
ajaxObjects[numericId]=new createAjaxObjeX(numericId,url+'?c_id='+numericId,'');
////showStatusMessage(numericId,loadingStr);
ajaxObjects[numericId].runAJAX();
}else{
i_str='<iframe src="'+contentsDIR+url+'" name="contFrame'+numericId+'" class="icIfram" frameborder="0" scrolling="auto"></iframe>';
document.getElementById(containerChildIdName + numericId).innerHTML=i_str;
}
setTimeout(function(){ReLoadContentsFLG = false},800);
}
}
function mouseover_refreshContents(){
this.src=refreshOve_src;
}
function mouseout_refreshContents(){
this.src=refreshBtn_src;
}
function cancelSelectionEvent(e){
if(document.all)e=event;
if(e.target) source=e.target;
else if(e.srcElement) source=e.srcElement;
if(source.nodeType==3)
source=source.parentNode;
if(source.tagName.toLowerCase()=='input')return true;
if(moveToCounter>=0)return false; else return true;
}
function initDragableBoxesScript(){
createContainer();
createHelpObjects();
initEvents();
createContentsFromCookie();
}
window.onload = initDragableBoxesScript;
function createContainer(){
columnParentBox=document.getElementById(containerIMBoxIdName);
if(numberOfContainers==1){
var containerWidth=100;
}else{
var containerWidth=Math.floor(100/numberOfContainers);
}
var class_name=0;
var sumWidth=0;
for(var no=0;no<numberOfContainers;no++){
var div=document.createElement('DIV');
if(numberOfContainers>1){
if(no==(numberOfContainers-1))containerWidth=99-sumWidth;
sumWidth=sumWidth+containerWidth;
}
class_name=no%2;
////////div.style.width=containerWidth+'%';
div.className=containerIMBoxIdName+'_'+class_name;
div.id=containerIMBoxIdName+(no+1);
columnParentBox.appendChild(div);

var clearObj=document.createElement('HR');
clearObj.style.clear='both';
clearObj.style.visibility='hidden';
div.appendChild(clearObj);
}
var clearingDiv=document.createElement('DIV');
columnParentBox.appendChild(clearingDiv);
clearingDiv.style.clear='both';
}
function createHelpObjects(){
rectangleDiv=document.createElement('DIV');
rectangleDiv.id='rectangleDiv';
rectangleDiv.style.display='none';
document.body.appendChild(rectangleDiv);
}
function initEvents(){
document.body.onmousemove=moveDragableElement;
document.body.onmouseup=stop_dragDropElement;
document.body.onselectstart=cancelSelectionEvent;
documentHeight=document.documentElement.clientHeight;
}

