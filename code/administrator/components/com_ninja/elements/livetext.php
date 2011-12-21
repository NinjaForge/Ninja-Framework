<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: livetext.php 794 2011-01-10 18:44:32Z stian $
 * @category	Napi
 * @package		Napi_Parameter
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

class ComNinjaElementLiveText extends ComNinjaElementAbstract
{
	function fetchElement($name, $value, &$node, $control_name)
	{
		$doc = & JFactory::getDocument();
		$size = ( $node['size'] ? 'size="'.$node['size'].'"' : '' );
		$jqui = ( $node['ui'] ? 'text ui-widget-content ui-corner-all preview ' : 'text_area preview' );
		$class = ( $node['class'] ? ' class="'.$jqui.$node['class'].'"' : ' class="'.$jqui.'"' );
		$placeholder = ( $node['placeholder'] ? ' placeholder="'.$node['placeholder'].'"' : ' ' );
		$brackets = "\\";
		$preview = ( $node['preview'] ? true : false );
		if($preview&&!defined($control_name.$node['type']))
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
			define($control_name.$node['type'], 1);
		}
        /*
         * Required to avoid a cycle of encoding &
         * html_entity_decode was used in place of htmlspecialchars_decode because
         * htmlspecialchars_decode is not compatible with PHP 4
         */
        $value = htmlspecialchars(html_entity_decode($value, ENT_QUOTES), ENT_QUOTES);

		return '<span class="ui-helper-inherit ui-state-default"><input type="text" name="'.$control_name.'['.$name.']" id="'.$control_name.$name.'"'.$placeholder.' value="'.$value.'" '.$class.' '.$size.' /></span>';
	}
}