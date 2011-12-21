<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: html.php 1399 2011-11-01 14:22:48Z stian $
 * @category	Koowa
 * @package		Koowa_Form
 * @copyright	Copyright (C) 2007 - 2010 Johan Janssens and Mathias Verraes. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://www.koowa.org
 */

/**
 * Default Form Class
 *
 * @author		Mathias Verraes <mathias@joomlatools.org>
 * @category	Koowa
 * @package     Koowa_Form
 */
class NinjaFormHtml extends NinjaFormAbstract 
{
	/**
	 * Constructor
	 *
	 * @param	array An optional associative array of configuration settings.
	 */
	public function __construct(KConfig $options)
	{
        parent::__construct($options);
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
		foreach($this->_xml->children() as $name => $xmlElem)
		{
			$type = $name == 'element' ? (string) $xmlElem['type'] : 'ninja:form.element.' . $name;
			$elem = $this->getService($type)
				->importXml($xmlElem);
			$this->addElement($elem);
		}
		
		return $this;
	}
	
}