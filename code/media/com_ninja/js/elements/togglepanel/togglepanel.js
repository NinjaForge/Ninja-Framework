window.addEvent('domready', function() {
        $$('.nj-tp-title').each(function (a, i) {
            var b = a.getNext().getNext(),
                i = a.getNext(),
                rel = a.getProperty('rel');
            if (b.hasClass('nj-tp-inner')) {
                var c = new Fx.Slide(b, {
                    duration: 400 + ((b.offsetHeight / 400).toInt() * 200),
                    wait: false,
                    onStart: function () {
                        if (this.open) {
                            this.wrapper.setStyle('overflow', 'hidden');
                            a.getElement('.switch').removeClass('toggleon');
                            i.value = 0;
                        } else {
                            a.getElement('.switch').addClass('toggleon');
                            i.value = 1;

                        }
                    },
                    onComplete: function () {
                        if (this.now[1] == 0) {
                            this.wrapper.setStyle('overflow', 'visible');
                        } else {
                            //a.getElement('.switch').removeClass('toggleon');
                        }
                    }
                });
            };
            a.addEvent('click', function () {
                c.toggle()
            })
          if (i.value == 0){
            c.toggle();
          }
        });
});//window.addEvent  