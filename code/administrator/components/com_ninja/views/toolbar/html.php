<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: html.php 980 2011-04-04 20:26:19Z stian $
 * @package		Ninja
 * @copyright	Copyright (C) 2011 NinjaForge. All rights reserved.
 * @license 	GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

class ComNinjaViewToolbarHtml extends KViewTemplate implements KToolbarInterface
{
	/**
	 * Buttons in the toolbar
	 *
	 * @var		array
	 */
	protected $_buttons = array();


	/**
	 * Constructor
	 *
	 * @param	array An optional associative array of configuration settings.
	 */
	public function __construct(KConfig $options)
	{
        parent::__construct($options);
		
		if(!isset($this->_buttons)) $this->_buttons = array();
		
		$this->_name  = empty($options->name) ? KRequest::get('get.view', 'cmd') : $options->name;
		$this->_title = empty($options->title) ? KInflector::humanize($this->getName()) : $options->title;
        
        KFactory::get('admin::com.ninja.helper.default')->css('/toolbar.css');
        KFactory::get('admin::com.ninja.helper.default')->js('/toolbar.js');
		if(KInflector::isSingular(KRequest::get('get.view', 'cmd', 'items')))
		{
			KFactory::get('admin::com.ninja.helper.default')->js('window.addEvent(\'domready\',function(){if(formToolbar = $(\''.KFactory::get('admin::com.ninja.helper.default')->formid().'\')) formToolbar.addClass(\'validator-inline\');});');
		}
        
        if(KInflector::isPlural($this->getName()))
        {		 
        	$this->append('new')
        		 ->append('edit')
        		 ->append('delete');	
        }
        else
        {
        	$this->append('save')
        		 ->append('apply')
        		 ->append('cancel');
        }        
        
        $template = KFactory::get('lib.joomla.application')->getTemplate();
        $path     = JPATH_THEMES.'/'.$template.'/html/com_'.$this->_identifier->package.'/toolbar';
		KFactory::get($this->getTemplate())->addPath($path);
        
        $this->setLayout('admin::com.ninja.view.toolbar.toolbar_render');
        
		$this->id = 'toolbar-'.$this->getName();
	}
	
	/**
	 * Remove buttons that require items in a list view
	 *
	 * Usually buttons like Edit, Delete, Enable and Disable.
	 * These buttons shouldn't show when there's no items.
	 *
	 * @author Stian Didriksen <stian@ninjaforge.com>
	 * @return void
	 */
	public function removeListButtons()
	{
		$keys = array();
		foreach($this->_buttons as $i => $button)
		{
			if(isset($button->list)) $keys[] = $i;
		}
		
		$buttons = $this->_buttons;
		foreach($keys as $key)
		{
			unset($buttons[$key]);
		}
		$this->_buttons = $buttons;
	}
	
	/**
	 * Append a button
	 *
	 * @param	KToolbarButtonInterface|string	Button
	 * @return	this
	 */
	public function append($button)
	{
		$buttons = $this->_buttons;
		array_push($buttons, $this->button($button));
		$this->_buttons = $buttons;
		return $this;
	}

	/**
	 * Prepend a button
	 *
	 * @param	KToolbarButtonInterface	Button
	 * @return	this
	 */
	public function prepend($button)
	{
		$buttons = $this->_buttons;
		array_unshift($buttons, $this->button($button));
		$this->_buttons = $buttons;
		return $this;
	}
	
	/**
	 * If the button isn't an instance of KToolbarButtonInterface, get it using the factory.
	 *
	 * Also sets the parent for hte button.
	 *
	 * @param string | KToolbarButtonInterface	Button
	 * @return KToolbarButtonInterface	Button
	 */
	private function button($button)
	{
		if(!($button instanceof KToolbarButtonInterface))
		{
			$app		= $this->_identifier->application;
			$package	= $this->_identifier->package;
			$button = KFactory::tmp($app.'::com.'.$package.'.toolbar.button.'.$button);
		}

		$button->setParent($this);
		
		return $button;
	}
	
	/**
	 * Reset the button array
	 *
	 * @return	this
	 */
	public function reset()
	{
		$this->_buttons = array();
		return $this;
	}
	
	/**
	 * Set the toolbar's title and icon
	 *
	 * @return 	string
	 */
	public function renderTitle()
	{
		$name = $this->getName();
	
		$img = KInflector::isPLural($name) 
						? KFactory::get('admin::com.ninja.helper.default')->img('/48/'.$name.'.png')
						: KFactory::get('admin::com.ninja.helper.default')->img('/48/'.KInflector::pluralize($name).'.png');
		if(!$img)
		{
			$img = KInflector::isSingular($name) 
						? KFactory::get('admin::com.ninja.helper.default')->img('/48/'.$name.'.png')
						: KFactory::get('admin::com.ninja.helper.default')->img('/48/'.KInflector::singularize($name).'.png');
		}
		if($img) 
		{
			KFactory::get('admin::com.ninja.helper.default')->css('.header.icon-48-'.$name.' { background-image: url(' . $img . '); }');
		}
	
		$this->name  = $this->getName();
		$this->title = $this->_title;

		return $this->getTemplate()
				->loadIdentifier('admin::com.ninja.view.toolbar.toolbar_title', array('name' => $this->getName(), 'title' => $this->_title))
				->render(true);
	}
	
	/**
	 * Makes the view::display() method work as a proxy to the toolbar::render().
	 *
	 * @throws KToolbarException When the button could not be found
	 * @return	string	HTML
	 */
	public function display()
	{
		return $this->render();
	}
	
	/**
	 * toString forwards to display
	 *
	 * @return string
	 */
	public function __toString()
	{
		return $this->render();
	}
	
	/**
	 * Render the toolbar
	 *
	 * @throws KToolbarException When the button could not be found
	 * @return View Object
	 */
	public function render()
	{
		if(KRequest::has('get.id', 'int'))
		{
			KFactory::get('admin::com.ninja.helper.default')->js('window.addEvent(\'domready\',function(){$(\''.KFactory::get('admin::com.ninja.helper.default')->formid().'\').validate();});');
		}
		
		$this->buttons = $this->_buttons;
		
		return parent::display();
	}
	
	/**
	 * Get the name
	 *
	 * @return 	string 	The name of the object
	 */
	public function getName()
	{
		return $this->_name;
	}
}