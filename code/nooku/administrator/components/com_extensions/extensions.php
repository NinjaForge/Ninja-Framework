<?php
/**
 * @version     $Id: extensions.php 49 2011-03-28 23:04:49Z stiandidriksen $
 * @category    Nooku
 * @package     Nooku_Components
 * @subpackage  Extensions
 * @copyright   Copyright (C) 2010 Timble CVBA and Contributors. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * Description
 *
 * @author      Stian Didriksen <stian@ninjaforge.com>
 * @category    Nooku
 * @package     Nooku_Components
 * @subpackage  Extensions
 */

// Check if Koowa is active
if(!defined('KOOWA')) {
    JError::raiseWarning(0, JText::_("Koowa wasn't found. Please install the Koowa plugin and enable it."));
    return;
}

// Require the defines
//KLoader::load('admin::com.extensions.defines');

// Create the controller dispatcher
echo KFactory::get('admin::com.extensions.dispatcher')->dispatch(KRequest::get('get.view', 'cmd', 'dashboard'));