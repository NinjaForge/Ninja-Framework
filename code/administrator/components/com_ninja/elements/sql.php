<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category	Napi
 * @package		Napi_Parameter
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

class NinjaElementSQL extends NinjaElementAbstract
{
	function fetchElement($name, $value, &$node, $control_name)
	{
		 // Base name of the HTML control.
		$ctrl  = $control_name .'['. $name .']';
		$db			= & JFactory::getDBO();
		$db->setQuery($node['query']);
		$key = ($node['key_field'] ? $node['key_field'] : 'value');
		$val = ($node['value_field'] ? $node['value_field'] : $name);
		// Construct the various argument calls that are supported.
        $attribs       = ' ';
        if ($v = $node['size']) {
                $attribs       .= 'size="'.$v.'"';
        }
        if ($v = $node['class']) {
                $attribs       .= 'class="'.$v.'"';
        } else {
                $attribs       .= 'class="inputbox"';
        }
        if ($node['multiple'])
        {
                $attribs       .= ' multiple="multiple"';
                $ctrl          .= '[]';
        }
		return JHTML::_('select.genericlist',  $db->loadObjectList(), $ctrl, $attribs, $key, $val, $value, $control_name.$name);
	}
}