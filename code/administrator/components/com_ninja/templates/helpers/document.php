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
 * Document helper for dealing with things like scripts, styles, formids
 *
 * @TODO patch ComDefaultTemplateFilterScript and Style with the check that prevents duplicate declarations
 *
 * @author      Stian Didriksen <stian@ninjaforge.com>
 * @category	Ninja
 * @package		Ninja_Template
 * @subpackage	Helper
 */
class NinjaTemplateHelperDocument extends KTemplateHelperDefault implements KServiceInstantiatable
{
    /**
     * Force creation of a singleton
     *
     * @param   object  An optional KConfig object with configuration options
     * @param   object  A KServiceServiceInterface object
     * @return KTemplateStack
     */
    public static function getInstance(KConfigInterface $config, KServiceInterface $container)
    { 
        // Check if an instance with this identifier already exists or not
        if (!$container->has($config->service_identifier))
        {
            //Create the singleton
            $classname = $config->service_identifier->classname;
            $instance  = new $classname($config);
            $container->set($config->service_identifier, $instance);
        }
        
        return $container->get($config->service_identifier);
    }

    /**
     * Cache for loaded things
     *
     * @var array
     */
    protected $_cache = array();

    /**
     * Method for setting the js framework that should be used, and getting the name of the current framework
     *
     * Examples:
     * <code>
     * // Outside a template layout
     * $helper = $this->getService('ninja:template.helper.document');
     * $helper->framework();
     * $helper->framework('mootools13');
     * </code>
     *
     * @param	boolean|string		Pass the fw name that should be used in here to change what js framework used
     * @return	string				The current js fw in use, defaults to mootools 1.2.x in the admin, 1.12 frontend
     */
    public function framework($force = false)
    {
        static $framework;
        
        if(!$framework && !$force) $framework = 'mootools12';
        if($force)                 $framework = $force;

        return $framework;
    }
    
    /**
     * Method for generating a unique element id
     *
     * Examples:
     * <code>
     * // Outside a template layout
     * $helper = $this->getService('ninja:template.helper.document');
     * $helper->formid();
     * $helper->formid('additional-text');
     *
     * // Inside a template layout
     * <?= @ninja('document.formid') ?>
     * <?= @ninja('document.formid', 'additional-text') ?>
     * </code>
     *
     * @param   string      additional text to add to the returned string
     * @return  string      
     */
    public function formid($extra = array(), $package = null)
    {
        $count = 1;
        $parts['type.package'] = str_replace('_', '-', KRequest::get('get.option', 'cmd'), $count);
        $parts['view'] = KRequest::get('get.view', 'cmd');
        
        return implode('-', array_merge($parts, (array) $extra));
    }
    
    /**
     * Method for generating a string based on a boolean value
     *
     * Examples:
     * <code>
     * // Outside a template layout
     * $helper = $this->getService('ninja:template.helper.document');
     * $helper->toggle($row->enabled, 'enabled', 'disabled');
     *
     * // Inside a template layout
     * <?= @ninja('document.toggle', $row->enabled, 'enabled', 'disabled') ?>
     * <?= @toggle($row->enabled, 'enabled', 'disabled')
     * </code>
     *
     * @param   bool     toggle value
     * @param   string   true toggle text string
     * @param   string   false toggle text string
     * @return  string   the true or false text string
     */
    public function toggle($toggle = 0, $a = null, $b = null)
    {
        if (is_array($a) || is_object($a)) 	$states = (array) $a;
        else 								$states = array($a, $b);
        $states = array_reverse($states);
        return $states[(int)$toggle];
    }
    
    /**
     * Method for loading something to the <head>
     *
     * Examples:
     * <code>
     * // Outside a template layout
     * $helper = $this->getService('ninja:template.helper.document');
     * $helper->load(array('/mootools.plugin.js', '/script.js', '/style.css'));
     * $helper->load('/style.css');
     * $helper->load('css', '* {background:red}');
     * $helper->load('js', 'alert(1)');
     *
     * // There is no reason to use this in the template views, load the scripts directly
     * </code>
     *
     * @param   array|string    An array over things to load, a string of the file to load or what to load (css or js)
     * @param   string          the inline js/css you wish to load
     * @return  boolean|string  If specified, then $load needs to be css or js. Defaults to false, meaning it's not inline
     */
    public function load($load, $inline = false)
    {
        $document = JFactory::getDocument();

        if($inline)
        {
            if($load == 'js') {
                $document->addScriptDeclaration($inline."\n");
            } elseif($load == 'css') {
                $document->addStyleDeclaration($inline."\n");
            }
            
            return;
        }

        $filter = $this->getService('koowa:filter.url');
        foreach((array)$load as $args)
        {
            if(count($args) > 1) {
                $this->load($args[0], $args[1]);
                continue;
            }

            $key = md5($args).md5($inline);
            if(!isset($this->_cache[$key])) $this->_cache[$key] = true;
            else                            return true;
            
            //Is array but not valid so skip it
            if(is_array($args)) continue;
            
            $src = $args;
            if(strpos($src, '.js'))
            {
                if($filter->validate($src)) {
                    $document->addScript($src);
                }
                elseif($override = self::_getAsset('js', '/'.self::framework().$src))
                {
                    $document->addScript($this->_appendModifiedQueryVariable($override));
                }
                elseif($src = self::_getAsset('js', $src))
                {
                    $document->addScript($this->_appendModifiedQueryVariable($src));
                }
            }
            elseif(strpos($src, '.css'))
            {
                //Used in case #3, to load LTR or RTL specific css
                $original = $src;

                if($filter->validate($src)) {
                    $document->addStylesheet($src);
                }
                elseif($src = self::_getAsset('css', $src))
                {
                    $document->addStylesheet($this->_appendModifiedQueryVariable($src));

                    //RTL+LTR support
                    $direction = '_'.JFactory::getDocument()->direction.'.css';
                    $this->load(str_replace('.css', $direction, $original));
                }
            }
        }
    }
    
    /**
     * Used in conjunction with ninja:template.filter.document
     *
     * @param   array|string    An array over things to load, a string of the file to load or what to load (css or js)
     */
    public function render($load)
    {
        $document = JFactory::getDocument();

        $html = array();

        $filter = $this->getService('koowa:filter.url');
        foreach((array)$load as $src)
        {
            if(strpos($src, '.js'))
            {
                if($filter->validate($src)) {
                    //@TODO make _appendModifiedQueryVariable work with media:// urls
                    $html[] = '<script src="'.$src.'" />';
                }
                elseif($override = self::_getAsset('js', '/'.self::framework().$src))
                {
                    $html[] = '<script src="'.$this->_appendModifiedQueryVariable($override).'" />';
                }
                elseif($src = self::_getAsset('js', $src))
                {
                    $html[] = '<script src="'.$this->_appendModifiedQueryVariable($src).'" />';
                }
            }
            elseif(strpos($src, '.css'))
            {
                //Used in case #3, to load LTR or RTL specific css
                $original = $src;

                if($filter->validate($src)) {
                    //@TODO make _appendModifiedQueryVariable work with media:// urls
                    $html[] = '<style src="'.$src.'" />';
                }
                elseif($src = self::_getAsset('css', $src))
                {
                    $html[] = '<style src="'.$this->_appendModifiedQueryVariable($src).'" />';

                    //RTL+LTR support
                    $direction = '_'.JFactory::getDocument()->direction.'.css';
                    $html[] = $this->render(str_replace('.css', $direction, $original));
                }
            }
        }

        return (implode(PHP_EOL, $html));
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
        $document = JFactory::getDocument();
        
        //Used in case #3, to load LTR or RTL specific css
        $original = $href;

        if($this->getService('koowa:filter.url')->validate($href))
        {
            $document->addStylesheet($href);
        }
        elseif(strpos($href, '{'))
        {
            $document->addStyleDeclaration($href."\n");
        }
        elseif($href = self::_getAsset('css', $href, $extension))
        {
            $html = '<style src="'.$href.'" />';

            //RTL+LTR support
            $direction = '_'.JFactory::getDocument()->direction.'.css';
            $html .= self::css(str_replace('.css', $direction, $original));
            
            return $html;
        }

        return false;
    }
    
    protected function _js($href = false, $extension = null)
    {
        $document = JFactory::getDocument();
        if($this->getService('koowa:filter.url')->validate($href))
        {
            $document->addScript($href);
        }
        elseif(strpos($href, '(') !== false)
        {
            $document->addScriptDeclaration($href."\n");
        }
        elseif($src = self::_getAsset('js', '/'.self::framework().$href))
        {
            $document->addScript($href = $src);
        }
        elseif($href = self::_getAsset('js', $href))
        {
            $document->addScript($href);
        }

        return false;
    }

    /**
     * Appends a query variable to the asset url
     *
     * This is to make sure that each url is unique, but changes as the asset change.
     * Doing so allows full usage of browser side cache without worrying about assets not taking effect when modified.
     *
     * @param   string  The relative and local url to the asset, urls with KRequest::root() also supported
     * @return  string  The asset url with the query variable appended
     */
    protected function _appendModifiedQueryVariable($url)
    {
        $count = 1;
        $src   = JPATH_ROOT.str_replace(KRequest::root(), '', $url, $count);

        if($modified = filemtime($src)) return $url.'?modified='.$modified;

        return $url;
    }
    
    protected function _getAsset($asset, $url, $extension = null)
    {
        if(!$extension){
            $extension = KRequest::get('get.option', 'cmd');
        }
        $template  = JFactory::getApplication()->getTemplate();
        
        $custom	   = '/images/'.$extension.$url;
        $framework = '/media/lib_koowa/'.$asset.$url;
        $fallback  = '/media/com_ninja/'.$asset.$url;
        $default   = '/media/'.$extension.'/'.$asset.$url;
        $overriden = '/templates/'.$template.'/'.$asset.'/'.$extension.$url;

        //Maybe support more types of assets for $custom in the future
        if($asset == 'images' && file_exists(JPATH_ROOT.$custom))	return KRequest::root().$custom;
        elseif(file_exists(JPATH_BASE.$overriden))					return KRequest::base().$overriden;
        elseif(file_exists(JPATH_ROOT.$default))					return KRequest::root().$default;
        elseif(file_exists(JPATH_ROOT.$fallback))					return KRequest::root().$fallback;
        elseif(file_exists(JPATH_ROOT.$framework))					return KRequest::root().$framework;
        
        return false;
    }
    
    public function __call($method, $arguments) 
    {
        if($this->getService('koowa:filter.alpha')->sanitize(strtolower($method)) != $method) {
            return parent::__call($method, $arguments);
        }

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