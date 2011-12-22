/**
 * @category    Koowa
 * @package     Koowa_Media
 * @subpackage  Javascript
 * @copyright	Copyright (C) 2007 - 2010 Johan Janssens and Mathias Verraes. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://www.koowa.org
 */

/**
 * Form validation class
 */
var NinjaValidate = new Class({
	
	Implements: [Events, Options],

	options: {
		toolbar: 'a.toolbar',
		filter: '.post',
		form: 'adminForm',
		isValid: false,
		msg: 'Select an item to @{action} first.',
		
		onValidateForm: function(){
			var els = $(this.options.form).getElements('input.required', 'select.required', 'textarea.required');
			var filtered = els.filter(function(el){ 
				if (el.getValue() == '' || (el.get('tag') == 'select' && el.value == '')) {
					el.fireEvent('onInvalid');
					return false;
				} else {
					return true; 
				}
			});
			this.options.isValid = els.length == filtered.length;
		},

		onValidateGrid: function(el){
			if($(this.options.form).getElements('.id').filterByAttribute('checked').length > 0) this.options.isValid = true;
			else  alert(this.options.msg.replace('@{action}', el.getText().toLowerCase()));
		},
		
		onSuccess: function(el){
			$each(Json.evaluate(el.getProperty('rel')), function(val, key){
				new Element('input', {type: 'hidden', name: key, value: val}).inject($(this.options.form));
			}.bind(this));
			$(this.options.form).submit();
		},
		
		onFormToolbar: $empty,
		
		onGridToolbar: function(event){
			var ids = $(this.options.form).getElements('input').filter('[name="id[]"]').filter('[checked]');
			if(ids.length === 0) $$(this.options.toolbar).filter('[href="#"]').addClass('invalid');
			else $$(this.options.toolbar).filter('[href="#"]').removeClass('invalid');
		},
		
		onFailure: $empty
	},
	
	initialize: function(context, options){
		var self = this;
		this.setOptions(options);
		this.context = context || 'grid';
		this.options.form.addEvent('click', function(event){
			this.fireEvent('on' + this.context.capitalize() + 'Toolbar', event);
		}.bindWithEvent(this));
		$$(this.options.toolbar).filter(this.options.filter).each(function(el){
		
			el.addEvent('click', function(event){
				if(!el.hasClass('disabled')){
					if(el.hasClass('validate')) self.fireEvent('onValidate'+self.context.capitalize(), el);
					if(!el.hasClass('validate') || self.options.isValid) self.fireEvent('onSuccess', el);
				} else {
					var event = new Event(event);
					event.stop();
				}
			});
		});
	}
});

/*
Class: Element
	Custom class to allow all of its methods to be used with any DOM element via the dollar function <$>.
*/
Element.implement({

	validate: function(context, options){
		new NinjaValidate(context, $merge({form: this}, options));		
		return this;
	}
});