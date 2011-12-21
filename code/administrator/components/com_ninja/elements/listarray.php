<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: listarray.php 794 2011-01-10 18:44:32Z stian $
 * @category	Napi
 * @package		Napi_Parameter
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

class ComNinjaElementListArray extends ComNinjaElementAbstract
{
	function fetchElement($name, $value, &$node, $control_name)
	{
		$class = ( $node['class'] ? 'class="'.$node['class'].'"' : 'class="inputbox"' );

		$options = array ();
		foreach ($node->children() as $option)
		{
			$val	= $option['value'];
			$text	= $option->data();
			$options[] = JHTML::_('select.option', $val, JText::_($text));
		}

		//return JHTML::_('select.genericlist',  $options, ''.$control_name.'['.$name.']', $class, 'value', 'text', $value, $control_name.$name);
		return $node->children();
	}
}
