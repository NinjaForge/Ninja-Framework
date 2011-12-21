<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: markitup.php 1399 2011-11-01 14:22:48Z stian $
 * @category	Napi
 * @package		Napi_Parameter
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

class NinjaElementMarkitup extends NinjaElementAbstract
{
	function fetchElement($name, $value, &$node, $control_name)
	{
		$doc = JFactory::getDocument();
		$doc->addScript(JURI::root(true).'/media/napi/js/jquery.markitup.pack.js');
		$doc->addScript(JURI::root(true).'/media/napi/js/markitup.set.js');
		$doc->addStyleSheet(JURI::root(true).'/media/napi/css/markitup.css');
		$script = "
		jQuery(document).ready(function($)	{
			$('#$control_name$name').markItUp();	
		});
		";
		$doc->addScriptDeclaration($script);
		$rows = $node['rows'];
		$cols = $node['cols'];
		$class = ( $node['class'] ? 'class="'.$node['class'].'"' : 'class="text_area"' );
		// convert <br /> tags so they are not visible when editing
		$value = str_replace('<br />', "\n", $value);

		return '<textarea name="'.$control_name.'['.$name.']" cols="'.$cols.'" rows="'.$rows.'" '.$class.' id="'.$control_name.$name.'" >'.$value.'</textarea>';
	}
}
