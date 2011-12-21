<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: filesystem.php 794 2011-01-10 18:44:32Z stian $
 * @category	Napi
 * @package		Napi_Model
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * Template Model Class
 * Provides interaction with a templates folder
 *
 * @author		Stian Didriksen <stian@ninjaforge.com>
 * @category	Napi
 * @package     Napi_Model
 */
class ComNinjaModelFilesystem extends KModelAbstract
{

	/**
	 * Table object or identifier (APP::com.COMPONENT.table.TABLENAME)
	 *
	 * @var	string|object
	 */
	protected $_path;

	/**
	 * Constructor
     *
     * @param	array An optional associative array of configuration settings.
	 */
	public function __construct(KConfig $options)
	{
		parent::__construct($options);

		// set the table associated to the model
		if(isset($options->path)) {
			$this->_path = $options->path;
		}
		else
		{
			$package		= $this->_identifier->package;
			$this->_path	= JPATH_ROOT.DS.'components'.DS.'com_'.$package.DS.KInflector::pluralize($this->_identifier->name);
		}
		
		// Set the state
		$this->_state
			->insert('id'       , 'int', 0)
			->insert('limit'    , 'int', 20)
			->insert('offset'   , 'int', 0)
			->insert('order'    , 'cmd')
			->insert('direction', 'word', 'asc')
			->insert('search'   , 'string')
			->insert('name'   , 'admin::com.ninja.filter.path');
		
		return $this;
	}

	/**
	 * Method to get a table object, load it if necessary.
	 *
	 * @param	array	Options array for view. Optional.
	 * @return	object	The table object
	 */
	public function getPath(array $options = array())
	{
		if(!is_object($this->_path)) {
			$package		= $this->_identifier->package;
			$this->_path	= JPATH_ROOT.DS.'components'.DS.'com_'.$package.DS.KInflector::pluralize($this->_identifier->name);
		}

		return $this->_path;
	}

	/**
	 * Method to set a table object or identifier
	 *
	 * @param	string|object The table identifier to be used in KFactory or a table object
	 * @return	this
	 */
	public function setPath($identifier)
	{
		$this->_path = $identifier;
		return $this;
	}

    /**
     * Get a list of items which represnts a  table rowset
     *
     * @return KDatabaseRowset
     */
    public function getTotal()
    {
        // Get the data if it doesn't already exist
        if (!isset($this->_total))
        {
           	//$table = $this->getTable();
        	//$query = $this->_buildQuery();
        	$this->_total = count($this->_fetchItems());
        }

        return parent::getTotal();
    }
    
    /**
     * Get a list of items which represnts a  table rowset
     *
     * @return KDatabaseRowset
     */
    public function getList()
    {
        // Get the data if it doesn't already exist
        if (!isset($this->_list))
        {
        	$this->_list = $this->_filterItems();
        	//$table = $this->getTable();
        	//$query = $this->_buildQuery();
        	//$this->_list = $table->fetchRowset($query);
        }

        return parent::getList();
    }

    /**
     * Get the total amount of items
     *
     * @return  int
     */
    protected function _fetchItems()
    {
        //Autoload the needed classes
        jimport('joomla.filesystem.folder');
		$folders = array();
		foreach (JFolder::folders($this->_path) as $folder)
		{
			$folders[] = $folder;
		}

        return $folders;
    }
    
    /**
     * Get the total amount of items
     *
     * @return  int
     */
    protected function _filterItems()
    {
    	//Autoload the needed classes
    	jimport('joomla.filesystem.file');
		$items = $this->_fetchItems();
		$templates = array();
		
		$offset = $this->_state->limit;
		foreach (range($this->_state->offset, $this->_state->limit+$this->_state->offset-1) as $i => $o)
		{
			if(empty($items[$o])) continue;
			if(isset($xml)) unset($xml); 
			$xml = JFactory::getXMLParser('simple');
			if(!JFile::exists($this->_path.DS.$items[$o].DS.$items[$o].'.xml')) continue;
			$xml->loadfile($this->_path.DS.$items[$o].DS.$items[$o].'.xml');
			$template = $this->_parseTemplate($xml->document->children());
			$template->id = $items[$i];
			$templates[] = $template;
		}

        return $templates;
    }
    
    protected function _parseTemplate($xml)
    {
    	$template = new KObject;
    	foreach ($xml as $name) 
    	{
    		$template->set($name->name(), $name->data());
    	}
    	return $template;
    }
    
    /**
     * Method to get a item object which represents a table row
     *
     * @return Item
     */
    public function getItem()
    {
        // Get the data if it doesn't already exist
        if (!isset($this->_item))
        {
        	if($this->_state->id)
        	{
	        	foreach ($this->_filterItems() as $item)
	        	{
	        		if($item->name != $this->_state->id) continue;
	       			$template = $item;
	        	}
	        	$this->_item = $template;
	        }
	        else
	        {
	        	$this->_item = current($this->_filterItems());
	        }
        }

        return parent::getItem();
    }
}