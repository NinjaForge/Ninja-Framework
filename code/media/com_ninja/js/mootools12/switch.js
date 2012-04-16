/*
	name: Switch
	version: 0.1
	description: On-off iPhone style switch button.
	license: MooTools MIT-Style License (http://mootools.net/license.txt)
	copyright: Valerio Proietti (http://mad4milk.net)
	authors: Valerio Proietti (http://mad4milk.net)
	requires: MooTools 1.2.5, Touch 0.1+ (http://github.com/kamicane/mootools-touch)
	notes: ported to MooTools 1.11 with some modifications by Djamil Legato (w00fzIT [at] gmail.com)
	notes: ported to MooTools 1.2.5 with some modifications by Stian Didriksen (stian [at] ninjaforge.com)
*/




var Switch = new Class({

	Implements: [Options, Events],

    options: {
        target: '.iphone-checkbox-incl',
        radius: 0,
        duration: 250,
        transition: Fx.Transitions.Sine.easeInOut,
        focus: false
    },
    initialize: function (c, d) {
        this.setOptions(d);
        this.check = $(c) || false;
        if (!this.check) {
            return false;
        }
        this.container = new Element("div", {
            'class': "ninja-iphone-incl-container"
        });
        this.sides = (new Element("div", {
            'class': "ninja-iphone-incl-sides"
        })).inject(this.container);
        this.wrapper = (new Element("div", {
            'class': "ninja-iphone-incl-wrapper"
        })).inject(this.sides);
        this.switcher = (new Element("div", {
            'class': "ninja-iphone-incl-switch"
        })).inject(this.sides.getFirst());
        this.button = (new Element("div", {
            'class': "ninja-iphone-incl-button"
        })).inject(this.sides);
        if (this.check.getParent().get('tag') == "label") {
            this.container.inject(this.check.getParent(), "after");
            this.check.getParent().inject(this.sides.getFirst()).setStyles({
                position: "absolute",
                left: "-100000px",
                top: 0
            });
        } else {
            this.container.inject(this.check, "after");
            this.check.inject(this.sides.getFirst()).setStyles({
                position: "absolute",
                left: "-100000px",
                top: 0
            });
        }
        var f = this;
        this.check.addEvents({
            attach: this.attach.bind(this),
            detach: this.detach.bind(this),
            set: function (a) {
                f.change(a, true);
            }
        });
        this.height = this.sides.getStyle("height").toInt();
        this.focused = false;
        var g = this.button.getStyle("width").toInt(),
            fullWidth = this.sides.getStyle("width").toInt();
        this.min = this.options.radius;
        this.max = fullWidth - g - this.min;
        this.width = fullWidth - g;
        this.height = this.height;
        this.half = this.width / 2;
        this.steps = this.options.duration / this.width;
        this.state = !!this.check.checked;
        this.change(this.state, true);
        this.fx = new Fx.Move(c, {
            duration: this.options.duration,
            transition: this.options.transition,
            wait: false
        });
        this.fx.set = function (a) {
            if (!$chk(a)) {
                a = this.fx.now;
            }
            this.update(a);
        }.bind(this);
        this.fx.increase = this.fx.set;
        this.drag = new Touch(this.button);
        var h = function () {
            if (!this.animating) {
                this.toggle();
            }
        }.bind(this);
        this.drag.addEvent("start", function (x) {
            this.check.focus();
            this.position = this.button.offsetLeft;
        }.bind(this));
        this.drag.addEvent("move", function (x) {
            this.update(this.position + x);
        }.bind(this));
        this.drag.addEvent("end", function (x) {
            var a = this.button.offsetLeft;
            var b = a > this.half ? true : false;
            this.change(b);
        }.bind(this));
        this.drag.addEvent("cancel", h);
        this.switchButton = new Touch(this.switcher);
        this.switchButton.addEvent("cancel", h);
        this.switchButton.addEvent("start", function (e) {
            this.check.focus();
        }.bind(this));
        return this;
    },
    attach: function () {
        this.container.removeClass("disabled");
        this.drag.attach();
        this.switchButton.attach();
    },
    detach: function () {
    	this.container.addClass("disabled");
        this.drag.detach();
        this.switchButton.detach();
    },
    update: function (x) {
        if (x < this.min) {
            x = 0;
        } else if (x > this.max) {
            x = this.width;
        }
        this.switcher.style.backgroundPosition = x - this.width + "px center";
        //this.switcher.style.left = x - this.width + "px";
        this.button.style.left = x + "px";
        this.updateSides(x);
    },
    updateSides: function (x) {
        var a = "0 0";
        var b = -this.height;
        var c = {
            off: "0 " + (this.focused && this.options.focus ? b * 6 : b * 3),
            on: "0 " + (this.focused && this.options.focus ? b * 5 : b * 2)
        };
        if (x == 0) {
            a = c.off + "px";
        } else if (x == this.width) {
            a = c.on + "px";
        } else {
            a = "0 " + b * 4 + "px";
        }
        this.sides.style.backgroundPosition = a;
    },
    toggle: function () {
        this.change(this.button.offsetLeft > this.half ? false : true);
    },
    change: function (a, b) {
        if (typeof a == "string") {
            a = a.toInt();
        }
        if (this.animating) {
            return this;
        }
        if (b) {
            this.set(a);
        } else {
            this.animate(a);
        }
        this.check.checked = a;
        this.check.value = !a ? 0 : 1;
        this.state = a;
        this.check.fireEvent("onChange", a);
        this.fireEvent("onChange", a);
        return this;
    },
    set: function (a) {
        if (typeof a == "string") {
            a = a.toInt();
        }
        this.update(a ? this.width : 0);
    },
    animate: function (a) {
        this.animating = true;
        var b = this.button.offsetLeft,
            to = a ? this.width : 0;
        this.fx.options.duration = Math.abs(b - to) * this.steps;
        this.drag.detach();
        this.fx.stop().start(b, to).chain(function () {
            this.drag.attach();
            this.animating = false;
        }.bind(this));
    }
});