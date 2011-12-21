/**
 * @version		$Id: validation.js 552 2010-10-28 19:41:51Z stian $
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
NinjaValidate = new Class({
	options: {
		toolbar: 'a.toolbar',
		filter: 'post',
		form: 'adminForm',
		isValid: false,
		msg: 'Select an item to @{action} first.',
		
		onValidateForm: function(){
			var els = $(this.options.form).getElements('input.required', 'select.required', 'textarea.required');
			var filtered = els.filter(function(el){ 
				if (el.getValue() == '' || (el.getTag() == 'select' && el.value == '')) {
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
		
		onFormToolbar: function(event){
			//console.log(event);
		},
		
		onGridToolbar: function(event){
			var ids = $(this.options.form).getElements('input').filterByAttribute('name', '=', 'id[]').filterByAttribute('checked');
			if(ids.length === 0) $$(this.options.toolbar).filterByAttribute('href', '=', '#').addClass('invalid');
			else $$(this.options.toolbar).filterByAttribute('href', '=', '#').removeClass('invalid');
		},
		
		onFailure: Class.empty
	},
	
	initialize: function(type, options){
		this.setOptions(options);
		this.type = type || 'grid';
		this.options.form.addEvent('click', function(event){
			this.fireEvent('on' + this.type.capitalize() + 'Toolbar', event);
		}.bindWithEvent(this));
		$$(this.options.toolbar).filterByClass(this.options.filter).each(function(el){
		
			el.addEvent('click', function(event){
				if(!el.hasClass('disabled')){
					if(el.hasClass('validate')) this.fireEvent('onValidate'+this.type.capitalize(), el);
					if(!el.hasClass('validate') || this.options.isValid) this.fireEvent('onSuccess', el);
				} else {
					(new Event(event)).stop();
				}
			}.bind(this));
		}.bind(this));
		if (this.options.initialize) this.options.initialize.call(this);
	}
});

NinjaValidate.implement(new Events, new Options);

/*
Class: Element
	Custom class to allow all of its methods to be used with any DOM element via the dollar function <$>.
*/

Element.extend({

	validate: function(type, options){
		new NinjaValidate(type, $merge({form: this}, options));		
		return this;
	}
});