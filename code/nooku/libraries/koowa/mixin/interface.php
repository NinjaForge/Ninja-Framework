<?php
/**
 * @version     $Id: interface.php 3565 2011-06-22 00:42:35Z johanjanssens $
 * @category	Koowa
 * @package     Koowa_Mixin
 * @copyright   Copyright (C) 2007 - 2010 Johan Janssens. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * Mixes a chain of command behaviour into a class
 *  
 * @author      Johan Janssens <johan@nooku.org>
 * @category    Koowa
 * @package     Koowa_Mixin
 */
interface KMixinInterface extends KObjectHandlable
{   
    /**
     * Get the methods that are available for mixin. 
     * 
     * @return array An array of methods
     */
    public function getMixableMethods();
    
	/**
     * Get the mixer object
     * 
     * @return object 	The mixer object
     */
    public function getMixer();
    
    /**
     * Set the mixer object
     * 
     * @param object The mixer object
     * @return KMixinInterface
     */
    public function setMixer($mixer);
}