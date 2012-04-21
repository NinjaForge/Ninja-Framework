<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
* @package		Joomla
* @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

class NinjaElementArticle extends NinjaElementAbstract
{

	function fetchElement($name, $value, &$node, $control_name)
	{
		global $mainframe;

		$db			=& JFactory::getDBO();
		$doc 		=& JFactory::getDocument();
		$template 	= $mainframe->getTemplate();
		$fieldName	= $control_name.'['.$this->group.']'.'['.$name.']';
		$class      = ( $node['class'] ? ' class="'.$node['class'].'" ' : '');
		$article =& JTable::getInstance('content');
		if ($value) {
			$article->load($value);
			$descr = JText::_('COM_NINJA_CAUTION_BY_SELECTING_A_NEW_ARTICLE_ITLL_REPLACE_YOUR_PREVIOUS_SELECTION_CLICK_TO_OPEN_A_MODAL_WINDOW_AND_PICK_YOUR_CONTENT_ITEM');
		} else {
			$article->title = JText::_('COM_NINJA_SELECT_AN_ARTICLE');
			$descr = JText::_('COM_NINJA_CLICK_TO_OPEN_A_MODAL_WINDOW_AND_PICK_YOUR_CONTENT_ITEM');
		}

		$js = "
		function jSelectArticle(id, title, object) {
			document.getElementById(object + '_id').value = id;
			document.getElementById(object + '_name').value = title;
			document.getElementById('sbox-btn-close').fireEvent('click');
		};";
		$doc->addScriptDeclaration($js);

		$link = 'index.php?option=com_content&amp;task=element&amp;tmpl=component&amp;object='.$name;

		//TODO remove shitty inline css
		JHTML::_('behavior.modal');
		$html = '<span class="value"><input type="text" name="'.$control_name.'['.$name.'_name]" id="'.$name.'_name" value="'.$article->title.'" class="text ui-widget-content ui-corner-all ui-state-disabled ui-width 60" disabled="disabled" size="20">';
		$html .= '<div class="button2-left" style="float:none;display:inline-block;">
				<div class="page" style="float:none;"><a style="float:none;" href="'.$link.'" title="'.JText::_('COM_NINJA_SELECT_AN_ARTICLE').'::'.$descr.'" class="modal hasTip" rel="{handler:\'iframe\',size:{x:window.getSize().scrollSize.x-80, y: window.getSize().size.y-80}, onShow:$(\'sbox-window\').setStyles({\'padding\': 0})}">'.htmlspecialchars(JText::_('COM_NINJA_SELECT_AN_ARTICLE'), ENT_QUOTES, 'UTF-8').'</a></div></div>
		';
//		JHTML::_('behavior.modal');
//		$html = '<span class="ui-helper-clearfix ui-resizable"><input type="text" name="'.$control_name.'['.$name.'_name]" id="'.$name.'_name" value="'.$article->title.'" class="text ui-widget-content ui-corner-all ui-state-disabled ui-width 60" disabled="disabled" size="20">';
//		$html .= '<a href="'.$link.'" title="'.JText::_('COM_NINJA_SELECT_AN_ARTICLE').'::'.$descr.'" class="fg-button ui-state-default ui-width-40 fg-button-icon-right modal shadowbox hasTip ui-corner-right text ui-widget-content" rel="{handler:\'iframe\',size:{x:window.getSize().scrollSize.x, y: window.getSize().size.y}, onShow:$(\'sbox-window\').setStyle(\'padding\', 0)}"><span class="ui-icon ui-icon-newwin"></span>'.htmlspecialchars(JText::_('COM_NINJA_SELECT_AN_ARTICLE'), ENT_QUOTES, 'UTF-8').'</a>   
//		';
		$html .= "\n".'</span><input type="hidden" '.$class.' id="'.$name.'_id" name="'.$fieldName.'" value="'.(int)$value.'" />';

		return $html;
	}
}
