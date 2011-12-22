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
	
	$db->setQuery("INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `enabled`) VALUES ($id, 'System - Koowa', 'plugin', 'koowa', 'system', 1) ON DUPLICATE KEY UPDATE `enabled` = 1;");
	$db->query();
	JPluginHelper::importPlugin('system', 'koowa');
	
	$manager = JFactory::getApplication()->isAdmin() ? '<a href="' . JRoute::_('index.php?option=com_plugins&view=plugin&client=site&task=edit&cid[]=' . $db->insertid()) . '">'.JText::_("Edit &laquo;System - Koowa&raquo; in the Plugin Manager.").'</a>' : null;
	$msg = JText::_("Koowa System Plugin activated. ") . $manager;
	if($user->authorize( 'com_plugins', 'manage' )) JFactory::getApplication()->enqueueMessage($msg);
}
else
{
	//Only people able to fix the problem should be notified of the cause
	$message	= JText::_('The «%s» plugin does not exist, which is responsible for loading the Nooku Framework. %2$s installs Nooku Framework automatically, so this should never happen. Please post in our %2$s forums so we can help you out immediately.');
	$message	= sprintf($message, 'System - Koowa ', $extension_name);
	$condition	= $user->authorize('com_installer', 'installer');
	return $notify($condition, $message);
}