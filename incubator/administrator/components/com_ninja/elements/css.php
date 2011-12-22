<?php
/**
 * @category	Napi
 * @package		Napi_Parameter
 * @copyright	Copyright (C) 2007 - 2010 NinjaForge. All rights reserved.
 * @license		GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link     	http://ninjaforge.com
 */
 
class ComNinjaElementCss extends ComNinjaElementAbstract
{
	public function fetchTooltip($label, $description, &$node, $control_name, $name)
	{
		
		if ( $this->_parent->get((string)$node['if']) ) {
			return parent::fetchTooltip($label, $description, &$node, $control_name, $name);
		}	
			
		return false;
	}
	
	function fetchElement($name, $value, &$node, $control_name)
	{	
		$return = '';
		$rows = $node['rows'];
		$cols = $node['cols'];
		$class = ( $node['class'] ? 'class="'.$node['class'].' value"' : 'class="text_area value"' );
		// convert <br /> tags so they are not visible when editing
		$value = str_replace('<br />', "\n", $value);
		
		if ( $this->_parent->get((string)$node['if']) ) {
			return '<textarea name="'.$control_name.'['.$this->group.']['.$name.']" cols="'.$cols.'" rows="'.$rows.'" '.$class.' id="'.$control_name.$this->group.$name.'" >'.$value.'</textarea>';
		}
		
		return false;
	}
}