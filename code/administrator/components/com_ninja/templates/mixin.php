<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category	Napi
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

class NinjaTemplateMixin extends KMixinAbstract implements KObjectServiceable
{
	/**
     * The service identifier
     *
     * @var KServiceIdentifier
     */
    private $__service_identifier;
    
    /**
     * The service container
     *
     * @var KService
     */
    private $__service_container;
     
    /**
     * Constructor.
     *
     * @param   object  An optional KConfig object with configuration options
     */
    public function __construct( KConfig $config = null) 
    { 
        //Set the service container
        if(isset($config->service_container)) {
            $this->__service_container = $config->service_container;
        }
        
        //Set the service identifier
        if(isset($config->service_identifier)) {
            $this->__service_identifier = $config->service_identifier;
        }

        parent::__construct($config);
    }
    
	/**
	 * Get an instance of a class based on a class identifier only creating it
	 * if it doesn't exist yet.
	 *
	 * @param	string|object	The class identifier or identifier object
	 * @param	array  			An optional associative array of configuration settings.
	 * @throws	KObjectException if the service container has not been defined.
	 * @return	object  		Return object on success, throws exception on failure
	 * @see 	KObjectServiceable
	 */
	public function getService($identifier, array $config = array())
	{
	    if(!isset($this->__service_container)) {
	        throw new KObjectException("Failed to call ".get_class($this)."::getService(). No service_container object defined.");
	    }
	    
	    return $this->__service_container->get($identifier, $config);
	}
	
	/**
	 * Gets the service identifier.
	 * 
	 * @throws	KObjectException if the service container has not been defined.
	 * @return	KServiceIdentifier
	 * @see 	KObjectServiceable
	 */
	public function getIdentifier($identifier = null)
	{
		if(isset($identifier)) 
		{
		    if(!isset($this->__service_container)) {
	            throw new KObjectException("Failed to call ".get_class($this)."::getIdentifier(). No service_container object defined.");
	        }
		    
		    $result = $this->__service_container->getIdentifier($identifier);
		} 
		else  $result = $this->__service_identifier; 
	    
	    return $result;
	}

	/**
	 * Escapes a value for output in a view script.
	 *
	 * @param  mixed $var The output to escape.
	 * @return mixed The escaped value.
	 */
	public function escape($var)
	{
	    return htmlspecialchars($var);
	}
}