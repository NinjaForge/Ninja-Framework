<?php
/**
 * @category	Napi
 * @package		Napi_Parameter
 * @copyright	Copyright (C) 2007 - 2010 NinjaForge. All rights reserved.
 * @license		GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link     	http://ninjaforge.com
 */

class ComNinjaElementHtml5 extends ComNinjaElementAbstract
{
	function fetchElement($name, $value, &$node, $control_name)
	{

		return '<input type="datetime" />';
	}
}