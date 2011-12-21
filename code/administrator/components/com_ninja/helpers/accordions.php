<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: accordions.php 794 2011-01-10 18:44:32Z stian $
 * @package		Ninja
 * @copyright	Copyright (C) 2011 NinjaForge. All rights reserved.
 * @license 	GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */
 
 /**
 * Template Accordions Behavior Helper
 *
 * @author		Stian Didriksen <stian@ninjaforge.com>
 * @category	Napi
 * @package		Napi_Template
 * @subpackage	Helper
 * @uses		KArrayHelper
 */
class ComNinjaHelperAccordions extends KTemplateHelperBehavior
{
	/**
	 * Constructor
	 *
	 * @param array Associative array of values
	 */
	public function __construct(KConfig $params)
	{
		parent::__construct($params);

		//Load mootools javascript
		$this->mootools();
	}

	/**
	 * Creates a pane and creates the javascript object for it
	 *
	 * @param	array	An associative array of behavior options
	 * @param	array	An associative array of pane attributes
	 * @param	array	An associative array of pane attributes
	 */
	public function startPane($config = array())////$id, array $options = array(), array $attribs = array(), $cevents = array() )
	{
		$config = new KConfig($config);
		
		$config->append(array(
			'id'	=> 'accordions',
			'options'	=> array(
				'duration'		=> 300,
				'opacity'		=> false,
				'alwaysHide'	=> true,
				'scroll'		=> false
			),
			'attribs'	=> array(),
			'events'	=> array()
		));
		
		$config->id = strtolower($config->id);
		
		//Cr
		$events			= '';
		$onActive 		= 'function(e){e.addClass(\'jpane-toggler-down\');e.removeClass(\'jpane-toggler\');}';
		$onBackground	= 'function(e){e.addClass(\'jpane-toggler\');e.removeClass(\'jpane-toggler-down\');}';
		if($config->events) $events = '{onActive:'.$onActive.',onBackground:'.$onBackground.'}';

		$scroll = $config->options->scroll ? ".addEvent('onActive', function(toggler){
			new Fx.Scroll(window, {duration: this.options.duration, transition: this.transition}).toElement(toggler);
		})" : '';

		/*
		 * Until we find a solution that let us pass a string into json_encode without it being quoted,
		 * we have to use the mootools $merge method to merge events and regular settings back into one
		 * options object.
		*/
		$js = '
		window.addEvent(\'domready\', function(){ 
			new Accordion($$(\'.panel h3.jpane-toggler\'),$$(\'.panel div.jpane-slider\'),$merge('.$events.','.json_encode($config->options->toArray()).'))'.$scroll.'; 
		});';

		$document = KFactory::get('lib.joomla.document')->addScriptDeclaration( $js );
		
		$attribs = KHelperArray::toString($config->attribs);
		return '<div id="'.$config->id.'" class="pane-sliders" '.$attribs.'>';
	}

	/**
	 * Ends the pane
	 */
	public function endPane()
	{
		return "</div>";
	}

	/**
	 * Creates a tab panel with title and starts that panel
	 *
	 * @param	string	The title of the tab
	 * @param	array	An associative array of pane attributes
	 */
	public function startPanel($config = array())//$title, array $attribs = array(), $translate = true)
	{
		$config = new KConfig($config);
		
		$config->append(array(
			'title'		=> 'Slide',
			'attribs'	=> array(),
			'translate'	=> true
		));
		
		$config->attribs = KHelperArray::toString($config->attribs);
		if($config->translate) $config->title = JText::_($config->title);
		return '<div class="panel"><h3 class="jpane-toggler title" '.$config->attribs.'><span>'.$config->title.'</span></h3><div class="jpane-slider content">';
	}

	/**
	 * Ends a tab page
	 */
	public function endPanel()
	{
		return '</div></div>';
	}
}