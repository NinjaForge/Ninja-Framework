<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: default.php 897 2011-02-23 14:09:23Z betweenbrain $
 * @package		Ninja
 * @copyright	Copyright (C) 2011 NinjaForge. All rights reserved.
 * @license 	GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */
 
 /**
 * Default button class for a toolbar
 * 
 * @author		Stian Didriksen <stian@ninjaforge.com>
 */
class ComNinjaToolbarButtonDefault extends ComNinjaToolbarButtonPost
{
	
	/**
	 * Gives the button 'list' status.
	 *
	 * When the view is plural, and the list has no items, this button wont render
	 *
	 * @var boolean true
	 */
	public $list = true;
	
	public function __construct(KConfig $options)
	{		
		parent::__construct($options);
		$this->attribs->set(array('class' => $this->attribs->class . ' validate'));
	}
}