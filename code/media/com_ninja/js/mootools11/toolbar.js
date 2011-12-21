/*
Class: Element.toolbar, simple forms validation.
*/
Element.extend({
	toolbar: function(options){
		var parts	= this.getProperty('id').split('-'),
			toolbar	= $('toolbar-'+parts[2]),
			buttons = toolbar.getElements('a.toolbar'),
			i		= 0;
		
		buttons.each(function(button){
			button.form = this;
		}.bind(this));
			
		toolbar.form = this;
		this.toolbar = toolbar;

		this.validator = new NinjaValidate(this);

		buttons.each(function(button){
			var vals = button.getProperty('class').split(' ').filter(function(cls){
				return cls.test(':');
			});
			if (!vals.length){
				button.toolbarProps = {};
			} else {
				props = {};
				vals.each(function(cls){
					var split = cls.split(':');
					if (split[1]) {
						try {
							props[split[0]] = Json.evaluate(split[1]);
						} catch(e) {}
					}
				});
				button.toolbarProps = props;
			}
		});
		
		buttons.each(function(button,i){	
			button.addEvent('click', function(event){
			
				var props = this.toolbarProps,
					form  = this.form;

				if(props.type == 'cancel'){
					new Element('input', {type: 'hidden', name: 'action', value: 'cancel'}).inject(form);
					return form.submit();
				}
				
				if(!this.hasClass('toolbar-form-validate')) return;
				
				event.preventDefault();
				
				if(this.form.validate()){
					
					if(props.type == 'post')
					{
						new Element('input', {type: 'hidden', name: 'action', value: props.action}).inject(form);
						return form.submit();
					} else if(props.type == 'edit')
					{
						url = this.getProperty('href');
						ids = form.getElements('.validate-reqchk-byname').filter(function(el){
							return el.getProperty('checked');
						});
					
						id	= ids[0].getValue();
						url	+= '&id=' + id;
						window.location = url;
						return;
					}
					return;
					
				} else {					
					form = this.form;
					failed = form.getElement('.validation-failed');
					
					if(failed.getProperty('rel')){
						msg = failed.getProperty('rel'); 
					} else {
						msg = this.form.validator.options.msg;
					}

					msg = msg.replace('{label}', failed.validatorProps.label.toLowerCase());
	
					if(typeof Roar == 'function') return new Roar().alert(msg);
					
					alert(msg);
				}
				
			});
		});
		
		this.addEvent('onElementPass', function(){
			this.toolbar.getElements('.toolbar-form-validate').removeClass('invalid');
		}.bind(this)).addEvent('onElementFail', function(){
			this.toolbar.getElements('.toolbar-form-validate').addClass('invalid');
		}.bind(this));

		return this;
	}
});

/**
 * Simple form validation class
 */
NinjaValidate = new Class({
	options: {
		isValid: false,
		msg: 'Please select one {label}.',
		
		onValidateForm: function(){
			var elements =	this.form.getElements('.required');
			
			elements.each(function(el){
				if(!el.getValue()){
					el.addClass('validation-failed');
					this.form.fireEvent('onElementFail', el);
				} else {
					el.removeClass('validation-failed');
					this.form.fireEvent('onElementPass', el);
				}
			}.bind(this));

		},

		onValidateGrid: function(){
			var elements =	this.form.getElements('.validate-reqchk-byname'),
				filtered =	elements.filter(function(el){
								return el.getValue();
							}.bind(this));

			if(filtered.length < 1){
				elements.each(function(el){
					el.addClass('validation-failed');
					this.form.fireEvent('onElementFail', el);
				}.bind(this));
			} else {
				elements.each(function(el){
					el.removeClass('validation-failed');
					this.form.fireEvent('onElementPass', el);
				}.bind(this));
			}
		}
				
	},
	
	initialize: function(form, options){
		var inline = form.hasClass('validator-inline');
		if(inline){
			this.options.msg = '{label} is required.';
			form.toolbar.getElements('.invalid').each(function(button){
				button.removeClass('invalid');
			});
		}
		this.type = inline ? 'form' : 'grid';
		
		this.setOptions(options);
		this.form = form;
		
		this.form.addEvent('onInject', this.update.bind(this));
		
		this.setProps();
		
		return this;
	},
	
	update: function(){
		this.setProps();
	},
	
	setProps: function(){
		$each(this.form.elements, function(element){
			if(typeof element.validatorProps == 'object') return;			
			var vals = element.getProperty('class').split(' ').filter(function(cls){
				return cls.test(':');
			});
			if (!vals.length){
				element.validatorProps = {label:'Item'};
			} else {
				props = {label:'Item'};
				vals.each(function(cls){
					var split = cls.split(':');
					if (split[1]) {
						try {
							props[split[0]] = Json.evaluate(split[1]);
						} catch(e) {}
					}
				});
				element.validatorProps = props;
			}
			
			element.addEvent('change', this.validate.bind(this));
		}.bind(this));
	},
	
	validate: function(){
		this.fireEvent('onValidate' + this.type.capitalize());
		return this.form.getElements('.validation-failed').length < 1;
	}
});

NinjaValidate.implement(new Events, new Options);

/*
Class: Element
	Custom class to allow all of its methods to be used with any DOM element via the dollar function <$>.
*/

Element.extend({

	validate: function(){
		if(typeof this.validator == 'undefined') this.validator = new NinjaValidate(this);

		return this.validator.validate();
	}

});