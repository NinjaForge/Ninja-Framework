/**
 * @category	Ninja
 * @package		Ninja_Media
 * @subpackage	Javascripts
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license 	GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * Tabs behavior - JQuery Port of KTabs
 *
 * Usage: $('dl-id').ktabs();
 *
 * @copyright   Copyright (C) 2007 - 2010 Johan Janssens. All rights reserved.
 *
 * @author		Richie Mortimer <richie@ninjaforge.com>
 * @category	Ninja
 * @package     Ninja_Media
 * @subpackage  Javascript
 */
(function($){

	$.fn.ktabs = function(options) {
	
		var config 			= $.extend({}, $.fn.ktabs.defaults, options),
			dlist			= $(this),
			titles			= dlist.children('dt')
			descriptions	= dlist.children('dd')
			content			= $('<div></div>').insertAfter(dlist).addClass('current');
		
		//if we have set a height add it 
		if(config.height) {
			content.setStyle('height', config.height);
		}
		
		//foreach tab add the click event
		titles.each(function(i, element)
		{
		    var title = $(titles[i]);
		    title.css('cursor', 'pointer');
		    title.click(function() {
		   		display(i);
		    });
		});
		
		//add the tab panes to the content div
		content.append(descriptions);
		
		//run the display function for the first time
		display(config.display);
		
		function display(i)
		{
			hideAllBut(i);
			config.onActive(titles[i], descriptions[i]);
			
		}
		
		function hideAllBut(but)
		{
			config.onBackground(titles.filter(function(e, index){ return index !== but; }), descriptions.filter(function(e, index){ return index !== but; }));
			
		}
		
		return dlist;		
	};
	
	$.fn.ktabs.defaults = {
		display: 0,
		height: null,
		cookies: 1,
		
		onActive: function(title, description) {
			$(description).show();
			title.removeClass('closed').addClass('open');
		},
		
		onBackground: function(title, description) {
			$(description).hide();
			title.removeClass('open').addClass('closed');
		}
	}
	
	
})(ninja);