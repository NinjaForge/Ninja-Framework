/*
---

description: Monkey patching the Meio.Autocomplete to alter its behavior and extend it into doing more

authors:
 - Fábio Miranda Costa

requires:
 - Meio.Autocomplete

license: @TODO

...
*/

if(!Koowa) var Koowa = {};

(function($){

    // Support Form.Validator if present
    if(Form && Form.Validator) {
        var dataReady = Meio.Autocomplete.prototype.dataReady, hide = Meio.Element.List.prototype.hide;
        
        Meio.Autocomplete.prototype.dataReady = function(){
            dataReady.apply(this);
            
            if(this.elements.list.showing) {
                this.elements.field.node.addClass('ma-focus');
            } else {
                this.elements.field.node.removeClass('ma-focus');
            }
        };
        
        Meio.Element.List.prototype.hide = function(){
            hide.apply(this);
            
            $$('.ma-required.ma-focus').removeClass('ma-focus');
        };
    
        Form.Validator.add('ma-required', {
        	errorMsg: function(){
        	    return Form.Validator.getMsg('required');
        	},
        	test: function(element){
        	    var value = $(element.get('data-value')).get('value');
        		return value && value != 0;
        	}
        });
    }
    
    Koowa.Autocomplete = new Class({
    
        Extends: Meio.Autocomplete.Select,
        
        options: {
            requestOptions: {
                formatResponse: function(response){
                    return response.items || response;
                }
            }
        }
    
    });

})(document.id);