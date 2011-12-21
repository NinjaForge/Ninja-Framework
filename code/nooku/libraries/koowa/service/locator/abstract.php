<?php
/**
 * @version 	$Id: abstract.php 1372 2011-10-11 18:56:47Z stian $
 * @category	Koowa
 * @package		Koowa_Service
 * @subpackage 	Locator
 * @copyright	Copyright (C) 2007 - 2010 Johan Janssens. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 */

/**
 * Service Abstract Locator
 *
 * @author		Johan Janssens <johan@nooku.org>
 * @category	Koowa
 * @package     Koowa_Service
 * @subpackage 	Locator
 */
abstract class KServiceLocatorAbstract extends KObject implements KServiceLocatorInterface
{
	/** 
	 * The type
	 * 
	 * @var string
	 */
	protected $_type = '';
	
	/**
	 * Get the type
	 *
	 * @return string	Returns the type
	 */
	public function getType()
	{
		return $this->_type;
	}
}