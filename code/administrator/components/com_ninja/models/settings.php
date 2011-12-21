<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: settings.php 1437 2011-12-02 11:21:58Z richie $
 * @category	Ninja
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

JLoader::register('JParameter', JPATH_LIBRARIES.'/joomla/html/parameter.php');

class NinjaModelSettings extends ComDefaultModelDefault
{
    /**
     * The params instance
     *
     * @var mixed
     */
    protected $_params;

	public function __construct(KConfig $options)
	{
		parent::__construct($options);
		
		//lets get our states
	   	$this->_state
	   		 ->insert('active', 'boolean', false)
	   		 ->insert('enabled', 'int', JFactory::getApplication()->isSite())
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
	    $identifier = 'com://'.$this->getIdentifier()->application.'/'.$this->getIdentifier()->package.'.settings';
		if (!KService::has($identifier))
		{
			$table  = $this->getTable();
   			$query = $table->getDatabase()->getQuery();

			if(!$this->_state->id && !JFactory::getApplication()->isAdmin())
			{
				$params = $this->_getPageParameters();
				$package = $this->getIdentifier()->package;
				if(array_key_exists($package.'_setting_id', $params)) $this->_state->id = $params[$package.'_setting_id'];
			}
			elseif(JFactory::getApplication()->isSite())
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

		 	$item = $table->select($query, KDatabase::FETCH_ROW);

			//No settings exists with this query get the default one or any row really
			if(!$item->id)
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
				
				$item = $table->select($query, KDatabase::FETCH_ROW);
			}


		 	if(!JFactory::getApplication()->isAdmin())
		 	{
		 	    if(is_array($item->params)) $item->params = new KConfig($item->params);
		 		$item->params->append($this->_getPageParameters());
		 	}
		 	
		 	if(isset($item->params) && is_array($item->params)) $item->params = new KConfig($item->params);
		 	
		 	KService::set($identifier, $item);
		}

		return KService::get($identifier)->params;
	}
	
	/**
	 * JFactory::getApplication()->getPageParameters() doesn't always work as expected, so we do this
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
			if(!JFactory::getApplication()->isAdmin())
			{
				$menus	= JSite::getMenu();
				$menu	= $menus->getActive() ? $menus->getActive() : $menus->getDefault();
				$settings	= new JParameter($menu->params);
				$params = (array) $settings->_registry['_default']['data'];
				$pk		= $this->getTable()->getPrimaryKey();
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
				$item->params->append($params);
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
			$item->xml		= simplexml_load_file($this->getTable()->getXMLPath());
			
			$this->_item = $item;
		}

		return $this->_item;
	}
	
	public function getDefault()
	{
		$table = $this->getService($this->getTable());
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
		
		if($this->_state->enabled !== false && $this->_state->enabled !== 0)
		{
			$query->where('tbl.enabled', '=', $this->_state->enabled);
		}
	}
}