(function($){
	window.addEvent('domready', function(){
		//IE seems to break if you simply do $$('form')
		var elements = new Elements(document.body.getElementsByTagName('form'));
		
		elements = elements.filter(function(element){
			return element.getElements('.toggle-state').length > 0;
		});


		elements.addEvent('click', function(event){
			var target = $(event.target);
			if(target.retrieve('busy')) return;
			if(target.hasClass('toggle-state'))
			{
				var options = target.retrieve('options') || Json.evaluate(target.get('rel')),
					toggle  = options[options.toggle],
					data	= {
						action: 'edit',
						_token: $(this).getElement('[name=_token]').get('value')
					};
					
				if(!options.id) options.id = target.getParent().getParent().getElement('input.id').get('value');
				
				data[options.toggle] = options[options.toggle] === 0 ? 1 : 0;
					
				if(options.edit) data[options.edit] = target.get('value');

				target.store('busy', true);
				new Request.JSON({
					url: this.get('action')+'&format=json&id='+options.id+'&view='+target.get('data-view'),
					data: data,
					onComplete: function(){
						options[options.toggle] = toggle == 0 ? 1 : 0;
						classes = [options.toggle + '-' + new Boolean(options[options.toggle]), options.toggle + '-' + new Boolean(toggle)];
						target.swapClass('icon-toggle-' + classes[1], 'icon-toggle-' + classes[0]);
						target.getParent().getParent().swapClass('state-' + classes[1], 'state-' + classes[0]);
						target.store('options', options).eliminate('busy');
					},
					onSuccess: function(response){
						if(!response.msg) return;
						if(typeof Roar == 'function') new Roar().alert(response.msg);
					}
				}).post();
			}
		});
	});
})(document.id);