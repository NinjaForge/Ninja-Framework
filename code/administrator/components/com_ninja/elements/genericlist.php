<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: genericlist.php 794 2011-01-10 18:44:32Z stian $
 * @category	Napi
 * @package		Napi_Parameter
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

class ComNinjaElementGenericlist extends ComNinjaElementAbstract
{
	function fetchElement($name, $value, &$node, $control_name)
	{
		$class = ( $node['class'] ? 'class="'.$node['class'].'"' : 'class="value"' );

		$options = array ();
		foreach ($node->children() as $option)
		{
			$val	= $option['value'];
			$text	= (string)$option;
			$options[] = JHTML::_('select.option', $val, JText::_($text));
		}

		return JHTML::_('select.genericlist',  $options, $this->name, $class, 'value', 'text', $value, $this->id);
	}
}
