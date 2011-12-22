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
	$message	= JText::_('The «%s» plugin does not exist, which is a vital part of the Ninja Framework used by NinjaForge extensions. %2$s installs %1$s automatically, so this should never happen. Please post in our %2$s forums so we can help you out immediately.');
	$message	= sprintf($message, $plugin_name, $extension_name);
	$condition	= $user->authorize('com_installer', 'installer');
	return $notify($condition, $message);
}