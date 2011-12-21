<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: images.php 1399 2011-11-01 14:22:48Z stian $
 * @category	Napi
 * @package		Napi_Parameter
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

class NinjaElementImages extends NinjaElementAbstract
{
	public function fetchElement($name, $value, &$node, $control_name)
	{
		$path = 'images/stories';
		if(isset($this->node['path'])) $path = (string) $this->node['path'];

		return $this->getService('ninja:template.helper.select')->images(array('path' => JPATH_ROOT.'/'.$path, 'name' => $this->name, 'selected' => $value, 'id' => $this->id));
	}
}
