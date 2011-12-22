<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category    Ninjaboard
 * @copyright   Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://ninjaforge.com
 */

/**
 * Ninja Dispatcher
 *
 * @author  Stian Didriksen <stian@ninjaforge.com>
 * @package Ninja
 */
abstract class NinjaDispatcher extends ComDefaultDispatcher
{
    /**
     * Constructor.
     *
     * @param   object  An optional KConfig object with configuration options.
     */
    public function __construct(KConfig $config)
    {    
        parent::__construct($config);        

        if($config->maps)
        {
            $package  = $this->getIdentifier()->package;
            $is_site  = JFactory::getApplication()->isSite();
            $app_from = 'com://'.($is_site ? 'site' : 'admin').'/'.$package;
            $app_to   = 'com://'.($is_site ? 'admin' : 'site').'/'.$package;
            foreach($config->maps as $plural)
            {
                $singular = KInflector::singularize($plural);

                KService::setAlias($app_from.'.model.'.$plural, $app_to.'.model.'.$plural);
                KService::setAlias($app_from.'.database.table.'.$plural, $app_to.'.database.table.'.$plural);
                KService::setAlias($app_from.'.database.rowset.'.$plural, $app_to.'.database.rowset.'.$plural);
                KService::setAlias($app_from.'.database.row.'.$singular, $app_to.'.database.row.'.$singular);
            }
        }

        if($config->plugin_group)
        {
            if($config->plugin_group === true) $config->plugin_group = $this->getIdentifier()->package;

            JPluginHelper::importPlugin($config->plugin_group, null, true, $this->getService('koowa:event.dispatcher'));
        }
    }

    /**
     * Initialize method
     *
     * @param   $config
     *                  ->plugin_group  string|bool     Loads a plugin group before dispatch. Use boolean true to load
     *                                                  a group with the same name as the extension.
     *                                                  Specify a custom group by passing a string, or boolean false to
     *                                                  avoid loading any plugin group.
     *                  ->maps          array           Array over entities (models, tables, rows) that should be mapped
     *                                                  from the application we're in to the one we're not
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'plugin_group'  => true,
            'maps'          => array()
        ));

        parent::_initialize($config);
    }
}