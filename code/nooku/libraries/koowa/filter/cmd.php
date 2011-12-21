<?php
/**
* @version		$Id: cmd.php 1054 2011-06-16 14:35:04Z stian $
* @category		Koowa
* @package      Koowa_Filter
* @copyright    Copyright (C) 2007 - 2010 Johan Janssens. All rights reserved.
* @license      GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
* @link 		http://www.nooku.org
*/

/**
 * Command filter.
 *
 * A 'command' is a string containing only the characters [A-Za-z0-9.-_]. Used 
 * for names of views, controllers, etc 
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @category    Koowa
 * @package     Koowa_Filter
 */
class KFilterCmd extends KFilterAbstract
{
    /**
     * Validate a value
     *
     * @param   scalar  Value to be validated
     * @return  bool    True when the variable is valid
     */
    protected function _validate($value)
    {
        $value = trim($value);
        $pattern = '/^[A-Za-z0-9.\-_]*$/';
        return (is_string($value) && (preg_match($pattern, $value)) == 1);
    }
    
    /**
     * Sanitize a value
     *
     * @param   mixed   Value to be sanitized
     * @return  string
     */
    protected function _sanitize($value)
    {
        $value = trim($value);
        $pattern    = '/[^A-Za-z0-9.\-_]*/';
        return preg_replace($pattern, '', $value);
    }
}