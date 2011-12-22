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
 * KInflector to pluralize and singularize English nouns.
 *
 * @author		Stian Didriksen <stian@ninjaforge.com>
 * @category	Ninja
 * @package		Ninja_Template
 * @subpackage	Helper
 */
class NinjaTemplateHelperInflector extends KTemplateHelperAbstract
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
	 * 'Apply' to 'Applied', 'Save' to 'Saved'
	 *
	 * Examples: 
	 * <code>
	 * // Outside a template layout
	 * $helper = $this->getService('ninja:template.helper.inflector');
	 * $helper->verablize('Apply');
	 *
	 * // Inside a template layout
	 * <?= @ninja('inflector.verbalize', 'Apply') ?>
	 * </code>
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
