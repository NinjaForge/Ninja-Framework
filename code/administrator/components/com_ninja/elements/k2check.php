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

/**
 * @package RocketTheme
 * @subpackage rokstories.elements
 */
class ComNinjaElementK2check extends ComNinjaElementAbstract {
	

	function fetchElement($name, $value, &$node, $control_name)
	{
		if (defined('K2_CHECK')) return;
		
		$k2 = JPATH_SITE.DS."components".DS."com_k2".DS."k2.php";
		
		if (!file_exists($k2)) {
			
			define('K2_CHECK', 0);
			$warning_style = "style='background: #FFF3A3;border: 1px solid #E7BD72;color: #B79000;display: block;padding: 8px 10px;'";
			$list = '#k2-label, #paramscatfilter0, #paramscategory_id, #paramsFeaturedItems, #paramsitemImgSize, #paramsk2_check-lbl';
			//TODO- put this in a language file
			return "<span $warning_style><strong>K2 Component</strong> Not Found. In order to use the <strong>K2 Content</strong> type, you will need to <a href=\"http://k2.joomlaworks.gr\" target=\"_blank\">download and install it</a>.</span>";
		} else {
			define('K2_CHECK', 1);
			$success_style = "style='background: #d2edc9;border: 1px solid #90e772;color: #2b7312;display: block;padding: 8px 10px;'";
			//TODO- put this in a language file			
			return "<span $success_style><strong>K2 Component</strong> has been found and is available to use.</span>";
		}
	}
}