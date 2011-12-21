<?php
/**
 * @version     $Id: default.php 4266 2011-10-08 23:57:41Z johanjanssens $
 * @category	Nooku
 * @package     Nooku_Components
 * @subpackage  Default
 * @copyright   Copyright (C) 2007 - 2010 Johan Janssens. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */


/**
 * Default Controller
.*
 * @author      Johan Janssens <johan@nooku.org>
 * @category    Nooku
 * @package     Nooku_Components
 * @subpackage  Default
 */
class ComDefaultControllerDefault extends KControllerService
{    
	/**
	 * Constructor
	 *
	 * @param 	object 	An optional KConfig object with configuration options.
	 */
	public function __construct(KConfig $config)
	{
		parent::__construct($config);

		if($config->persistable && $this->isDispatched()) {
			$this->addBehavior('persistable');
		}
	}
	
	/**
     * Initializes the default configuration for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param 	object 	An optional KConfig object with configuration options.
     * @return void
     */
    protected function _initialize(KConfig $config)
    {
    	/* 
         * Disable controller persistency on non-HTTP requests, e.g. AJAX, and requests containing 
         * the tmpl variable set to component, e.g. requests using modal boxes. This avoids 
         * changing the model state session variable of the requested model, which is often 
         * undesirable under these circumstances. 
         */  
        
        $config->append(array(
    		'persistable'  => (KRequest::type() == 'HTTP' && KRequest::get('get.tmpl','cmd') != 'component'),
            //'behaviors'  =>  array('cacheable')
        ));

        parent::_initialize($config);
    }
 	
 	/**
     * Read action
     *
     * This functions implements an extra check to hide the main menu is the view name
     * is singular (item views)
     *
     *  @return KDatabaseRow    A row object containing the selected row
     */
    protected function _actionRead(KCommandContext $context)
    {
        //Perform the read action
        $row = parent::_actionRead($context);
        
        //Add the notice if the row is locked
        if(isset($row))
        {
            if(!isset($this->_request->layout) && $row->isLockable() && $row->locked()) {
                JFactory::getApplication()->enqueueMessage($row->lockMessage(), 'notice');
            }
        }

        return $row;
    }
    
	/**
     * Browse action
     * 
     * Use the application default limit if no limit exists in the model and limit the
     * limit to a maximum of 100.
     *
     * @param   KCommandContext A command context object
     * @return  KDatabaseRow(set)   A row(set) object containing the data to display
     */
    protected function _actionBrowse(KCommandContext $context)
    {
        if($this->isDispatched()) 
        {
            $limit = $this->getModel()->get('limit');
            
            //If limit is empty use default
            if(empty($limit)) {
                $limit = JFactory::getApplication()->getCfg('list_limit');
            }

            //Limit cannot be larger then 100
            if($limit > 100) {
                $limit = 100;
            }
            
            $this->limit = $limit; 
        }
         
        return parent::_actionBrowse($context);
    }
    
    /**
     * Display action
     * 
     * This function will load the language files of the component if the controller was
     * not dispatched. 
     *
     * @param   KCommandContext A command context object
     * @return  KDatabaseRow(set)   A row(set) object containing the data to display
     */
    protected function _actionGet(KCommandContext $context)
    {
        //Load the language file for HMVC requests who are not routed through the dispatcher
        if(!$this->isDispatched()) {
            JFactory::getLanguage()->load('com_'.$this->getIdentifier()->package); 
        }
         
        return parent::_actionGet($context);
    }
    
	/**
     * Set a request property
     * 
     *  This function translates 'limitstart' to 'offset' for compatibility with Joomla
     *
     * @param  	string 	The property name.
     * @param 	mixed 	The property value.
     */
 	public function __set($property, $value)
    {          
        if($property == 'limitstart') {
            $property = 'offset';
        } 
        	
        parent::__set($property, $value);     
  	}
}