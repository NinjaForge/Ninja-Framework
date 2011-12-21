<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
* @version      $Id: cancel.php 552 2010-10-28 19:41:51Z stian $
* @category		Koowa
* @package		Koowa_Toolbar
* @subpackage	Button
* @copyright    Copyright (C) 2007 - 2010 Johan Janssens and Mathias Verraes. All rights reserved.
* @license      GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
*/

/**
 * Cancel button class for a toolbar
 * 
 * @author		Mathias Verraes <mathias@koowa.org>
 * @category	Koowa
 * @package		Koowa_Toolbar
 * @subpackage	Button
 */
class ComNinjaToolbarButtonCancel extends ComNinjaToolbarButtonAbstract
{
	public function __construct(KConfig $options)
	{
		$this->setMethod('cancel');
		$attribs = $options->attribs;
		$attribs['class'] = 'toolbar type:\'cancel\'';
		$attribs['href'] = '#';
		$optionss->attribs = $attribs;
		parent::__construct($options);
	}
}