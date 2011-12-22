<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @package		Ninja
 * @copyright	Copyright (C) 2011 NinjaForge. All rights reserved.
 * @license 	GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */ 

/**
 * Filter those paths, and avoid those nasty ../..
 *
 * @author		Stian Didriksen <stian@ninjaforge.com>
 * @package     Ninja
 * @subpackage 	Filters
 */
class NinjaFilterPath extends KFilterAbstract
{
	const PATTERN = '/^([\\\\\/]|[A-Za-z0-9_-\s])+[A-Za-z0-9_\.-\s]*([\\\\\/][A-Za-z0-9_-]+[A-Za-z0-9_\.-\s]*)*$/';

	/**
	 * Validate a value
	 *
	 * @param	scalar	Value to be validated
	 * @return	bool	True when the variable is valid
	 */
	protected function _validate($value)
	{
		$value = trim($value);
		return (is_string($value) && (preg_match(self::PATTERN, $value)) == 1);
	}
	
	/**
	 * Sanitize a value
	 *
	 * @param	mixed	Value to be sanitized
	 * @return	string
	 */
	protected function _sanitize($value)
	{
		$value = trim($value);
		preg_match(self::PATTERN, $value, $matches);
		return @ (string) $matches[0];
	}
}