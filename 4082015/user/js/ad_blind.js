window.onbeforeunload = function(e) {
	window.document.chat.SetVariable("cutConnection", "execute");
}

$(function() {
    $("#normal-flash-screen").bind("contextmenu",function(){
        $("body").unbind("contextmenu");
    }).bind("mouseout",function(){
        $("body").bind("contextmenu",function(){ return false; });
    });
});
window.onload = function(){
    $("body").bind("contextmenu",function(){ return false; });
}
