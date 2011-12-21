<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: document.php 794 2011-01-10 18:44:32Z stian $
 * @category	Ninja
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * Template rule to handle document scripts and styles/stylesheets
 *
 * @author		Stian Didriksen <stian@ninjaforge.com>
 * @category	Ninja
 * @package     Ninja_Template
 * @subpackage	Filter
 */
class ComNinjaTemplateFilterDocument extends KTemplateFilterAbstract implements KTemplateFilterRead
{
	/**
	 * Search replace inline style and script tags with @css and @js calls
	 *
	 * @param string $text
	 */
	public function read(&$text)
	{
		//@TODO get rid of the LEGACY!!
		// Legacy for Chameleon
		$text		= str_replace('@render(', 'KFactory::get($this->getView())->render(', $text);
		// Legacy for placeholders
		$text		= str_replace(array('@$placeholder(', '$placeholder('), 'KFactory::get($this->getView())->placeholder(', $text);
		// Legacy for edit links in lists
		$text		= str_replace(array('@$edit(', '@edit(', '$edit('), 'KFactory::get($this->getView())->edit(', $text);
		// Legacy for css
		$text		= str_replace(array('@$css(', '@css(', '$css('), 'KFactory::get($this->getView())->css(', $text);
		// Legacy for js
		$text		= str_replace(array('@$js(', '@js(', '$js('), 'KFactory::get($this->getView())->js(', $text);
		// Legacy for img
		$text		= str_replace(array('@$img(', '@img(', '$img('), "KFactory::get('admin::com.ninja.helper.default')->img(", $text);
		
		// Alias shortcuts
		// @TODO remember to move this to the view later
		$text		= str_replace('@id(', "KFactory::get('admin::com.ninja.helper.default')->formid(", $text);
		$text		= str_replace('@toggle(', "KFactory::get('admin::com.ninja.helper.default')->toggle(", $text);
		$text		= str_replace('@ninja(\'', '$this->loadHelper(\'admin::com.ninja.helper.', $text);
	
		// match all scripts where there's an 'src' attribute
		$pattern	= '!(<script\s+type=\"text/javascript\"\s+src=\"(.*?)\"></script>)!is';

		$text = preg_replace_callback($pattern, array($this, 'addScript'), $text);
		
		// match all link tags where rel="stylesheet"
		$pattern	= '!(<link\s+rel=\"stylesheet\"\s+href=\"(.*?)\"\s*\/>)!is';

		$text = preg_replace_callback($pattern, array($this, 'addStyleSheet'), $text);

		// match all styles where type="text/css"
		$pattern	= '!(<style[^>]*>)(.*?)(</style>)!is';

		$text = preg_replace_callback($pattern, array($this, 'addStyleDeclaration'), $text);
		
		// match all scripts where type="text/javascript"
		$pattern	= '!(<script\s+type=\"text/javascript\">)(.*?)(</script>)!is';

		$text = preg_replace_callback($pattern, array($this, 'addScriptDeclaration'), $text);
	}
	
	public function addScript($matches)
	{
		return '<?php KFactory::get(\'admin::com.ninja.helper.default\')->js(\'' . $matches[2] . '\') ?>';
	}
	
	public function addScriptDeclaration($matches)
	{
		return '<?php ob_start() ?>' . $matches[2] . '<?php KFactory::get(\'admin::com.ninja.helper.default\')->js(ob_get_clean()) ?>';
	}
	
	public function addStyleSheet($matches)
	{
		return '<?php ob_start() ?>' . $matches[2] . '<?php KFactory::get(\'admin::com.ninja.helper.default\')->css(ob_get_clean()) ?>';
	}
	
	public function addStyleDeclaration($matches)
	{
		return '<?php ob_start() ?>' . $matches[2] . '<?php KFactory::get(\'admin::com.ninja.helper.default\')->css(ob_get_clean()) ?>';
	}
}