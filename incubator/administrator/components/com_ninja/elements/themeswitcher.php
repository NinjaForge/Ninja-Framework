<?php
/**
 * @category	Napi
 * @package		Napi_Parameter
 * @copyright	Copyright (C) 2007 - 2010 NinjaForge. All rights reserved.
 * @license		GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link     	http://ninjaforge.com
 */

class ComNinjaElementThemeSwitcher extends ComNinjaElementAbstract
{
	function fetchElement($name, $value, &$node, $control_name)
	{
		$doc = & JFactory::getDocument();
		$doc->addScript(JURI::root(true)."/media/napi/js/themeswitchertool.js");
		//$doc->addScript('http://jqueryui.com/themeroller/themeswitchertool/');
		$label 	= JText::_($node->attributes('label', 'Switch Theme'));
		$top 	= $node->attributes('top', '348px');
		$right 	= $node->attributes('right', '50%');
		$script = "
		jQuery.noConflict();
 
		jQuery(document).ready(function($){
			//Theme switcher to select the best icons
			$('body').themeswitcher({loadTheme: '$value', initialText: '$label', cookieName: '$name'}).find('a.jquery-ui-themeswitcher-trigger').css({
				position: 'fixed',
				top:      '$top',
				left:     '50%'
			});
		});";
		$doc->addScriptDeclaration($script);
		return '<div id="'.$control_name.$name.'"></div>';
	}

	/*function fetchTooltip($label, $description, &$xmlElement, $control_name='', $name='') {
		return false;
	}*/
}
