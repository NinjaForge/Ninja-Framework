<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @package     gantry
 * @subpackage  admin.elements
 * @version		2.0.3 January 10, 2010
 * @author		RocketTheme http://www.rockettheme.com
 * @copyright 	Copyright (C) 2007 - 2010 RocketTheme, LLC
 * @license		http://www.gnu.org/licenses/gpl.html GNU/GPLv3 only
 *
 * Gantry uses the Joomla Framework (http://www.joomla.org), a GNU/GPLv2 content management system
 *
 */
/**
 * @category	Napi
 * @package		Napi_Parameter
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

class NinjaElementTogglePanel extends NinjaElementAbstract
{
	function fetchElement($name, $value, &$node, $control_name)
	{
		$output = "";
		$document = &JFactory::getDocument();
		
// 		$presetSaver = $node->attributes('preset-saver');
// 		if (isset($presetSaver) && $presetSaver == "true") {
// 			$saver  = "<span class='preset-saver'>";
// 			$saver .= "		<a href='#' class='hasTip' title='".JText::_('COM_NINJA_PRESET_SAVER_DESC')."'>";
// 			$saver .= "			<span>".JText::_('COM_NINJA_PRESET_SAVER')."</span>";
// 			$saver .= "		</a>";
// 			$saver .= "</span>";
// 		}
// 		else $saver = '';
		
// 		$this->template = end(explode(DS, $gantry->templatePath));

    $document->addScript(JURI::root(true)."/media/com_ninja/js/elements/togglepanel/togglepanel.js");
    //this will determine if our toggle is set to on or off
    if ($value)
      $toggleClass = ' toggleon';
    else
      $toggleClass = '';
      
    if ($node['notoggle'] == 'on')
      $toggleCode = '';
    else
      $toggleCode = "<span class='switch$toggleClass'></span>";
      
		$opensTable = "<table class='paramlist admintable' width='100%' cellspacing='1'><tbody><tr><td>";
		$closeTable = "</td></tr></tbody></table>";
		$surroundOpens = "<div id='nj-tp-$name' class='nj-tp-surround'>";
		$surroundClose = "</div>";
		$title = "<h3 class='nj-tp-title' rel='$name'>".JText::_($node['tplabel'])."$toggleCode</h3>";
		$innerOpens = "<div id='nj-tp-$name-inner' class='nj-tp-inner'>";
		$innerClose = "</div>";
		$hiddenValue = '<input type="hidden" value="'.$value.'" name="'.$control_name.'['.$name.']"/>';
		
    //append the control_name so we can have multiple groups of spacers for different parameter groups 		
		if (!defined("NTOGGLESPACER".$control_name)) {
			$output = $closeTable . $surroundOpens . $title . $hiddenValue . $innerOpens . $opensTable;
			define("NTOGGLESPACER".$control_name, 1);
		} else {
			$output = $closeTable . $innerClose . $surroundClose . $surroundOpens . $title . $hiddenValue . $innerOpens . $opensTable;
		}
		
		return $output;
		
	}
}