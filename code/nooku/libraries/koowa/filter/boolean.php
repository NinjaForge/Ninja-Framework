<?php
/**
* @version		$Id: boolean.php 2725 2010-10-28 01:54:08Z johanjanssens $
* @category		Koowa
* @package      Koowa_Filter
* @copyright    Copyright (C) 2007 - 2010 Johan Janssens. All rights reserved.
* @license      GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
* @link 		http://www.nooku.org
*/

/**
 * Boolean filter
 *
 * @author		Johan Janssens <johan@nooku.org>
 * @category	Koowa
 * @package     Koowa_Filter
 */
class KFilterBoolean extends KFilterAbstract
{
	/**
	 * Validate a value
	 * 
	 *  Returns TRUE for boolean values: "1", "true", "on" and "yes", "0", 
	 * "false", "off", "no", and "". Returns FALSE for all non-boolean values. 
	 *
	 * @param	scalar	Value to be validated
	 * @return	bool	True when the variable is valid
	 */
	protected function _validate($value)
	{
		return (null !== filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) );
	}
	
	/**
	 * Sanitize a value
	 * 
	 * Returns TRUE for "1", "true", "on" and "yes". Returns FALSE for all other values. 
	 *
	 * @param	scalar	Value to be sanitized
	 * @return	bool
	 */
	protected function _sanitize($value)
	{
		return filter_var($value, FILTER_VALIDATE_BOOLEAN);
	}
}