<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category	Napi
 * @package		Napi_Parameter
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

class NinjaElementModuleClassSfx extends NinjaElementAbstract
{
	function fetchTooltip($label, $description, &$xmlElement, $control_name='', $name)
	{
		if(!$description) $description = 'MODSFXTXT';
		if(!$name) $name = 'moduleclass_sfx';
		if(!$label||$label===$name) $label = 'MODSFX';
		$output = '<label id="'.$control_name.$name.'-lbl" for="'.$control_name.$name.'"';
		if ($description) {
			$output .= ' class="hasTip" title="'.JText::_($label).'::'.JText::_($description).'">';
		} else {
			$output .= '>';
		}
		$output .= JText::_( $label ).'</label>';

		return $output;
	}

	function fetchElement($name, $value, &$node, $control_name)
	{
		if(!$name) $name = 'moduleclass_sfx';
		$size = ( $node['size'] ? 'size="'.$node['size'].'"' : '' );
		$class = ( $node['class'] ? 'class="'.$node['class'].'"' : 'class="text_area"' );
        /*
         * Required to avoid a cycle of encoding &
         * html_entity_decode was used in place of htmlspecialchars_decode because
         * htmlspecialchars_decode is not compatible with PHP 4
         */
        $value = htmlspecialchars(html_entity_decode($value, ENT_QUOTES), ENT_QUOTES);

		return '<input type="text" name="'.$control_name.'['.$name.']" id="'.$control_name.$name.'" value="'.$value.'" '.$class.' '.$size.' />';
	}
}