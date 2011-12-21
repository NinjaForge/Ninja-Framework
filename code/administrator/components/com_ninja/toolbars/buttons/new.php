<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
* @version      $Id: new.php 794 2011-01-10 18:44:32Z stian $
* @category		Napi
* @package		Napi_Toolbar
* @subpackage	Button
* @copyright	Copyright (C) 2011 NinjaForge. All rights reserved.
* @license      GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
*/

/**
 * New
 *
 * Takes you to a «Add Item» form
 * 
 * @author		Stian Didriksen <stian@ninjaforge.com>
 * @category	Napi
 * @package		Napi_Toolbar
 * @subpackage	Button
 */
class ComNinjaToolbarButtonNew extends ComNinjaToolbarButtonAbstract
{
	public function render()
	{				
		$option = KRequest::get('get.option', 'cmd');
		$view	= KInflector::singularize(KRequest::get('get.view', 'cmd'));
		$link	= 'index.php?option='.$option.'&view='.$view;

		$this->attribs->set(array(
			'class' => 'toolbar',
			'href'  => $this->_parent->createRoute($link)
		));
		
		return parent::render();
	}
}