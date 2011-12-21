<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: convert.php 794 2011-01-10 18:44:32Z stian $
 * @package		Ninja
 * @copyright	Copyright (C) 2011 NinjaForge. All rights reserved.
 * @license 	GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */
 
 /**
 * Helper for converting various units, like bytes to kilobytes
 *
 * @author		Stian Didriksen <stian@ninjaforge.com>
 */
class ComNinjaHelperConvert extends KTemplateHelperAbstract
{
	/**
	 * Turns filesizes into human readable text
	 *
	 * @param int $bytes
	 * @param int $precision	rounding precision
	 * @return string
	 */
	public static function bytes($config = array())
	{
		$config = new KConfig($config);
		
		$config->append(array(
			'bytes'		=> 0,
			'precision'	=> 2
		));
	
		$units = array('B', 'kB', 'MB', 'GB', 'TB');

		$bytes = max($config->bytes, 0);
		$pow = floor(($bytes ? log($bytes) : 0) / log(1024));
		$pow = min($pow, count($units) - 1);

		$bytes /= pow(1024, $pow);

		return round($bytes, $config->precision) . $units[$pow];
	}
}