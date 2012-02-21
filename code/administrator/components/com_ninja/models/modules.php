<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @package		Koowa
 * @copyright	Copyright (C) 2010 Nooku. All rights reserved.
 * @license 	GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://www.nooku.org
 */

class NinjaModelModules extends KModelAbstract
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
		
		jimport('joomla.application.module.helper');
	}
	
	/**
     * Get a list of items
     *
     * @return  object
     */
    public function getList()
    {
    	if(!isset($this->_list))
    	{
    		$this->_list = &JModuleHelper::_load();
    	}

        return $this->_list;
    }

    /**
     * Get the total amount of items
     *
     * @return  int
     */
    public function getTotal()
    {
    	if(!isset($this->_totial))
    	{
    		$this->_total = count($this->getList());

    	}

        return $this->_total;
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
		$list 		 = $this->getList();
		$list[]      = $module;
		$this->_list = $list;

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
		return count(array_filter($this->getList(), array($this, '_count')));
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
		$template = JFactory::getApplication()->getTemplate();
		
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
		$model->setState('clientId', (int) JFactory::getApplication()->isAdmin());

		return $model->getPositions();
	}
}