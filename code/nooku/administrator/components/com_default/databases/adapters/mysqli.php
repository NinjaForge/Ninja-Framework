<?php
/**
 * @version     $Id: mysqli.php 994 2011-04-04 20:35:56Z stian $
 * @category	Nooku
 * @package     Nooku_Components
 * @subpackage  Default
 * @copyright   Copyright (C) 2007 - 2010 Johan Janssens. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */


/**
 * Default Database MySQLi Adapter
.*
 * @author      Johan Janssens <johan@nooku.org>
 * @category    Nooku
 * @package     Nooku_Components
 * @subpackage  Default
 */
class ComDefaultDatabaseAdapterMysqli extends KDatabaseAdapterMysqli
{ 
    protected function _initialize(KConfig $config)
    {
        $db = KFactory::get('lib.joomla.database');
        
		$resource = method_exists($db, 'getConnection') ? $db->getConnection() : $db->_resource;
		$prefix   = method_exists($db, 'getPrefix')     ? $db->getPrefix()     : $db->_table_prefix;
        
        $config->append(array(
    		'connection'   => $resource,
            'table_prefix' => $prefix,
        ));
          
        parent::_initialize($config);
    }
}