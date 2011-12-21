/*
Script: NSortables.js
	Contains <NSortables> Class.

License:
	MIT-style license.
*/

/*
Class: NSortables
	Creates an interface for <Drag.Base> and drop, resorting of a list.

Note:
	The Sortables require an XHTML doctype.

Arguments:
	list - required, the list that will become sortable.
	options - an Object, see options below.

Options:
	handles - a collection of elements to be used for drag handles. defaults to the elements.

Events:
	onStart - function executed when the item starts dragging
	onComplete - function executed when the item ends dragging
*/

if (!$chk(Koowa)) var Koowa = {};

NSortables = new Class({

	options: {
		canSaveOrder: 1,
		saveDelay: 1000,
		form: 'adminForm',
		list: '.sortable',
		handles: '.sortable td.handle',
		duration: 300,
		msg: {
			saving: 'Saving ordering\u2026',
			success: 'Ordering saved successfully!'
		},
		onStart: Class.empty,
		onComplete: Class.empty,
		ghost: true,
		offset: 0,
		snap: 3,
		tree: false,
		spinner: {},
		onDragStart: function(element, shadow, ghost){
			element.setStyle('opacity', 0.0);
			ghost.setStyles({
				opacity: 1
			});
			shadow.style.webkitTransitionDuration = this.options.duration + 'ms';
			shadow.effects({duration: this.options.duration, transition: Fx.Transitions.Sine.easeInOut}).start({
//				opacity: [1, 0.2]
			});
			shadow.addClass('animate');
		},
		onDragComplete: function(element, shadow, ghost){
					
			shadow.element = element;
			shadow.options = this.options;
			shadow.trash = this.trash;
			
			pos = element.getPosition();
			shadow.removeClass('animate');
			shadow.effects({duration: this.options.duration, transition: Fx.Transitions.Sine.easeInOut, onComplete:function(shadow){
				shadow.options.emptyTrash(shadow);
				shadow.element.setStyles({'opacity':1});
				shadow.remove();
			}}).start({
//				opacity: 1,
				top: pos.y,
				left: pos.x
			});
		},
		
		onOrderChange: function(order, spinner){
				form = $(this.options.form);
				var child = spinner.element.getChildren()[0].setText(this.options.msg.saving);
				spinner.set({height: this.list.getSize().size.y, width: this.list.getSize().size.x}).start({opacity:0.6});
					var success = this.options.msg.success;
					$(this.options.form).send({
					data: {
						action: 'order',
						ordering: Json.toString(order),
						_token: $(this.options.form).getElement('input[name="_token"]').getValue()
					},
					onComplete: function(data) {	
						child.setText(success);
						spinner.stop();
						spinner.start({opacity:0});
					}
				});	
		},
		
		emptyTrash: function(e){
			e.trash.remove();
		},
		
		onMorphStart: function(ghost){
			ghost.addClass('morph').addClass('morphStart');
			(function(){ ghost.removeClass('morphStart'); }).delay(500);
			//$$('.droppable').each(function(e){
			//	e.addEvent('mousemove', function(){
			//		this.addClass('active');
			//	});
			//});
			
		},
		onMorphEnd: function(ghost){
			ghost.addClass('morphEnd');
			(function(){ ghost.removeClass('morph').removeClass('morphEnd'); }).delay(500);
			//$$('.droppable').each(function(e){
			//	e.removeEvent('mousemove').removeClass('active');
			//});
		}
	},

	initialize: function(options){
		this.setOptions(options);
		this.list = $$(this.options.list)[0];
		this.elements = this.list.getChildren().filterByClass('sortable');
		if(this.elements.length <= 1) return;
		this.morphing = false;
		if (this.options.initialize) this.options.initialize.call(this);
		if(this.options.handles)
		{
			this.handles = new Array;
			this.elements.each(function(e,i){ 
				this.handles[i] = e.getElement(this.options.handles);
			}.bind(this));
		}
		else this.handles = this.elements;
		this.bound = {
			'start': [],
			'moveGhost': this.moveGhost.bindWithEvent(this)
		};
		for (var i = 0, l = this.handles.length; i < l; i++){
			this.bound.start[i] = this.start.bindWithEvent(this, this.elements[i]);
		}
		//if(src = this.options.spinner.src) this.options.spinner.image = new Asset.image(src).inject($(this.options.spinner.id));
		if(this.options.spinner) this.options.spinner.image = new Element('div', {"class": 'sortable-spinner'}).setText(this.options.msg.saving).setStyles({
			position: 'absolute',
			top: '50%',
			backgroundRepeat: 'no-repeat',
			paddingLeft: 20,
			left: '50%',
			marginTop: '-8px',
			marginLeft: '-' + (this.options.msg.saving).length*4 + 'px',
			height: 16,
			fontSize: 14
		}).inject(new Element('div').setStyles({
			width: 400,
			position: 'absolute',
			top: this.list.getPosition().y,
			left: this.list.getPosition().x,
			height: 100,
			textAlign: 'center',
			background: 'white'
		}).inject(document.body));
		if(img = this.options.spinner.image.getParent()) this.options.spinner.image.fx = img.effects({duration: this.options.duration, transition: Fx.Transitions.Sine.easeInOut}).set({opacity:0});
		this.options.order = this.serialize();
		this.attach();
		
		//We need to make this support tree lists before we can use it again.
//		this.list.addEvent('mousedown', function(event){
//			if(event.target.hasClass('handle')) new NSortables(options); $(event.target).fireEvent('mousedown');
//		}, this);
		this.bound.move = this.move.bindWithEvent(this);
		this.bound.end = this.end.bind(this);
	},

	attach: function(){
		this.handles.each(function(handle, i){
			handle.addEvent('mousedown', this.bound.start[i]);
		}, this);
	},

	detach: function(){
		this.handles.each(function(handle, i){
			handle.removeEvent('mousedown', this.bound.start[i]);
		}, this);
	},

	start: function(event, el){
		this.active = el;
		this.coordinates = this.list.getCoordinates();
		if (this.options.ghost){
			var position = el.getPosition();
			var size	 = el.getSize();
			this.offset = {y: event.page.y - position.y, x: event.page.x - position.x};
			this.trash = new Element('div', {'class':'ghost-outer'}).inject(document.body);
			this.ghost = new Element('div', {'class':'ghost-shadow'}).inject(this.trash).setStyles({
				'position': 'absolute',
				'overflow':'hidden',
				'left': event.page.x - this.offset.x,
				'top': event.page.y - this.offset.y,
				width: size.size.x,
				height: size.size.y
			});
			
			var treeClass = this.options.tree ? ' treelist' : '';
			this.innerghost = new Element('table', {'class':'adminlist ghost nowrap' + treeClass}).inject(this.ghost);
			this.ghostelement = el.clone().inject(new Element('tbody').inject(this.innerghost));
			document.addListener('mousemove', this.bound.moveGhost);
			this.fireEvent('onDragStart', [el, this.ghost, this.ghostelement]);
		}
		document.addListener('mousemove', this.bound.move);
		document.addListener('mouseup', this.bound.end);
		this.fireEvent('onStart', el);
		event.stop();
	},

	moveGhost: function(event){
		var value = {x: event.page.x - this.offset.x, y: event.page.y - this.offset.y};
		if(event.page.y > this.coordinates.top) {
			value.x = this.coordinates.left;
			if(this.morphing) {
				this.fireEvent('onMorphEnd', [this.ghost]);
				this.morphing = false;
			}
			
		} else {
			if(this.morphing) {
				this.fireEvent('onMorphStart', [this.ghost]);
				this.morphing = true;
			} else {
				value.x = this.coordinates.left;
			}
		}
		//value = value.limit(this.coordinates.top, this.coordinates.bottom - this.ghost.offsetHeight);
		value.y = value.y.limit(this.coordinates.top, this.coordinates.bottom - this.ghost.offsetHeight);
		//this.ghost.setStyle('top', value);
		this.ghost.setStyles({'top': value.y, 'left': value.x});
		event.stop();
	},

	move: function(event){
		var now = event.page.y;
		this.previous = this.previous || now;
		var up = ((this.previous - now) > 0);
		var prev = this.active.getPrevious();
		var next = this.active.getNext();
		if (prev && up && now < prev.getCoordinates().bottom) {
			this.active.injectBefore(prev);
		}
		if (next && !up && now > next.getCoordinates().top) {
			this.active.injectAfter(next);
		}
		this.previous = now;
	},

	serialize: function(converter){
		i = 0;
		return this.list.getChildren().filterByClass('sortable').map(converter || function(el){
			index = this.elements.indexOf(el);
			return {id:this.elements[index].getElement('input[name^="id"]').value,ordering:(i++)};
		}, this);
	},

	end: function(){
		this.previous = null;
		document.removeListener('mousemove', this.bound.move);
		document.removeListener('mouseup', this.bound.end);
		if (this.options.ghost){
			document.removeListener('mousemove', this.bound.moveGhost);
			if(this.morphing) {
				this.fireEvent('onMorphEnd', [this.ghost]);
				this.morphing = false;
			}
			this.fireEvent('onDragComplete', [this.active, this.ghost, this.ghostelement]);
			if(Json.toString(this.serialize()) != Json.toString(this.options.order)&&this.options.canSaveOrder){ 
				this.options.canSaveOrder = false;
				(function(){
					this.options.canSaveOrder = true;
					this.options.order = this.serialize();
					this.fireEvent('onOrderChange', [this.serialize(), this.options.spinner.image.fx]);
				}).delay(this.options.saveDelay, this);
			}
		}
		this.fireEvent('onComplete', this.active);
	}

});

NSortables.implement(new Events, new Options);