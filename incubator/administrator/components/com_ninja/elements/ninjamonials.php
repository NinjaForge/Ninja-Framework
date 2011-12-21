<?php
/**
 * @version		$Id: ninjamonials.php 365 2010-06-23 11:43:38Z stian $
 * @category	Napi
 * @package		Napi_Parameter
 * @copyright	Copyright (C) 2007 - 2010 NinjaForge. All rights reserved.
 * @license		GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link     	http://ninjaforge.com
 */

class ComNinjaElementNinjamonials extends ComNinjaElementAbstract
{
	function fetchElement($name, $value, &$node, $control_name)
	{
		$com = JComponentHelper::getComponent('com_ninjamonials', true);
		if(!$com->enabled) 
		{	
			return '<span class="ui-widget">
				<span class="ui-state-highlight ui-corner-all" style="margin-top: 20px; padding: 0 .7em;"> 
					<p style="font-size: 12px;display: block;
margin: 1em 0px;"><span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span>
					<strong>'.JText::_('Did you know?').'</strong> '.JText::_('This module support integration with Ninjamonials. Our Testimonials extension. <a href="#">Click!</a>').'</p>
				</span>
			</span>';
		} else {
		global $mainframe;

		$db			=& JFactory::getDBO();
		$doc 		=& JFactory::getDocument();
		$template 	= $mainframe->getTemplate();
		$fieldName	= $control_name.'['.$name.']';
		$class      = ( $node['class'] ? ' class="'.$node['class'].'" ' : '');
		JTable::addIncludePath ( JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_ninjamonials' . DS . 'tables' );
		$article = & JTable::getInstance ( 'Ninjamonials', 'Table' );
		if ($value) {
			$article->load($value);
			$article->title = ( $article->summary ? $article->summary : $article->testimonial );
			$descr = JText::_('Caution! By selecting a new article it\'ll replace your previous selection. Click to open a modal window and pick your content item.');
		} else {
			$article->title = JText::_('Select a Testimonial');
			$descr = JText::_('Click to open a modal window and pick your content item.');
		}

		$js = "
		function jSelectArticle(id, title, object) {
			document.getElementById(object + '_id').value = id;
			document.getElementById(object + '_name').value = title;
			jQuery('#sb-nav-close').click();
		}";
		$doc->addScriptDeclaration($js);

		$link = 'index.php?option=com_ninjamonials&task=display&controller=testimonials&tmpl=component&amp;object='.$name;

		//TODO remove shitty inline css
		JHTML::_('behavior.shadowbox');
		$html = '<span class="ui-helper-clearfix ui-resizable"><input type="text" name="'.$control_name.'['.$name.'_name]" id="'.$name.'_name" value="'.$article->title.'" class="text ui-widget-content ui-corner-all ui-state-disabled ui-width 60" disabled="disabled" size="20">';
		$html .= '<a href="'.$link.'" title="'.JText::_('Select a Testimonial').'::'.$descr.'" class="fg-button ui-state-default ui-width-40 fg-button-icon-right modal shadowbox hasTip ui-corner-right text ui-widget-content" rel="shadowbox;height=375;width=650;title=Select Testimonial;"><span class="ui-icon ui-icon-newwin"></span>'.htmlspecialchars(JText::_('Select a Testimonial'), ENT_QUOTES, 'UTF-8').'</a>   
		';
		$html .= "\n".'</span><input type="hidden" '.$class.' id="'.$name.'_id" name="'.$fieldName.'" value="'.(int)$value.'" />';
		}
		return $html;
	}
}
