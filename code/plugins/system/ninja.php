<?php defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @category    Ninja
 * @copyright   Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://ninjaforge.com
 */

/**
 * Ninja System plugin
 *
 * @author      Stian Didriksen <stian@ninjaforge.com>
 * @category    Ninja
 * @package     Ninja_Plugins
 * @subpackage  System
 */
class plgSystemNinja extends JPlugin
{
    /**
     * Plugin constructor
     *
     * @param   object  The JDispatcher instance
     * @param   array   Plugin configuration array with name, type and params
     * @return  void
     */
    public function __construct($subject, $config)
    {
        parent::__construct($subject, $config);

        // If com_ninja don't exist, abort execution to prevent errors
        if(!is_dir(JPATH_ADMINISTRATOR . '/components/com_ninja')) return;

        // Check if Koowa isn't defined, try importing it and see if that helps
        // @TODO Consider warning messages instead of stopping silently
        if(!defined('KOOWA'))
        {
            if(!file_exists(JPATH_PLUGINS.'/system/koowa.php') && !file_exists(JPATH_PLUGINS.'/system/koowa/koowa.php')) return;

            JFactory::getApplication()->set('wrong_koowa_plugin_order', true);
            
            return false;
        }

        require_once JPATH_ADMINISTRATOR.'/components/com_ninja/loader/loader.php';
        KLoader::addAdapter(new NinjaLoader(array('basepath' => JPATH_ADMINISTRATOR)));
        
        require_once JPATH_ADMINISTRATOR.'/components/com_ninja/service/locator/locator.php';
        KServiceIdentifier::addLocator(new NinjaServiceLocator());

        // Override JModuleHelper if we're on Joomla! 1.6 or later
        if(JVersion::isCompatible('1.6.0'))
        {
            $override = JPATH_ADMINISTRATOR.'/components/com_ninja/overrides/modulehelper.php';
            //if(file_exists($override)) require_once $override;
        }

        $napiElement = JPATH_ADMINISTRATOR . '/components/com_ninja/elements/napi.php';
        if(!class_exists('JElementNapi', false) && file_exists($napiElement)) require_once $napiElement;
    }
}