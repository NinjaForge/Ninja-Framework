<?php
/**
 * @version 	$Id: module.php 1372 2011-10-11 18:56:47Z stian $
 * @category	Koowa
 * @package		Koowa_Service
 * @subpackage 	Locator
 * @copyright	Copyright (C) 2007 - 2010 Johan Janssens. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 */

/**
 * Service Locator for a plugin
 *
 * @author		Johan Janssens <johan@nooku.org>
 * @category	Koowa
 * @package     Koowa_Service
 * @subpackage 	Locator
 */
class KServiceLocatorModule extends KServiceLocatorAbstract
{
	/** 
	 * The type
	 * 
	 * @var string
	 */
	protected $_type = 'mod';
	
	/**
	 * Get the classname based on an identifier
	 * 
	 * This locator will try to create an generic or default classname on the identifier information
	 * if the actual class cannot be found using a predefined fallback sequence.
	 * 
	 * Fallback sequence : -> Named Module Specific
	 *                     -> Named Module Default  
	 *                     -> Default Module Specific
	 *                     -> Default Module Default
	 *                     -> Framework Specific 
	 *                     -> Framework Default
	 *
	 * @param mixed  		 An identifier object - mod:[//application/]module.[.path].name
	 * @return string|false  Return object on success, returns FALSE on failure
	 */
	public function findClass(KServiceIdentifier $identifier)
	{		
		$path = KInflector::camelize(implode('_', $identifier->path));
		$classname = 'Mod'.ucfirst($identifier->package).$path.ucfirst($identifier->name);
				
		//Don't allow the auto-loader to load module classes if they don't exists yet
		if (!$this->getService('koowa:loader')->loadClass($classname, $identifier->basepath))
		{
			$classpath = $identifier->path;
			$classtype = !empty($classpath) ? array_shift($classpath) : $identifier->name;
				
			//Create the fallback path and make an exception for views
			$path = ($classtype != 'view') ? KInflector::camelize(implode('_', $classpath)) : '';
					
			/*
			 * Find the classname to fallback too and auto-load the class
			 * 
			 * Fallback sequence : -> Named Module Specific 
			 *                     -> Named Module Default  
			 *                     -> Default Module Specific 
			 *                     -> Default Module Default
			 *                     -> Default Component Specific 
			 *                     -> Default Component Default
			 *                     -> Framework Specific 
			 *                     -> Framework Default
			 */
			if(class_exists('Mod'.ucfirst($identifier->package).ucfirst($classtype).$path.ucfirst($identifier->name))) {
				$classname = 'Mod'.ucfirst($identifier->package).ucfirst($classtype).$path.ucfirst($identifier->name);
			} elseif(class_exists('Mod'.ucfirst($identifier->package).ucfirst($classtype).$path.'Default')) {
				$classname = 'Mod'.ucfirst($identifier->package).ucfirst($classtype).$path.'Default';
			} elseif(class_exists('ModDefault'.ucfirst($classtype).$path.ucfirst($identifier->name))) {
				$classname = 'ModDefault'.ucfirst($classtype).$path.ucfirst($identifier->name);
			} elseif(class_exists('ModDefault'.ucfirst($classtype).$path.'Default')) {
				$classname = 'ModDefault'.ucfirst($classtype).$path.'Default';
			} elseif(class_exists('ComDefault'.ucfirst($classtype).$path.ucfirst($identifier->name))) {
				$classname = 'ComDefault'.ucfirst($classtype).$path.ucfirst($identifier->name);
			} elseif(class_exists('ComDefault'.ucfirst($classtype).$path.'Default')) {
				$classname = 'ComDefault'.ucfirst($classtype).$path.'Default';
			} elseif(class_exists( 'K'.ucfirst($classtype).$path.ucfirst($identifier->name))) {
				$classname = 'K'.ucfirst($classtype).$path.ucfirst($identifier->name);
			} elseif(class_exists('K'.ucfirst($classtype).$path.'Default')) {
				$classname = 'K'.ucfirst($classtype).$path.'Default';
			} else {
				$classname = false;
			}
		
		}
	    
		return $classname;
	}
	
	/**
	 * Get the path based on an identifier
	 *
	 * @param  object  	An identifier object - mod:[//application/]module.[.path].name
	 * @return string	Returns the path
	 */
	public function findPath(KServiceIdentifier $identifier)
	{
		$path  = '';
	    $parts = $identifier->path;
		$name  = $identifier->package;
				
		if(!empty($identifier->name))
		{
			if(count($parts)) 
			{
				$path    = KInflector::pluralize(array_shift($parts)).
				$path   .= count($parts) ? '/'.implode('/', $parts) : '';
				$path   .= '/'.strtolower($identifier->name);	
			} 
			else $path  = strtolower($identifier->name);	
		}
				
		$path = $identifier->basepath.'/modules/mod_'.$name.'/'.$path.'.php';			
	    return $path;
	}
}