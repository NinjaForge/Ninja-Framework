<?php
/**
 * @version 	$Id: interface.php 2725 2010-10-28 01:54:08Z johanjanssens $
 * @category	Koowa
 * @package		Koowa_Factory
 * @subpackage 	Adapter
 * @copyright	Copyright (C) 2007 - 2010 Johan Janssens. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 */

/**
 * Factory Adapter Interface
 *
 * @author		Johan Janssens <johan@nooku.org>
 * @category	Koowa
 * @package     Koowa_Factory
 * @subpackage 	Adapter
 */
interface KFactoryAdapterInterface extends KCommandInterface
{
	/**
	 * Create an object instance based on a class identifier
	 *
	 * @param 	mixed 	The class identifier
	 * @param 	object 	An optional KConfig object with configuration options
	 * @return 	object|false 	Return object on success, returns FALSE on failure
	 */
	public function instantiate($identifier, KConfig $config);
}