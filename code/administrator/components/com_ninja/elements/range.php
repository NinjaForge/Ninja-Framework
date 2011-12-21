<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: range.php 895 2011-02-20 01:11:58Z stian $
 * @category	Napi
 * @package		Napi_Parameter
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

class ComNinjaElementRange extends ComNinjaElementText
{
	public function fetchElement($name, $value, &$node, $control_name)
	{
		//@TODO make this an reusabe MooTools class
		KFactory::get('admin::com.ninja.helper.default')->js("
			window.addEvent('domready', function(){
				var input = $('".$this->id."');
				if(input) {
					var status = new Element('small', {
						text: input.value + '%',
						styles: {
							marginLeft: '2%',
							padding: '3px',
							position: 'absolute',
							top: '28%'
						},
						'class': 'value'
					}).inject(input, 'after'), update = function(){
						status.set('text', this.value + '%');
					};

					['change', 'keydown', 'keyup', 'click', 'focus', 'blur', 'mousewheel'].each(function(event){
						input.addEvent(event, update);
					});
					input.set('type', 'range').set('min', 0).set('max', 100);
					if(input.type != 'range') {
						input.set('type', 'hidden');
						var slider = new Element('div', {
							'class': 'slider'
						})
							.grab(new Element('div', {'class': 'knob'}))
							.inject(input, 'after');
							
						// Create the new slider instance
					    new Slider(slider, slider.getElement('.knob'), {
					        steps: 100,
					        range: [0, 100],
					        initialStep: input.value,
					        onChange: function(value){
					        	input.set('value', value);
					        	status.set('text', value + '%');
					        }
					    });
					    status.setStyles({
					    	marginLeft: '140px',
					    	padding: 0,
					    	top: '36%'
					    });
					}
				}
			});
		");

		return parent::fetchElement($name, $value, $node, $control_name);
	}
}