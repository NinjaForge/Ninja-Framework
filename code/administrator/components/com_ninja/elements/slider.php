<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: slider.php 794 2011-01-10 18:44:32Z stian $
 * @category	Napi
 * @package		Napi_Parameter
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

class ComNinjaElementSlider extends ComNinjaElementAbstract
{
	function fetchElement($name, $value, &$node, $control_name)
	{
		$doc = & JFactory::getDocument();
		$size = ( $node['size'] ? 'size="'.$node['size'].'"' : '' );
		$jqui = ( $node['ui'] ? 'text ui-widget-content ui-corner-all preview ' : 'text_area preview' );
		$class = ( $node['class'] ? ' class="'.$jqui.$node['class'].'"' : ' class="'.$jqui.'"' );
		$placeholder = ( $value ? ' placeholder="'.$value.'"' : ' ' );
		$brackets = "\\";
		$preview = ( $node['preview'] ? true : false );
		if($preview)
		{
			$doc->addScript(JURI::root(true)."/media/napi/js/jquery.magicpreview.pack.js");
			$script = "
				jQuery(document).ready(function($){
					$('.preview:enabled').livequery('focus', function(){
						$(this).magicpreview('mp_'); 
					});
				});
			";
			$doc->addScriptDeclaration($script);
		}
		if(!defined(( $node['instance'] ? $node['instance'] : $name )))
		{
			$script = "
				jQuery(document).ready(function($){
					$('.nf-slider".( $node['instanceclass'] ? '.'.$node['instanceclass'] : '.nf-slider-'.$name )."').livequery(function(){
						var _value = $(this).parent().find('.nf-slider-value input').val();
						$(this).slider({
							value: _value,
							min: ".( $node['min'] ? $node['min'] : '0' ).",
							max: ".( $node['max'] ? $node['max'] : '10' ).",
							step: ".( $node['step'] ? $node['step'] : '1' ).",
							slide: function(event, ui) {
								$(this).parent().find('.nf-slider-value input').val(ui.value);
							}
						});
					});
				});
			";
			$doc->addScriptDeclaration($script);
			define(( $node['instance'] ? $node['instance'] : $name ), 1);
		}
        /*
         * Required to avoid a cycle of encoding &
         * html_entity_decode was used in place of htmlspecialchars_decode because
         * htmlspecialchars_decode is not compatible with PHP 4
         */
        $value = htmlspecialchars(html_entity_decode($value, ENT_QUOTES), ENT_QUOTES);

		return '
		<span class="ui-helper-inherit ui-state-default nf-slider-value ui-corner-all">
			<input type="text" name="'.$control_name.'['.$name.']" id="'.$control_name.$name.'"'.$placeholder.' value="'.$value.'" '.$class.' '.$size.' />
		</span>
		<span class="nf-slider'.( $node['instanceclass'] ? ' '.$node['instanceclass'] : ' nf-slider-'.$name ).'"></span>';
	}
}