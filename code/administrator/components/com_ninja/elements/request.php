<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: request.php 1018 2011-04-12 13:11:15Z stian $
 * @category	Napi
 * @package		Napi_Parameter
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 *
 * usage: <element name="theme" type="request" key="name" request="name" get="admin::com.ninjaboard.model.themes" default="chameleon" load="&amp;option=com_ninjaboard&amp;view=theme&amp;layout=settings&amp;format=raw" />
 *
 */

class ComNinjaElementRequest extends ComNinjaElementGetlist
{
	public function fetchElement($name, $value, &$node, $control_name)
	{
		$key = (string)$node['key'];
		$request = isset($node['request']) ? $node['request'] : $key;
		
		//@TODO make the following optional
		$current = KRequest::has('get.id') ? '&'.KRequest::get('get.view', 'cmd').'='.KRequest::get('get.id', 'int') : false;
	
		//@TODO make this an reusabe MooTools class
		KFactory::get('admin::com.ninja.helper.default')->js("
			window.addEvent('domready', function(){
				var input = $('".$this->id."'), load = new URI(".json_encode(JRoute::_((string)$node['load'].$current, false))."), runway = new Element('div', {text: load}).set('load', {onSuccess: function(){window.fireEvent('domupdate')}}), fieldset = input.getParent('fieldset');
				if(input) {
					fieldset.grab(runway, 'after');
					
					input.addEvent('change', function(){
						runway.empty().load(load.setData({".$key.": this.value}, true).toString());
					}).fireEvent('change');
				}
			});
		");
		
		return parent::fetchElement($name, $value, $node, $control_name);
	}
}
