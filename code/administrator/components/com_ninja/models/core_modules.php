<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @package		Koowa
 * @copyright	Copyright (C) 2010 Nooku. All rights reserved.
 * @license 	GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://www.nooku.org
 */

class NinjaModelCore_modules extends KModelAbstract
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
		
		jimport('joomla.application.module.helper');
		$this->_list = JModuleHelper::_load();
		$this->_total = count($this->_list);
	}
}