<?php
/**
* @version		$Id: dirname.php 1054 2011-06-16 14:35:04Z stian $
* @category		Koowa
* @package      Koowa_Filter
* @copyright    Copyright (C) 2007 - 2010 Johan Janssens. All rights reserved.
* @license      GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
* @link 		http://www.nooku.org
*/

/**
 * Dirname filter 
 *
 * @author		Johan Janssens <johan@nooku.org>
 * @category	Koowa
 * @package     Koowa_Filter
 */
class KFilterDirname extends KFilterAbstract
{
	/**
	 * Validate a value
	 *
	 * @param	scalar	Variable to be validated
	 * @return	bool	True when the variable is valid
	 */
	protected function _validate($value)
	{
		$value = trim($value);
	   	return ((string) $value === $this->sanitize($value));
	}
	
	/**
	 * Sanitize a value
	 *
	 * @param	scalar	Variable to be sanitized
	 * @return	string
	 */
	protected function _sanitize($value)
	{
		$value = trim($value);
    	return dirname($value);
	}
}