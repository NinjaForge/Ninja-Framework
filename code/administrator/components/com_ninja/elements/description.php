<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: description.php 1399 2011-11-01 14:22:48Z stian $
 * @category	Napi
 * @package		Napi_Parameter
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

class NinjaElementDescription extends NinjaElementText
{	
	public function fetchTooltip()
	{
		return '';
	}
	
	public function fetchElement()
	{
		return JText::_(trim((string)$this->node));
	}
}