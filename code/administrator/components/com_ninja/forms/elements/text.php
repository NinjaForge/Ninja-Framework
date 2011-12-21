<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: text.php 552 2010-10-28 19:41:51Z stian $
 * @category	Koowa
 * @package		Koowa_Form
 * @subpackage 	Element
 * @copyright	Copyright (C) 2007 - 2010 Johan Janssens and Mathias Verraes. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://www.koowa.org
 */

/**
 * Form Text Element
 *
 * @author		Mathias Verraes <mathias@joomlatools.org>
 * @category	Koowa
 * @package     Koowa_Form
 * @subpackage 	Element
 */
class ComNinjaFormElementText extends ComNinjaFormElementAbstract implements ComNinjaFormElementInterface
{
	/**
	 * Valid attributes for the element
	 *
	 * @var array	Array of strings
	 */
	protected $_validAttribs = array('disabled', 'maxlength', 'placeholder', 'readonly', 'size', 'accesskey', 'class', 'dir', 'id', 'lang', 'style', 'tabindex', 'title', 'xml:lang');
	
	/**
	 * Attributes for the element
	 *
	 * @var 	array	Assoc list of key=>value
	 */
	protected $_attribs = array('type' => 'text');
}