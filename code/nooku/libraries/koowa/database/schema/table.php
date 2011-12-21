<?php
/**
 * @version		$Id: table.php 2725 2010-10-28 01:54:08Z johanjanssens $
 * @category	Koowa
 * @package     Koowa_Database
 * @subpackage  Schema
 * @copyright	Copyright (C) 2007 - 2010 Johan Janssens. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://www.nooku.org
 */

/**
 * Database Schema Table Class
 *
 * @author		Johan Janssens <johan@nooku.org>
 * @category	Koowa
 * @package     Koowa_Database
 * @subpackage  Schema
 */
class KDatabaseSchemaTable extends KObject
{
	/**
	 * Table name
	 * 
	 * @var string
	 */
	public $name;
	
	/**
	 * The storage engine
	 * 
	 * @var string
	 */
	public $engine;
	
	/**
	 * Table type
	 * 
	 * @var	string
	 */
	public $type;
	
	/**
	 * Table length
	 * 
	 * @var integer
	 */
	public $length;
	
	/**
	 * Table next auto increment value
	 * 
	 * @var integer
	 */
	public $autoinc;
	
	/**
	 * The tables character set and collation
	 * 
	 * @var string
	 */
	public $collation;
	
	/**
	 * The tables description
	 * 
	 * @var string
	 */
	public $description;
	
	/**
	 * List of behaviors
	 * 
	 * Associative array of behaviors, where key holds the behavior identifier string
	 * and the value is an identifier object.
	 * 
	 * @var	array
	 */
	public $behaviors = array();
}