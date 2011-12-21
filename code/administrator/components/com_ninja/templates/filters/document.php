<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: document.php 1399 2011-11-01 14:22:48Z stian $
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
class NinjaTemplateFilterDocument extends KTemplateFilterAbstract implements KTemplateFilterRead
{
	/**
	 * Search replace inline style and script tags
	 *
	 * @param string $text
	 */
	public function read(&$text)
	{
		//@TODO get rid of the LEGACY!!
		// Legacy for Chameleon
		$text		= str_replace('@render(', '$this->getService($this->getView())->render(', $text);
		// Legacy for placeholders
		$text		= str_replace(array('@$placeholder(', '$placeholder('), '$this->getService($this->getView())->placeholder(', $text);
		// Legacy for edit links in lists
		$text		= str_replace(array('@$edit(', '@edit(', '$edit('), '$this->getService($this->getView())->edit(', $text);
		// Legacy for img
		$text		= str_replace(array('@$img(', '@img(', '$img('), "\$this->getService('ninja:template.helper.document')->img(", $text);
		
		// Alias shortcuts
		// @TODO remember to move this to the view later
		$text		= str_replace('@id(', "\$this->getService('ninja:template.helper.document')->formid(", $text);
		$text		= str_replace('@toggle(', "\$this->getService('ninja:template.helper.document')->toggle(", $text);
		$text		= str_replace('@ninja(\'', '$this->renderHelper(\'ninja:template.helper.', $text);
	
		// match all scripts where there's an 'src' attribute
		$pattern	= '!(<script\s+type=\"text/javascript\"\s+src=\"(.*?)\"></script>)!is';
		$text = preg_replace_callback($pattern, array($this, 'format'), $text);
		
		// match all link tags where rel="stylesheet"
		$pattern	= '!(<link\s+rel=\"stylesheet\"\s+href=\"(.*?)\"\s*\/>)!is';
		$text = preg_replace_callback($pattern, array($this, 'format'), $text);
	}

	/**
	 * preg callback that formats the script and style tags
	 *
	 * @param string $text
	 */
	public function format($matches)
	{
	    //@TODO optimize by not initializing the template helper on every match
	    return $this->getService('ninja:template.helper.document')->render($matches[2]);
	}
}