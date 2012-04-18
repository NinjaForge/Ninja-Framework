<?php 
/**
 * @category	Ninja
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

if(JFile::exists(JPATH_PLUGINS.'/system/koowa.php'))
{
    //Remember me plugin ordering fix
    if(JPluginHelper::isEnabled('system', 'remember'))
    {
        $db->setQuery("UPDATE #__plugins SET ordering = -2 WHERE folder = 'system' AND element = 'remember' AND ordering > -2");
        $db->query();
    }

	$db->setQuery("SELECT id FROM #__plugins WHERE folder = 'system' AND element = 'koowa'");
	$result	= $db->loadResult();
	$id		= $result ? $result : 'NULL';
	
	$db->execute("INSERT INTO `#__plugins` (`id`, `name`, `element`, `folder`, `published`, `ordering`) VALUES ($id, 'System - Koowa', 'koowa', 'system', 1, -1) ON DUPLICATE KEY UPDATE `published` = 1, `ordering` = -1;");
	
	$manager = JFactory::getApplication()->isAdmin() ? '<a href="' . JRoute::_('index.php?option=com_plugins&view=plugin&client=site&task=edit&cid[]=' . $db->insertid()) . '">'.JText::_('COM_NINJA_EDIT_LAQUO;SYSTEM_-_KOOWARAQUO;_IN_THE_PLUGIN_MANAGER').'</a>' : null;
	$msg = JText::_('COM_NINJA_KOOWA_SYSTEM_PLUGIN_ACTIVATED') . $manager;
	if($user->authorize( 'com_plugins', 'manage' )) JFactory::getApplication()->enqueueMessage($msg);
	$uri = clone JURI::getInstance();
	JFactory::getApplication()->redirect($uri->toString());
}
else
{
	//Only people able to fix the problem should be notified of the cause
	$message	= JText::_('COM_NINJA_THE_%S_PLUGIN_DOES_NOT_EXIST_WHICH_IS_RESPONSIBLE_FOR_LOADING_THE_NOOKU_FRAMEWORK_%2$S_INSTALLS_NOOKU_FRAMEWORK_AUTOMATICALLY_SO_THIS_SHOULD_NEVER_HAPPEN_PLEASE_POST_IN_OUR_%2$S_FORUMS_SO_WE_CAN_HELP_YOU_OUT_IMMEDIATELY');
	$message	= sprintf($message, 'System - Koowa ', $extension_name);
	$condition	= $user->authorize('com_installer', 'installer');
	return $notify($condition, $message);
}