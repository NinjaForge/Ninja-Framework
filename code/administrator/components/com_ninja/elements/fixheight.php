<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: fixheight.php 794 2011-01-10 18:44:32Z stian $
 * @category	Napi
 * @package		Napi_Parameter
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

class ComNinjaElementFixHeight extends ComNinjaElementAbstract
{
	public function fetchElement($name, $value, &$node, $control_name)
	{
		
		$html = "\n".'<button name="'.$name.'" id="'.$control_name.$this->group.$name.'" type="button" onclick="update(this)">'.JText::_((string)$node['label']).'</button>';

		return $html;
	}
}
