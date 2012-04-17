<?php 
/**
 * @category	Ninja
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

$plugin_name	= 'System - Ninja Framework';
if(JFile::exists(JPATH_PLUGINS.'/system/ninja.php') || JFile::exists(JPATH_PLUGINS.'/system/ninja/ninja.php'))
{
	$db->setQuery("SELECT extension_id FROM #__extensions WHERE type = 'plugin' AND folder = 'system' AND element = 'ninja'");
	$result	= $db->loadResult();
	$id		= $result ? $result : 'NULL';
	
	$db->setQuery("INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `enabled`) VALUES ($id, '$plugin_name', 'plugin', 'ninja', 'system', 1) ON DUPLICATE KEY UPDATE `enabled` = 1;");
	$db->query();
	
	$manager = JFactory::getApplication()->isAdmin() ? '<a href="' . JRoute::_('index.php?option=com_plugins&view=plugin&client=site&task=edit&cid[]=' . $db->insertid()) . '">'.sprintf(JText::_("Edit &laquo;%s&raquo; in the Plugin Manager."), $plugin_name).'</a>' : null;
	$msg = sprintf(JText::_("&laquo;%s&raquo; activated. "), $plugin_name) . $manager;
	if(!$user->authorize( 'com_plugins', 'manage' )) $msg = false;
	$uri = clone JFactory::getURI();
	JFactory::getApplication()->redirect($uri->toString(), $msg);
}
else
{
	//Only people able to fix the problem should be notified of the cause
	$message	= JText::_('COM_NINJA_THE_%S_PLUGIN_DOES_NOT_EXIST_WHICH_IS_A_VITAL_PART_OF_THE_NINJA_FRAMEWORK_USED_BY_NINJAFORGE_EXTENSIONS_%2$S_INSTALLS_%1$S_AUTOMATICALLY_SO_THIS_SHOULD_NEVER_HAPPEN_PLEASE_POST_IN_OUR_%2$S_FORUMS_SO_WE_CAN_HELP_YOU_OUT_IMMEDIATELY');
	$message	= sprintf($message, $plugin_name, $extension_name);
	$condition	= $user->authorize('com_installer', 'installer');
	return $notify($condition, $message);
}