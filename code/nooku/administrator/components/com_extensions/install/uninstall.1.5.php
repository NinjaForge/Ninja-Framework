<?php
/**
 * @version     $Id: uninstall.1.5.php 49 2011-03-28 23:04:49Z stiandidriksen $
 * @category    Koowa
 * @package     Koowa_Components
 * @subpackage  Extensions
 * @copyright   Copyright (C) 2010 Timble CVBA and Contributors. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// Delete the plugin from the database
$database->setQuery("DELETE FROM `#__plugins` WHERE element = 'koowa' AND folder = 'system'");
$database->query();