<?php
/**
 * @version		$Id: event.php 1372 2011-10-11 18:56:47Z stian $
 * @category	Koowa
 * @package		Koowa_Command
 * @copyright	Copyright (C) 2007 - 2010 Johan Janssens. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://www.nooku.org
 */

/**
 * Event Command
 * 
 * The event commend will translate the command name to a onCommandName format 
 * and let the event dispatcher dispatch to any registered event handlers.
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @category    Koowa
 * @package     Koowa_Command
 * @uses        KService
 * @uses        KEventDispatcher
 * @uses        KInflector
 */
class KCommandEvent extends KCommand
{
    /**
     * The event dispatcher object
     *
     * @var KEventDispatcher
     */
    protected $_dispatcher;
    
    /**
     * Constructor.
     *
     * @param   object  An optional KConfig object with configuration options
     */
    public function __construct( KConfig $config = null) 
    { 
        //If no config is passed create it
        if(!isset($config)) $config = new KConfig();
        
        parent::__construct($config);
        
        $this->_dispatcher = $config->dispatcher;
    }
    
    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   object  An optional KConfig object with configuration options
     * @return void
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'dispatcher'   => $this->getService('koowa:event.dispatcher')
        ));

        parent::_initialize($config);
    }
    
    /**
     * Command handler
     * 
     * @param   string      The command name
     * @param   object      The command context
     * @return  boolean     Always returns true
     */
    public function execute( $name, KCommandContext $context) 
    {
        $type = '';
        
        if($context->caller)
        {   
            $identifier = clone $context->caller->getIdentifier();
            
            if($identifier->path) {
                $type = array_shift($identifier->path);
            } else {
                $type = $identifier->name;
            }
        }
        
        $parts = explode('.', $name);   
        $event = 'on'.ucfirst(array_shift($parts)).ucfirst($type).KInflector::implode($parts);
       
        $this->_dispatcher->dispatchEvent($event, clone($context));
        
        return true;
    }
}