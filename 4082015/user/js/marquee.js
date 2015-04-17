// 
// @name marquee.js
// @author miya2000
// @namespace http://d.hatena.ne.jp/miya2000/
// @version 1.0.0
// 

var Marquee = function ( id, opt ){
    if( ! document.getElementById(id) ) throw 'invalid id. [' + id + ']';
    var option = opt || {};
    this.id     = id;
    this.amount = option.amount || 6;
    this.delay  = option.delay  || 100;
    this.position = Number.POSITIVE_INFINITY; // means out of range.
    this._wrap();
    this.start();
}
Marquee.prototype = {
    /* wrap child nodes */
    _wrap : function() {
        var t = document.getElementById( this.id );
        with ( t.style ){
            position = 'relative'; // for ie6.
            overflow = 'hidden';
        }
        var w = document.createElement( 'div' );
        with ( w.style ){
            margin     = '0';
            padding    = '0';
            background = 'transparent';
            border     = 'none';
        }
        t.normalize();
        while( t.firstChild ){
            w.appendChild( t.removeChild( t.firstChild ) );
        }
        t.appendChild(w);
        /* get minimum width. */
        w.style.position = 'absolute';
        this.minWidth = w.offsetWidth;
        /* put back */
        w.style.position = 'relative';
        w.style.width = '100%'; // for ie6.
    },
    start : function() {
        this.stop();
        this._next();
    },
    _next : function() {
        var t = document.getElementById( this.id );
        this.curWidth = Math.min(t.offsetWidth,t.parentNode.offsetWidth); // dirty. (for "overflow:hidden" parent)
        this.position = this.position - this.amount;
        if ( this._isOutOfRange() ) {
            this.position = this._startPosition();
        }
        var w = t.firstChild;
        w.style.left = this.position + 'px';
        var self = this;
        this.tid = window.setTimeout(
            function(){ self._next(); },
            this.delay
        );
    },
    _startPosition : function() {
        return ( this.amount > 0 ) ?  this.curWidth
                                   : -this.minWidth;
    },
    _isOutOfRange : function() {
        return this.position < -this.minWidth || this.curWidth < this.position;
    },
    stop : function() {
        if ( this.tid ) window.clearTimeout( this.tid );
        this.tid = null;
    },
    isMarqueeing : function() {
        return ( this.tid ) ? true : false;
    }
}