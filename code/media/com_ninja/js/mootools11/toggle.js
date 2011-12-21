window.addEvent('domready', function(){
	var forms = $$('form').filter(function(form,i){
					return form.getElements('.toggle-state').length > 0;
				});

	forms.each(function(form){
		form.addEvent('click', function(event){
			var event  = new Event(event),
				target = $(event.target);
			if(target.$tmp.busy) return;
			if(target.hasClass('toggle-state'))
			{
				var options = target.$tmp.options || Json.evaluate(target.getProperty('rel')),
					toggle  = options[options.toggle],
					data	= {
						action: 'edit',
						_token: $(this).getElement('[name=_token]').getValue()
					};
					
				if(!options.id) options.id = target.getParent().getParent().getElement('input.id').getValue();
				console.log(options.id);
				data[options.toggle] = options[options.toggle] === 0 ? 1 : 0;
					
				if(options.edit) data[options.edit] = target.getValue();

				target.$tmp.busy = true;
				new Ajax(this.getProperty('action')+'&format=json&id='+options.id+'&view='+target.getProperty('data-view'), {
					data: data,
					onComplete: function(){
						options[options.toggle] = toggle == 0 ? 1 : 0;
						classes = [options.toggle + '-' + new Boolean(options[options.toggle]), options.toggle + '-' + new Boolean(toggle)];
						target.removeClass('icon-toggle-' + classes[1]).addClass('icon-toggle-' + classes[0]);
						target.getParent().getParent().removeClass('state-' + classes[1]).addClass('state-' + classes[0]);
						target.$tmp.options = options;
						target.$tmp.busy = false;
					},
					onSuccess: function(response){
						if(!response.msg) return;
						if(typeof Roar == 'function') new Roar().alert(response.msg);
					}
				}).request();
			}
		return this;
		
			var event  = new Event(event),
				target = $(event.target);
				
			if(target.hasClass('toggle-state'))
			{
				var toggle = Json.evaluate(target.getProperty('rel')),
					token  =  this.getElements('input').filter(function(e){return e.getProperty('name')=='_token'})[0].getValue();
				
				this.send({
					data: {
						id: target.getParent().getParent().getElement('input.id').getValue(),
						action: toggle.state[toggle.toggle],
						_token: token
					},
					method: 'post',
					onComplete: function(){
						target.addClass('icon-toggle-' + toggle.state[toggle.toggle]).getParent().getParent().addClass('state-' + toggle.state[toggle.toggle]);
						toggle.toggle = toggle.toggle == 0 ? 1 : 0;
						target.removeClass('icon-toggle-' + toggle.state[toggle.toggle]).getParent().getParent().removeClass('state-' + toggle.state[toggle.toggle]);
						target.setProperty('rel', Json.toString(toggle).replace(/"/g, "'"));
					}
				});
			}
		});
	});
});