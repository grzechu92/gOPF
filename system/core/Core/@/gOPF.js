/**
 * JavaScript gOPF Plugin for jQuery
 * @/System/Core/gOPF.js
 * 
 * Requires:
 * @/System/Terminal/jQuery.js (1.11.1)
 * 
 * @version 2.0
 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
 */
function gOPF() {
	this.Base64 = {_keyStr:"ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",encode:function(a){var b="";var c,d,e,f,g,h,i;var j=0;a=this._utf8_encode(a);while(j<a.length){c=a.charCodeAt(j++);d=a.charCodeAt(j++);e=a.charCodeAt(j++);f=c>>2;g=(c&3)<<4|d>>4;h=(d&15)<<2|e>>6;i=e&63;if(isNaN(d)){h=i=64}else if(isNaN(e)){i=64}b=b+this._keyStr.charAt(f)+this._keyStr.charAt(g)+this._keyStr.charAt(h)+this._keyStr.charAt(i)}return b},decode:function(a){var b="";var c,d,e;var f,g,h,i;var j=0;a=a.replace(/[^A-Za-z0-9\+\/\=]/g,"");while(j<a.length){f=this._keyStr.indexOf(a.charAt(j++));g=this._keyStr.indexOf(a.charAt(j++));h=this._keyStr.indexOf(a.charAt(j++));i=this._keyStr.indexOf(a.charAt(j++));c=f<<2|g>>4;d=(g&15)<<4|h>>2;e=(h&3)<<6|i;b=b+String.fromCharCode(c);if(h!=64){b=b+String.fromCharCode(d)}if(i!=64){b=b+String.fromCharCode(e)}}b=this._utf8_decode(b);return b},_utf8_encode:function(a){a=a.replace(/\r\n/g,"\n");var b="";for(var c=0;c<a.length;c++){var d=a.charCodeAt(c);if(d<128){b+=String.fromCharCode(d)}else if(d>127&&d<2048){b+=String.fromCharCode(d>>6|192);b+=String.fromCharCode(d&63|128)}else{b+=String.fromCharCode(d>>12|224);b+=String.fromCharCode(d>>6&63|128);b+=String.fromCharCode(d&63|128)}}return b},_utf8_decode:function(a){var b="";var c=0;var d=c1=c2=0;while(c<a.length){d=a.charCodeAt(c);if(d<128){b+=String.fromCharCode(d);c++}else if(d>191&&d<224){c2=a.charCodeAt(c+1);b+=String.fromCharCode((d&31)<<6|c2&63);c+=2}else{c2=a.charCodeAt(c+1);c3=a.charCodeAt(c+2);b+=String.fromCharCode((d&15)<<12|(c2&63)<<6|c3&63);c+=3}}return b}};

	this.prefixer = function(element, property, value) {
		["", "-webkit-", "-moz-", "-o-", "-ms-"].map(function(prefix) {
			this.element.css(prefix + this.property, this.value);
		}, {element: element, property: property, value: value});
	};

	this.microtime = function() {
		return (new Date().getMilliseconds() / 1000) + new Date().getTime();
	};

	this.console = function(status) {
		if (!status) {
			return;
		}

		$(document).keypress(function(e) {
			if (e.charCode != 126) {
				return;
			}

			if ($("#gOPFterminal").length > 0) {
				$("#gOPFterminal").remove();
				$("body").css({marginTop: 0});
			} else {
				$("body").css({marginTop: 100});
				$("body").prepend('<div id="gOPFterminal"></div>');

				var terminal = $("#gOPFterminal");

				terminal.css({
					width: "100%",
					height: 100,
					position: "fixed",
					top: 0,
					left: 0
				});

				terminal.html('<iframe width="100%" height="100%" src="/terminal"></iframe>');

				terminal.find("iframe").css({
					margin: 0,
					border: "none"
				});
			}
		});
	}
}

var gOPF = new gOPF();