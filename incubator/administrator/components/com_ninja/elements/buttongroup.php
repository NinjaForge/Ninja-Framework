<?php
/**
 * @version		$Id: buttongroup.php 365 2010-06-23 11:43:38Z stian $
 * @category	Napi
 * @package		Napi_Parameter
 * @copyright	Copyright (C) 2007 - 2010 NinjaForge. All rights reserved.
 * @license		GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link     	http://ninjaforge.com
 */

class ComNinjaElementButtonGroup extends ComNinjaElementAbstract
{	
	/**
	 * Wether a script is loaded or not
	 *
	 * @var boolean
	 */
	protected $_script = false;

	public function fetchElement($name, $value, &$node, $control_name)
	{	
		jimport('joomla.application.component.helper');
		$doc = & JFactory::getDocument();
		$scope = (string) $node['scope'];
		$class = (string) $node['class'];
		$options = array ();
		$html    = array();
		$html[]    = '
<span id="'.$name.'" class="value nf-filterset fg-buttonset fg-buttonset-single nf-buttonset-group ui-corner-all ui-widget-header '.$class.'">
';			$i = 1;
			$c = count($node->children());
			foreach ($node->children() as $option)
			{
				$val	= (string) $option['value'];
				// Lazy code below
				$childclass  = null;
				$childclass .= $option['childclass'];
				if ($option['com'])
				{
					$compare = JComponentHelper::getComponent((string)$option['com'], true);
					if($compare->enabled) {
						$valid = true;
					}
					else
					{
						$valid = false;
					}
				}
				else
				{
					$valid = true;
				}

				$priority = ( isset($option['promo']) ? true : false );
				
				if($priority && !$valid && !($value==$val))
				{
					$priorityClass 	= ' ui-priority-primary ui-priority-secondary ui-state-highlight fg-button fg-button-icon-left ';
					$icon 			= '<span class="ui-icon ui-icon-info"></span>';
					$error 			= false;
				}
				elseif($value==$val && !$valid || isset($option['trigger-alert']))
				{
					$priorityClass 	= ' ui-priority-primary ui-state-error fg-button fg-button-icon-left ';
					$icon 			= '<span class="ui-icon ui-icon-alert"></span>';
					$error 			= true;
				}
				elseif($value==$val)
				{
					$priorityClass	= ' ui-priority-primary ';
					$icon			= '<span class="ui-icon ui-icon-radio-off fm m-invert m-'.$val.' hasTip" title="This your previously saved selection"></span><span class="ui-icon ui-icon-radio-on fm m-'.$val.' hasTip" title="This your previously saved selection"></span>';
					$error 			= false;
				}
				else
				{
					$priorityClass 	= ' ui-priority-primary ';
					$icon			= null;
					$error 			= false;
				}
				
				$text	= (string) $option;
				$active = ( $value==$val ? ' ui-state-active ui-state-original fg-button-icon-left ' : '' );
				$left   = ( $i===1 ? 'ui-corner-left' : '' );
				$right  = ( $i===$c ? 'ui-corner-right' : '' );
				if($valid||$priority||$error)
				{
					$html [] = '<a href="#'.$val.'" class="all fg-button ui-filter-trigger ui-state-default '.$left . $right . $priorityClass . $childclass . $active.'">'.$icon.$text.'</a>';
				}
				$i++;
				$valid = null;
			}//end foreach
			//Save which button are active
			$html[] = '<input type="hidden" name="'.$control_name.'['.$this->group.']['.$name.']" value="'.$value.'" class="ui-filter-avoid" /></span>';
				$script = "
			jQuery(document).ready(function($){			
				$('.nf-filterset.fg-buttonset').bind('filter', function(){
					var check = $(this).children().filter('.ui-state-default:not(.ui-state-disabled)').is('.ui-state-active');
					if(!check)
					{
						var one = false;
						$(this).children().filter('.ui-state-default:not(.ui-state-disabled)').each(function(){
							if(!one)
							{
								$(this).mousedown().mouseup();
								one = true;
							}
						});
					} 
					else 
					{
						var one = false;
						$(this).children().filter('.ui-state-default.ui-state-active').each(function(){
							if(!one)
							{
								$(this).mousedown().mouseup();
								one = true;
							}
						});
					}
					var activeValue = $(this).children().filter('.ui-state-active').attr('href');
					$(this).children().filter('input').val(activeValue.replace(/#/gi, ''));
				});
				$('.nf-filterset.fg-buttonset .fg-button:visible').live('click', function(){
					$(this).trigger('filter');
				});
				//all hover and click logic for buttons
						$('.fg-button:not(.ui-state-disabled)')
						.hover(
							function(){ 
								$(this).addClass('ui-state-hover'); 
							},
							function(){ 
								$(this).removeClass('ui-state-hover'); 
							}
						)
						.mousedown(function(){
								$(this).parents('.fg-buttonset-single:first').find('.fg-button.ui-state-active').removeClass('ui-state-active');
								if( $(this).is('.ui-state-active.fg-button-toggleable, .fg-buttonset-multi .ui-state-active') ){ $(this).removeClass('ui-state-active'); }
								else { $(this).addClass('ui-state-active'); }	
						})
						.mouseup(function(){
							if(! $(this).is('.fg-button-toggleable, .fg-buttonset-single .fg-button,  .fg-buttonset-multi .fg-button') ){
								$(this).removeClass('ui-state-active');
							}
						});
			});";
		if(!defined((string)$node['type'])) 
		{
			$doc->addScriptDeclaration($script);
			$doc->addStyleDeclaration(".fg-button { outline: 0; margin:0 4px 0 0; padding: .4em 1em; text-decoration:none !important; cursor:pointer; position: relative; text-align: center; zoom: 1; }
				.fg-button .ui-icon { position: absolute; top: 50%; margin-top: -8px; left: 50%; margin-left: -8px; }
				
				a.fg-button { float:left; }
				
				/* remove extra button width in IE */
				button.fg-button { width:auto; overflow:visible; }
				
				.fg-button-icon-left { padding-left: 2.1em; }
				.fg-button-icon-right { padding-right: 2.1em; }
				.fg-button-icon-left .ui-icon { right: auto; left: .2em; margin-left: 0; }
				.fg-button-icon-right .ui-icon { left: auto; right: .2em; margin-left: 0; }
				
				.fg-button-icon-solo { display:block; width:8px; text-indent: -9999px; }	 /* solo icon buttons must have block properties for the text-indent to work */	
				
				.fg-buttonset { float:left; border-width: 0; background: transparent; }
				.fg-buttonset .fg-button { float: left; }
				.fg-buttonset-single .fg-button, 
				.fg-buttonset-multi .fg-button { margin-right: -1px;}
				
				.fg-toolbar { padding: .5em; margin: 0;  }
				.fg-toolbar .fg-buttonset { margin-right:1.5em; padding-left: 1px; }
				.fg-toolbar .fg-button { font-size: 1em;  }");
			define((string)$node['type'], '1');
		}
		return implode("\n", $html);
	}
}