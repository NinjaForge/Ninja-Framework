<?php
/**
 * @version		$Id: k2.php 365 2010-06-23 11:43:38Z stian $
 * @category	Napi
 * @package		Napi_Parameter
 * @copyright	Copyright (C) 2007 - 2010 NinjaForge. All rights reserved.
 * @license		GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link     	http://ninjaforge.com
 */

class ComNinjaElementK2 extends ComNinjaElementAbstract
{
	function fetchElement($name, $value, &$node, $control_name)
	{
		global $mainframe;
		$com_k2 = JComponentHelper::getComponent('com_k2', true);
		if(!$com_k2->enabled) 
		{	
			return false;
		}
		else 
		{

			$db			=& JFactory::getDBO();
			$doc 		=& JFactory::getDocument();
			$template 	= $mainframe->getTemplate();
			$fieldName	= $control_name.'['.$name.']';
			$class      = ( $node['class'] ? ' class="'.$node['class'].'" ' : '');
			JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_k2'.DS.'tables');
			$item = & JTable::getInstance('K2Item', 'Table');
			if ($value) {
				$item->load($value);
				$descr = JText::_('Caution! By selecting a new article it\'ll replace your previous selection. Click to open a modal window and pick your content item.');
			} else {
				$item->title = JText::_('Select an Item');
				$descr = JText::_('Click to open a modal window and pick your content item.');
			}
	
			$js = "
			function jSelectItem(id, title) {
			document.getElementById('$control_name$name' + '_id').value = id;
			document.getElementById('$control_name$name' + '_name').value = title;
			document.getElementById('sbox-window').close();
		};";
			$doc->addScriptDeclaration($js);
			$link = 'index.php?option=com_k2&amp;view=items&amp;task=element&amp;tmpl=component&amp;object='.$name;
	
			//TODO remove shitty inline css
			JHTML::_('behavior.modal');
			$html = '<span class="ui-helper-clearfix ui-resizable"><input type="text" name="'.$control_name.'['.$name.'_name]" id="'.$control_name.$name.'_name" value="'.$item->title.'" class="text ui-widget-content ui-corner-all ui-state-disabled ui-width 60" disabled="disabled" size="20">';
			$html .= '<a href="'.$link.'" title="'.JText::_('Select an Item').'::'.$descr.'" class="fg-button ui-state-default ui-width-40 fg-button-icon-right modal shadowbox hasTip ui-corner-right text ui-widget-content" rel="{handler:\'iframe\',size:{x:650, y: 375},iframePreload:true}" ><span class="ui-icon ui-icon-newwin"></span>'.htmlspecialchars(JText::_('Select an item from K2'), ENT_QUOTES, 'UTF-8').'</a>   
			';
			$html .= "\n".'</span><input type="hidden" id="'.$control_name.$name.'_id" name="'.$fieldName.'" value="'.(int)$value.'" />';
		} 
		return $html;
	}
}
