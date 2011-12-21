<?php
/**
 * @version     $Id: install.1.6.php 49 2011-03-28 23:04:49Z stiandidriksen $
 * @category    Koowa
 * @package     Koowa_Components
 * @subpackage  Extensions
 * @copyright   Copyright (C) 2010 Timble CVBA and Contributors. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// Prevent the plugin row to be inserted more than once
$query = "SELECT COUNT(*) FROM `#__extensions` WHERE type = 'plugin' AND folder = 'system' AND element = 'koowa'";
$database->setQuery($query);
if(!$database->loadResult())
{
    $database->setQuery("INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `enabled`) VALUES (NULL, 'System - Koowa', 'plugin', 'koowa', 'system', 1);");
    if (!$database->query()) {
        // Install failed, roll back changes
        $this->parent->abort(JText::_('Plugin').' '.JText::_('Install').': '.$database->stderr(true));
        return false;
    }
}

// Disable com_extensions from the admin menu until its realy for primetime
$query = "UPDATE `#__extensions` SET `enabled` = '0' WHERE type = 'component' AND element = 'com_extensions'";
$database->setQuery($query);
$database->query();