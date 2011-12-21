<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: settings.php 1399 2011-11-01 14:22:48Z stian $
 * @category	Ninja
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

class NinjaDatabaseTableSettings extends KDatabaseTableDefault
{
	/**
	 * The path to the settings xml
	 *
	 * @var dir
	 */
	protected $_xml_path;
	
	/**
	 * Array over xml defaults, separated by path to avoid the same xml doc being parsed queried more than once
	 *
	 * @var array
	 */
	protected $_xml_defaults_cache = array();

	public function __construct(KConfig $config)
	{
		$config->append(array(
			'filters'	=> array(
				'params' => 'json'
			),
			'behaviors'	=> array('orderable')
		));
	
		parent::__construct($config);


		$this->_xml_path = $config->xml_path;

	   		 
	   	foreach($this->getColumns() as $field)
	   	{
	   		if($field->name == 'default')
	   		{
	   			$field->unique = 1;
	   			break;
	   		}
	   		
	   	}
	}
	
	/**
	 * Initializes the options for the object
	 *
	 * Called from {@link __construct()} as a first step of object instantiation.
	 *
	 * @param 	object 	An optional KConfig object with configuration options.
	 * @return  void
	 */
	protected function _initialize(KConfig $config)
	{
		$package = $this->getIdentifier()->package;
		$name	 = KInflector::singularize($this->getIdentifier()->name);
		
		$config->append(array(
			'xml_path'        => JPATH_ADMINISTRATOR.'/components/com_'.$package.'/views/'.$name.'/tmpl/'.$name.'.xml',

		));
		
		parent::_initialize($config);
	}

	/**
	 * Table select method
	 *
	 * The name of the resulting row(set) class is based on the table class name
	 * eg <Mycomp>Table<Tablename> -> <Mycomp>Row(set)<Tablename>
	 * 
	 * This function will return an empty rowset if called without a parameter.
	 *
	 * @param	mixed	KDatabaseQuery, query string, array of row id's, or an id or null
	 * @param 	integer	The database fetch mode. Default FETCH_ROWSET.
	 * @return	KDatabaseRow or KDatabaseRowset depending on the mode. By default will 
	 * 			return a KDatabaseRowset 
	 */
	public function select( $query = null, $mode = KDatabase::FETCH_ROWSET)
	{
		$result = parent::select($query, $mode);

		if($mode === KDatabase::FETCH_FIELD) return $result;

		foreach(is_a($result, 'KDatabaseRowInterface') ? array($result) : $result as $row)
		{
			$params = json_decode($row->params, true);
			if(!is_array($params)) $params = array();
			
			$defaults = $this->_getDefaultsFromXML($row);
			$params = new KConfig($params);
			$params->append($defaults);
    		$row->params = $params->toArray();
		}

		return $result;
	}
	
	/**
	 * Get the default values from the xml document
	 *
	 * @author	Stian Didriksen <stian@ninjaforge.com>
	 * @return  array 	key/value data from the doc
	 */
	protected function _getDefaultsFromXML()
	{
		if(!isset($this->_xml_defaults_cache[$this->_xml_path]))
		{
			$xml		= simplexml_load_file($this->_xml_path);
			$values	= array();

			foreach($xml->children() as $i => $group)
			{
				$value = array();
				foreach($group->children() as $i => $element)
				{				
					if(!$element['default']) continue;
					$value[(string)$element['name']] = (string)$element['default'];
				}
				if(count($value) < 1) continue;
				$values[(string)(isset($group['name']) ? $group['name'] : $group['group'])] = $value;
				
			}
			
			$this->_xml_defaults_cache[$this->_xml_path] = $values;
		}
		
		return $this->_xml_defaults_cache[$this->_xml_path];
	}

	public function getXMLPath()
	{
		return $this->_xml_path;
	}
}