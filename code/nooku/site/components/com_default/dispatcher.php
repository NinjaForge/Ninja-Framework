<?php
/**
 * @version     $Id: dispatcher.php 3076 2011-04-07 20:24:25Z johanjanssens $
 * @category	Nooku
 * @package     Nooku_Components
 * @subpackage  Default
 * @copyright   Copyright (C) 2007 - 2010 Johan Janssens. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * Default Dispatcher
.*
 * @author      Johan Janssens <johan@nooku.org>
 * @category    Nooku
 * @package     Nooku_Components
 * @subpackage  Default
 */
class ComDefaultDispatcher extends KDispatcherDefault
{ 
    /**
     * Dispatch the controller and redirect
     * 
     * This function divert the standard behavior and will redirect if no view
     * information can be found in the request.
     * 
     * @param   string      The view to dispatch. If null, it will default to
     *                      retrieve the controller information from the request or
     *                      default to the component name if no controller info can
     *                      be found.
     *
     * @return  KDispatcherDefault
     */
    protected function _actionDispatch(KCommandContext $context)
    {
        //Redirect if no view information can be found in the request
        if(!KRequest::has('get.view')) 
        {
            $view = count($context->data) ? $context->data : $this->_controller_default;
            
            $url = clone(KRequest::url());
            $url->query['view'] = $view;
            
            KFactory::get('lib.joomla.application')->redirect($url);
        }
        
        return parent::_actionDispatch($context);
    }
    
    /**
     * Push the controller data into the document
     * 
     * This function divert the standard behavior and will push specific controller data
     * into the document
     *
     * @return  KDispatcherDefault
     */
    protected function _actionRender(KCommandContext $context)
    {
        $controller = KFactory::get($this->getController());
        $view       = KFactory::get($controller->getView());
    
        $document = KFactory::get('lib.joomla.document');
        $document->setMimeEncoding($view->mimetype);
        
        return parent::_actionRender($context);
    }
}