<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: interface.php 552 2010-10-28 19:41:51Z stian $
 * @category	Koowa
 * @package		Koowa_Form
 * @subpackage 	Element
 * @copyright	Copyright (C) 2007 - 2010 Johan Janssens and Mathias Verraes. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://www.koowa.org
 */

/**
 * Form Element Interface
 *
 * @author		Mathias Verraes <mathias@joomlatools.org>
 * @category	Koowa
 * @package     Koowa_Form
 * @subpackage 	Element
 */
interface ComNinjaFormElementInterface
{
	/**
	 * Set the element's name attribute
	 * 
	 * @param	string	Name
	 * @return 	NinjaFormElementInterface
	 */
	public function setName($name);
	
	/**
	 * Get the element's name
	 * 
	 * @return	string
	 */
	public function getName();
	
	/**
	 * Set the value for the element
	 *
	 * @param 	string|array	Value
	 * @return	NinjaFormElementInterface
	 */
	public function setValue($value);
	
	/**
	 * Get the value for the element
	 *
	 * @return 	string|array	Value
	 */
	public function getValue();
	
	/**
	 * Add an attribute
	 *
	 * @param 	string	Attribute name
	 * @param 	string	Attribute value
	 * @return 	this
	 */
	public function setAttribute($name, $value);
	
	/**
	 * Getan attribute's value
	 *
	 * @param 	string	Attribute name
	 * @param 	mixed	Default value
 	* @return	mixed	Attribute's value 		
	 */
	public function getAttribute($name, $default = null);

	/**
	 * Get all attributes
	 * 
	 * @return 	array	List of named arrays [key, value] 
	 */
	public function getAttributes();
	
	/**
	 * Import an XML element definition
	 *
	 * @param 	SimpleXMLElement SimpleXMLElement object for the element
	 * @return 	this
	 */
	public function importXml(SimpleXMLElement $xml);
	
	/**
	 * Render the element
	 *
	 * @param 	DOMDocument used to create the DOMElement
	 * @return	DOMElement
	 */
	public function renderDomElement(DOMDocument $dom);
	
	/**
	 * Render the label
	 *
	 * @param 	DOMDocument used to create the DOMElement
	 * @return	DOMElement
	 */
	public function renderDomLabel(DOMDocument $dom);
	
	/**
	 * Render the element
	 *
	 * @return	string
	 */
	public function renderHtmlElement();
	
	/**
	 * Render the label
	 *
	 * @return	string
	 */
	public function renderHtmlLabel();
	
	/**
	 * Render both label and element
	 *
	 * @return	string
	 */
	public function renderHtml();
		
	/**
	 * Get the element's label & description
	 *
	 * @return 	array	Named array [label, description]
	 */
	public function getLabel();
	
	/**
	 * Set the element's label and optional description
	 *
	 * @param	string	Label
	 * @param 	string	Description
	 * @return 	NinjaFormElementInterface
	 */
	public function setLabel($label, $description = null);
	
	/**
	 * Get the element's default value
	 *
	 * @return 	string|array
	 */
	public function getDefault();
	
	/**
	 * Set the element's default value
	 *
	 * @param 	string|array	Default value
	 * @return 	NinjaFormElementInterface
	 */
	public function setDefault($value);
	
}