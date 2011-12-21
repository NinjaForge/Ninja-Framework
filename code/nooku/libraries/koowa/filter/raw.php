<?php
/**
* @version		$Id: raw.php 2876 2011-03-07 22:19:20Z johanjanssens $
* @category		Koowa
* @package      Koowa_Filter
* @copyright    Copyright (C) 2007 - 2010 Johan Janssens. All rights reserved.
* @license      GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
* @link 		http://www.nooku.org
*/

/**
 * Raw filter
 *
 * Always validates and returns the raw variable
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @category    Koowa
 * @package     Koowa_Filter
 */
class KFilterRaw extends KFilterAbstract
{
    /**
     * Validate a value
     *
     * @param   scalar  Variable to be validated
     * @return  bool    True when the variable is valid
     */
    protected function _validate($value)
    {
        return true;
    }
    
    /**
     * Sanitize a value
     *
     * @param   scalar  Variable to be sanitized
     * @return  mixed
     */
    protected function _sanitize($value)
    {
        return $value;
    }
}