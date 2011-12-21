<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: checklist.php 1399 2011-11-01 14:22:48Z stian $
 * @category	Koowa
 * @package		Koowa_Form
 * @subpackage 	Element
 * @copyright	Copyright (C) 2007 - 2010 Johan Janssens and Mathias Verraes. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://www.koowa.org
 */

/**
 * Form Select Element
 *
 * @author		Mathias Verraes <mathias@joomlatools.org>
 * @category	Koowa
 * @package     Koowa_Form
 * @subpackage 	Element
 */
class NinjaFormElementSelectChecklist extends NinjaFormElementAbstract implements NinjaFormElementInterface
{
	/**
	 * Options for the element
	 *
	 * @var 	array
	 */
	protected $_options = array();
	
	/**
	 * Valid attributes for the element
	 *
	 * @var array	Array of strings
	 */
	protected $_validAttribs = array('disabled', 'multiple', 'size', 'accesskey', 'class', 'dir', 'id', 'lang', 'style', 'tabindex', 'title', 'xml:lang');
	
	
	public function importXml(SimpleXMLElement $xml)
	{
		parent::importXml($xml);
		
		foreach($this->_xml->option as $option)	
		{
			$elem = $this->getService('ninja:form.element.select.checkbox')
				->importXml($option);
			$this->addOption($elem);
		}
		return $this;
	}
	
	/**
	 * Add an option
	 *
	 * @param 	string	Value
	 * @param 	string	Label
	 * @return 	this
	 */
	public function addOption(comNinjaFormElementInterface $option)
	{
		$this->_options[] = $option;
		return $this;
	}
	
	/**
	 * Get the options for the select box
	 *
	 * @return 	array
	 */
	public function getOptions()
	{
		return $this->_options;
	}
	
	public function renderDomElement(DOMDocument $dom)
	{
		$elem = $dom->createElement('ul');
		$elem->setAttribute('name', $this->getName());
		$elem->setAttribute('id', $this->getName().'_id');
		$elem->setAttribute('class', 'group');
		
		foreach($this->getAttributes() as $key => $val) {
			if($key == 'multiple') $elem->setAttribute('name', $this->getName() . '[]');
			$elem->setAttribute($key, $val);
		}
		
		/*
		$filter 	= $this->getService('koowa:filter.boolean');
		
		$name 		= ' name="'.htmlspecialchars($this->getName()).'"';
		$id			= ' id="'.htmlspecialchars($this->getName()).'_id"';
		$size	 	= !empty($this->_attribs['size']) ? ' size="'.htmlspecialchars($this->_attribs['size']).'" ' : '';
		$disabled 	= $filter->sanitize(@$this->_attribs['disabled']) ? ' disabled="disabled" ' : '';
		$multiple	= $filter->sanitize(@$this->_attribs['multiple']) ? ' multiple="multiple" ' : '';
		$class	 	= !empty($this->_attribs['class']) ? ' class="'.htmlspecialchars($this->_attribs['class']).'" ' : '';
		$tabIndex 	= ' tabindex="'.++NinjaForm::$tabIndex.'"';
		*/
		foreach($this->_options as $option) {
//			if($this->getValue() == $option->getValue()) {
//				$option->setChecked(true);
//			}
			$child = $dom->createElement('li');
			$child->setAttribute('class', 'value');
			$option->setName($this->getName());
			$elem->appendChild($child);
			$child->appendChild( $option->renderDomElement($dom) );
			$child->appendChild( $option->renderDomLabel($dom) );
		}

		$dummy = $dom->createElement('input');
		$dummy->setAttribute('type', 'hidden');
		$dummy->setAttribute('name', $this->getName().'[]');
		$dummy->setAttribute('value', null);
		$child->appendChild($dummy);
		
		return $elem;
		
		
		/*
		 return '<select type="text"'.$name.$id.$size.$class.$multiple.$disabled.$tabIndex.'>'.PHP_EOL
			.implode(PHP_EOL, $options).PHP_EOL
			.'</select>'.PHP_EOL;
		*/
	}
	
	/**
	 * Set the checked options
	 *
	 * @param 	array
	 * @return 	NinjaFormElementInterface
	 */
	public function setValue($vals)
	{
		settype($vals, 'array');
		foreach($this->getOptions() as $option)
		{
			if(in_array($option->getValue(), $vals)) {
				$option->setChecked(true);
			} else {
				$option->setChecked(false);
			}
		}
		
		return $this;
	}
}