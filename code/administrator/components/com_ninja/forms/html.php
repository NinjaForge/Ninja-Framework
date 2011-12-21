<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: html.php 552 2010-10-28 19:41:51Z stian $
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
class ComNinjaFormHtml extends ComNinjaFormAbstract 
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
			$type = $name == 'element' ? (string) $xmlElem['type'] : 'admin::com.ninja.form.element.' . $name;
			$elem = KFactory::tmp($type)
				->importXml($xmlElem);
			$this->addElement($elem);
		}
		
		return $this;
	}
	
}