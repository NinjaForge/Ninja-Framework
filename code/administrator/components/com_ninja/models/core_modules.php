<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: core_modules.php 913 2011-03-17 18:19:44Z stian $
 * @package		Koowa
 * @copyright	Copyright (C) 2010 Nooku. All rights reserved.
 * @license 	GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://www.nooku.org
 */

class ComNinjaModelCore_modules extends KModelAbstract
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
		
		KLoader::load('lib.joomla.application.module.helper');
		$this->_list = JModuleHelper::_load();
		$this->_total = count($this->_list);
	}
}