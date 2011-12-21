<?php
/**
 * @package NinjaForge
 * @subpackage com_ninja.elements
 * @version   1.0 December 17, 2010
 * @author    Ninja Forge http://ninjaforge.com
 * @copyright Copyright (C) 2010 Ninja Forge
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 * package RocketTheme
 * subpackage rokstories.elements
 * version   1.9 September 1, 2010
 * author    RocketTheme http://www.rockettheme.com
 * copyright Copyright (C) 2007 - 2010 RocketTheme, LLC
 * license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 * "K2 Items" Module by JoomlaWorks for Joomla! 1.5.x - Version 2.0.0
 * Copyright (c) 2006 - 2009 JoomlaWorks Ltd. All rights reserved.
 * Released under the GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 * More info at http://www.joomlaworks.gr
 * Designed and developed by the JoomlaWorks team
 * *** Last update: June 20th, 2009 ***
 */

// no direct access
defined('_JEXEC') or die ('Restricted access');

/**
 * @package NinjaForge
 * @subpackage com_ninja.elements
 */
class ComNinjaElementK2categories extends ComNinjaElementAbstract
{

	var	$_name = 'categories';

	function fetchElement($name, $value, &$node, $control_name) {
		$db = &JFactory::getDBO();

		$query = 'SELECT m.* FROM #__k2_categories m WHERE published=1 AND trash = 0 ORDER BY parent, ordering';
		$db->setQuery( $query );
		$mitems = $db->loadObjectList();
		$children = array();
		if ( $mitems )
		{
			foreach ( $mitems as $v )
			{
				$pt 	= $v->parent;
				$list 	= @$children[$pt] ? $children[$pt] : array();
				array_push( $list, $v );
				$children[$pt] = $list;
			}
		}
		$list = JHTML::_('menu.treerecurse', 0, '', array(), $children, 9999, 0, 0 );
		$mitems = array();

		foreach ( $list as $item ) {
			$mitems[] = JHTML::_('select.option',  $item->id, '&nbsp;&nbsp;&nbsp;'.$item->treename );
		}
		
		
		$output= JHTML::_('select.genericlist',  $mitems, ''.$name.'[]', 'class="inputbox" multiple="multiple" size="7"', 'value', 'text', $value );
		return $output;
		
	}
	
}
