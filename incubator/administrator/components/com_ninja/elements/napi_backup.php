<?php
/**
 * @category	Napi
 * @package		Napi_Parameter
 * @copyright	Copyright (C) 2007 - 2010 NinjaForge. All rights reserved.
 * @license		GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link     	http://ninjaforge.com
 */

// import library dependencies
jimport('joomla.application.component.helper');
jimport('joomla.application.component.controller');

// verify the NAPI libraries are available and the correct version
if (!function_exists('nimport')) {
	JError::raiseWarning(500, JText::_('NAPI_MISSING'));
} else {
	if (version_compare('1.0.0', NAPI)) {
		JError::raiseWarning(500, JText::sprintf('NAPI_OUTDATED', '1.0.0'));
	}
}

class ComNinjaElementNapi extends ComNinjaElementAbstract
{
	function fetchElement($name, $value, &$node, $control_name)
	{	
		JHTML::_('behavior.jquery');
		JHTML::_('behavior.jqueryui');
		JHTML::_('behavior.livequery');
		$doc = & JFactory::getDocument();
		//$doc->addScript('http://weston.ruter.net/projects/jquery-css-transitions/code/jquery.color.js');
		//$doc->addScript(JURI::root(true).'/media/napi/js/jquery.css-transitions.js');
		$doc->addScript(JURI::root(true).'/media/napi/js/jquery.mousewheel.js');
//		$doc->addScript('http://jquery-ui.googlecode.com/svn/branches/dev/spinner/ui/ui.core.js');
//		$doc->addScript('http://jquery-ui.googlecode.com/svn/branches/dev/spinner/ui/ui.slider.js');
//		$doc->addScript('http://jquery-ui.googlecode.com/svn/branches/dev/spinner/ui/ui.sortable.js');
		$doc->addScript('http://jquery-ui.googlecode.com/svn/branches/dev/spinner/ui/ui.spinner.js');
		$doc->addScript('http://jquery-ui.googlecode.com/svn/branches/dev/spinner/ui/ui.spinner.sync.js');
		//$doc->addScript(JURI::root(true).'/media/napi/js/ui.spinner.js');
//		$doc->addScript('http://jquery-ui.googlecode.com/svn/branches/dev/spinner/ui/ui.tabs.js');
		$doc->addStyleSheet(JURI::root(true).'/media/napi/css/jquery-ui-extended.css');
		$doc->addStyleSheet(JURI::root(true).'/media/napi/css/themes/napi/jquery-ui.css');
		$script = "
		jQuery(document).ready(function($){
		
			//all hover and click logic for buttons
			$('.fg-button:not(.ui-state-disabled)')
			.live('mouseover',
			function(){ 
				$(this).addClass('ui-state-hover'); 
			})
			.live('mouseout',
			function(){ 
				$(this).removeClass('ui-state-hover'); 
			})
			.live('mouseup', function(){
					$(this).parents('.fg-buttonset-single:first').find('.fg-button.ui-state-active').removeClass('ui-state-active');
					if( $(this).is('.ui-state-active.fg-button-toggleable, .fg-buttonset-multi .ui-state-active') ){ $(this).removeClass('ui-state-active'); }
					else { $(this).addClass('ui-state-active'); }	
			})
			.live('mousedown', function(){
				if(! $(this).is('.fg-button-toggleable, .fg-buttonset-single .fg-button,  .fg-buttonset-multi .fg-button') ){
					$(this).removeClass('ui-state-active');
				}
			});
			
			$('.ui-state-disabled') 
				.live('click', function(){ 
				// Stop the disabled buttons
					 return false;	   
				});
			$('.ui-helper-toggleset .fg-button-toggleable').live('click', function(){
				$(this).closest('.ui-helper-toggleset').toggleClass('ui-helper-toggle-active');
				jQuery(this).closest('.ui-helper-toggleset').contents().trigger('mousedown').removeClass('ui-helper-toggle-active').removeClass('ui-helper-toggle-active');
			});
			$('.ui-helper-toggleset .fg-buttonset label .fg-button.ui-state-active').live('dblclick', function(){
				$(this).closest('.ui-helper-toggleset').toggleClass('ui-helper-toggle-active');
				jQuery(this).closest('.ui-helper-toggleset').contents().trigger('mousedown').removeClass('ui-helper-toggle-active').removeClass('ui-helper-toggle-active');
			});
			
			$('.jpane-toggler-down + .content').not(':animating').css('height', 'auto');
			$('.panel:not(.scrollable-wrap)').addClass('ui-corner-all');
			$('.panel:not(.scrollable-wrap) h3').addClass('ui-corner-all');
			$('.panel:not(.scrollable-wrap) .content').addClass('ui-corner-bottom');
		});";
		$doc->addScriptDeclaration($script);
		$class = ( $node['class'] ? 'class="'.$node['class'].'"' : 'class="text_area"' );
		
		return;
	}


	function fetchTooltip($label, $description, &$xmlElement, $control_name='', $name='') {
		return false;
	}
}
