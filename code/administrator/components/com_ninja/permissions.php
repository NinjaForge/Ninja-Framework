<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category	Ninja
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * NinjaPermissions
 *
 * Singleton for getting permissions, as well as making it possible for 
 * plugins to add new permission objects.
 * Used by various ninja extensions
 * 
 * @author Stian Didriksen <stian@ninjaforge.com>
 */
abstract class NinjaPermissions extends KObjectArray
{
	/**
	 * Constructor
	 *
	 * @param 	object 	An optional KConfig object with configuration options.
	 */
	public function __construct(KConfig $config = null)
	{
		//If no config is passed create it
		if(!isset($config)) $config = new KConfig();

		parent::__construct($config);

		$data			= is_a($config->data, 'KConfig') ? $config->data->toArray() : $config->data;
		$this->_data	= $data;
	}

	/**
	 * Initializes the options for the object
	 *
	 * Called from {@link __construct()} as a first step of object instantiation.
	 *
	 * @param 	object 	An optional KConfig object with configuration options.
	 * @return void
	 */
	protected function _initialize(KConfig $config)
	{
		$config->append(array(
			'data'	=> array()
		));

		parent::_initialize($config);
	}
	
	/**
	 * Get the permission objects
	 *
	 * @return array $objects	The objects array, without the default level
	 */
	public function getObjects()
	{
		return array_keys($this->_data);
	}

	/**
	 * Add permission object to this instance
	 *
	 * @param  	string	$object	Object name, which needs to be singular and variablized, 
	 * 							e.g. 'foo_bar', no camelCase. Object names are later run by
	 *							KInflector::humanize() for titles and such.
	 * @param	int		$level	Default permission level.		Defaults to 1
	 * @return 			$this
	 */
	public function addObject($object, $level = 1)
	{
		$this->_data[$object] = (int) $level;

		return $this;
	}
}