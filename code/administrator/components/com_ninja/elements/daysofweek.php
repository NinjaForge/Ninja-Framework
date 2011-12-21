<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: daysofweek.php 794 2011-01-10 18:44:32Z stian $
 * @category	Napi
 * @package		Napi_Parameter
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

class ComNinjaElementDaysofweek extends ComNinjaElementAbstract
{
	public function fetchElement($name, $value, &$node, $control_name)
    {
       	$class        = $node['class'];
        $size = ( $node['size'] ? $node['size'] : 7 );
        if (!$class) 
            $class = "inputbox";
		
		$days = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
		
		foreach($days as $day)
        	$options[] =  JHTML::_('select.option', $day, JText::_($day), 'key', 'title' );
        
        return JHTML::_('select.genericlist',  $options, ''.$name.'[]', ' multiple="multiple" size="' . $size . '" class="'.$class.' value"', 'key', 'title', $value, $control_name.$name );
    }
}