<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category	Napi
 * @package		Napi_Parameter
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

class NinjaElementSpinner extends NinjaElementAbstract
{
	function fetchElement($name, $value, &$node, $control_name)
	{
		/*
         * Required to avoid a cycle of encoding &
         * html_entity_decode was used in place of htmlspecialchars_decode because
         * htmlspecialchars_decode is not compatible with PHP 4
         */
        $value = htmlspecialchars(html_entity_decode($value, ENT_QUOTES), ENT_QUOTES);
		$doc = & JFactory::getDocument();
		$size = ( $node['size'] ? 'size="'.$node['size'].'"' : '' );
		$jqui = ( $node['ui'] ? 'text ui-widget-content ui-corner-all preview ' : 'text_area preview' );
		$class = ( $node['class'] ? ' class="'.$jqui.$node['class'].' ui-spinner"' : ' class="ui-spinner '.$jqui.'"' );
		$placeholder = ( $node['placeholder'] ? ' placeholder="'.$node['placeholder'].'"' : ' ' );
		
		$options = null;
		if($value) { $options[] = 'start: \''.$value.'\''; }
		if($node['min']==='0'||$node['min']) { $options[] = 'min: \''.$node['min'].'\''; }
		if($node['max']) { $options[] = ' max: \''.$node['max'].'\''; }
		if($node['step']) { $options[] = ' step: \''.$node['step'].'\''; }
		if($node['decimals']) { $options[] = ' decimals: \''.$node['decimals'].'\''; }
		$opt = null;
		if($options) {$opt 	   = '{'.implode(',', $options).'}'; }

		if(!defined(( $node['instance'] ? $node['instance'] : $name )))
		{
			$script = "
				jQuery(document).ready(function($){
					$('#$control_name$name').spinner($opt);
				});
			";
			$doc->addScriptDeclaration($script);
			define(( $node['instance'] ? $node['instance'] : $name ), 1);
		}

		return ' <span class="ui-helper-inherit ui-state-default nj-spinner-value ui-corner-all"><input type="text" name="'.$control_name.'['.$name.']" id="'.$control_name.$name.'"'.$placeholder.' value="'.$value.'" '.$class.' '.$size.' /></span>';
	}
}