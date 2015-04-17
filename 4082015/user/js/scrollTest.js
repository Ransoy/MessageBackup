Array.prototype.clone = function(){
    return Array.apply(null,this)
}

var SetTimer = function (id,func,time){	this.initialize.apply(this, arguments);  }

SetTimer.prototype = 
{
	initialize: function(id,func,time)
	{
			this.id =id;
			this.func = func;
			this.time = time;
	},
	set: function ()
	{
		var self = this;
		this.id = setInterval(self.func, self.time);
	},
	clear: function ()
	{
		clearInterval(this.id);
	}
}

var gadjetSetting = {
	pattern:null
}


var horizontalSlider = function (target) {
	this.initialize.apply(this,arguments);
}

horizontalSlider.prototype = {
	outer: null,
	contents: null,
	contentsArr:null,
	timerID:null,
	scope:null,
	contentWidth:null,
	initialize: function (target) {
		this.target = target;
		this.outer = $("ul.textBannerStyle",this.target);
		this.setOuter();
		this.setEvent();
	},
	setEvent: function () {
		hscope = this;
		$("p.btnRight",this.target).bind("click",hscope, function(e) {e.data.scrollLeft()});
		$("p.btnLeft",this.target).bind("click", hscope, function(e) {e.data.scrollRight()});

	},
	setOuter: function () {
		hscope = this;
		this.contents = $("ul.textBannerStyle li",this.target);
		this.contentsArr = $(this.contents).get();
		$(this.outer).css("width", 210 * this.contents.length);
	},
	scrollLeft: function () {

		$("p.btnRight",this.target).unbind();
		hscope = this;
		if ( $(this.outer).position().left != 0 ) {
			$(hscope.outer).css({"left":"0"});
		}

		
		var len = this.contents.length;
		var mod = len % 3;
		if (mod == 0) {
			$(this.outer).animate({"left": "-630px"}, {
				duration:500,
				easing: 'swing',
				complete: function(){
					hscope.shiftArr();
					$("p.btnRight",this.target).bind("click",hscope, function(e) {e.data.scrollLeft()});
				}
			});
		

			
		} else {
			var temp = 3 - mod;
			$(hscope.outer).css({"left":"0"});
			for (i = 0; i < temp; i++) {
				$(this.outer).append("<li><dl><dt>&nbsp;</dt><dd>&nbsp;</dd></dl></li>");
			}
			this.setOuter();
			$(this.outer).animate({"left": "-630px"}, {
				duration:500,
				easing: 'swing',
				complete: function(){
					hscope.shiftArr();
					$("p.btnRight",this.target).bind("click",hscope, function(e) {e.data.scrollLeft()});
				}
			});
		}

	},
	scrollRight: function () {
		hscope = this;
		$("p.btnLeft",this.target).unbind();
		$(this.outer).css("left","-630px");
		var len = this.contents.length;
		var mod = len % 3;
		if (mod == 0) {
			hscope.popArr();
			$(this.outer).animate({"left": "0px"}, {
				duration:500,
				easing: 'swing',
				complete: function(){
					$("p.btnLeft",this.target).bind("click",hscope, function(e) {e.data.scrollRight()});
				}
			});
		} else {
			var temp = 3 - mod;
			for (i = 0; i < temp; i++) {
				$(this.outer).append("<li><dl><dt>&nbsp;</dt><dd>&nbsp;</dd></dl></li>");
			}
			this.setOuter();
			hscope.popArr();
			$(this.outer).animate({"left": "0px"}, {
				duration:500,
				easing: 'swing',
				complete: function(){
					$("p.btnLeft",this.target).bind("click",hscope, function(e) {e.data.scrollRight()});
				}

			});
		}
	},
	popArr: function() {
		var c = $("ul.textBannerStyle li",this.target);
		var n = $(c).splice(-3,3);
		$(this.outer).prepend(n);
	}
}

//コンテンツ説明
var verticalSlider = function (target) {
	this.initialize.apply(this,arguments);
}

verticalSlider.prototype = {
	outer: null,
	contents: null,
	contentsArr:null,
	timerID:null,
	initialize: function (target) {
		this.target = target;
		this.outer = $("ul.textBannerStyle",this.target);
		this.contentHeight = [];
		scope = this;
		$("ul.textBannerStyle li",this.target).each(function(){scope.contentHeight.push($(this).outerHeight({margin:true}))})
		maxVal = this.contentHeight.clone().sort().pop();
		this.setOuter(maxVal);
		this.setEvent();
	},
	setEvent: function () {
		scope = this;
		//$(this.outer).css("top","-64px");
		$("p.btnTop",this.target).bind("click",scope, function(e) {e.data.scrollBottom()});
		$("p.btnBottom",this.target).bind("click", scope, function(e) {e.data.scrollTop()});
	},
	setOuter: function (val) {
		this.contents = $("ul.textBannerStyle li",this.target);
		this.contentsArr = $(this.contents).get();
		$(this.outer).css("height", val * this.contents.length);
		$("div.inner",this.target).css("height",(val * 4) +  24 + "px");
	},
	scrollTop: function () {
		scope = this;
		$("p.btnBottom",this.target).unbind();
/* 		$(this.outer).css("left","0px"); */
		$(this.outer).animate({"top": "-=128px"}, "fast", "linear", function(){
			scope.shiftArr();
			$("p.btnBottom",scope.target).bind("click", scope, function(e) {e.data.scrollTop()});
		});
	},
	scrollBottom: function () {
		this.popArr();
		scope = this;
		$("p.btnTop",this.target).unbind();
		$(this.outer).animate({"top": "+=128px"}, "fast", "linear", function(){
			$("p.btnTop",scope.target).bind("click",scope, function(e) {e.data.scrollBottom()});
		});
	},
	shiftArr: function() {
		var c = $("ul.textBannerStyle li",this.target).get();
		var n = $(c).slice(0,2);
		for (i=0; i<n.length; i++){
			c.push(n[i]);
		} 
		$(this.outer).html(c);
		$(this.outer).css("top","0px");
	},
	popArr: function() {
		temp = $("ul.textBannerStyle li",this.target).get();
		var n = temp.slice(-2);
		$(this.outer).prepend(n);
		$(this.outer).css("top","-128px");
		/* $(this.outer).html(c); */
	}
}

var closeBoxes = function (target) {
	$(target).each(function(){  $(this).fadeOut("fast"); });	
};

$(function(){
	if ( $("#campaignBox2").length != 0 ) {
		var vslider = new verticalSlider ("#campaignBox2");
		$("#campaignBox2").hover(function(){vertical.clear()},function(){vertical.set();});
	}
});


//イベント

var verticalSlider2 = function (target) {
	this.initialize.apply(this,arguments);
}

verticalSlider2.prototype = {
	outer: null,
	contents: null,
	contentsArr:null,
	timerID:null,
	initialize: function (target) {
		this.target = target;
		this.outer = $("ul.textBannerStyle",this.target);
		this.contentHeight = [];
		scope = this;
		$("ul.textBannerStyle li",this.target).each(function(){scope.contentHeight.push($(this).outerHeight({margin:true}))})
		maxVal = this.contentHeight.clone().sort().pop();
		this.setOuter(maxVal);
		this.setEvent();
	},
	setEvent: function () {
		scope = this;
		//$(this.outer).css("top","-64px");
		$("p.btnTop",this.target).bind("click",scope, function(e) {e.data.scrollBottom()});
		$("p.btnBottom",this.target).bind("click", scope, function(e) {e.data.scrollTop()});
	},
	setOuter: function (val) {
		this.contents = $("ul.textBannerStyle li",this.target);
		this.contentsArr = $(this.contents).get();
		$(this.outer).css("height", val * this.contents.length);
		$("div.inner",this.target).css("height",(val * 4) +  24 + "px");
	},
	scrollTop: function () {
		scope = this;
		$("p.btnBottom",this.target).unbind();
/* 		$(this.outer).css("left","0px"); */
		$(this.outer).animate({"top": "-=128px"}, "fast", "linear", function(){
			scope.shiftArr();
			$("p.btnBottom",scope.target).bind("click", scope, function(e) {e.data.scrollTop()});
		});
	},
	scrollBottom: function () {
		this.popArr();
		scope = this;
		$("p.btnTop",this.target).unbind();
		$(this.outer).animate({"top": "+=128px"}, "fast", "linear", function(){
			$("p.btnTop",scope.target).bind("click",scope, function(e) {e.data.scrollBottom()});
		});
	},
	shiftArr: function() {
		var c = $("ul.textBannerStyle li",this.target).get();
		var n = $(c).slice(0,1);
		for (i=0; i<n.length; i++){
			c.push(n[i]);
		} 
		$(this.outer).html(c);
		$(this.outer).css("top","0px");
	},
	popArr: function() {
		temp = $("ul.textBannerStyle li",this.target).get();
		var n = temp.slice(-1);
		$(this.outer).prepend(n);
		$(this.outer).css("top","-128px");
		/* $(this.outer).html(c); */
	}
}


var closeBoxes = function (target) {
	$(target).each(function(){  $(this).fadeOut("fast"); });	
};

$(function(){
	
	if ( $("#campaignBox").length != 0 ) {
		var vslider = new verticalSlider2 ("#campaignBox");
		vertical = new SetTimer("vtimer",function(){vslider.scrollTop()},5100);
		//vertical.set();
		//$("#campaignBox").hover(function(){vertical.clear()},function(){vertical.set();});
		$("#campaignBox").hover(function(){vertical.clear()},function(){});
	}
	
});
