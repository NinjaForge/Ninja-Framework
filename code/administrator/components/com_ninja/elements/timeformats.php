<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: timeformats.php 794 2011-01-10 18:44:32Z stian $
 * @category	Napi
 * @package		Napi_Parameter
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

class ComNinjaElementTimeFormats extends ComNinjaElementAbstract
{
	public function fetchElement($name, $value, &$node, $control_name)
	{
		$date = KFactory::tmp('lib.joomla.utilities.date');
		$options = array(
			JHTML::_('select.option',  'DATE_FORMAT_LC1', $date->toFormat(JText::_( 'DATE_FORMAT_LC1' )) ),
			JHTML::_('select.option',  'DATE_FORMAT_LC2', $date->toFormat(JText::_( 'DATE_FORMAT_LC2' )) ),
			JHTML::_('select.option',  'DATE_FORMAT_LC3', $date->toFormat(JText::_( 'DATE_FORMAT_LC3' )) ),
			JHTML::_('select.option',  'DATE_FORMAT_LC4', $date->toFormat(JText::_( 'DATE_FORMAT_LC4' )) )
		);

		return JHTML::_('select.genericlist',  $options, $control_name.'['.$name.']', 'class="inputbox"', 'value', 'text', $this->_parent->get($name));
	}
}