<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category	Koowa
 * @package		Koowa_Form
 * @subpackage 	Element
 * @copyright	Copyright (C) 2007 - 2010 Johan Janssens and Mathias Verraes. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://www.koowa.org
 */

/**
 * Form Description Element
 *
 * @author		Stian Didriksen <stian@ninjaforge.com>
 * @category	Koowa
 * @package     Koowa_Form
 * @subpackage 	Element
 */
class NinjaFormElementDescription extends NinjaFormElementText
{	
	public function renderHtmlLabel()
	{
		return '';
	}
	
	public function renderHtmlElement()
	{
		return JText::_((string)$this->_xml);
	}
}