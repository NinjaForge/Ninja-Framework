<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: modules.php 913 2011-03-17 18:19:44Z stian $
 * @package		Koowa
 * @copyright	Copyright (C) 2010 Nooku. All rights reserved.
 * @license 	GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://www.nooku.org
 */

class ComNinjaModelJoomlaModules extends KModelAbstract
{	
	/**
	 * Constructor
	 *
	 * @param	array An optional associative array of configuration settings.
	 */
	public function __construct(KConfig $config)
	{
		parent::__construct($config);
		
		$this->_state
						->insert('position', 'cmd')
						->insert('module', 'cmd')
						->insert('limit', 'int', 0);
		
		KLoader::load('lib.joomla.application.module.helper');
		$this->_list = &JModuleHelper::_load();
		$this->_total = count($this->_list);
	}
	
	/**
	 * Append new modules to the modules array
	 *
	 * @author Stian Didriksen <stian@ninjaforge.com>
	 * @param $module Module object
	 * @return $this
	 */
	public function append($module)
	{
		$this->_list[] = $module;
		
		return $this;
	}
	
	/**
	 * Count modules based on states
	 *
	 * @author Stian Didriksen <stian@ninjaforge.com>
	 * @return int
	 */
	public function count()
	{
		return count(array_filter($this->_list, array($this, '_count')));
	}
	
	/**
	 * Count callback function for array_filter
	 */
	protected function _count($module)
	{
		$result = true;
		if($this->_state->position)
		{
			if($this->_state->position != $module->position) return false;
		}

		if($this->_state->module)
		{
			if($this->_state->module != $module->module) return false;
		}

		return $result;
	}

	/**
	 * Gets the available module positions
	 *
	 * @author Stian Didriksen <stian@ninjaforge.com>
	 * @return array
	 */
	public function getPositions()
	{
		$template = KFactory::get('lib.joomla.application')->getTemplate();
		
		//Don't fail silently if KDEBUG is false
		if(KDEBUG)	$xml	  =  simplexml_load_file(JPATH_THEMES.'/'.$template.'/templateDetails.xml');
		else		$xml	  = @simplexml_load_file(JPATH_THEMES.'/'.$template.'/templateDetails.xml');
		
		//If there's a xml parser error in the template xml, $xml->positions wont exist
		if(!isset($xml->positions)) return array();
		
		$positions = array();
		foreach($xml->positions->children() as $position)
		{
			$positions[] = (string)$position;
		}
		
		return $positions;
		
		//The following fails miserably
		JLoader::import('module', JPATH_ADMINISTRATOR.'/components/com_modules/models');
		$model = JModel::getInstance('Module', 'ModulesModel');
		
		//Model will fail if we don't supply the clientId
		$model->setState('clientId', (int) KFactory::get('lib.joomla.application')->isAdmin());

		return $model->getPositions();
	}
}