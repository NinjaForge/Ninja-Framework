<?php
/**
 * @version		$Id: defines.php 23 2010-10-27 03:58:17Z johanjanssens $
 * @category	Nooku
 * @package     Nooku_Components
 * @subpackage  Extensions
 * @copyright	Copyright (C) 2010 Timble CVBA and Contributors. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://www.nooku.org
 */

/**
 * Description
 *
 * @author		Stian Didriksen <stian@ninjaforge.com>
 * @category	Nooku
 * @package     Nooku_Components
 * @subpackage  Extensions
 */
class ComExtensions
{
	const _VERSION = '0.7.0';

	public static function getVersion()
	{
		return self::_VERSION;
	}
}