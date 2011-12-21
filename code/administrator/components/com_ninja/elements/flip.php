<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: flip.php 794 2011-01-10 18:44:32Z stian $
 * @category	Napi
 * @package		Napi_Parameter
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

class ComNinjaElementFlip extends ComNinjaElementAbstract
{
	function fetchElement($name, $value, &$node, $control_name)
	{	
		$doc = & JFactory::getDocument();
		$scope = $node['scope'];
		$class = $node['class'];
		$options = array ();
		$html    = '
<div id="'.$name.'" class="fg-buttonset fg-buttonset-single '.$class.'">
';			$i = 1;
			$c = count($node->children());
			foreach ($node->children() as $option)
			{
				$val	= $option['value'];
				$text	= $option->data();
				$active = ( $value==$val ? ' ui-state-active' : '' );
				$left   = ( $i===1 ? 'ui-corner-left' : '' );
				$right  = ( $i===$c ? 'ui-corner-right' : '' );
				$html  .= "<a href='#$val' rel='$val' class='all fg-button ui-state-default $left $right ui-priority-primary $active'>$text</a>";
			$i++;}
	//Save which button are active
	$html .= '<input type="hidden" name="'.$control_name.'['.$name.']" value="'.$value.'" class="all" /></div>';
		$script = "jQuery(document).ready(function($){
		
			var _self = $(\"#$control_name$name\").closest('$scope');
			$('#$control_name$name a:first').addClass('ui-corner-left');
			$('#$control_name$name a:last').addClass('ui-corner-right');
			
			//Get the keys
			var keys = [];
			$('#$control_name$name a').each(function(i){
				keys[i] = 'flip'+$(this).attr('rel');
			});
			//Get the active flip button
			var activeState = $(\"#$control_name$name a.ui-state-active\").attr('rel');
			
			//Set the values
			$(_self).find('.flip').each(function(i){ 
				$(this).closest('tr').addClass(keys[i]).addClass('fliphidden').find(':input').each(function(i){ 
					//$(this).attr('disabled', 'disabled'); 
				}); 
			});
			$(_self)
			.find('tr .flip:last').closest('tr').removeClass('fliphidden').addClass('flipcontainer');
			//all hover and click logic for buttons
			var startheight = $(_self).find('.flipcontainer').height();
			$('#$control_name$name .fg-button:not(.ui-state-disabled)')
			.hover(
				function(){ 
					$(this).addClass('ui-state-hover'); 
				},
				function(){ 
					$(this).removeClass('ui-state-hover'); 
				}
			)
			.live('click', function(){
			if($('$scope *').is(':animated'))return false;
				$(this).parents('.fg-buttonset-single:first').find('.fg-button.ui-state-active').removeClass('ui-state-active');
				if( $(this).is('.ui-state-active.fg-button-toggleable, .fg-buttonset-multi .ui-state-active') ){ 
					$(this).removeClass('ui-state-active'); 
				} else { 
					$(this).addClass('ui-state-active'); 
				}
				$(_self).find('tr.flipactive').removeClass('flipactive').addClass('fliphidden');
				var selector = 'flip'+$(this).attr('rel');
				$(_self).find('.'+selector).removeClass('fliphidden').addClass('fliptransparent');
				
				var content = $(this).closest('$scope').find('.'+selector).clone(true);
				var position = $(_self).find('.'+selector).offset();
				
				$(_self).find('.flipcontainer').append(content).css('opacity', 1).width($(_self).find('.flipcontainer').width());
				var curHeight = $(_self).find('.'+selector).height();
				$(_self).find('.flipcontainer').width($(_self).find('.'+selector).width()).flip({
					width: $(_self).find('.flipcontainer').width(),
					height:	curHeight,
					direction: 'tb',
					color: $('.ui-widget-header').css('background-color'),
					bgColor: $('.ui-widget-content').css('background-color'),
					transparent: $('.ui-widget-content').css('background-color'),
					//content: content,
					startheight: startheight,
					onAnimation: function(){
			
						//$(_self).find('.'+selector).height(startheight);
					},
					onEnd: function(){
						$(_self).find('.flipcontainer').fadeTo('normal', 0, function(){
								startheight = $(_self).find('.flipcontainer').height();
								$(_self).find('.flipcontainer').empty();
								$(_self).find('.'+selector).removeClass('fliptransparent').addClass('flipactive');
						});
						
					}
				}).css({
					top: position.top,
					left: position.left
				});
				$('#$control_name$name input').val($(this).attr('rel'));
				return false;
			});
			$('#$control_name$name a.ui-state-active').click();
		});";
		//$doc->addScriptDeclaration($nano);
		$doc->addScript(JURI::root(true).'/media/napi/js/idTabs.js');
		return $html;
	}
}