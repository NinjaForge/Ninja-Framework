<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: settings.php 980 2011-04-04 20:26:19Z stian $
 * @category	Ninja
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

JLoader::register('JParameter', JPATH_LIBRARIES.'/joomla/html/parameter.php');

class ComNinjaModelSettings extends ComDefaultModelDefault
{
	public function __construct(KConfig $options)
	{
		parent::__construct($options);
		
		//lets get our states
	   	$this->_state
	   		 ->insert('active', 'boolean', false)
	   		 ->insert('enabled', 'int', KFactory::get('lib.joomla.application')->isSite())
	   		 ->insert('default', 'boolean', false);
	}
	
	/**
	 * Method to get a KConfig object which represents a table row params
	 *
	 * If no id state are set, and the application isn't JAdmin, 
	 * then the current page parameters will be checked for an id.
	 * If there are no id state, or page parameter settings id, the default setting will be fetched
	 *
	 * @return KConfig
	 */
	public function getParams()
	{
		if (!isset($this->_params))
		{
			$table  = $this->getTable();
   			$query = $table->getDatabase()->getQuery();

			if(!$this->_state->id && !KFactory::get('lib.joomla.application')->isAdmin())
			{
				$params = $this->_getPageParameters();
				$package = $this->getIdentifier()->package;
				if(array_key_exists($package.'_setting_id', $params)) $this->_state->id = $params[$package.'_setting_id'];
			}
			elseif(KFactory::get('lib.joomla.application')->isSite())
			{
				$query->where('default', '=', 1);
			}

			$this->_buildQueryColumns($query);
			$this->_buildQueryFrom($query);
			$this->_buildQueryJoins($query);
			$this->_buildQueryWhere($query);
			$this->_buildQueryGroup($query);
			$this->_buildQueryHaving($query);
			$query->order('default', 'desc');

		 	$this->_params = $table->select($query, KDatabase::FETCH_ROW);

			//No settings exists with this query get the default one or any row really
			if(!$this->_params->id)
			{
				$query = $table->getDatabase()->getQuery();
				unset($this->_state->id);
				$this->_buildQueryColumns($query);
				$this->_buildQueryFrom($query);
				$this->_buildQueryJoins($query);
				$this->_buildQueryWhere($query);
				$this->_buildQueryGroup($query);
				$this->_buildQueryHaving($query);
				$query->order('default', 'desc');
				
				$this->_params = $table->select($query, KDatabase::FETCH_ROW);
			}


		 	if(!KFactory::get('lib.joomla.application')->isAdmin())
		 	{
		 		$this->_params->params->append($this->_getPageParameters());
		 	}
		}

		return $this->_params->params;
	}
	
	/**
	 * KFactory::get('lib.joomla.application')->getPageParameters() doesn't always work as expected, so we do this
	 *
	 * @author Stian Didriksen <stian@ninjaforge.com>
	 * @return array 	page parameter values
	 */
	protected function _getPageParameters()
	{
		$menus	= JSite::getMenu();
		$menu	= $menus->getActive() ? $menus->getActive() : $menus->getDefault();
		$params	= new JParameter($menu->params);
		return (array) $params->_registry['_default']['data'];
	}
		
	public function getItem()
	{
		if(!$this->_item)
		{
			$identifier = $this->getIdentifier();
			$isNotSetting = KRequest::get('get.view', 'cmd') != 'setting';
			
			$params = array();
			if(!KFactory::get('lib.joomla.application')->isAdmin())
			{
				$menus	= JSite::getMenu();
				$menu	= $menus->getActive() ? $menus->getActive() : $menus->getDefault();
				$settings	= new JParameter($menu->params);
				$params = (array) $settings->_registry['_default']['data'];
				$pk		= KFactory::get($this->getTable())->getPrimaryKey();
				$name	= $pk['id']->name;

				$id = !empty($params[$name]) ? $params[$name] : false;
				if($id)
				{
					$table = $this->getTable();
					
					$item = $table->select($id, KDatabase::FETCH_ROW);
				}
				else if($isNotSetting)
				{
					$item = $this->getDefault();
				}
			} else {
				if($isNotSetting)
				{
					$item = $this->getDefault();
				}
				else
				{
					$item = parent::getItem();
				}
			}
	
			$item->params->append($params);
			$item->xml		= simplexml_load_file($this->getTable()->getXMLPath());
			
			$this->_item = $item;
		}

		return $this->_item;
	}
	
	public function getDefault()
	{
		$table = KFactory::get($this->getTable());
		$query = $table->getDatabase()->getQuery()
					->where('default', '=', 1);
		
		return $table->select($query, KDatabase::FETCH_ROW);
	}
	
	/**
	 * Query WHERE clause
	 */
	protected function _buildQueryWhere(KDatabaseQuery $query)
	{
		parent::_buildQueryWhere($query);
	
		if($search = $this->_state->search)
		{
			$query->where('tbl.title', 'LIKE', '%'.$search.'%');
		}
		
		if($this->_state->enabled !== false && $this->_state->enabled !== '')
		{
			$query->where('tbl.enabled', '=', $this->_state->enabled);
		}
	}
}