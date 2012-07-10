<?php 
/**
 * @category	Ninja
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

if(JFile::exists(JPATH_PLUGINS.'/system/koowa/koowa.php') || JFile::exists(JPATH_PLUGINS.'/system/koowa.php'))
{
	$db->setQuery("SELECT extension_id FROM #__extensions WHERE type = 'plugin' AND folder = 'system' AND element = 'koowa'");
	$result	= $db->loadResult();
	$id		= $result ? $result : 'NULL';
	
	$db->setQuery("INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `enabled`, `access`, `ordering`) VALUES ($id, 'System - Koowa', 'plugin', 'koowa', 'system', 1, 1, 0) ON DUPLICATE KEY UPDATE `enabled` = 1, `access` = 1, `ordering` = 0;");
	$db->query();
	JPluginHelper::importPlugin('system', 'koowa');
	
	$manager = JFactory::getApplication()->isAdmin() ? '<a href="' . JRoute::_('index.php?option=com_plugins&view=plugin&client=site&task=edit&cid[]=' . $db->insertid()) . '">'.JText::_('COM_NINJA_EDIT_LAQUO;SYSTEM_-_KOOWARAQUO;_IN_THE_PLUGIN_MANAGER').'</a>' : null;
	$msg = JText::_('COM_NINJA_KOOWA_SYSTEM_PLUGIN_ACTIVATED') . $manager;
	if($user->authorize( 'com_plugins', 'manage' )) JFactory::getApplication()->enqueueMessage($msg);
}
else
{
	//Only people able to fix the problem should be notified of the cause
	$message	= JText::_('COM_NINJA_NOOKU_PLUGIN_DOES_NOT_EXIST');
	$message	= sprintf($message, 'System - Koowa ', $extension_name);
	$condition	= $user->authorize('com_installer', 'installer');
	return $notify($condition, $message);
}