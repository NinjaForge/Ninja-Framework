<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category	Napi
 * @package		Napi_Parameter
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

class NinjaElementGetlist extends NinjaElementAbstract
{
	function fetchElement($name, $value, &$node, $control_name)
	{
		
		$attr = array('class' => 'value');
		$namesuffix = '';
		
		$key  = isset($node['val']) ? (string)$node['val'] : 'id';
		$text = isset($node['key']) ? (string)$node['key'] : 'title';
				
		
		if(isset($node['multi']) && (string)$node['multi']){
			$attr['multiple'] = 'multiple';
			$namesuffix = '[]';
			$value = explode('|', $value);			
		}

		$options = array();
		foreach($node->children() as $element => $child)
		{
			$options[] = ($element == 'option') ? JHTML::_('select.option', null, (string)$child, $key, $text, false, array('class' => 'value')) : JHTML::_('select.optgroup', (string)$child, $key, $text);
		}
		
		foreach($this->getList() as $item)
		{
			if($item->$key == false)
			{
				$options[] = JHTML::_('select.optgroup', $item->$text, $key, $text);
			}
			else
			{
				$options[] = JHTML::_('select.option', $item->$key, $item->$text, $key, $text, false, array('class' => 'value'));
			}
		}

		return JHTML::_('select.genericlist', $options, $this->name.$namesuffix, $attr, $key, $text, $value, $this->id, true);
	}
}
