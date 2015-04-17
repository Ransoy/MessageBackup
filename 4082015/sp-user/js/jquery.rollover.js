/****************************************
[jquery] jquery.rollover.js
*****************************************
* Copyright (C) 2010 Naoya HIGASHI
* Dual licensed under the MIT and GPL licenses:
* http://www.opensource.org/licenses/mit-license.php
* http://www.gnu.org/licenses/gpl.html
****************************************/

$(function(){

$('[src*="_off."]')
.mouseover(function(){$(this).attr("src",$(this).attr("src").replace(/^(.+)_off(\.[a-z]+)$/,"$1_on$2"));})
.mouseout(function(){$(this).attr("src",$(this).attr("src").replace(/^(.+)_on(\.[a-z]+)$/,"$1_off$2"));})
.each(function(){$("<img>").attr("src",$(this).attr("src").replace(/^(.+)_off(\.[a-z]+)$/,"$1_on$2"));})


});