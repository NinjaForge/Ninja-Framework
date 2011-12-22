<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category	Napi
 * @package		Napi_Parameter
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

class NinjaElementFixHeight extends NinjaElementAbstract
{
	public function fetchElement($name, $value, &$node, $control_name)
	{
		
		$html = "\n".'<button name="'.$name.'" id="'.$control_name.$this->group.$name.'" type="button" onclick="update(this)">'.JText::_((string)$node['label']).'</button>';

		return $html;
	}
}
