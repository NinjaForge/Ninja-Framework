<?php
/**
* @version		$Id: path.php 4477 2012-02-10 01:06:38Z johanjanssens $
* @category		Koowa
* @package      Koowa_Filter
* @copyright    Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
* @license      GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
* @link 		http://www.nooku.org
*/

/**
 * Path Filter
 *
 * Filters Windows and Unix style file paths
 *
 * @author		Johan Janssens <johan@nooku.org>
 * @category	Koowa
 * @package     Koowa_Filter
 */
class KFilterPath extends KFilterAbstract
{
	const PATTERN = '#^(?:[a-z]:/|~*/)[a-z0-9_\.-\s/~]*$#i';
 
    /**
     * Validate a value
     *
     * @param   scalar  Value to be validated
     * @return  bool    True when the variable is valid
     */
    protected function _validate($value)
    {
        $value = trim(str_replace('\\', '/', $value));
        return (is_string($value) && (preg_match(self::PATTERN, $value)) == 1);
    }
 
    /**
     * Sanitize a value
     *
     * @param   mixed   Value to be sanitized
     * @return  string
     */
    protected function _sanitize($value)
    {
        $value = trim(str_replace('\\', '/', $value));
        preg_match(self::PATTERN, $value, $matches);
        $match = isset($matches[0]) ? $matches[0] : '';
               
        return $match;
    }
}