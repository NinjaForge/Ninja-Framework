<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category	Koowa
 * @package		Koowa_Form
 * @copyright	Copyright (C) 2007 - 2010 Johan Janssens and Mathias Verraes. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://www.koowa.org
 */

/**
 * Abstract Form Class
 *
 * @author		Mathias Verraes <mathias@joomlatools.org>
 * @category	Koowa
 * @package     Koowa_Form
 * @uses		KMixinClass
 * @uses		KObjectArray
 * @uses		KFactory
 */
abstract class NinjaFormAbstract extends KObjectArray implements NinjaFormInterface
{
	/**
	 * Array with all form elements for this form
	 *
	 * @var array
	 */
	protected $_data;
	
	/**
	 * Raw XML form definition
	 *
	 * @var	string
	 */
	protected $_xml;
    
    /**
     * Add an element
     *
     * @param	NinjaFormElementInterface $elem
     * @return 	this
     */
	public function addElement(comNinjaFormElementInterface $elem)
	{
		$data = $this->_data;
		$data[] = $elem;
		$this->_data = KConfig::toData($data);
		return $this;
	}
	
	/**
	 * Get an array of form elements
	 *
	 * @return 	array
	 */
	public function getElements()
	{
		return $this->_data;
	}
	
	/**
	 * Get the form's name
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->getIdentifier()->package.'_'.$this->getIdentifier()->name;
	}
	
	/**
	 * Render the form as a DOM object
	 *
	 * @return	DOMDocument
	 */
	public function renderDom()
	{
		$dom 	= new DOMDocument;

		
		//$form   = $dom->createElement('form');
		$form   = $dom->createElement('div');
		$name 	= $this->getName();
		$id  	= $name.'_id';
		//$form->setAttribute('name', $name);
		//$form->setAttribute('id', $id);
		$dom->appendChild($form);
		
		//$properties = $this->getProperties();
		foreach($this->getElements() as $k => $elem)
		{
			//if(array_key_exists($elem->getName(), $properties)) {
			//	$elem->setValue($properties[$elem->getName()]);
			//}
			$paragraph   = $dom->createElement('p');
			$paragraph->setAttribute('class', 'field-'.$name);
			$form->appendChild($paragraph);
			
			if($label = $elem->renderDomLabel($dom)) $paragraph->appendChild($label);
			if($element = $elem->renderDomElement($dom)) $paragraph->appendChild($element);

		}

		
		return $dom;
	}
	
	/**
	 * Render the form as HTMl
	 *
	 * @return	string	Html
	 */
	public function renderHtml()
	{
		$dom 	= $this->renderDom();
		$form 	= $dom->getElementsByTagName('form')->item(0);
		$string = $dom->saveXml($form);
		return str_replace('<', PHP_EOL.'<', $string);
	}
	
	/**
	 * Get the DOM document
	 *
	 * @return	DOMDocument
	 */
	public function getDom()
	{
		return $this->_dom;
	}
	
	/**
	 * Import an XML form definition.
	 *
	 * @param	SimpleXMLElement The form in XML format
	 * @return 	NinjaFormAbstract
	 */
	public function importXml(SimpleXMLElement $xml)
	{
		$this->_xml = $xml;
		
		// Add each element to the form
		foreach($this->_xml->element as $xmlElem)
		{
			$elem = $this->getService((string) $xmlElem['type'])
				->importXml($xmlElem);
			$this->addElement($elem);
		}
		
		return $this;
	}
	
	/**
	 * Import an XML file
	 *
	 * @param	string	The path to the form in XML format
	 * @return 	NinjaFormAbstract
	 */
	public function importXmlFile($xml)
	{
		return $this->importXml(new SimpleXMLElement(file_get_contents($xml)));
	}
}