<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
* @version      $Id: spacer.php 794 2011-01-10 18:44:32Z stian $
* @category		Napi
* @package		Napi_Toolbar
* @subpackage	Button
* @copyright	Copyright (C) 2011 NinjaForge. All rights reserved.
* @license      GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
*/

/**
 * Spacer
 * 
 * @author		Stian Didriksen <stian@ninjaforge.com>
 * @category	Napi
 * @package		Napi_Toolbar
 * @subpackage	Button
 */
class ComNinjaToolbarButtonSpacer extends KToolbarButtonAbstract
{
	public function render()
	{
		return $this->_parent->getTemplate()->loadIdentifier('button_spacer')->render(true);
	}

}