<?php
/**
 * @category	Napi
 * @package		Napi_Parameter
 * @copyright	Copyright (C) 2007 - 2010 NinjaForge. All rights reserved.
 * @license		GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link     	http://ninjaforge.com
 */

class ComNinjaElementHideGroup extends ComNinjaElementAbstract
{
	function fetchToolTip()
	{
		return false;
	}
	
	function fetchElement($name, $value, &$node, $control_name)
	{	
		$doc = & JFactory::getDocument();
		//$doc->addScript("http://ajax.googleapis.com/ajax/libs/jquery/1.3/jquery.min.js");
		
		$script = "
			jQuery(document).ready(function($){
				setTimeout(function () { $('#$control_name$name').closest('.panel').$value(); }, 100);
				$('#$control_name$name').closest('.panel').css('visibility', 'hidden');
			});";
		$doc->addScriptDeclaration($script);
		return '<input type="hidden" name="'.$control_name.'['.$name.']" id="'.$control_name.$name.'" value="'.$value.'" />';	
	}
}