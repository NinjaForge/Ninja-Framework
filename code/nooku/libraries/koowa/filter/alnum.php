<?php
/**
* @version		$Id: alnum.php 1372 2011-10-11 18:56:47Z stian $
* @category		Koowa
* @package      Koowa_Filter
* @copyright    Copyright (C) 2007 - 2010 Johan Janssens. All rights reserved.
* @license      GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
* @link 		http://www.nooku.org
*/

/**
 * Alphanumeric filter.
 *
 * @author		Johan Janssens <johan@nooku.org>
 * @category	Koowa
 * @package     Koowa_Filter
 */
class KFilterAlnum extends KFilterAbstract
{
	/**
	 * Validate a variable
	 *
	 * @param	scalar	Value to be validated
	 * @return	bool	True when the variable is valid
	 */
	protected function _validate($value)
	{
		$value = trim($value);
		
		return ctype_alnum($value);
	}
	
	/**
	 * Sanitize a variable
	 *
	 * @param	scalar	Value to be sanitized
	 * @return	string
	 */
	protected function _sanitize($value)
	{
		$value = trim($value);
		
	    $pattern 	= '/[^\w]*/';
    	return preg_replace($pattern, '', $value);
	}
}