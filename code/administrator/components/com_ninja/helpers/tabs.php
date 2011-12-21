<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: tabs.php 635 2010-11-09 10:25:43Z stian $
 * @category	Koowa
 * @package		Koowa_Template
 * @subpackage	Helper
 * @copyright	Copyright (C) 2007 - 2009 Johan Janssens and Mathias Verraes. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://www.koowa.org
 */

/**
 * Template Tabs Behavior Helper
 *
 * @author		Johan Janssens <johan@koowa.org>
 * @category	Koowa
 * @package		Koowa_Template
 * @subpackage	Helper
 * @uses		KArrayHelper
 */
class ComNinjaHelperTabs extends KTemplateHelperTabs
{
	/**
	 * Constructor
	 *
	 * @param array Associative array of values
	 */
	public function __construct(KConfig $params)
	{
		//Load koowa javascript
		KFactory::get('admin::com.ninja.helper.default')->js('/tabs.js');
	}
	
	/**
	 * Creates a pane and creates the javascript object for it
	 *
	 * @param 	array 	An optional array with configuration options
	 * @return 	string	Html
	 */
	public function startPane( $config = array() )
	{
		$config = new KConfig($config);
		$config->append(array(
			'id'      => 'pane',
			'attribs' => array(),
			'options' => array()
		));
		
		$html  = '';
		
		// Load the necessary files if they haven't yet been loaded
		if (!isset($this->_loaded['tabs'])) 
		{			
			$this->_loaded['tabs'] = true;
		}
		
		$id      = strtolower($config->id);
		$attribs = KHelperArray::toString($config->attribs);
	
		$html .= "
			<script>
				window.addEvent('domready', function(){ new KTabs('tabs-".$id."', ".json_encode($config->toData($config->options))."); });
			</script>";
	
		$html .= '<dl class="tabs" id="tabs-'.$id.'" '.$attribs.'>';
		return $html;
	}
}