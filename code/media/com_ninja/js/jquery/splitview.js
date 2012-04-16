/*
Script: splitview.js
	Displays a splitview, master view to the left (a list over entities) and detail view to the right (expanded view, singular)

	Authors:
		Stian Didriksen <stian@ninjaforge.com>
*/

(function($){

$.fn.extend({
	
	splitview: function(custom){

		var options = {
		    primary_key: 'id',
			master_class: 'splitview-list',
			detail_class: 'splitview-item'
		};
		$.extend(options, custom);

		return this.each(function(){
			var self = this,
				cache = {},
				detailView = $('<div/>', {
					'class': options.detail_class
				}).appendTo(this),
				openDetailView = function(event){
					var id = $(this).attr('data-'+options.primary_key);
					
					if(cache[id]) {
						detailView.html(cache[id]);
					} else {
						$(self).addClass('loading');
						detailView.load(options.detail_url+'&'+options.primary_key+'='+id, function(html){
							cache[id] = html;
							$(self).removeClass('loading');
						});
					}
					
					$(this).siblings('.active').removeClass('active');
					$(this).addClass('active');
					
					$(self).trigger('select', event.data);
				}
				masterView = $('<ul/>', {
					'class': options.master_class
				}).prependTo(this);

			$.getJSON(options.master_url, function(data) {
				var items = [], rowset = {};

				$.each(data, function(i, item) {
				    rowset[item[options.primary_key]] = item;
					items.push('<li data-'+options.primary_key+'="' + item[options.primary_key] + '">' + item.html + '</li>');
				});
				
				masterView.html(items.join(''));
				
				masterView.children().each(function(i, item){
				    var row = $(item);

				    row.click(rowset[row.attr('data-'+options.primary_key)], openDetailView);
				});
				
				$(self).trigger('loaded');
			});
		});
	}
	
});

})(ninja);