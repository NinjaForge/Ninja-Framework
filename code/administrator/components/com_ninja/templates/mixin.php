<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: mixin.php 794 2011-01-10 18:44:32Z stian $
 * @category	Napi
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

class ComNinjaTemplateMixin extends KMixinAbstract
{
	/**
	 * Escapes a value for output in a view script.
	 *
	 * @param  mixed $var The output to escape.
	 * @return mixed The escaped value.
	 */
	public function escape($var)
	{
	    return htmlspecialchars($var);
	}
}