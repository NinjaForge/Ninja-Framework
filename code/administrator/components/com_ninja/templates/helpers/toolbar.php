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
 * Toolbar Helper Class
 *
 * @author      Stian Didriksen <stian@ninjaforge.com>
 * @category	Ninja
 * @package		Ninja_Template
 * @subpackage	Helper
 */
class NinjaTemplateHelperToolbar extends ComDefaultTemplateHelperToolbar
{
    /**
     * Render the toolbar title, specialised to load js and css
     * 
     * @param   array   An optional array with configuration options
     * @return  string  Html
     */
    public function title($config = array())
    {
        $config = new KConfig($config);
        $config->append(array(
        	'toolbar' => null
        ));

        $name = $config->toolbar->getName();

        //@TODO patch pending: https://groups.google.com/d/topic/nooku-framework/xODy9b8aNj4/discussion
        $config->toolbar->setIcon($name);

        $html = parent::title($config);

        $helper = $this->getService('ninja:template.helper.document');
        $helper->load(array('/toolbar.css', '/toolbar.js'));
        
        $img = $helper->img('/48/'.KInflector::pluralize($name).'.png');
        if(!$img) {
            $img = $helper->img('/48/'.KInflector::singularize($name).'.png');
        }
    	if($img) {
    		$helper->load('css', '.header.icon-48-'.$name.' { background-image: url(' . $img . '); }');
    	}
        

        return $html;
    }
}