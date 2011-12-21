<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: hidden.php 794 2011-01-10 18:44:32Z stian $
 * @category	Napi
 * @package		Napi_Parameter
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

class ComNinjaElementHidden extends ComNinjaElementAbstract
{
	public function fetchToolTip()
	{
		return false;
	}
	
	public function before()
	{
		return null;
	}
	
	public function after()
	{
		return null;
	}
	
	public function fetchElement($name, $value, &$node, $control_name)
	{
		return '<input type="hidden" name="'.$control_name.'['.$this->_parent->getGroup().']['.$name.']" id="'.$control_name.$this->_parent->getGroup().$name.'" value="'.$value.'" />';
	}
}