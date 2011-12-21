(function($){

	$.fn.checkall = function(){
		
		return this.each(function(){
			var checked	= $(this).attr('checked'),
				els		= $('[type=checkbox].id'+(checked ? '' : ':checked'), this.form);
			
			els.each(function(){
				$(this).attr('checked', checked).trigger('change');
			});
	
			$('[type=checkbox].id', this.form).count();
		});

	};
	
	$.fn.count = function(){
	
			var els		= $('[type=checkbox].id', this[0].form),
				count	= els.filter(':checked').length;

			$('[type=checkbox].toggle', this[0].form).attr('checked', els.length === count);
	
			return this;
	};
	
	$.fn.selectables = function(){
		
		//@TODO in progress
		return this;
		
		
		this.getElements('tr').each(function(tr){
			var checkbox = tr.getElement('input[type=checkbox]');
			if(!checkbox) return;
			checkbox.addEvent('change', function(tr){
				this.getProperty('checked') ? tr.addClass('selected') : tr.removeClass('selected');
				var selected = tr.hasClass('selected') + tr.getSiblings('.selected').length, parent = tr.getParent();
				if(selected > 1) {
					parent.addClass('selected-multiple').removeClass('selected-single')
				} else {
					parent.removeClass('selected-multiple').addClass('selected-single');
				}
			}.pass(tr, checkbox)).fireEvent('change');
			tr.addEvent('click', function(event){
				if(event.target.hasClass('toggle-state') || event.target.match('[type=checkbox]')) return;
				var checkbox = this.getElement('input[type=checkbox]'), checked = checkbox.getProperty('checked');
				if(checked) {
					this.removeClass('selected');
					checkbox.setProperty('checked', false);
				} else {
					this.addClass('selected');
					checkbox.setProperty('checked', true);
				}
				checkbox.fireEvent('change').count();
			});
		});
	
		return this;
	};
	
})(jQuery);