<?php
/**
 * @category	Napi
 * @package		Napi_Parameter
 * @copyright	Copyright (C) 2007 - 2010 NinjaForge. All rights reserved.
 * @license		GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link     	http://ninjaforge.com
 */

class ComNinjaElementSlider extends ComNinjaElementAbstract
{
	function fetchTooltip($label, $description, &$node, $control_name, $name) 
	{
		
		return '';
	}

	function fetchElement($name, $value, &$node, $control_name)
	{	
		$return  = '</tbody></table></div></div>';
		
		jimport('joomla.html.pane');
        // TODO: allowAllClose should default true in J!1.6, so remove the array when it does.
		$panel 		= &JPane::getInstance('tabs');
		$countParam = $node['counter'];
		$count 		= $this->_parent->get($countParam);
		$return    .= $panel->startPane('dynLoop');
		$return .= $panel->startPanel($node['label'], $name);
		$return .= 'Hi thar';
		$return .= $panel->endPanel();
		$return .= $panel->startPanel($node['label'], $name.'1');
		$return .= 'Hi thar';
		$return .= $panel->endPanel();
		$return .= $panel->startPanel($node['label'], $name.'2');
		$return .= 'Hi thar';
		$return .= $panel->endPanel();
		$return .= $panel->endPane();
		$return .= '<div><div><table width="100%" class="paramlist admintable" cellspacing="1"><tbody>';
			
		return $return;
	}
}