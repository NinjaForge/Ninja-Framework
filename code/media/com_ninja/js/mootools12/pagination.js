/*
Script: NSortables.js
	Contains <NSortables> Class.

License:
	MIT-style license.
*/

/*
Class: NSortables
	Creates an interface for <Drag.Base> and drop, resorting of a list.

Note:
	The Sortables require an XHTML doctype.

Arguments:
	list - required, the list that will become sortable.
	options - an Object, see options below.

Options:
	handles - a collection of elements to be used for drag handles. defaults to the elements.

Events:
	onStart - function executed when the item starts dragging
	onComplete - function executed when the item ends dragging
*/

NPaginator = new Class({

	Implements: [Events, Options],

	options: {
		state: {
			total: 0
		},
		template: "<a href=\"#offset=@{offset}\">@{page}<\/a>"
	},

	initialize: function(paginator, options){
		this.setOptions(options);
		//if(cookie = Cookie.get(this.options.identificator)) this.setOptions({state: Json.evaluate(cookie)});
		if(this.options.state.total <= 10) {
			this.options.state.total = 0;
			return this.setData();
		}
		var total = this.options.state.total;
		this.cache = new Array;
		this.value = window.location.href;
		this.paginator = paginator.addClass('ajax');
		this.xhr = new XHR({method: 'get', onSuccess: function(data){
			document.getElement('table.adminlist tbody').empty().setHTML(data);
			this.cache[Json.toString(this.options.state)] = data;
		}.bind(this)});
		if(select = paginator.getElement('.limit select')) select.addEvent('change', this.limit.bindWithEvent(this));
		//paginator.addEvent('click', this.page.bindWithEvent(this));
		this.options.state.total = total;
		this.setData();
		this.setList();
		this.update();
		this.checkURL.periodical(500,this);
	},
	
	checkURL: function(){
		if(window.location.href != this.value){
			this.cache[Json.toString(this.options.state)] = document.getElement('table.adminlist tbody').innerHTML;
			this.value = window.location.href;	
			this.inject();
		}
	},

	page: function(event){
		this.cache[Json.toString(this.options.state)] = document.getElement('table.adminlist tbody').innerHTML;
		//this.value = event.target.href;
		
		this.inject();
		this.update();
		event.stop();
	},
	
	formatHash: function(str){
		str = str.toString();
		var index = str.indexOf('#');
		if(index > -1){
			str = str.substr(index+1);
		}
		return str;		
	},
	
	limit: function(event){
		this.options.state.limit = event.target.getValue();
	},
	
	inject: function(){
		//Change this.value from using the uri string for the key to using the states object
		var value = this.value.split('#');
		var query = value[0].toURI();
		console.log(query, value[1].toURI())
		var href  = value[0].replace(window.location.search, '');
		var uris  = query.getData();
		query = [];
		if(value.length > 1)
		{
			value[1].split('&').each(function(val, i){
				var state = val.split('=');
				this.options.state[state[0]] = state[1];
			}.bind(this));
		}
		var states = [];
		var i = 0;
		$each($extend(uris, this.options.state), function(val, key){
			//Bug in KQuery??
			if(key != 'toString') states[i++] = key + '=' + val;
		});
		this.setData();
		this.setList();
		states.length > 0 ? (query = '&' + states.join('&')) : (query = '');
		if(this.cache[Json.toString(this.options.state)]) document.getElement('table.adminlist tbody').empty().setHTML(this.cache[Json.toString(this.options.state)]);
		else this.xhr.send(href, 'layout=default_items&format=ajax' + query);
		this.update();
	},
	
	update: function(){
		//First button
		if(!this.options.list.first.active) {
			var first = this.paginator.getElement('.first');
			var text  = first.getText();
			first.empty().setHTML('<span>' + text + '</span>');
		} else {
			var first = this.paginator.getElement('.first');
			first.empty().setHTML(this.options.template.replace('@{page}', this.options.text.first).replace('@{offset}', this.options.list.first.offset));
			
		}
		
		//Previous button
		if(!this.options.list.previous.active) {
			var previous = this.paginator.getElement('.previous');
			var text  = previous.getText();
			previous.empty().setHTML('<span>' + text + '</span>');
		} else {
			var previous = this.paginator.getElement('.previous');
			previous.empty().setHTML(this.options.template.replace('@{page}', this.options.text.previous).replace('@{offset}', this.options.list.previous.offset));
		}
		
		//Pages buttons
		var pages = [];
		var length = this.options.list.pages.length - 1;
		var classname  = '';
		this.options.list.pages.each(function(page, i){
				if (i === 0) classname = ' first-child';
				if (i == length) classname += ' last-child';
				if(page.current) pages[i] = '<li class="page active' + classname + '"><span class="active">' + page.page +'</span></li>';
				else pages[i] = '<li class="page' + classname + '">' + this.options.template.replace('@{page}', page.page).replace('@{offset}', page.offset) + '</li>';
				classname = '';
				//$html .= '<li class="page' . active . '">'.$this->link(page, page->page).'</li>';
		}.bind(this));
		this.paginator.getElements('.pages')[1].setHTML(pages.join(''));
		
		//Next button
		if(!this.options.list.next.active) {
			var next = this.paginator.getElement('.next');
			var text  = next.getText();
			next.empty().setHTML('<span>' + text + '</span>');
		} else {
			var next = this.paginator.getElement('.next');
			next.empty().setHTML(this.options.template.replace('@{page}', this.options.text.next).replace('@{offset}', this.options.list.next.offset));
		}
		
		//Last button
		if(!this.options.list.last.active) {
			var last = this.paginator.getElement('.last');
			var text  = last.getText();
			last.empty().setHTML('<span>' + text + '</span>');
		} else {
			var last = this.paginator.getElement('.last');
			last.empty().setHTML(this.options.template.replace('@{page}', this.options.text.last).replace('@{offset}', this.options.list.last.offset));
		}
		
		
		this.paginator.getElement('.count').setText(this.options.text.count.replace('@{current}', this.options.state.current).replace('@{total}', this.options.state.count));
	},
	
	setData: function(){
		
		if(this.options.state.total == 0)
		{
			var limit   = 0;
			var offset  = 0;
			var count   = 0;
			var current = 0;
		} 
		else
		{
			var total	= parseInt(this.options.state.total);
			var limit	= parseInt(Math.max(this.options.state.limit, 1));
			var offset	= parseInt(Math.max(this.options.state.offset, 0));
			
			if(limit >= total) {
				offset = 0;
			}
			
			if(!this.options.state.limit || this.options.state.limit == 0) 
			{
				offset = 0;
				limit  =  total;
			}
			
			count	= parseInt(Math.ceil(total / limit));

    		if(offset > total) {
    			
				offset = (count-1) * limit;
			}

			current = parseInt(Math.floor(offset / limit) + 1);
		}
		
		this.options.state.limit   = limit;
		this.options.state.offset  = offset;
		this.options.state.count   = count;
		this.options.state.current = current;
		states = this.options.state;
		//delete states.total;
		if(this.options.identificator) Cookie.set(this.options.identificator, Json.toString(states));
    },
    
    setList: function()
    {
    	var elements = {};
    	var current   = (this.options.state.current - 1) * this.options.state.limit;

    	// First
    	var page    = 1;
    	var offset  = 0;
    	var active  = offset != this.options.state.offset;
    	elements['first']  = {'page': 1, 'offset': offset, 'limit': this.options.state.limit, 'current': false, 'active': active };

    	// Previous
    	offset  = Math.max(0, (this.options.state.current - 2) * this.options.state.limit);
    	active  = offset != this.options.state.offset;
    	elements['previous'] = {'page': this.options.state.current - 1, 'offset': offset, 'limit': this.options.state.limit, 'current': false, 'active': active};

		// Pages
		elements['pages'] = [];
		this.getOffsets().each(function(page, i){
			current = page.offset == this.options.state.offset;
			elements['pages'][i] = {'page': page.page, 'offset': page.offset, 'limit': this.options.state.limit, 'current': current, 'active': !current};
		}.bind(this));

		// Next
    	offset  = Math.min((this.options.state.count-1) * this.options.state.limit, (this.options.state.current) * this.options.state.limit);
 		active  = offset != this.options.state.offset;
    	elements['next'] = {'page': this.options.state.current + 1, 'offset': offset, 'limit': this.options.state.limit, 'current': false, 'active': active};

    	// Last
    	offset  = (this.options.state.count - 1) * this.options.state.limit;
    	active  = offset != this.options.state.offset;
    	elements['last'] = {'page': this.options.state.count, 'offset': offset, 'limit': this.options.state.limit, 'current': false, 'active': active};

    	this.options.list = elements;
    },
    
    getOffsets: function(){
   	 	if(display = this.options.state.display)
    	{
    		var start	= parseInt(Math.max(this.options.state.current - display, 1));
    		var start	= Math.min(this.options.state.count, start);
    		var stop	= parseInt(Math.min(this.options.state.current + display, this.options.state.count));
    	}
    	else // show all pages
    	{
    		var start = 1;
    		var stop = this.options.state.count;
    	}

    	var result = [];
    	if(start > 0)
    	{
    		this.range(start, stop).each(function(pagenumber, i) {
    			result.include({page: pagenumber, offset: (pagenumber-1) * this.options.state.limit});
    		}.bind(this));
    	}

    	return result;
    },
    
    range: function( low, high, step ) {
	    var matrix = [];
	    var inival, endval, plus;
	    var walker = step || 1;
	    var chars  = false;
	
	    if ( !isNaN( low ) && !isNaN( high ) ) {
	        inival = low;
	        endval = high;
	    } else if ( isNaN( low ) && isNaN( high ) ) {
	        chars = true;
	        inival = low.charCodeAt( 0 );
	        endval = high.charCodeAt( 0 );
	    } else {
	        inival = ( isNaN( low ) ? 0 : low );
	        endval = ( isNaN( high ) ? 0 : high );
	    }
	
	    plus = ( ( inival > endval ) ? false : true );
	    if ( plus ) {
	        while ( inival <= endval ) {
	            matrix.push( ( ( chars ) ? String.fromCharCode( inival ) : inival ) );
	            inival += walker;
	        }
	    } else {
	        while ( inival >= endval ) {
	            matrix.push( ( ( chars ) ? String.fromCharCode( inival ) : inival ) );
	            inival -= walker;
	        }
	    }
	
	    return matrix;
	}
});

Element.implement({

	paginator: function(options){
		new NPaginator(this, options);
		return this;
	}
});