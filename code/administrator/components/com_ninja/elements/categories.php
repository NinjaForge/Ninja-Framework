<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: categories.php 794 2011-01-10 18:44:32Z stian $
 * @category	Napi
 * @package		Napi_Parameter
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

class ComNinjaElementCategories extends ComNinjaElementAbstract
{
	public function fetchElement($name, $value, &$node, $control_name)
    {
        $db = &JFactory::getDBO();

        $section    = $node['section'];
        $class        = $node['class'];
        $size = ( $node['size'] ? $node['size'] : 5 );
        if (!$class) 
            $class = "inputbox";

        if (!isset ($section)) {
            // alias for section
            $section = $node['scope'];
            if (!isset ($section)) 
                $section = 'content';
        }

        if ($section == 'content') {
            // This might get a conflict with the dynamic translation - TODO: search for better solution
            $query = 'SELECT c.id, CONCAT_WS( " / ",s.title, c.title ) AS title, 2 AS sorter' .
                ' FROM #__categories AS c' .
                ' LEFT JOIN #__sections AS s ON s.id=c.section' .
                ' WHERE c.published = 1' .
                ' AND s.scope = '.$db->Quote($section).
                ' UNION SELECT 0 AS id, "'.JText::_('Uncategorized').'" AS title, 1 AS sorter'.
                ' ORDER BY sorter, title';
        } else {
            $query = 'SELECT c.id, c.title' .
                ' FROM #__categories AS c' .
                ' WHERE c.published = 1' .
                ' AND c.section = '.$db->Quote($section).
                ' ORDER BY c.title';
        }
        $db->setQuery($query);
        $options = $db->loadObjectList();
        

        return JHTML::_('select.genericlist',  $options, ''.$name.'[]', ' multiple="multiple" size="' . $size . '" class="'.$class.' value"', 'id', 'title', $value, $control_name.$name );
    }
}