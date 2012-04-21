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
 * Form Select Element
 *
 * @author		Mathias Verraes <mathias@joomlatools.org>
 * @category	Koowa
 * @package     Koowa_Form
 * @subpackage 	Element
 */
class NinjaFormElementSelectGenericlist extends NinjaFormElementAbstract implements NinjaFormElementInterface
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
			$elem = $this->getService('ninja:form.element.select.option')
				->importXml($option);
			$this->addOption($elem);
		}

		if($this->_xml['get']) 
		{
			$get = isset($this->_xml['tmp']) && $this->_xml['tmp'] == true ? $this->getService(new KServiceIdentifier((string)$this->_xml['get'])) : $this->getService(new KServiceIdentifier((string)$this->_xml['get']));
			if($this->_xml['set'])
			{
				$json 	= '{"'.str_replace(array(';', ':'), array('","', '":"'), (string)$this->_xml['set']).'"}';
				$states = json_decode(str_replace('",""}', '"}', $json));
				foreach($states as $state => $set)
				{
					$get->{$state}($set);
				}
			}
			
			$key = $this->_xml['key'] ? $this->_xml['key'] : 'title';
			$val = $this->_xml['val'] ? $this->_xml['val'] : 'id';
			
			foreach($get->getList() as $item)
			{
				if($item->$val == false)
				{
					$elem = $this->getService('ninja:form.element.select.optgroup')
						->importXml(simplexml_load_string('<optgroup label="' . $item->$key . '"></optgroup>'));
					$this->addOption($elem);
				}
				else
				{
					$elem = $this->getService('ninja:form.element.select.option')
						->importXml(simplexml_load_string('<option value="' . $item->$val . '">' . $item->$key . '</option>'));
					$this->addOption($elem);
				}
			}
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
	public function addOption(NinjaFormElementSelectOption $option)
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
		$elem = $dom->createElement('select');
		$elem->setAttribute('name', $this->getName());
		$elem->setAttribute('id', $this->getName().'_id');
		$elem->setAttribute('class', 'value');
		
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
			$elem->appendChild( $option->renderDomElement($dom) );
		}
		
		if(count($this->_options) == 0)
		{
			$option = $this->getService('ninja:form.element.select.option')
							->importXml(simplexml_load_string('<option>' . JText::_('COM_NINJA_NO_OPTIONS') . '</option>'))
							->renderDomElement($dom);
			$elem->appendChild($option);
			$elem->setAttribute('disabled', true);
		}
		
		return $elem;
		
		
		/*
		 return '<select type="text"'.$name.$id.$size.$class.$multiple.$disabled.$tabIndex.'>'.PHP_EOL
			.implode(PHP_EOL, $options).PHP_EOL
			.'</select>'.PHP_EOL;
		*/
	}
	
	/**
	 * Set the selected options
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
				$option->setSelected(true);
			} else {
				$option->setSelected(false);
			}
		}
		
		return $this;
	}
}