<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: chrome.php 794 2011-01-10 18:44:32Z stian $
 * @category	Napi
 * @package		Napi_Parameter
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

class ComNinjaElementChrome extends ComNinjaElementAbstract
{
	public function fetchElement($name, $value, &$node, $control_name)
	{
		$options = KFactory::get('admin::com.ninja.model.module_chrome')->client(0)->optgroup('<OPTGROUP>')->getList();	

		return JHTML::_('select.genericlist', $options, $this->name, array('class' => 'value'), 'id', 'title', $value, $this->id, false);
	}
}
