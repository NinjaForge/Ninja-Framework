<?php
/**
* @version		$Id: interface.php 2876 2011-03-07 22:19:20Z johanjanssens $
* @category		Koowa
* @package      Koowa_Filter
* @copyright    Copyright (C) 2007 - 2010 Johan Janssens. All rights reserved.
* @license      GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
* @link 		http://www.nooku.org
*/

/**
 * Filter interface
 *
 * Validate or sanitize data
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @category    Koowa
 * @package     Koowa_Filter
 */
interface KFilterInterface extends KCommandInterface
{
    /**
     * Validate a value or data collection
     *
     * NOTE: This should always be a simple yes/no question (is $value valid?), so 
     * only true or false should be returned
     * 
     * @param   mixed   Data to be validated
     * @return  bool    True when the variable is valid
     */
    public function validate($value);
    
    /**
     * Sanitize a value or data collection
     *
     * @param   mixed   Data to be sanitized
     * @return  mixed
     */
    public function sanitize($value);
}