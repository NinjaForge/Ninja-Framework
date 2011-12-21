<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: default.php 1133 2011-07-12 12:16:26Z stian $
 * @package		Ninja
 * @copyright	Copyright (C) 2011 NinjaForge. All rights reserved.
 * @license 	GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */
  /**
 * Helper for rendering changelogs, instructions, links, footer, etc.
 *
 * @author		Stian Didriksen <stian@ninjaforge.com>
 * @category	Koowa
 * @package		Koowa_Template
 * @subpackage	Helper
 * @uses		KArrayHelper
 */
class ComNinjaHelperDefault extends KTemplateHelperDefault
{
	/**
	 * Cache for loaded things
	 *
	 * @var array
	 */
	protected $_cache = array();

	/**
	 * Method for setting the js framework that should be used, and getting the name of the current framework
	 *
	 * @param	boolean|string		Pass the fw name that should be used in here to change what js framework used
	 * @return	string				The current js fw in use, defaults to mootools 1.2.x in the admin, 1.12 frontend
	 */
	public function framework($force = false)
	{
		static $framework;
		
		if(!$framework && !$force)
		{
			$app = KFactory::get('lib.joomla.application');
			$framework = $app->isAdmin() ? 'mootools12' : 'mootools11';
		}
		if($force) $framework = $force;
		return $framework;
	}
	
	public function formid($extra = array(), $package = null)
	{
		$count = 1;
		$parts['type.package'] = str_replace('_', '-', KRequest::get('get.option', 'cmd'), $count);
		$parts['view'] = KRequest::get('get.view', 'cmd');
		
		return implode('-', array_merge($parts, (array) $extra));
	}
	
	public function toggle($toggle = 0, $a = null, $b = null)
	{
		if (is_array($a) || is_object($a)) 	$states = (array) $a;
		else 								$states = array($a, $b);
		$states = array_reverse($states);
		return $states[(int)$toggle];
	}
	
	protected function _img($src, $extension = null)
	{
		$result = self::_getAsset('images', $src, $extension);

		//Images might have whitespace in their names, especially if they're user content
		if($result)
		{
			$name	= basename($result);
			$path	= dirname($result);
			$result	= $path.'/'.rawurlencode($name);
		}

		return $result;
	}
	
	protected function _css($href = false, $extension = null)
	{
		$document = KFactory::get('lib.joomla.document');
		
		//Used in case #3 if direction is RTL
		if($document->direction == 'rtl') $original = $href;

		if(KFactory::get('lib.koowa.filter.url')->validate($href))
		{
			$document->addStylesheet($href);
		}
		elseif(strpos($href, '{'))
		{
			if($this->_isMorph()) {
				$morph = Morph::getInstance();
				$morph->addStyleDeclaration($href."\n");
			} else {
				$document->addStyleDeclaration($href."\n");
			}
		}
		elseif($href = self::_getAsset('css', $href, $extension))
		{
			if($this->_isMorph()) {
				$morph = Morph::getInstance();
				$morph->addStylesheet(substr($href, strlen(JURI::root(1))));
			} else {
				$document->addStylesheet($href);
			}
			//RTL support
			if(KFactory::get('lib.joomla.document')->direction == 'rtl' && strpos($original, '_rtl.css') === false) {
				$this->css(str_replace('.css', '_rtl.css', $original));
			}
		}
		else
		{
			return false;
		}

		return $href;
	}
	
	protected function _js($href = false, $extension = null)
	{
		$document = KFactory::get('lib.joomla.document');
		if(KFactory::get('lib.koowa.filter.url')->validate($href))
		{
			$document->addScript($href);
		}
		elseif(strpos($href, '(') !== false)
		{
			if($this->_isMorph()) {
				$morph = Morph::getInstance();
				$morph->addScriptDeclaration($href."\n");
			} else {
				$document->addScriptDeclaration($href."\n");
			}
		}
		elseif($src = self::_getAsset('js', '/'.self::framework().$href))
		{
			if($this->_isMorph()) {
				$morph = Morph::getInstance();
				$morph->addScriptAfter($href = substr($src, strlen(JURI::root(1))));
			} else {
				$document->addScript($href = $src);
			}
		}
		elseif($href = self::_getAsset('js', $href))
		{
			if($this->_isMorph()) {
				$morph = Morph::getInstance();
				$morph->addScriptAfter(substr($href, strlen(JURI::root(1))));
			} else {
				$document->addScript($href);
			}
		}
		else
		{
			return false;
		}

		return $href;		
	}
	
	private function _isMorph()
	{
		$isTemplate = KFactory::get('lib.joomla.application')->getTemplate() == 'morph' && class_exists('Morph');
		$hasAPIs	= false;
		if($isTemplate) $hasAPIs	= method_exists(Morph::getInstance(), 'addStyleDeclaration');
		return $isTemplate && $hasAPIs;
	}
	
	protected function _getAsset($asset, $url, $extension = null)
	{
		if(!$extension){
			$extension = KRequest::get('get.option', 'cmd');
		}
		$template  = KFactory::get('lib.joomla.application')->getTemplate();
		$isMorph   = $template == 'morph';
		
		$custom	   = '/images/'.$extension.$url;
		$framework = '/media/plg_koowa/'.$asset.$url;
		$fallback  = '/media/com_ninja/'.$asset.$url;
		$default   = '/media/'.$extension.'/'.$asset.$url;
		$overriden = '/templates/'.$template.'/'.$asset.'/'.$extension.$url;
		if($isMorph)
		{
			$overriden = '/templates/'.$template.'/core/'.$asset.'/'.$extension.$url;
			if(class_exists('Morph')) $themelet = '/morph_assets/themelets/'.Morph::getInstance()->themelet.'/'.$asset.'/'.$extension.$url;
			else $themelet = null;
		}

		//Maybe support more types of assets for $custom in the future
		if($asset == 'images' && file_exists(JPATH_ROOT.$custom))	return KRequest::root().$custom;
		elseif($isMorph && file_exists(JPATH_ROOT.$themelet))		return KRequest::root().$themelet;
		elseif(file_exists(JPATH_BASE.$overriden))					return KRequest::base().$overriden;
		elseif(file_exists(JPATH_ROOT.$default))					return KRequest::root().$default;
		elseif(file_exists(JPATH_ROOT.$fallback))					return KRequest::root().$fallback;
		elseif(file_exists(JPATH_ROOT.$framework))					return KRequest::root().$framework;
        
		return false;
	}
	
	public function __call($method, $arguments) 
	{
		$key = md5(serialize($arguments));
		if(isset($this->_cache[$method][$key])) return $this->_cache[$method][$key];

		if(method_exists($this, '_'.$method)) {
			$result = count($arguments) === 1 
						? $this->{'_'.$method}($arguments[0])
						: $this->{'_'.$method}($arguments[0], $arguments[1]);
		} else {
			$result = self::_getAsset($method, $arguments[0]);
		}

		$this->_cache[$method][$key] = $result;

		return $result;
	}
}