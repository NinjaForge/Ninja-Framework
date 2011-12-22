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
 * Abstract Option Element
 *
 * @author		Mathias Verraes <mathias@joomlatools.org>
 * @category	Koowa
 * @package     Koowa_Form
 * @subpackage 	Element
 */
class NinjaFormElementSelectOptgroup extends NinjaFormElementSelectOption
{
	/**
	 * Valid attributes for the element
	 *
	 * @var array	Array of strings
	 */
	protected $_validAttribs = array('disabled', 'selected', 'value', 'accesskey', 'class', 'dir', 'id', 'lang', 'style', 'tabindex', 'title', 'xml:lang');

	
	/**
	 * Import an XML element definition
	 *
	 * @param 	SimpleXMLElement SimpleXMLElement object
	 * @return 	NinjaFormElementSelectOption
	 */
	public function importXml(SimpleXMLElement $xml)
	{
		parent::importXml($xml);
		
		$this->setValue(null);
		$this->setLabel((string) $this->_xml['label']);
		return $this;
	}
	
	/**
	 * Is the option selected?
	 *
	 * @return 	bool
	 */
	public function isSelected()
	{
		return $this->_selected;
	}
	
	public function renderDomElement(DOMDocument $dom)
	{
		$elem = $dom->createElement('optgroup');
		$elem->setAttribute('label', (string)current($this->getLabel()));
		$elem->appendChild( $dom->createTextNode('&nbsp;'));
				
		foreach($this->getAttributes() as $key => $val) {
			$elem->setAttribute($key, $val);
		}
		
		return $elem;
	}

}