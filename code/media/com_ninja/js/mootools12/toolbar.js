Selectors.Pseudo = new Hash({

	defaultSelected: function(){
		return this.defaultSelected;
	},
	
	defaultChecked: function(){
		return this.defaultChecked;
	}

});

if(Browser.Platform.mac){
	Event.Keys.extend({
		'cmd': 91
	});
}

/*
Class: Element.toolbar, integrates Form.Validator with a toolbar.
*/
Element.implement({

	toolbar: function(options){
		var parts	= this.getProperty('id').split('-'),
			toolbar	= $('toolbar-'+parts[2]),
			buttons = toolbar.getElements('a.toolbar').store('form', this),
			i		= 0,
			msg		= false,
			isInline= this.hasClass('validator-inline'),
			keyboard= new Keyboard;
			
		toolbar.store('form', this);
		this.store('toolbar', toolbar);

		if(isInline) new Form.Validator.Inline(this);
		
		buttons.each(function(button){
			if(isInline && button.hasClass('invalid')) button.removeClass('invalid');
			var vals = button.get('class').split(' ').filter(function(cls){
				return cls.test(':');
			});
			if (!vals.length){
				button.store('toolbarProps', {});
			} else {
				props = {};
				vals.each(function(cls){
					var split = cls.split(':');
					if (split[1]) {
						try {
							props[split[0]] = JSON.decode(split[1]);
						} catch(e) {}
					}
				});
				button.store('toolbarProps', props);
				
				if(props.action == 'save'){
					keyboard.addShortcut('save', {
						keys: 'ctrl+shift+s',
						description: 'Toolbar save action',
						handler: function(){
							button.fireEvent('click', new Event);
						}.bind(button)
					});
					button.set('title', 'Keyboard shortcut: ctrl+shift+s');
				}
				if(props.type == 'edit'){
					keyboard.addShortcut('edit', {
						keys: 'ctrl+e',
						description: 'Toolbar edit action',
						handler: function(){
							button.fireEvent('click', new Event);
						}.bind(button)
					});
					button.set('title', 'Keyboard shortcut: ctrl+e');
				}
				if(props.action == 'delete'){
					keyboard.addShortcut('delete', {
						keys: 'ctrl+d',
						description: 'Toolbar delete action',
						handler: function(){
							if(confirm('Are you sure you want to delete?')) button.fireEvent('click', new Event);
						}.bind(button)
					});
					button.set('title', 'Keyboard shortcut: ctrl+d');
				}
				if(props.action == 'apply'){
					keyboard.addShortcut('apply', {
							keys: 'ctrl+s',
						description: 'Toolbar apply action',
						handler: function(){
							button.fireEvent('click', new Event);
						}.bind(button)
					});
					button.set('title', 'Keyboard shortcut: ctrl+s');
				}
				if(props.type == 'cancel'){
					keyboard.addShortcut('cancel', {
						keys: 'ctrl+w',
						description: 'Toolbar cancel action',
						handler: function(){
							button.fireEvent('click', new Event);
						}.bind(button)
					});
					button.set('title', 'Keyboard shortcut: ctrl+w');
				}
			}
		});
		
		if(newButton = $('toolbar-'+parts[2]+'-new')){
			keyboard.addShortcut('new', {
				keys: 'ctrl+n',
				description: 'Toolbar new action',
				handler: function(){
					window.location = this.get('href');
				}.bind(newButton.getElement('a').set('title', 'Keyboard shortcut: ctrl+n'))
			});
		}
		
		buttons.addEvent('click', function(event){
		
			var props = this.retrieve('toolbarProps'),
				type  = props.type,
				form  = this.retrieve('form');
			
			if(props.type == 'cancel'){
				var changed = $$(form.elements).filter(function(e,i){
					if(e.match('[type=checkbox]') || e.match('[type=radio]')) return e.defaultChecked != e.checked;
					if(def = e.get('defaultChecked')) return def != e.get('checked');
					if(e.get('tag') == 'select'){
						selected = e.getElement(':defaultSelected');
						if(selected) return selected.get('value') != e.get('value');
						else		 return e.get('value');
					}
					return e.get('value') != e.get('defaultValue');
				});
				if(changed.length && !confirm('You have unsaved changes. Are you sure you want to cancel?')) return;
				new Element('input', {type: 'hidden', name: 'action', value: 'cancel'}).inject(form);
				return form.submit();
			}
			
			if(!this.hasClass('toolbar-form-validate')) return;
			
			event.preventDefault();
			
			if(this.retrieve('form').validate()){
				
				if(type == 'post') {
					if(form.getElement('[name="id[]"][checked]')) {
						uri = new URI(form.get('action'));
						ids = [];
						form.getElements('[name="id[]"][checked]').each(function(el){
							ids.push(el.get('value'));
							
						});
						url	= uri.setData({'id': ids}, true);		
						form.set('action', url);
					}
					new Element('input', {type: 'hidden', name: 'action', value: props.action}).inject(form);
					return form.submit();
				} else if(type == 'edit') {
					uri = new URI(this.get('href'));
					id	= form.getElement('[name="id[]"][checked]').get('value');
					url	= uri.setData({id: id}, true);
					window.location = url;
					return;
				}
				return;
				
			} else {

				if(this.retrieve('form').hasClass('validator-inline')) return this.fireEvent('submit');
				
				form = this.retrieve('form');
				failed = form.getElement('.validation-failed');

				msg = this.retrieve('form').retrieve('msg');

				if(typeof Roar == 'function') return new Roar().alert(msg);
				
				window.alert(msg);
			}
		});

		this.get('validator').addEvent('onElementPass', function(result){
			this.element.retrieve('toolbar').getElements('.toolbar-form-validate').removeClass('invalid');
		}).addEvent('onElementFail', function(element, validators){
			this.element.retrieve('toolbar').getElements('.toolbar-form-validate').addClass('invalid');
			msg = this.getValidator(validators[0]).getError(element);
			this.element.store('msg', msg).store('failedElement', element);
		});

		return this;
	}
});