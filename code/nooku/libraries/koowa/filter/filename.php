<?php
/**
* @version		$Id: filename.php 4477 2012-02-10 01:06:38Z johanjanssens $
* @category		Koowa
* @package      Koowa_Filter
* @copyright    Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
* @license      GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
* @link 		http://www.nooku.org
*/

/**
 * Filename filter, strips path info 
 *
 * @author		Johan Janssens <johan@nooku.org>
 * @category	Koowa
 * @package     Koowa_Filter
 */
class KFilterFilename extends KFilterAbstract
{
	/**
	 * Validate a value
	 *
	 * @param	scalar	Value to be validated
	 * @return	bool	True when the variable is valid
	 */
	protected function _validate($value)
	{
	   	return ((string) $value === $this->sanitize($value));
	}
	
	/**
	 * Sanitize a value
	 *
	 * @param	scalar	Value to be sanitized
	 * @return	string
	 */
	protected function _sanitize($value)
	{
    	return basename($value);
	}
}