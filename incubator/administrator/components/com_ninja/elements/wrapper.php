<?php
/**
 * @version		$Id: wrapper.php 365 2010-06-23 11:43:38Z stian $
 * @category	Napi
 * @package		Napi_Parameter
 * @copyright	Copyright (C) 2007 - 2010 NinjaForge. All rights reserved.
 * @license		GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link     	http://ninjaforge.com
 */

class ComNinjaElementWrapper extends ComNinjaElementAbstract
{
	function fetchTooltip($label, $description, &$node, $control_name, $name) {

		return false;
	}

	function fetchElement($name, $value, &$node, $control_name)
	{
		return $value;
	}
}
