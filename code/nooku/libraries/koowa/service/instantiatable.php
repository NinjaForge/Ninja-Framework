<?php
/**
 * @version     $Id: instantiatable.php 4266 2011-10-08 23:57:41Z johanjanssens $
 * @category    Koowa
 * @package     Koowa_Service
 * @copyright   Copyright (C) 2007 - 2010 Johan Janssens. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 */

/**
 * Service Instantiatable Interface
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @category    Koowa
 * @package     Koowa_Service
 */
interface KServiceInstantiatable
{
    /**
     * Get the object identifier
     * 
     * @param 	object 	An optional KConfig object with configuration options
     * @param 	object	A KServiceInterface object
     * @return  object 
     */
    public static function getInstance(KConfigInterface $config, KServiceInterface $container);
}