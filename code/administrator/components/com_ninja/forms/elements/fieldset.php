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
 * Form Text Element
 *
 * @author		Mathias Verraes <mathias@joomlatools.org>
 * @category	Koowa
 * @package     Koowa_Form
 * @subpackage 	Element
 */
class NinjaFormElementFieldset extends NinjaFormElementAbstract implements NinjaFormElementInterface
{
	/**
	 * Valid attributes for the element
	 *
	 * @var array	Array of strings
	 */
	protected $_validAttribs = array('disabled', 'placeholder', 'accesskey', 'class', 'dir', 'id', 'lang', 'style', 'tabindex', 'title', 'xml:lang');
	
	protected $_label = array( 'label', 'description', 'legend');

	
	public function renderDomElement(DOMDocument $dom)
	{
		$elem = $dom->createElement('fieldset');
		$elem->setAttribute('name', $this->getName());
		if($this->_xml['legend']) $this->_label['legend'] = $this->_xml['legend'];
		$elem->appendChild($this->renderDomLegend($dom));
		
		foreach($this->_xml->children() as $name => $xmlElem)
		{
			$type = $name == 'element' ? (string) $xmlElem['type'] : 'ninja:form.element.' . $name;
			$element = $this->getService($type)
				->importXml($xmlElem);
			$elem->addElement($element);
		}
		
		foreach($this->getAttributes() as $key => $val) {
			$elem->setAttribute($key, $val);
		}
		
		return $elem;
	}
	
	public function renderDomLegend(DOMDocument $dom)
	{
		$data 		= $this->getLabel();
		
		$elem = $dom->createElement('legend', $data['legend']);
		
		return $elem;
	}
	
	public function renderDomLabel(DOMDocument $dom)
	{
		return false;
	}
}