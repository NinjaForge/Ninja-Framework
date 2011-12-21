<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: inflector.php 552 2010-10-28 19:41:51Z stian $
 * @category	Koowa
 * @package		Koowa_Inflector
 * @copyright	Copyright (C) 2007 - 2010 Johan Janssens and Mathias Verraes. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://www.koowa.org
 */

/**
 * KInflector to pluralize and singularize English nouns.
 *
 * @author		Johan Janssens <johan@koowa.org>
 * @author		Mathias Verraes <mathias@koowa.org>
 * @category	Koowa
 * @package		Koowa_Inflector
 * @static
 */
class ComNinjaHelperInflector extends KTemplateHelperAbstract
{
   	/**
	 * Rules for pluralizing and singularizing of nouns.
	 *
	 * @var array
     */
	protected static $_rules = array
	(	
		'verbalization' => array(
			'/y$/i' 					=> 'ied',
			'/(e)$/i' 					=> '$1d',
			'/$/' 						=> 'ed',
		)
	);

   	/**
 	 * Cache of pluralized and singularized nouns.
	 *
	 * @var array
     */
	protected static $_cache = array(
		'verbalized'   => array()
 	);

	/**
	 * Present English verb conjugated to preterite participle.
	 *
	 * @param 	string Word to verbalize.
	 * @return 	string Present verb
	 */
	public static function verbalize($word)
	{
		//Get the cached noun of it exists
 	   	if(isset(self::$_cache['verbalized'][$word])) {
			return self::$_cache['verbalized'][$word];
 	   	}

		foreach (self::$_rules['verbalization'] as $regexp => $replacement)
		{
			$matches = null;
			$singular = preg_replace($regexp, $replacement, $word, -1, $matches);
			if ($matches > 0) {
				$_cache['verbalized'][$word] = $singular;
				return $singular;
			}
		}

 	   return $word;
	}
}
