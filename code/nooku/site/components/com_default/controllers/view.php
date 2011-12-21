<?php
/**
 * @version     $Id: view.php 934 2011-03-24 17:44:56Z stian $
 * @category    Nooku
 * @package     Nooku_Components
 * @subpackage  Default
 * @copyright   Copyright (C) 2007 - 2010 Johan Janssens. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * Default View Controller
.*
 * @author      Johan Janssens <johan@nooku.org>
 * @category    Nooku
 * @package     Nooku_Components
 * @subpackage  Default
 */
class ComDefaultControllerView extends KControllerView
{
 	/**
     * Constructor
     *
     * @param   object  An optional KConfig object with configuration options
     */
    public function __construct(KConfig $config)
    {
        parent::__construct($config);
 
        //Enqueue the authorization command
        $this->getCommandChain()->enqueue( KFactory::get('site::com.default.command.authorize'));
    }
    
    /**
     * Set the request information
     * 
     * This function translates 'limitstart' to 'offset' for compatibility with Joomla
     *
     * @param array An associative array of request information
     * @return KControllerBread
     */
    public function setRequest(array $request = array())
    {
        if(isset($request['limitstart'])) {
            $request['offset'] = $request['limitstart'];
        }
        
        $this->_request = new KConfig($request);
        return $this;
    }
    
    /**
     * Display action
     * 
     * If the controller was not dispatched manually load the langauges files 
     *
     * @param   KCommandContext A command context object
     * @return  KDatabaseRow(set)   A row(set) object containing the data to display
     */
    protected function _actionDisplay(KCommandContext $context)
    {
        //Load the language file for HMVC requests who are not routed through the dispatcher
        if(!$this->isDispatched()) {
            KFactory::get('lib.joomla.language')->load('com_'.$this->getIdentifier()->package); 
        }
        
        return parent::_actionDisplay($context);
    }
}