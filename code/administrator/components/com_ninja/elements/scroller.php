<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: scroller.php 1399 2011-11-01 14:22:48Z stian $
 * @category	Napi
 * @package		Napi_Parameter
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

class NinjaElementScroller extends NinjaElementAbstract
{
	function fetchElement($name, $value, &$node, $control_name)
	{
		$order  = $node->attributes('order', '0');
		$target = $node['target'];
		$filter = $node['filter'];
		$fadeout = $node['fadeout'];
		$fadein = $node['fadein'];
		$update = $node['update'];
		$thumbpath = ( $node['thumbpath'] ? JURI::root(true).$node['thumbpath'] : JURI::root(true).'/media/napi/img/tmpl/' );
		
		$options 	= array ();
		$children 	= '';
		$count		= 0;
		$multiplier	= '128';
		$leftMargin	= '0';
		foreach ($node->children() as $option)
		{
			$val	= $option['value'];
			$text	= (string) $option;
			$thumb	= ( $option['thumbnail'] ? 'style="background-image: url('.$thumbpath.$option['thumbnail'].');"' : '' );
			$style  = ( $thumb ? 'thumb ui-corner-all' : '' );
			$active = ( $value===$val ? ' active ' : '' );
			if($active) { $leftMargin = $count * $multiplier; } elseif($count===0||$count) { $count++; };
			$children  .= ' <div id="'.$val.'" class="'.$style.$active.'"><img src="'.$thumbpath.$option['thumbnail'].'" title="'.JText::sprintf('%s plugin', $val).'" alt="'.JText::sprintf('%s plugin', $val).'" class="ui-corner-all"><span>'.JText::_($text).'</span></div>';
		}
		$html    = '
<div id="'.$control_name.$name.'" class="scrollable-wrap ui-corner-all panel">
	<!-- navigator -->
	<div class="navi"></div>
	
	<!-- prev link -->
	<a class="prevPage ui-icon ui-icon-circle-triangle-w"></a>
	
	<!-- root element for scrollable -->
	<div class="scrollable">
		<!-- root element for the items -->
		<div class="items">
			'.$children .'
		</div>
		
	</div>
	
	<!-- next link -->
	<a class="nextPage ui-icon ui-icon-circle-triangle-e"></a>
	<!-- power tag: let rest of the page layout normally --> 
	<input type="hidden" name="'.$control_name.'['.$name.']" id="'.$control_name.$name.'-val" value="'.$value.'" />
	<div style="display:block;clear:both;width:100%;"></div>
	<!--<div class="scrollable-status">Test</div>-->
</div>';
		
		$doc = & JFactory::getDocument();
		$doc->addScript(JURI::root(true)."/media/napi/js/jquery.scrollable-1.0.2.min.js");
		$doc->addScript(JURI::root(true)."/media/napi/js/jquery.mousewheel.js");
		$doc->addStyleSheet(JURI::root(true).'/media/napi/css/scrollable-minimal.css');
	
		$script = "
		jQuery(function($){
			$(window).load(function () {
					$('#$control_name$name div.scrollable .items div.active').triggerHandler('click');
				});
			$('#$control_name$name div.scrollable .items div').live('click', function() {
				$('#$control_name$name-val').val($('#$control_name$name div.scrollable .items div.active').attr('id'));
			});
			$('#$control_name$name').prependTo('#menu-pane');
			$('#$control_name$name div.scrollable').scrollable({size: Math.floor($('#$control_name$name .scrollable').width()/$('#$control_name$name .scrollable .items > :first').outerWidth(true)) });
	    	
	    	//Initiate first theme
	    	var api = $('#$control_name$name div.scrollable').scrollable();
	    	var mainNavLinks = $('#$control_name$name div.scrollable .items .thumb');
			mainNavLinks.one('click', function(){
				api.seekTo(mainNavLinks.index(this));
			});
		});";
		
		$doc->addScriptDeclaration($script);
		$doc->addStyleDeclaration('.hidden{display:none;} .jpane-slider.content{overflow:hidden;}');
		return $html;
	}
	
	function fetchToolTip()
	{
		return false;
	}
}