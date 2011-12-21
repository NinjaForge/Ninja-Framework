<?php
/**
* @version      $Id: cancel.php 2876 2011-03-07 22:19:20Z johanjanssens $
* @category		Koowa
* @package		Koowa_Toolbar
* @subpackage	Button
* @copyright    Copyright (C) 2007 - 2010 Johan Janssens. All rights reserved.
* @license      GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
*/

/**
 * Cancel button class for a toolbar
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @category    Koowa
 * @package     Koowa_Toolbar
 * @subpackage  Button
 */
class KToolbarButtonCancel extends KToolbarButtonPost
{
    public function getOnClick()
    {
        $option = KRequest::get('get.option', 'cmd');
        $view   = KRequest::get('get.view', 'cmd');
        $id     = KRequest::get('get.id', 'int');
        $token  = JUtility::getToken();
        $json   = "{method:'post', url:'index.php?option=$option&view=$view&id=$id', element:'adminForm', params:{action:'cancel', _token:'$token'}}";

        return 'new Koowa.Form('.$json.').submit();';
    }

}