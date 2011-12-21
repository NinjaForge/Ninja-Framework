<?php
/**
* K2 Check, Custom Param
*
 * @package NinjaForge
 * @subpackage com_ninja.elements
 * @version   1.0 December 17, 2010
 * @author    Ninja Forge http://ninjaforge.com
 * @copyright Copyright (C) 2010 Ninja Forge
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
* package RocketTheme
* subpackage rokstories.elements
* version   1.9 September 1, 2010
* author    RocketTheme http://www.rockettheme.com
* copyright Copyright (C) 2007 - 2010 RocketTheme, LLC
* license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/


// no direct access
defined('_JEXEC') or die();

/* a basic check for a component is isntalled or not */

/**
 * @package RocketTheme
 * @subpackage rokstories.elements
 */
class ComNinjaElementComponentcheck extends ComNinjaElementAbstract {
	

	function fetchElement($name, $value, &$node, $control_name)
	{
	
		$check = $node['check'];
		$download = $node['download'];
		$text = $node['text'];
		$define = strtoupper($check).'_CHECK';
		
		
		if (defined($define)) return;
		
		$file = JPATH_SITE.DS."components".DS."com_".$check.DS.$check.".php";
		
		if (!file_exists($file)) {
			
			define($define, 0);
			$warning_style = "style='background: #FFF3A3;border: 1px solid #E7BD72;color: #B79000;display: block;padding: 8px 10px;'";
			//TODO- put this in a language file
			return "<span $warning_style><strong>Component ".$text."</strong> Not Found. In order to use this parameter, you will need to <a href=\"".$download."\" target=\"_blank\">download and install the extension</a>.</span>";
		} else {
			define($define, 1);
			$success_style = "style='background: #d2edc9;border: 1px solid #90e772;color: #2b7312;display: block;padding: 8px 10px;'";
			//TODO- put this in a language file			
			return "<span $success_style><strong>Component ".$text."</strong> has been found and is available to use.</span>";
		}
	}
}