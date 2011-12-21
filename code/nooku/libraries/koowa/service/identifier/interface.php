<?php
/**
 * @version 	$Id: interface.php 1372 2011-10-11 18:56:47Z stian $
 * @category	Koowa
 * @package		Koowa_Service
 * @copyright	Copyright (C) 2007 - 2010 Johan Janssens. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 */

/**
 * Service Identifier interface
 *
 * Wraps identifiers of the form [application::]type.component.[.path].name
 * in an object, providing public accessors and methods for derived formats
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @category    Koowa
 * @package     Koowa_Service
 */
interface KServiceIdentifierInterface extends Serializable 
{ 
    /**
     * Formats the indentifier as a [application::]type.component.[.path].name string
     *
     * @return string
     */
    public function __toString();
}