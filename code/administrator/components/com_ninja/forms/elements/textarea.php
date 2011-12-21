<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: textarea.php 552 2010-10-28 19:41:51Z stian $
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
class ComNinjaFormElementTextarea extends ComNinjaFormElementAbstract implements ComNinjaFormElementInterface
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
	//protected $_attribs = array('type' => 'text');
	
	public function renderDomElement(DOMDocument $dom)
	{
		$elem = $dom->createElement('textarea');
		$elem->setAttribute('name', $this->getName());
		$elem->setAttribute('id', $this->getName().'_id');
		$elem->setAttribute('class', 'value');
		
		
		foreach($this->getAttributes() as $key => $val) {
			$elem->setAttribute($key, $val);
		}
		
		$elem->appendChild($dom->createTextNode($this->getValue()));
		
		return $elem;
	}
}