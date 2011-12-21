<?php
/**
 * @version		$Id: decorator.php 2725 2010-10-28 01:54:08Z johanjanssens $
 * @category	Koowa
 * @package		Koowa_Pattern
 * @subpackage	Decorator
 * @copyright	Copyright (C) 2007 - 2010 Johan Janssens. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://www.nooku.org
 */

/**
 * Decorator class
 *
 * @author		Johan Janssens <johan@nooku.org>
 * @category	Koowa
 * @package     Koowa_Pattern
 * @subpackage  Decorator
 * @uses 		KObject
 */
abstract class KPatternDecorator extends KObject
{
	/**
     * Class methods
     *
     * @var array
     */
    private $__methods = array();
	
	/**
	 * The decorated object
	 *
	 * @var object
	 */
	protected $_object;

	/**
	 * Constructor
	 *
	 * @param	object	The object to decorate
	 * @return	void
	 */
	public function __construct($object)
	{
		$this->_object = $object;
	}

	/**
	 * Get the decorated object
	 *
	 * @return	object The decorated object
	 */
	public function getObject()
	{
		return $this->_object;
	}

	/**
	 * Set the decorated object
	 *
	 * @param 	object
	 * @return 	KPatternDecorator
	 */
	public function setObject($object)
	{
		$this->_object = $object;
		return $this;
	}

	/**
	 * Get a list of all the available methods
	 *
	 * This function returns an array of all the methods, both native and mixed.
	 * It will also return the methods exposed by the decorated object.
	 *
	 * @return array An array
	 */
	public function getMethods()
	{
		if(!$this->__methods)
		{
			$methods = array();
			$object  = $this->getObject();

			if(!($object instanceof KObject)) 
			{
				$reflection	= new ReflectionClass($object);
     			foreach($reflection->getMethods() as $method) {
     				$methods[] = $method->name;
     			}
     		} 
     		else $methods = $object->getMethods();
     		
     		$this->__methods = array_merge(parent::getMethods(), $methods);
		}
     	
		return $this->__methods;
	}

	/**
     * Checks if the decorated object or one of it's mixins inherits from a class.
     *
     * @param 	string|object 	The class to check
     * @return 	boolean 		Returns TRUE if the object inherits from the class
     */
	public function inherits($class)
    {
		$result = false;
    	$object = $this->getObject();

        if($object instanceof KObject) {
          	$result = $object->inherits($class);
        } else {
        	$result = $object instanceof $class;
        }

        return $result;
    }

	/**
	 * Overloaded set function
	 *
	 * @param  string	The variable name
	 * @param  mixed 	The variable value.
	 * @return mixed
	 */
	public function __set($key, $value)
	{
		$this->getObject()->$key = $value;
	}

	/**
	 * Overloaded get function
	 *
	 * @param  string 	The variable name.
	 * @return mixed
	 */
	public function __get($key)
	{
		return $this->getObject()->$key;
	}

	/**
	 * Overloaded isset function
	 *
	 * Allows testing with empty() and isset() functions
	 *
	 * @param  string 	The variable name
	 * @return boolean
	 */
	public function __isset($key)
	{
		return isset($this->getObject()->$key);
	}

	/**
	 * Overloaded isset function
	 *
	 * Allows unset() on object properties to work
	 *
	 * @param string 	The variable name.
	 * @return void
	 */
	public function __unset($key)
	{
		if (isset($this->getObject()->$key)) {
            unset($this->getObject()->$key);
        }
	}

   	/**
	 * Overloaded call function
	 *
	 * @param  string 	The function name
	 * @param  array  	The function arguments
	 * @throws BadMethodCallException 	If method could not be found
	 * @return mixed The result of the function
	 */
	public function __call($method, array $arguments)
	{
		$object = $this->getObject();

		//Check if the method exists
		if($object instanceof KObject)
		{
			$methods = $object->getMethods();
			$exists  = in_array($method, $methods);
		}
		else $exists = method_exists($object, $method);

		//Call the method if it exists
		if($exists)
		{
 			$result = null;

			// Call_user_func_array is ~3 times slower than direct method calls.
 		    switch(count($arguments))
 		    {
 		    	case 0 :
 		    		$result = $object->$method();
 		    		break;
 		    	case 1 :
 	              	$result = $object->$method($arguments[0]);
 		           	break;
 	           	case 2:
 	               	$result = $object->$method($arguments[0], $arguments[1]);
 		           	break;
 		      	case 3:
 	              	$result = $object->$method($arguments[0], $arguments[1], $arguments[2]);
 	               	break;
 	           	default:
 	             	// Resort to using call_user_func_array for many segments
 		            $result = call_user_func_array(array($object, $method), $arguments);
 	         }

 	         //Allow for method chaining through the decorator
 	         $class = get_class($object);
             if ($result instanceof $class) {
          		return $this;
             }

             return $result;
		}

		return parent::__call($method, $arguments);
	}
}