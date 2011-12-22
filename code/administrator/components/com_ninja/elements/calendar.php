<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category	Napi
 * @package		Napi_Parameter
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

class NinjaElementCalendar extends NinjaElementAbstract
{
	public function fetchElement($name, $value, &$node, $control_name)
	{
		$format	= ( $node['format'] ? $node['format'] : 'yy-mm-dd' );
		$class	= $node['class'] ? 'class="'.$node['class'].' value"' : 'class="inputbox value"';

		$id   = $control_name.$name;
		$name = $control_name.'['.$this->group.']['.$name.']';
		
		$doc = & JFactory::getDocument();
		$script = "
				jQuery(function($){
					$('#$id').datepicker({showOn: 'button', buttonImage: '".JURI::root(true)."/templates/system/images/calendar.png', buttonImageOnly: true});
				});
			";
		$doc->addScriptDeclaration($script);
		$doc->addStyleDeclaration(".element img{
			position: absolute;
			bottom: 10px;
			margin-left: 1px;
		}
		.wrapper .chain-calendar { 
			position: relative;
		}
		.wrapper .chain-calendar input {
			margin-right: 7px;
		}
		.wrapper .chain-calendar img{
			bottom: 0px;
			right: -10px;
		}");
		
		return '<input type="text" '.$class.' name="'.$name.'" id="'.$id.'" value="'.htmlspecialchars($value, ENT_COMPAT, 'UTF-8').'" />';
	}
}
