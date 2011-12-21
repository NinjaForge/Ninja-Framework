<?php
/**
 * @version     $Id: koowa.php 1380 2011-10-11 22:29:43Z stian $
 * @category    Nooku
 * @package     Nooku_Components
 * @subpackage  Koowa
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
 * @subpackage  Koowa
 */

// Check if Koowa is active
if(!defined('KOOWA')) {
    JError::raiseWarning(0, JText::_("Koowa wasn't found. Please install the Koowa plugin and enable it."));
    return;
}

// Create the controller dispatcher
echo KService::get('com://admin/koowa.dispatcher')->dispatch();