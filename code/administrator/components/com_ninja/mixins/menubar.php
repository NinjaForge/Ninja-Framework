<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/** 
 * @version		$Id: menubar.php 552 2010-10-28 19:41:51Z stian $
 * @package		Koowa
 * @copyright	Copyright (C) 2010 Nooku. All rights reserved.
 * @license 	GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://www.nooku.org
 */

/**
 * Menubar mixin, can be used in all views to display to main menu
 */
class ComNinjaMixinMenubar extends KMixinAbstract
{
	/**
	 * Associatives array of view names
	 * 
	 * @var array
	 */
	protected $_views;
	
	/**
	 * Object constructor
	 *
	 * @param	array 	An optional associative array of configuration settings.
	 * Recognized key values include 'mixer' (this list is not meant to be Comprehensive).
	 */
	public function __construct(KConfig $options)
	{
		parent::__construct($options);
		
		$this->_views = $options->views;
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
            'views' =>  array(),
        ));
        
        parent::_initialize($options);
    }
	
	public function displayMenubar()
	{
		$name = KInflector::underscore($this->_mixer->getName());
		$subview = array();
		foreach($this->_views->menu as $view)
		{
			$title 	   = (string)$view;
			$link	   = (string)$view['view'];
			$component = $this->_mixer->getIdentifier()->package;
			$active    = ($link == $name );
			
			if((bool) $this->_views->{KInflector::underscore((string) $view)}) 	{
				$subview[] = ' <strong>&#9662;</strong></a><ul>';
				foreach ($this->_views->{KInflector::underscore((string) $view)}->children() as $subviews)
				{
					//This subview is named the same as the view
					$isIdentical = (string) $subviews['view'] === (string) $view['view'];
					//Current view is active
					$viewActive = (string) $view['view'] === $name;
					$subviewActive = (string) $subviews['view'] === $name;
					$subviewarr = (array)$this->_views->{KInflector::underscore((string) $view)};
					 if(!$subviewActive)
					 {
						$subview[] = '<li><a href="index.php?option=com_'.$component.'&view='.$subviews['view'].'">';
						$subview[] = JText::_((string)$subviews);
						$subview[] = '</a></li>';
					} else {
						$title = (string) $subviews;
						$link  = (string) $subviews['view'];
						$active    = true;
						$subview[] = '<li class="disabled"><a>&#9656;&nbsp;';
						$subview[] = JText::_((string)$subviews);
						$subview[] = '</a></li>';
					}
				}
				$subview[] = '</ul><a style="display:none;">';
			}
			
			JSubMenuHelper::addEntry(JText::_($title) . implode($subview), 'index.php?option=com_'.$component.'&view='.$link, $active );
			$subview = array();
		}
	}
}