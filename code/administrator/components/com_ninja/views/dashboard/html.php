<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category	Ninja
 * @package		Ninja_View
 * @subpackage	Dashboard
 * @copyright	Copyright (C) 2011 NinjaForge. All rights reserved.
 * @license 	GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * Dashboard HTML View Class - Renders the NinjaForge extensions dashboard
 *
 * @category	Ninja
 * @package		Ninja_View
 * @subpackage	Dashboard
 */
class NinjaViewDashboardHtml extends NinjaViewHtml
{
	/**
	 * Initializes the config for the object
	 *
	 * Called from {@link __construct()} as a first step of object instantiation.
	 *
	 * @param 	object 	An optional KConfig object with configuration options
	 * @return  void
	 */
	protected function _initialize(KConfig $config)
	{
	    $config->append(array(
	        'layout' => 'basic',
	  	));
	    
	    parent::_initialize($config);
	}
	
	/**
	 * Return the views output
	 *
	 * Customized in order to change the default identifier used to load layouts
	 *
	 * @param  boolean 	If TRUE apply write filters. Default FALSE.
	 * @return string 	The output of the view
	 */
	public function display()
	{
	    if(empty($this->output))
		{
		    //Override identifier
		    $identifier                  = $this->getIdentifier();
		    $identifier->package         = $this->getModel()->getIdentifier()->package;


		    // if this is joomla 1.5 check to see if the mootools upgrade plugin is installed
		    if (!version_compare(JVERSION,'1.6.0','ge')) {
			    if (!JPluginHelper::getPlugin('system', 'mtupgrade'))
			    	 JError::raiseWarning(0, JText::_('COM_NINJA_MOO_UPGRADE_REQUIRED'));
			}

		    //die('<pre>'.var_export($this->__service_identifier, true));
		    $this->__service_identifier  = $identifier;
		
	        //We need a full identifier to load the base template
		    $identifier = new KServiceIdentifier('com://admin/ninja.view.dashboard.html');
	        $identifier->name = $this->getLayout();
		   
	        $this->output = $this->getTemplate()
	                             ->loadIdentifier($identifier, $this->_data)
	                             ->render();
		}
	                    
	    return parent::display();
	}
}