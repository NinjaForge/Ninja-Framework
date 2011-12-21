<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: json.php 794 2011-01-10 18:44:32Z stian $
 * @package		Ninja
 * @copyright	Copyright (C) 2011 NinjaForge. All rights reserved.
 * @license 	GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */
 
class ComNinjaViewJson extends KViewJson
{
	/**
	 * Constructor
	 *
	 * @param 	object 	An optional KConfig object with configuration options
	 */
	public function __construct(KConfig $config)
	{
		// set the document object
		//@TODO submit koowa patch for this one
		$this->_document = KFactory::get('lib.joomla.document');
	
		parent::__construct($config);
	}
		
	/**
	 * Renders and echo's the views output wrapping it in a js callback if present
 	 *
	 * @return string JSON data
	 */
    public function display()
    {
    	if(!KRequest::has('get.callback')) return parent::display();
    
		$callback = KRequest::get('get.callback', 'cmd');
    	
    	$json  = $callback . '(';
    	$json .= parent::display();
    	
    	//Set the correct mime type
    	$this->_document->setMimeEncoding('application/javascript');
    	
    	$json .= ');';
    	
    	return $json;
    }
}