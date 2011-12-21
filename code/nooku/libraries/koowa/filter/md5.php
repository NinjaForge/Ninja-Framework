<?php
/**
* @version		$Id: md5.php 2876 2011-03-07 22:19:20Z johanjanssens $
* @category		Koowa
* @package      Koowa_Filter
* @copyright    Copyright (C) 2007 - 2010 Johan Janssens. All rights reserved.
* @license      GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
* @link 		http://www.nooku.org
*/

/**
 * MD5 filter
 *
 * Validates or sanitizes an md5 hash (32 chars [a-f0-9])
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @category    Koowa
 * @package     Koowa_Filter
 */
class KFilterMd5 extends KFilterAbstract
{
    /**
     * Validate a value
     *
     * @param   scalar  Variable to be validated
     * @return  bool    True when the variable is valid
     */
    protected function _validate($value)
    {
        $value = trim($value);
        $pattern = '/^[a-f0-9]{32}$/';
        return (is_string($value) && preg_match($pattern, $value) == 1);
    }
    
    /**
     * Sanitize a valaue
     *
     * @param   scalar  Variable to be sanitized
     * @return  string
     */
    protected function _sanitize($value)
    {
        $value      = trim(strtolower($value));
        $pattern    = '/[^a-f0-9]*/';
        return substr(preg_replace($pattern, '', $value), 0, 32);
    }
}