<?php
/**
* @version      $Id: alpha.php 2725 2010-10-28 01:54:08Z johanjanssens $
* @category		Koowa
* @package      Koowa_Filter
* @copyright    Copyright (C) 2007 - 2010 Johan Janssens. All rights reserved.
* @license      GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
* @link 		http://www.nooku.org
*/

/**
 * Alphabetic filter.
 *
 * @author		Johan Janssens <johan@nooku.org>
 * @category	Koowa
 * @package     Koowa_Filter
 */
class KFilterAlpha extends KFilterAbstract
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
		
		return ctype_alpha($value);
	}
	
	/**
	 * Sanitize a variable
	 *
	 * @param	scalar	Value to be sanitized
	 * @return	string
	 */
	protected function _sanitize($value)
	{
		$pattern 	= '/[^[a-zA-Z]*/';
    	return preg_replace($pattern, '', $value);
	}
}