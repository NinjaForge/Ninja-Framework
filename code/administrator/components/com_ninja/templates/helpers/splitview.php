<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category	Ninja
 * @package		Ninja_Template
 * @subpackage	Helper
 * @copyright	Copyright (C) 2011 NinjaForge. All rights reserved.
 * @license 	GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * Splitview Helper Class
 *
 * @author		Stian Didriksen <stian@ninjaforge.com>
 * @category	Ninja
 * @package		Ninja_Template
 * @subpackage	Helper
 */
class NinjaTemplateHelperSplitview extends KTemplateHelperAbstract
{
	/**
	 * Generates an HTML optionlist based on the distinct data from a model column.
	 * 
	 * The column used will be defined by the name -> value => column options in
	 * cascading order. 
	 * 
	 * If no 'model' name is specified the model identifier will be created using 
	 * the helper identifier. The model name will be the pluralised package name. 
	 * 
	 * If no 'value' option is specified the 'name' option will be used instead. 
	 * If no 'text'  option is specified the 'value' option will be used instead.
	 * 
	 * @param 	array 	An optional array with configuration options
	 * @return	string	Html
	 * @see __call()
	 */
	protected function _splitview($config = array())
 	{
		$config = new KConfig($config);
		$config->append(array(
			'id'		=> 'splitview',
			'name'		=> '',
			'package'	=> 'com_'.$this->getIdentifier()->package
		))->append(array(
			'master_view'	=> KInflector::pluralize($config->name),
			'detail_view'	=> KInflector::singularize($config->name)
		))->append(array(
			'options' => array(
				'master_url' => '?option='.$config->package.'&view='.$config->master_view.'&format=json&sort=created_on&direction=desc',
				'detail_url' => '?option='.$config->package.'&view='.$config->detail_view.'&format=raw',
				'label' => array(
				    'empty' => JText::_('COM_NINJA_NO_'.KInflector::humanize($config->master_view).''),
				    'select' => JText::_('COM_NINJA_NO_'.KInflector::humanize($config->detail_view).'_SELECTED')
				)
			)
		));
		
		$this->getService('ninja:template.helper.document')->load(array('/jquery/jquery.js', '/jquery/splitview.js'));
		$this->getService('ninja:template.helper.document')->load('js', "\nninja(function($){
			$('#".$config->id."').splitview(".json_encode($config->options->toArray()).");
		});\n");

		return '<div id="'.$config->id.'" class="splitview"></div>';
 	}
	
	/**
     * Search the mixin method map and call the method or trigger an error
     * 
     * This function check to see if the method exists in the mixing map if not
     * it will call the 'splitview' function. The method name will become the 'name'
     * in the config array.
     * 
     * This can be used to auto-magically create select filters based on the 
     * function name.
     *
   	 * @param  string 	The function name
	 * @param  array  	The function arguments
	 * @throws BadMethodCallException 	If method could not be found
	 * @return mixed The result of the function
     */
 	public function __call($method, array $arguments)
    {	
    	if(!in_array($method, $this->getMethods())) 
    	{
    		$config = $arguments[0];
    		$config['name']  = strtolower($method);
    		
    		return $this->_splitview($config);
    	}
    	
		return parent::__call($method, $arguments);
    }
}