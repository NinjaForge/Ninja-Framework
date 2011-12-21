<?php
/**
 * @version   	$Id: default.php 1372 2011-10-11 18:56:47Z stian $
 * @category	Nooku
 * @package     Nooku_Components
 * @subpackage  Default
 * @copyright  	Copyright (C) 2007 - 2010 Johan Janssens. All rights reserved.
 * @license   	GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * Default Toolbar
.*
 * @author      Johan Janssens <johan@nooku.org>
 * @category    Nooku
 * @package     Nooku_Components
 * @subpackage  Default
 */
class ComDefaultControllerToolbarDefault extends KControllerToolbarDefault
{
    /**
     * Enable toolbar command
     * 
     * @param   object  A KControllerToolbarCommand object
     * @return  void
     */
    protected function _commandEnable(KControllerToolbarCommand $command)
    {
        $command->icon = 'icon-32-publish'; 
        
        $command->append(array(
            'attribs' => array(
                'data-action' => 'edit',
                'data-data'   => '{enabled:1}'
            )
        ));
    }
    
    /**
     * Disable toolbar command
     * 
     * @param   object  A KControllerToolbarCommand object
     * @return  void
     */
    protected function _commandDisable(KControllerToolbarCommand $command)
    {
        $command->icon = 'icon-32-unpublish';
        
        $command->append(array(   
            'attribs' => array(
                'data-action' => 'edit',
                'data-data'   => '{enabled:0}'
            )
        ));
    }
    
    /**
     * Export toolbar command
     * 
     * @param   object  A KControllerToolbarCommand object
     * @return  void
     */
    protected function _commandExport(KControllerToolbarCommand $command)
    {
        //Get the states
        $states = $this->getController()->getModel()->getState()->toArray(); 
        
        unset($states['limit']);
        unset($states['offset']);
        
        $states['format'] = 'csv';
          
        //Get the query options
        $query  = http_build_query($states);
        $option = $this->getIdentifier()->package;
        $view   = $this->getIdentifier()->name;
        
        $command->append(array(
            'attribs' => array(
                'href' =>  JRoute::_('index.php?option=com_'.$option.'&view='.$view.'&'.$query)
            )
        ));
    }
      
    /**
     * Modal toolbar command
     * 
     * @param   object  A KControllerToolbarCommand object
     * @return  void
     */
    protected function _commandModal(KControllerToolbarCommand $command)
    { 
        $option = $this->getIdentifier()->package;
        
        $command->append(array(
            'width'   => '640',
            'height'  => '480',
            'href'	  => ''
        ))->append(array(
            'attribs' => array(
                'class' => array('modal'),
                'href'  => $command->href,
                'rel'   => '{handler: \'iframe\', size: {x: '.$command->width.', y: '.$command->height.'}}'
            )
        ));
    }
}