<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
* @version      $Id: uninstall.php 768 2010-12-20 23:12:13Z stian $
* @category		Koowa
* @package		Koowa_Toolbar
* @subpackage	Button
* @copyright    Copyright (C) 2007 - 2010 Johan Janssens and Mathias Verraes. All rights reserved.
* @license      GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
*/

/**
 * Delete button class for a toolbar
 * 
 * @author		Mathias Verraes <mathias@koowa.org>
 * @category	Koowa
 * @package		Koowa_Toolbar
 * @subpackage	Button
 */
class ComNinjaToolbarButtonUninstall extends KToolbarButtonPost
{

	public function __construct(KConfig $options)
	{
		$options->icon	= 'icon-32-delete';
		parent::__construct($options);
	}
	
	public function getLink()
	{
		return '#';
	}
	
	public function getOnClick()
	{
		KFactory::get('admin::com.ninja.helper.default')->js('validation.js');
		return 'Koowa.Form.addField(\'action\', \'uninstall\');Koowa.Validate.check(\''.JText::_('Please select an item from the list').'\', document.adminForm.boxchecked.value);';
	}
}