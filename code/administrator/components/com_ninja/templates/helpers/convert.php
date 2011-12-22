<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category	Ninja
 * @package		Ninja_Template
 * @subpackage	Helper
 * @copyright	Copyright (C) 2011 NinjaForge. All rights reserved.
 * @license 	GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */
 
 /**
 * Convert Helper - for converting various units to other units
 *
 * @author		Stian Didriksen <stian@ninjaforge.com>
 * @category	Ninja
 * @package		Ninja_Template
 * @subpackage	Helper
 */
class NinjaTemplateHelperConvert extends KTemplateHelperAbstract
{
	/**
	 * Converts filesizes in bytes to human readable text (MB GB ect)
	 *
	 * Examples: 
	 * <code>
	 * // Outside a template layout
     * $helper = $this->getService('ninja:template.helper.convert');
     * $helper->bytes(array('bytes' => '716800'));
     *
     * // Inside a template layout
     * <?= @ninja('convert.bytes', array('bytes' => '716800')) ?>
     * </code>
	 *
	 * @param	array	optional array of configuration options
	 * @return  string  the converted filesize
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