<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: description.php 552 2010-10-28 19:41:51Z stian $
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
class ComNinjaFormElementDescription extends ComNinjaFormElementText
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