<?php
/**
 * @version     $Id: link.php 2876 2011-03-07 22:19:20Z johanjanssens $
 * @category	Nooku
 * @package     Nooku_Components
 * @subpackage  Default
 * @copyright   Copyright (C) 2007 - 2010 Johan Janssens. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * Script Filter
.*
 * @author      Johan Janssens <johan@nooku.org>
 * @category    Nooku
 * @package     Nooku_Components
 * @subpackage  Default
 */
class ComDefaultTemplateFilterLink extends KTemplateFilterLink
{   
    /**
     * Render script information
     * 
     * @param string    The script information
     * @param array     Associative array of attributes
     * @return string   
     */
    protected function _renderScript($link, $attribs = array())
    {   
        if(KRequest::type() == 'AJAX') {
            return parent::_renderLink($script, $attribs);
        }
        
        $relType  = 'rel';
        $relValue = $attribs['rel'];
        unset($attribs['rel']);
            
        KFactory::get('lib.joomla.document')
            ->addHeadLink($link, $relValue, $relType, $attribs);
    }
}