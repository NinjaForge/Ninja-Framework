<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: radio.php 1399 2011-11-01 14:22:48Z stian $
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
class NinjaFormElementSelectRadio extends NinjaFormElementAbstract
{
	/**
	 * Is the option checked
	 *
	 * @var bool
	 */
	protected $_checked = false;

	/**
	 * Valid attributes for the element
	 *
	 * @var array	Array of strings
	 */
	protected $_validAttribs = array('disabled', 'checked', 'value', 'accesskey', 'class', 'dir', 'id', 'lang', 'style', 'tabindex', 'title', 'xml:lang');

	
	/**
	 * Import an XML element definition
	 *
	 * @param 	SimpleXMLElement SimpleXMLElement object
	 * @return 	NinjaFormElementSelectOption
	 */
	public function importXml(SimpleXMLElement $xml)
	{
		parent::importXml($xml);
		
		$this->setValue((string) $this->_xml['value']);
		$this->setLabel((string) $this->_xml);
		return $this;
	}
	
	/**
	 * Select or unselet the option
	 *
	 * @param 	bool
	 * @return 	NinjaFormElementInterface
	 */
	public function setChecked($bool)
	{
		$this->_checked = (bool) $bool;
		return $this;
	}
	
	/**
	 * Is the option checked?
	 *
	 * @return 	bool
	 */
	public function isChecked()
	{
		return $this->_checked;
	}
	
	public function renderDomElement(DOMDocument $dom)
	{
		$elem = $dom->createElement('input');
		$elem->setAttribute('type', 'radio');
		$elem->setAttribute('name', $this->getName());
		$elem->setAttribute('id', $this->getName() . '_' . $this->getValue());
		$elem->setAttribute('value', $this->getValue());
		$label = $this->_xml;
		//$elem->appendChild( $dom->createTextNode((string)$label));
		
		if($this->isChecked()) {
			$elem->setAttribute('checked', 'checked');
		}
		
		foreach($this->getAttributes() as $key => $val) {
			$elem->setAttribute($key, $val);
		}
		
		return $elem;
	}
	
	public function renderDomLabel(DOMDocument $dom)
	{
		$data 		= $this->getLabel();
		
		$elem = $dom->createElement('label', JText::_($data['label']));
		$elem->setAttribute('title', JText::_($data['description']));
		$elem->setAttribute('for', $this->getName().'_'.$this->getValue());
		
		return $elem;
	}
}