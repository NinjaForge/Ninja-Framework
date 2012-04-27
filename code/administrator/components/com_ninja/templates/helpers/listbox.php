<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category    Ninja
 * @package     Ninja_Template
 * @subpackage  Helper
 * @copyright   Copyright (C) 2011 NinjaForge. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://ninjaforge.com
 */
 
 /**
 * Listbox Helper
 *
 * @author      Richie Mortimer <stian@ninjaforge.com>
 * @category    Ninja
 * @package     Ninja_Template
 * @subpackage  Helper
 */
class NinjaTemplateHelperListbox extends KTemplateHelperListbox
{
    /**
     * Gerenates a hierarchical listbox to select an entry of a nested set.
     * If the config param 'exclude' is set to a value of table id, this entry (and all of it's childrens)
     * are not shown in the list box.
     *
     * @param KConfig $config  Nooku configuration object
     * @return string HTML-Code of listbox
     */
    public function nestedlist($config = array())
    {
        $config = new KConfig($config);
        $config->append(array(
            'name' => '',
            'state' => null,
            'attribs' => array(),
            'model' => null,
            'prompt' => 'COM_NINJA_SELECT',
            'exclude' => null
        ))->append(array(
            'selected' => $config->{$config->name}
        ))->append(array(
            'column' => $config->value,
            'deselect' => true,
            'filter' => false,
            'noparents' => false
        ));

        $option     = KRequest::get('get.option', 'cmd');
        $app        = JFactory::getApplication()->isAdmin() ? 'admin' : 'site';
        $package    = str_replace('com_', null, $option);
        $identifier = 'com://'.$app.'/'.$package.'.model.'. ($config->model ? $config->model : KInflector::pluralize($package));

        $list = $this->getService($identifier)->getList();

        $options = array();
        if ($config->deselect)
        {
            $options[] = $this->option(array('text' => JText::_($config->prompt), 'value' => 0));
        }

        foreach ($list as $item)
        {
            // exclude this category and any children
            if (isset($config->exclude) && $config->exclude == $item->id)
            {
                $myLft = $item->lft;
                $myRgt = $item->rgt;
                continue;
            }

            if (isset($myLft) && isset($myRgt) && $item->lft > $myLft && $item->rgt < $myRgt)
                continue;

            // Only take parent categories
            if (isset($config->onlyparents) && $config->onlyparents && $item->rgt - $item->lft == 1)
                continue;

            $disabled = ($config->noparents && $item->level == 0 && $item->rgt - $item->lft != 1)  ? true : false;

            $options[] = $this->option(array('text' => str_repeat('|—', $item->level) . ' '.$item->title, 'value' => $item->id, 'disable' => $disabled));
        }
        
        //Add the options to the config object
        $config->options = $options;

        return $this->optionlist($config);
    }
}