/*=
	name: Switch
	version: 0.1
	description: On-off iPhone style switch button.
	license: MooTools MIT-Style License (http://mootools.net/license.txt)
	copyright: Valerio Proietti (http://mad4milk.net)
	authors: Valerio Proietti (http://mad4milk.net)
	requires: MooTools 1.11, Touch 0.1+ (http://github.com/kamicane/mootools-touch)
	notes: ported to MooTools 1.11 with some modifications by Djamil Legato (w00fzIT [at] gmail.com)
=*/
//
//eval(function(p,a,c,k,e,r){e=function(c){return(c<a?'':e(parseInt(c/a)))+((c=c%a)>35?String.fromCharCode(c+29):c.toString(36))};if(!''.replace(/^/,String)){while(c--)r[e(c)]=k[c]||e(c);k=[function(e){return r[e]}];e=function(){return'\\w+'};c=1};while(c--)if(k[c])p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c]);return p}('k U=j 1w({q:{\'v\':\'.w-1x\',\'18\':3,\'B\':1y,\'V\':19.1z.1A.1B,\'C\':r},1C:8(c,d){7.1D(d);7.9=$(c)||r;i(!7.9)O r;7.t=j D(\'E\',{\'v\':\'F-w-t\'});7.l=j D(\'E\',{\'v\':\'F-w-l\'}).s(7.t);7.1a=j D(\'E\',{\'v\':\'F-w-1a\'}).s(7.l);7.W=j D(\'E\',{\'v\':\'F-w-1E\'}).s(7.l.X());7.n=j D(\'E\',{\'v\':\'F-w-n\'}).s(7.l);i(7.9.Y().1F()==\'1G\'){7.t.s(7.9.Y(),\'1b\');7.9.Y().s(7.l.X()).1c({\'P\':\'1d\',\'Q\':\'-1e\',\'1f\':0})}G{7.t.s(7.9,\'1b\');7.9.s(7.l.X()).1c({\'P\':\'1d\',\'Q\':\'-1e\',\'1f\':0})}k f=7;7.9.1H({\'y\':7.y.o(7),\'z\':7.z.o(7),\'H\':8(a){f.I(a,J)}});7.K=7.l.Z(\'K\').L();7.10=r;k g=7.n.Z(\'m\').L(),11=7.l.Z(\'m\').L();7.12=7.q.18;7.1g=11-g-7.12;7.m=11-g;7.K=7.K;7.13=7.m/2;7.1h=7.q.B/7.m;7.14=!!(7.9.1i);7.I(7.14,J);7.u=j 19.1I({B:7.q.B,V:7.q.V,\'1J\':r});7.u.H=8(a){i(!$1K(a))a=7.u.1L;7.R(a)}.o(7);7.u.1M=7.u.H;7.p=j 1j(7.n);k h=8(){i(!7.S)7.1k()}.o(7);7.p.A(\'15\',8(x){7.9.C();7.P=7.n.T}.o(7));7.p.A(\'1N\',8(x){7.R(7.P+x)}.o(7));7.p.A(\'1O\',8(x){k a=7.n.T;k b=(a>7.13)?J:r;7.I(b)}.o(7));7.p.A(\'1l\',h);7.M=j 1j(7.W);7.M.A(\'1l\',h);7.M.A(\'15\',8(e){7.9.C()}.o(7));O 7},y:8(){7.t.1P(\'1m\');7.p.y();7.M.y()},z:8(){7.t.1Q(\'1m\');7.p.z();7.M.z()},R:8(x){i(x<7.12)x=0;G i(x>7.1g)x=7.m;7.W.16.Q=x-7.m+\'N\';7.n.16.Q=x+\'N\';7.1n(x)},1n:8(x){k a=\'0 0\';k b=-7.K;k c={\'1o\':\'0 \'+((7.10&&7.q.C)?(b*6):(b*3)),\'1p\':\'0 \'+((7.10&&7.q.C)?(b*5):(b*2))};i(x==0)a=c.1o+\'N\';G i(x==7.m)a=c.1p+\'N\';G a=\'0 \'+(b*4)+\'N\';7.l.16.1R=a},1k:8(){7.I((7.n.T>7.13)?r:J)},I:8(a,b){i(1q a==\'1r\')a=a.L();i(7.S)O 7;i(b)7.H(a);G 7.1s(a);7.9.1i=a;7.9.1S=(!a)?0:1;7.14=a;7.9.1t(\'1u\',a);7.1t(\'1u\',a);O 7},H:8(a){i(1q a==\'1r\')a=a.L();7.R(a?7.m:0)},1s:8(a){7.S=J;k b=7.n.T,17=(a)?7.m:0;7.u.q.B=1T.1U(b-17)*7.1h;7.p.z();7.u.1V().15(b,17).1W(8(){7.p.y();7.S=r}.o(7))}});U.1v(j 1X);U.1v(j 1Y);',62,123,'|||||||this|function|check|||||||||if|new|var|sides|width|button|bind|drag|options|false|inject|container|fx|class|iphone||attach|detach|addEvent|duration|focus|Element|div|ninja|else|set|change|true|height|toInt|switchButton|px|return|position|left|update|animating|offsetLeft|Toggle|transition|switcher|getFirst|getParent|getStyle|focused|fullWidth|min|half|state|start|style|to|radius|Fx|wrapper|after|setStyles|absolute|100000px|top|max|steps|checked|Touch|toggle|cancel|disabled|updateSides|off|on|typeof|string|animate|fireEvent|onChange|implement|Class|checkbox|250|Transitions|Sine|easeInOut|initialize|setOptions|switch|getTag|label|addEvents|Base|wait|chk|now|increase|move|end|removeClass|addClass|backgroundPosition|value|Math|abs|stop|chain|Options|Events'.split('|'),0,{}))



var Toggle = new Class({
    options: {
        class: ".iphone-checkbox",
        radius: 3,
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
            class: "ninja-iphone-container"
        });
        this.sides = (new Element("div", {
            class: "ninja-iphone-sides"
        })).inject(this.container);
        this.wrapper = (new Element("div", {
            class: "ninja-iphone-wrapper"
        })).inject(this.sides);
        this.switcher = (new Element("div", {
            class: "ninja-iphone-switch"
        })).inject(this.sides.getFirst());
        this.button = (new Element("div", {
            class: "ninja-iphone-button"
        })).inject(this.sides);
        if (this.check.getParent().getTag() == "label") {
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
        this.fx = new(Fx.Base)({
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
        this.switcher.style.left = x - this.width + "px";
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
Toggle.implement(new Options);
Toggle.implement(new Events);