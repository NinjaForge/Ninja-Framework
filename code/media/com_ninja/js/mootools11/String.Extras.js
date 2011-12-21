/*
---

script: String.Extras.js

description: Extends the String native object with a couple of useful utilities; Like a slugify method, useful for url slugs.

license: MIT-style license

authors: Stian Didriksen

requires:
- core:1.11/String
- core:1.11/Array

provides: [String.Extras]

...
*/

(function(){

var special = ['À','à','Á','á','Â','â','Ã','ã','Ä','ä','Å','å','?','?','?','?','?','?','?','?','Ç','ç', '?','?','?','?', 'È','è','É','é','Ê','ê','Ë','ë','?','?','?','?', '?','?','Ì','ì','Í','í','Î','î','Ï','ï', '?','?','?','?','?','?', 'Ñ','ñ','?','?','?','?','Ò','ò','Ó','ó','Ô','ô','Õ','õ','Ö','ö','Ø','ø','?','?','?','?','?','Š','š','?','?','?','?', '?','?','?','?','?','?','Ù','ù','Ú','ú','Û','û','Ü','ü','?','?', 'Ÿ','ÿ','ý','Ý','Ž','ž','?','?','?','?', 'Þ','þ','Ð','ð','ß','Œ','œ','Æ','æ','µ'];

var standard = ['A','a','A','a','A','a','A','a','Ae','ae','A','a','A','a','A','a','C','c','C','c','C','c','D','d','D','d', 'E','e','E','e','E','e','E','e','E','e','E','e','G','g','I','i','I','i','I','i','I','i','L','l','L','l','L','l', 'N','n','N','n','N','n', 'O','o','O','o','O','o','O','o','Oe','oe','O','o','o', 'R','r','R','r', 'S','s','S','s','S','s','T','t','T','t','T','t', 'U','u','U','u','U','u','Ue','ue','U','u','Y','y','Y','y','Z','z','Z','z','Z','z','TH','th','DH','dh','ss','OE','oe','AE','ae','u'];

var tidymap = {
	"[\xa0\u2002\u2003\u2009]": " ",
	"\xb7": "*",
	"[\u2018\u2019]": "'",
	"[\u201c\u201d]": '"',
	"\u2026": "...",
	"\u2013": "-",
	"\u2014": "--",
	"\uFFFD": "&raquo;"
};

String.extend({

	standardize: function(){
		var text = this;
		special.each(function(ch, i){
			text = text.replace(new RegExp(ch, 'g'), standard[i]);
		});
		return text;
	},

	tidy: function(){
		var txt = this.toString();
		$each(tidymap, function(value, key){
			txt = txt.replace(new RegExp(key, 'g'), value);
		});
		return txt;
	},

	slugify: function(){

		var txt = this.toString().tidy().standardize().replace(/\s+/g,'-').toLowerCase().replace(/[^a-z0-9\-]/g,'');
		
		return txt;
	}

});

})();