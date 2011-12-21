<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: timezones.php 913 2011-03-17 18:19:44Z stian $
 * @package		Koowa
 * @copyright	Copyright (C) 2010 Nooku. All rights reserved.
 * @license 	GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://www.nooku.org
 */

class ComNinjaModelTimezones extends KModelAbstract
{	
	/**
	 * Constructor
	 *
	 * @param	array An optional associative array of configuration settings.
	 */
	public function __construct(KConfig $options)
	{
		parent::__construct($options);
		
		$this->_state->insert('limit', 'int', 0);
		
		foreach (DateTimeZone::listIdentifiers() as $timezone)
		{
			if ($group != substr($timezone, 0, strpos($timezone, '/'))) {
				$group = substr($timezone, 0, strpos($timezone, '/'));
				$this->_list[] = (object) array('id' => false, 'title' => $group);
			}
			$this->_list[] = (object) array('id' => $timezone, 'title' => $timezone);
		}
		
		$this->_total = count($this->_list);
	}
}