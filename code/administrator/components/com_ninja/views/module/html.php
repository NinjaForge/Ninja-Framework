<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
* @category		Koowa
* @package      Koowa_Modules
* @copyright    Copyright (C) 2007 - 2009 Johan Janssens and Mathias Verraes. All rights reserved.
* @license      GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
* @link         http://www.koowa.org
*/

/**
 * NinjaViewModuleHtml
 *
 * Originally ModDefaultHtml, but we moved it here so that we don't have to install two dummy modules on our users sites.
 * It also allows us to make modules more easily do things that we do often @NinjaForge (like rendering modules within modules)
 * Not to forget have our standard view apis at hand :)
 * 
 * @author stian didriksen <stian@ninjaforge.com>
 */
class NinjaViewModuleHtml extends NinjaViewHtml
{
	/**
	 * Constructor
	 *
	 * @param	array An optional associative array of configuration settings.
	 */
	public function __construct(KConfig $options)
	{
        parent::__construct($options);
        
        // Initialize the options
		$options  = $this->_initialize($options);
        
		//Assign module specific options
        $this->params  = $options->params;
        $this->module  = $options->module;
        $this->attribs = $options->attribs;
        
        $template = JFactory::getApplication()->getTemplate();
        $path     = JPATH_THEMES.DS.$template.DS.'html'.DS.'mod_'.$this->getIdentifier()->package;
          
		$this->getService($this->getTemplate())->addPath($path);
	}
	
	/**
	 * Get the name
	 *
	 * Since module views have a shorter identifier string, we have to override this method.
	 *
	 * @return 	string 	The name of the object
	 */
	public function getName()
	{
		return $this->getIdentifier()->package;
	}
	
	/**
	 * Initializes the options for the object
	 *
	 * Called from {@link __construct()} as a first step of object instantiation.
	 *
	 * @param   array   Options
	 * @return  array   Options
	 */
	protected function _initialize(KConfig $options)
	{
		$options->append(array(
            'params'  => null,
			'module'  => null,
			'attribs' => array(),
       	));
       	
        parent::_initialize($options);
    }
	
	/**
	 * Renders and echo's the views output
 	 *
	 * @return modDefaultHtml
	 */
	public function display()
	{
		//Render the template
		echo $this->loadTemplate();
		
		return $this;
	}
}