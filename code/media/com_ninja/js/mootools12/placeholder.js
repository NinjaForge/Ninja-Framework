var Placeholder = function(){
	$$('.placeholder .title').each(function(title){
		var top, parent = title.getParent(), offset = parent.getStyle('height').toInt() / 2;
		if(!title.getNext()) {
			top = offset - (title.getStyle('height').toInt() / 2);
		}
		title.setStyle('top', top);
	});
}

window.addEvents({domready: Placeholder, resize: Placeholder, load: Placeholder});
window.addEvent('domready', function(){
	var buttons = $$('.placeholder.resize a'),
		sizes   = buttons.getSize(),
		width   = 0,
		x;
		
	sizes.each(function(size){
		x  = size.x-62;
		if(x > width) width = x;
	});
	
	buttons.setStyle('width', width);
	
	buttons.getChildren().each(function(span){
		bg = span[0].getStyle('background-image');		
		span[0].style.webkitMaskImage = bg;
	});
});