<?php
/**
 * @version     $Id: event.php 4477 2012-02-10 01:06:38Z johanjanssens $
 * @category	Koowa
 * @package     Koowa_Event
 * @copyright   Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * Event Class
 *
 * You can call the method stopPropagation() to abort the execution of
 * further listeners in your event listener.
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @category    Koowa
 * @package     Koowa_Event
 */
class KEvent extends KConfig
{
 	/**
     * Priority levels
     */
    const PRIORITY_HIGHEST = 1;
    const PRIORITY_HIGH    = 2;
    const PRIORITY_NORMAL  = 3;
    const PRIORITY_LOW     = 4;
    const PRIORITY_LOWEST  = 5;
 	
 	/**
     * The propagation state of the event
     * 
     * @var boolean 
     */
    protected $_propagate = true;
 	
 	/**
     * The event name
     *
     * @var array
     */
    protected $_name;
    
    /**
     * Constructor.
     *
     * @param	string 			The event name
     * @param   array|KConfig 	An associative array of configuration settings or a KConfig instance.
     */
    public function __construct( $name, $config = array() )
    { 
        parent::__construct($config);
         
        //Set the command name
        $this->_name = $name;
    } 
    
    /**
     * Get the event name
     * 
     * @return string	The event name
     */
    public function getName()
    {
        return $this->_name;
    }
    
    /**
     * Returns whether further event listeners should be triggered.
     *
     * @return boolean 	TRUE if the event can propagate. Otherwise FALSE
     */
    public function canPropagate()
    {
        return $this->_propagate;
    }

    /**
     * Stops the propagation of the event to further event listeners.
     *
     * If multiple event listeners are connected to the same event, no
     * further event listener will be triggered once any trigger calls
     * stopPropagation().
     * 
     * @return KEvent
     */
    public function stopPropagation()
    {
        $this->_propagate = false;
        return $this;
    }
}