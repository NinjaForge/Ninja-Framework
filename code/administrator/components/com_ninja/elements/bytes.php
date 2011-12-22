<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category	Napi
 * @package		Napi_Parameter
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

class NinjaElementBytes extends NinjaElementText
{
	public function fetchElement($name, $value, &$node, $control_name)
	{
		//@TODO Number.implement needs to be in its own js file
		$this->getService('ninja:template.helper.document')->load('js', "
			window.addEvent('domready', function(){
				var input = $('".$this->id."');
				if(input) {
					Number.implement({
						bytes: function(){
							var units = ['B', 'kB', 'MB', 'GB', 'TB']
								bytes = Math.max(this, 0);

							pow = Math.floor((bytes ? Math.log(bytes) : 0) / Math.log(1024));
							pow = Math.min(pow, units.length - 1);

							bytes /= Math.pow(1024, pow);

							return bytes.toFixed(2) + units[pow];
						}
					});

					var status = new Element('small', {
						text: input.value.toInt().bytes(),
						styles: {
							marginLeft: '2%'
						}
					}).inject(input, 'after'), humanize = function(){
						var value = this.value.toInt().bytes();
						if(isNaN(this.value.toInt()) || this.value.toInt() < 1) value = " . json_encode(JText::_((string)$node['onzero'])) . ";
						status.set('text', value);
					};

					['change', 'keydown', 'keyup', 'click', 'focus', 'blur', 'mousewheel'].each(function(event){
						input.addEvent(event, humanize);
					});
					input.setStyles({width: '22%', padding: '1%'}).set('type', 'number').set('min', 0).set('step', 1024);
				}
			});
		");

		return parent::fetchElement($name, $value, $node, $control_name);
	}
}