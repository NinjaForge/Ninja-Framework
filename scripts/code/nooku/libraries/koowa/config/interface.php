<?php
/**
 * @version		$Id: interface.php 4266 2011-10-08 23:57:41Z johanjanssens $
 * @category	Koowa
 * @package		Koowa_Config
 * @copyright	Copyright (C) 2007 - 2010 Johan Janssens. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://www.nooku.org
 */

/**
 * Config Interface
 * 
 * KConfig provides a property based interface to an array
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @category    Koowa
 * @package     Koowa_Config
 */
interface KConfigInterface extends IteratorAggregate, ArrayAccess, Countable
{ 
    /**
     * Retrieve a configuration item and return $default if there is no element set.
     *
     * @param string 
     * @param mixed 
     * @return mixed
     */
    public function get($name, $default = null);
    
    /**
     * Append values 
     * 
     * This funciton only adds keys that don't exist and it filters out any duplicate values
     *
     * @param  mixed    A value of an or array of values to be appended
     * @return KConfig
     */
    public function append($config);
    
    /**
     * Return the data 
     *
     * If the data being passed is an instance of KConfig the data will be transformed
     * to an associative array.
     *
     * @return array|scalar
     */
    public static function unbox($data);
    
    /**
     * Return an associative array of the config data.
     *
     * @return array
     */
    public function toArray();
    
 	/**
     * Returns a string with the encapsulated data in JSON format
     *             
     * @return string   returns the data encoded to JSON
     */
    public function toJson();
}