<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: images.php 794 2011-01-10 18:44:32Z stian $
 * @category	Napi
 * @package		Napi_Parameter
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

class ComNinjaElementImages extends ComNinjaElementAbstract
{
	public function fetchElement($name, $value, &$node, $control_name)
	{
		$path = 'images/stories';
		if(isset($this->node['path'])) $path = (string) $this->node['path'];

		return KFactory::get('admin::com.ninja.helper.select')->images(array('path' => JPATH_ROOT.'/'.$path, 'name' => $this->name, 'selected' => $value, 'id' => $this->id));
	}
}
