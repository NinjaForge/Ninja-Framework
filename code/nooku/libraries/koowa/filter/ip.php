<?php
/**
* @version		$Id: ip.php 2857 2011-03-05 14:49:33Z johanjanssens $
* @category		Koowa
* @package      Koowa_Filter
* @copyright    Copyright (C) 2007 - 2010 Johan Janssens. All rights reserved.
* @license      GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
* @link 		http://www.nooku.org
*/

/**
 * IP filter
 * 
 * @author		Johan Janssens <johan@nooku.org>
 * @category	Koowa
 * @package     Koowa_Filter
 */
class KFilterIP extends KFilterAbstract
{
	/**
	 * Validate a value
	 *
	 * @param	scalar	Value to be validated
	 * @return	bool	True when the variable is valid
	 */
	protected function _validate($value)
	{
		$value = trim($value);
		return (false !== filter_var($value, FILTER_VALIDATE_IP));
	}
	
	/**
	 * Sanitize a value
	 *
	 * @param	scalar	Value to be sanitized
	 * @return	string
	 */
	protected function _sanitize($value)
	{
		return preg_replace('#[^a-f0-9:\.]#i', '', $value);
	}
}