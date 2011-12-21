<?php
/**
 * @version		$Id: set.php 1006 2011-04-08 16:27:49Z stian $
 * @category	Koowa
 * @package		Koowa_Object
 * @copyright	Copyright (C) 2007 - 2010 Johan Janssens. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://www.nooku.org
 */

/**
 * An Object Set Class
 * 
 * KObjectSet implements an associative container that stores objects, and in which the object
 * themselves are the keys. Objects are stored in the set in FIFO order.
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @category    Koowa
 * @package     Koowa_Object
 * @see			http://www.php.net/manual/en/class.splobjectstorage.php
 */
class KObjectSet extends KObject implements IteratorAggregate, ArrayAccess, Countable, Serializable
{
   /**
     * Object set
     *
     * @var array
     */
    protected $_object_set = null;
    
    /**
     * Constructor.
     *
     * @param   object  An optional KConfig object with configuration options
     */
    public function __construct(KConfig $config = null)
    {
        //If no config is passed create it
        if(!isset($config)) $config = new KConfig();
        
        parent::__construct($config);
        
         $this->_object_set = new ArrayObject();
    }
    
  	/**
     * Inserts an object in the set
     * 
     * @param   object      The object to insert
     * @return  KObjectSet
     */
    public function insert( KObjectHandlable $object)
    { 
        if($handle = $object->getHandle()) {
            $this->_object_set->offsetSet($handle, $object);
        }
       
       return $this;
    }
    
    /**
     * Removes an object from the set
     * 
     * All numerical array keys will be modified to start counting from zero while
     * literal keys won't be touched.
     * 
     * @param   object      The object to remove
     * @return  KObjectQueue
     */
    public function extract( KObjectHandlable $object)
    {
        $handle = $object->getHandle();
        
        if($this->_object_set->offsetExists($handle)) {
           $this->_object_set->offsetUnset($handle);
        }
        
        return $this;
    }
    
	/**
     * Checks if the set contains a specific object
     * 
     * @param   object      The object to look for.
     * @return  bool		Returns TRUE if the object is in the set, FALSE otherwise.
     */
    public function contains( KObjectHandlable $object)
    {
        return $this->_object_set->offsetExists($object->getHandle());
    }
    
	/**
     * Merge-in another set 
     * 
     * @param   object      The object set to merge 
     * @return  KObjectQueue
     */
    public function merge( KObjectSet $set)
    {
        foreach($set as $object) {
            $this->insert($object);
        }
        
        return $this;
    }
    
    /**
     * Check if the object exists in the queue
     *
     * Required by interface ArrayAccess
     *
     * @param   object 	The object to look for.
     * @return  bool	Returns TRUE if the object exists in the storage, and FALSE otherwise
     */
    public function offsetExists($object)
    {
        if($object instanceof KConfigHandlable) {
            return $this->contains($object);
        }
    }

    /**
     * Returns the object from the set
     * 
     * Required by interface ArrayAccess
     *
     * @param   object  The object to look for.
     * @return  mixed   The object
     */
    public function offsetGet($object)
    {       
        if($object instanceof KConfigHandlable) {
            return $this->_object_set->offsetGet($object->getHandle());
        }
    }

    /**
     * Store an object in the set
     *
     * Required by interface ArrayAccess
     *
     * @param   object  The object to store
     * @param   mixed   The data to associate with the object. [UNUSED]
     * @return  object  KObjectSet
     */
    public function offsetSet($object, $data)
    {
        if($object instanceof KConfigHandlable) {
            $this->insert($object);
        }
        return $this;
    }

    /**
     * Removes an object from the set
     *
     * Required by interface ArrayAccess
     *
     * @param   object  The object to remove.
     * @return  object 	KObjectSet
     */
    public function offsetUnset($object)
    {
        if($object instanceof KConfigHandlable) {
            $this->extract($object);
        }
        
        return $this;
    }
     
    /**
     * Return a string representation of the set
     * 
     * Required by interface Serializable
     *
     * @return  string  A serialized object
     */
    public function serialize()
    {
        return serialize($this->_object_set);
    }
    
    /**
     * Unserializes a set from its string representation
     * 
     * Required by interface Serializable
     * 
     * @param   string  An serialized data
     */
    public function unserialize($serialized)
    {
        $this->_object_set = unserialize($serialized);
    }
    
    /**
     * Returns the number of elements in the collection.
     *
     * Required by the Countable interface
     *
     * @return int
     */
    public function count()
    {
        return $this->_object_set->count();
    }
    
	/**
     * Return the first object in the set
     *
     * @return	KObject or NULL is queue is empty
     */
	public function top() 
	{
	    $objects = array_values($this->_object_set->getArrayCopy());
	    
	    $object = null;
	    if(isset($objects[0])) {
	        $object  = $objects[0];
	    }
	
	    return $object;
	}
    
    /**
     * Defined by IteratorAggregate
     * 
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return $this->_object_set->getIterator();
    }
     
	/**
     * Return an associative array of the data.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->_object_set->getArrayCopy();
    }
    
    /**
     * Preform a deep clone of the object
     *
     * @retun void
     */
    public function __clone()
    { 
        $this->_object_set = clone $this->_object_set;
    }
}