<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: textarea.php 794 2011-01-10 18:44:32Z stian $
 * @category	Napi
 * @package		Napi_Parameter
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

class ComNinjaElementTextarea extends ComNinjaElementAbstract
{
	public function fetchElement($name, $value, &$node, $control_name)
	{
		$doc = & JFactory::getDocument();
		$size = ( $node['size'] ? 'size="'.$node['size'].'"' : '' );
		$jqui = ( $node['ui'] ? 'text ui-widget-content ui-corner-all preview ' : 'text_area preview' );
		$class = ( $node['class'] ? ' class="'.$jqui.$node['class'].' value"' : ' class="'.$jqui.' value"' );
		$placeholder = ( $node['placeholder'] ? ' placeholder="'.$node['placeholder'].'"' : ' ' );
		$brackets = "\\";
		$preview = ( $node['preview'] ? true : false );
		if($preview&&!defined($control_name.(string)$node['type']))
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
			define($control_name.(string)$node['type'], 1);
		}
		$value = str_replace('\n', "\n", $value);

		return '<textarea name="'.$this->name.'" id="'.$this->id.'"'.$placeholder.' '.$class.' '.$size.'>'.$value.'</textarea>';
	}
}