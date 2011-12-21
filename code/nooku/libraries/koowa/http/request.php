<?php
/**
 * @version     $Id: request.php 928 2011-03-23 20:31:16Z stian $
 * @category	Koowa
 * @package     Koowa_Http
 * @copyright   Copyright (C) 2007 - 2010 Johan Janssens. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * HTTP Request class
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @category    Koowa
 * @package     Koowa_Http
 */
class KHttpRequest
{
    // Methods
    const GET     = 'GET';  
    const POST    = 'POST';  
    const PUT     = 'PUT';  
    const DELETE  = 'DELETE';  
    const HEAD    = 'HEAD';  
    const OPTIONS = 'OPTIONS';  
}