/*
Script: Tips.js
	Tooltips, BubbleTips, whatever they are, they will appear on mouseover

License:
	MIT-style license.

Credits:
	The idea behind Tips.js is based on Bubble Tooltips (<http://web-graphics.com/mtarchive/001717.php>) by Alessandro Fulcitiniti <http://web-graphics.com>
*/

/*
Class: Tips
	Display a tip on any element with a title and/or href.

Note:
	Tips requires an XHTML doctype.

Arguments:
	elements - a collection of elements to apply the tooltips to on mouseover.
	options - an object. See options Below.

Options:
	maxTitleChars - the maximum number of characters to display in the title of the tip. defaults to 30.
	hideDelay - the delay the onHide method is called. (defaults to 100 ms)

	className - the prefix for your tooltip classNames. defaults to 'tool'.

		the whole tooltip will have as classname: tool-tip

		the title will have as classname: tool-title

		the text will have as classname: tool-text

	offsets - the distance of your tooltip from the mouse. an Object with x/y properties.
	fixed - if set to true, the toolTip will not follow the mouse.

Events:
	onShow - optionally you can alter the default onShow behaviour with this option (like displaying a fade in effect);
	onHide - optionally you can alter the default onHide behaviour with this option (like displaying a fade out effect);

Example:
	(start code)
	<img src="/images/i.png" title="The body of the tooltip is stored in the title" class="toolTipImg"/>
	<script>
		var myTips = new Tips($$('.toolTipImg'), {
			maxTitleChars: 50	//I like my captions a little long
		});
	</script>
	(end)

Note:
	The title of the element will always be used as the tooltip body. If you put :: on your title, the text before :: will become the tooltip title.
*/

NTooltip = new Class({

	Implements: [Events, Options],

	options: {
		onShow: function(tip){
			var top = tip.getPosition().y;
			var offset = this.options.fixed ? 20 : 0;
			$clear(this.timer);
			if(tip.getStyle('opacity')==0) {
    			tip.addClass('scale-in').effects({duration: 150, onStart: function(){ 
    				tip.setStyle('display', 'block');
    			}.bind(this)}).start({'opacity': 1});
			}
		},
		onHide: function(tip){
			var top = tip.getPosition().y;
			var offset = this.options.fixed ? 20 : 0;
			tip.addClass('scale-out').effects({duration: 150, onComplete: function(){ 
				tip.removeClass('scale-out').removeClass('scale-in').setStyle('display', 'none'); 
			}}).start({'opacity': 0});
		},
		template: '<div class="cooltip-tip"><div class="top left"></div><div class="top center"></div><div class="top right"></div><span class="close">&times;</span><div class="middle left"></div><div class="middle center"><span>@{text}</span></div><div class="middle right"></div><div class="bottom left"></div><div class="bottom center"></div><div class="bottom right"></div></div></div>',
		hideDelay: 0,
		className: 'cooltip',
		offsets: {
			moving: {
				'x': 0, 
				'y': 5
			},
			fixed: {
				'x': 0,
				'y': 0
			}
		},
		fixed: true,
		showOnce: false,
		closeOnClick: false,
		showOnLoad: false
	},

	initialize: function(elements, options){
		this.setOptions(options);
		fixed = this.options.fixed ? ' fixed' : '';
		this.toolTip = new Element('div', {
			'class': this.options.className + '-tip-outer' + fixed,
			'styles': {
				'position': 'absolute',
				'top': '0',
				'left': '0',
				'opacity': 0,
				'cursor': 'pointer'
			}
		})
		
		.set('html', this.options.template)
		.inject(document.body);
		if (!this.options.fixed && this.options.closeOnClick) {
			this.toolTip.addEvents({'mouseenter': function(){
					$clear(this.timer);
				}.bind(this),
				'mouseleave': function(){
					$clear(this.timer);
					this.timer = this.hide.delay(this.options.hideDelay, this);
				}.bind(this)
			});
		}
		
		//this.wrapper = new Element('div').inject(this.toolTip);
		$$(elements).each(this.build, this);
		if(this.options.showOnLoad) {
			if (this.options.showOnce && Cookie.get(this.options.showOnce)) return;
			var el = $$(elements)[Number(this.options.showOnLoad)-1];
			this.start(el);
			this.position(el);
		}
	},

	build: function(el){
	    //Don't build more than once
	    if(el.myTitle) return;
	
		el.myTitle = (el.href && el.get('tag') == 'a') ? el.href.replace('http://', '') : (el.rel || false);
		if (el.title){
			el.myText = el.title;
			el.removeAttribute('title');
		} else {
			el.myText = false;
		}
		if (el.myTitle && el.myTitle.length > this.options.maxTitleChars) el.myTitle = el.myTitle.substr(0, this.options.maxTitleChars - 1) + "&hellip;";
		el.addEvent('mouseenter', function(event){
			if(this.options.showOnce && Cookie.get(this.options.showOnce)) return;
			this.start(el);
			if (!this.options.fixed) this.locate(event);
			else this.position(el);
		}.bindWithEvent(this));
		if (!this.options.fixed) el.addEvent('mousemove', this.locate.bindWithEvent(this));
		var end = this.end.bind(this);
		if(!this.options.fixed && !this.options.closeOnClick) el.addEvent('mouseleave', end);
		el.addEvent('trash', end);
	},

	start: function(el){
		if (el.myText){
			var html = this.toolTip.innerHTML;
			if(html != el.myText)
			{
				this.toolTip.getElement('.middle span').empty();
				this.text = this.toolTip.getElement('.middle span').set('html', el.myText);
				el.myText = this.toolTip.getElement('.middle span').innerHTML;
				var once = this.once.bind(this);
				this.toolTip.addEvent('click', once);
				if(this.options.closeOnClick) this.toolTip.addClass('close-on-click');
			}
		}
		$clear(this.timer);
		this.show();
		return this;
	},
	
	once: function(el) {
		if(this.options.showOnce) Cookie.set(this.options.showOnce, true);
		
		this.text = this.toolTip.innerHTML;
		var event = new Event(el);
		if (this.options.fixed && !this.options.closeOnClick && !$(event.target).hasClass('close')) return;
		
		this.end(el);
	},

	end: function(event){
		$clear(this.timer);
		this.timer = this.hide.delay(this.options.hideDelay, this);
	},

	position: function(element){
		var pos = element.getPosition();
		//console.warn(element.getCoordinates());
		var size = element.getSize().size;
		var computed = this.toolTip.getComputedSize();
		var tip = {'x': this.toolTip.offsetWidth, 'y': this.toolTip.offsetHeight};
		//console.log(tip, this.toolTip.getSize(), this.toolTip.innerHTML, computed);
		var offsets = this.options.fixed ? {x: this.options.offsets.fixed.x-tip.x, y: this.options.offsets.fixed.y-tip.y} : {x:  this.options.offsets.moving.x, y: this.options.offsets.moving.y}
		var leftoffset = this.options.fixed ? size.x/2 : 0;
		if((pos.x+leftoffset)<tip.x) { 
			this.toolTip.addClass('flip');
		}
		else { 
			leftoffset = offsets.x + leftoffset;
			this.toolTip.removeClass('flip');
		}
		//console.warn(parseInt(pos.y + offsets.y + (this.options.fixed ? size.y/2 : 0)));
		this.toolTip.setStyles({
			'left': pos.x + leftoffset,
			'top': pos.y + offsets.y + (this.options.fixed ? size.y/2 : 0)
		});
	},

	locate: function(event){
		var win = {'x': window.getWidth(), 'y': window.getHeight()};
		var scroll = {'x': window.getScrollLeft(), 'y': window.getScrollTop()};
		var tip = {'x': this.toolTip.offsetWidth, 'y': this.toolTip.offsetHeight};
		var prop = {'x': 'left', 'y': 'top'};
		var offsets = this.options.fixed ? {x: this.options.offsets.fixed.x-tip.x, y: this.options.offsets.fixed.y-tip.y} : {x:  this.options.offsets.moving.x, y: this.options.offsets.moving.y}
		for (var z in prop){
			var pos = event.page[z] + offsets[z];
			//if ((pos + tip[z] - scroll[z]) > win[z]) pos = event.page[z] - offsets[z] - tip[z];
			if(event.page[z]>tip[z]) pos = event.page[z] - offsets[z] - tip[z];
			this.toolTip.setStyle(prop[z], pos);
			
		};
		if(event.page.x<tip.x) this.toolTip.addClass('flip');
		else this.toolTip.removeClass('flip');
		return this;
	},

	show: function(){
		this.fireEvent('onShow', [this.toolTip]);
	},

	hide: function(){
		this.fireEvent('onHide', [this.toolTip]);
	}

});