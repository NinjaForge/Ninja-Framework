/**
 * @version		$Id: tabs.js 552 2010-10-28 19:41:51Z stian $
 * @category    Koowa
 * @package     Koowa_Media
 * @subpackage  Javascript
 * @copyright	Copyright (C) 2007 - 2009 Johan Janssens and Mathias Verraes. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://www.koowa.org
 */
 
/**
 * Tabs behavior
 *
 * @package     Koowa_Media
 * @subpackage	Javascript
 */
var KTabs = new Class({

    options: {
    
        display: 	0,
        
        height:		null,
        
        cookies:	1,

        onActive: function(title, description){
            description.setStyle('display', 'block');
            title.removeClass('open').addClass('closed');
        },

        onBackground: function(title, description){
        	for(i=0; i<title.length; i++){
        		description[i].setStyle('display', 'none');
        		title[i].addClass('open').removeClass('closed');
        	}
        }
    },

    initialize: function(dlist, options)
    {
        this.dlist = $(dlist);
        this.setOptions(options);
        this.titles = this.dlist.getElements('dt');
        this.descriptions = this.dlist.getElements('dd');
        this.content = new Element('div').injectAfter(this.dlist).addClass('current');
          
        if(this.options.height) {
        	this.content.setStyle('height', this.options.height);
        }

        for (var i = 0, l = this.titles.length; i < l; i++)
        {
            var title = this.titles[i];
            title.setStyle('cursor', 'pointer');
            title.addEvent('click', this.display.bind(this, i));
        }
        this.descriptions.injectInside(this.content);
		
		if(this.options.cookies) {
			if(Cookie.get('ktabs.' + dlist)) this.options.display = Cookie.get('ktabs.' + dlist);
		}
        if ($chk(this.options.display)) this.display(this.options.display);

        if (this.options.initialize) this.options.initialize.call(this);
    },

    hideAllBut: function(but)
    {
        this.fireEvent('onBackground', [this.titles.filter(function(e, index){ return index !== but; }), this.descriptions.filter(function(e, index){ return index !== but; })]);
    },
    
    display: function(i)
    {
    	if (this.options.cookies) Cookie.set('ktabs.' + this.dlist.getProperty('id'), i);
    	
        this.hideAllBut(i);
        this.fireEvent('onActive', [this.titles[i], this.descriptions[i]])
    }
});

KTabs.implement(new Events, new Options);