<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: abstract.php 552 2010-10-28 19:41:51Z stian $
 * @category	Koowa
 * @package		Koowa_Form
 * @subpackage 	Element
 * @copyright	Copyright (C) 2007 - 2010 Johan Janssens and Mathias Verraes. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://www.koowa.org
 */

/**
 * Abstract Form Element
 *
 * @author		Mathias Verraes <mathias@joomlatools.org>
 * @category	Koowa
 * @package     Koowa_Form
 * @subpackage 	Element
 */
abstract class ComNinjaFormElementAbstract extends KObject implements ComNinjaFormElementInterface, KObjectIdentifiable
{
	/**
	 * Attributes for the element
	 *
	 * @var 	array	Assoc list of key=>value
	 */
	protected $_attribs = array();
	
	/**
	 * Valid attributes for the element
	 *
	 * @var array	Array of strings
	 */
	protected $_validAttribs = array('accesskey', 'class', 'dir', 'id', 'lang', 'style', 'tabindex', 'title', 'xml:lang');
	
	/**
	 * The element's name
	 *
	 * @var string
	 */
	protected $_name;
		
	/**
	 * Value (or selected values) for the element
	 *
	 * @var string|array
	 */
	protected $_value;
	
	/**
	 * Default if no value is available
	 *
	 * @var string|array
	 */
	protected $_default;
	
	protected $_label = array( 'label', 'description');
		
	/**
	 * The object identifier
	 *
	 * @var object 
	 */
	protected $_identifier = null;

	/**
	 * Constructor
	 *
	 * @param	array An optional associative array of configuration settings.
	 */
	public function __construct(KConfig $options)
	{
        $this->_identifier = $options->identifier;
		parent::__construct($options);
	}

	/**
	 * Initializes the options for the object
	 *
	 * Called from {@link __construct()} as a first step of object instantiation.
	 *
	 * @param   array   Options
	 * @return  array   Options
	 */
	protected function _initialize(KConfig $options)
	{
		$options->append(array(
			'identifier' => null
       	));
       	
        parent::_initialize($options);
    }
    
    /**
	 * Get the identifier
	 *
	 * @return 	object A KFactoryIdentifier object
	 */
	public function getIdentifier()
	{
		return $this->_identifier;
	}
	
	/**
	 * Set value
	 *
	 * @param 	string|array
	 * @return 	NinjaFormElementInterface
	 */
	public function setValue($value)
	{
		$this->_value = $value;
		return $this;
	}
	
	/**
	 * Get value
	 *
	 * @return	string|array
	 */
	public function getValue()
	{
		return isset($this->_value) ? $this->_value : $this->_default;
	}
	
	/**
	 * Set default
	 *
	 * @param 	string|array
	 * @return 	NinjaFormElementInterface
	 */
	public function setDefault($default)
	{
		$this->_default = $default;
		return $this;
	}
	
	/**
	 * Get default
	 *
	 * @return	string|array
	 */
	public function getDefault()
	{
		return $this->_default;
	}
	
	/**
	 * Set the element's name
	 *
	 * @param 	string
	 * @return 	NinjaFormElementInterface
	 */
	public function setName($name)
	{
		$this->_name = $name;
		return $this;
	}
	
	/**
	 * Get the element's name
	 *
	 * @return	string
	 */
	public function getName()
	{
		return $this->_name;
	}	

	
	/**
	 * Add an attribute
	 *
	 * @param 	string	Attribute key
	 * @param 	string	Attribute value
	 * @return 	this
	 */
	public function setAttribute($key, $value)
	{
		if(in_array($key, $this->_validAttribs)) {
			$this->_attribs[$key] = $value;	
		} else {
			throw new NinjaFormElementException('The attribute "'.$key.'" is not valid for '.get_class($this));
		}
		
		return $this;
	}

	/**
	 * Get the value for an attribute
	 *
	 * @param 	string	Attribute key
	 * @param 	mixed 	Default value
	 * @return 	mixed
	 */
	public function getAttribute($key, $default = null)
	{
		return in_array($key, $this->_attribs) ? $this->_attribs[$key] : $default;	
	}
	
	public function getAttributes()
	{
		return $this->_attribs;
	}
	
	public function getLabel()
	{
		return $this->_label; 
	}
	
	/**
	 * Set the label
	 *
	 * @param 	string	Label
	 * @param 	string	Description
	 * @return 	NinjaFormElementInterface
	 */
	public function setLabel($label, $description = null)
	{
		$this->_label = array( 'label' => $label, 'description' => $description);
		return $this;
	}
	
	/**
	 * Import an XML element definition
	 *
	 * @param 	SimpleXMLElement SimpleXMLElement object
	 * @return 	this
	 */
	public function importXml(SimpleXMLElement $xml)
	{
		$this->_xml = $xml;
		
		$label = $this->_xml['label'] ? $this->_xml['label'] : KInflector::humanize($this->_xml['name']);

		$this->setName((string) $this->_xml['name']);
		$this->setDefault((string) $this->_xml['default']);
		$this->setLabel(JText::_((string) $label), $this->_xml['description']);
		
		foreach($this->_xml->attributes() as $key => $attrib) 
		{
			if(in_array($key, $this->_validAttribs)){
				$this->setAttribute($key, (string) $attrib);
			}
		}
		return $this;
	}
	
	public function renderDomLabel(DOMDocument $dom)
	{
		$data 		= $this->getLabel();
		
		$elem = $dom->createElement('label', JText::_($data['label']));
		$elem->setAttribute('title', JText::_($data['description']));
		$elem->setAttribute('for', $this->getName().'_id');
		$elem->setAttribute('class', 'key hasTip');
		
		return $elem;
	}
	
	public function renderDomElement(DOMDocument $dom)
	{
		$elem = $dom->createElement('input');
		$elem->setAttribute('name', $this->getName());
		$elem->setAttribute('value', $this->getValue());
		$elem->setAttribute('id', $this->getName().'_id');
		$elem->setAttribute('class', 'value');
		
		
		foreach($this->getAttributes() as $key => $val) {
			$elem->setAttribute($key, $val);
		}
		
		return $elem;
	}
	
	public function renderHtmlLabel($dom = false)
	{
		if(!$dom) $dom = new DOMDocument;
		$dom->appendChild($this->renderDomLabel($dom));
		return $dom->saveXml($dom->getElementsByTagName('label')->item(0));
	}
	
	public function renderHtmlElement($dom = false)
	{
		if(!$dom) $dom = new DOMDocument;
		$dom->appendChild($this->renderDomElement($dom));
		$xpath	= new DOMXPath($dom);
		$query	= '//*[@id="' . $this->getName().'_id"]';
		$result	= $xpath->query($query)->item(0);
		return $result ? $dom->saveXml($result) : null;
	}
	
	public function renderHtml(){}
}