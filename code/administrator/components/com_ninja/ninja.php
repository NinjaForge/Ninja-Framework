<?php 
/**
 * @version		$Id: ninja.php 1167 2011-08-06 14:45:57Z stian $
 * @category	Ninja
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.filesystem.file');

$db				= JFactory::getDBO();
$user			= JFactory::getUser();

$extension_name	= JRequest::getCmd('option');


$root	= JPATH_ROOT.'/administrator/components/com_ninja/';
$ver	= new JVersion;
$ver	= $ver->getShortVersion();
$ver	= explode('.', $ver);
$ver	= $ver[0].'.'.$ver[1];


$public = "'".addslashes(JText::_('Whoops, something went terribly wrong!'))."'";
$notify = create_function('$condition, $confidential, $public = '.$public, '
	if($condition) {
		JError::raiseWarning(500, $confidential);
	} else {
		JError::raiseWarning(500, $public);
	}
');


//Batch of system critical checks that can only be fixed on the server.
if(!version_compare($db->getVersion(), '5.0.41', '>=')) {
	$message	= JText::_('%s does not support MySQL server %s. The minimum requirement is MySQL server 5.0.41 or later.');
	$message	= sprintf($message, $extension_name, $db->getVersion());
	$condition	= $user->authorize( 'com_config', 'manage' );
	return $notify($condition, $message);
}
if(!version_compare(phpversion(), '5.2', '>=')) {
	$message	= JText::_('%s does not support PHP %s. The minimum requirement is PHP 5.2 or later.');
	$message	= sprintf($message, $extension_name, phpversion());
	$condition	= $user->authorize( 'com_config', 'manage' );
	return $notify($condition, $message);
}
if(!class_exists('mysqli')) {
	$message	= JText::_('%s needs the MySQLi (MySQL improved) PHP extension enabled in order to connect with your MySQL database server. MySQLi gives access to security and performance features in MySQL server 4.1 and higher.');
	$message	= sprintf($message, $extension_name);
	$condition	= $user->authorize( 'com_config', 'manage' );
	return $notify($condition, $message);
}

if(version_compare('5.3', phpversion(), '<=') && extension_loaded('ionCube Loader')) {

	if(ioncube_loader_iversion() < 40002) {
		$message	= JText::_('Your server is affected by a bug in ionCube Loader for PHP 5.3 that causes our template layout parsing to fail. Please update to a version later than ionCube Loader 4.0 (your server is %s) before using %s.');
		$message	= sprintf($message, ioncube_loader_version(), $extension_name);
		$condition	= $user->authorize( 'com_config', 'manage' );
		//Don't return this one, in case the site still works with ionCube loader present
		$notify($condition, $message);
	}
}


// Check if Koowa is active
if(JFactory::getApplication()->getCfg('dbtype') != 'mysqli')
{
		$conf = JFactory::getConfig();
		$path = JPATH_CONFIGURATION.DS.'configuration.php';
		if(JFile::exists($path)) {
			JPath::setPermissions($path, '0644');
			$search  = JFile::read($path);
			$replace = str_replace('var $dbtype = \'mysql\';', 'var $dbtype = \'mysqli\';', $search);
			JFile::write($path, $replace);
			JPath::setPermissions($path, '0444');
		}
		$uri = clone JFactory::getURI();
		$msg = $user->authorize('com_config', 'manage') ? JText::_('Database configuration setting changed to \'mysqli\'.') : false;
		
		return JFactory::getApplication()->redirect($uri->toString(), $msg);
}

if(!JPluginHelper::isEnabled('system', 'koowa') || JFactory::getApplication()->get('wrong_koowa_plugin_order'))
{
	require $root.'setup/koowa.'.$ver.'.php';
}

if(!JPluginHelper::isEnabled('system', 'ninja') || JFactory::getApplication()->get('wrong_koowa_plugin_order'))
{
	require $root.'setup/ninja.'.$ver.'.php';
}


// date.timezone fix to avoid errors in date helpers
if(version_compare('5.3', phpversion(), '<='))
{
    // If NULL, then that means ini_get is a disabled function, not necessarily that date.timezone is undefined
    if(ini_get('date.timezone') !== NULL && !ini_get('date.timezone'))
    {
        //Using @ to silence any PHP warnings complaining about date.timezone not being defined in the ini file
        @date_default_timezone_set(@date_default_timezone_get());
    }
}


// Add Ninja template filters and some legacy
if(defined('KOOWA'))
{
	//@TODO get rid of this legacy mapping	
	KFactory::map('lib.koowa.document', 'lib.joomla.document');

	$rules = array(
		KFactory::get('admin::com.ninja.template.filter.document')
	);

	KFactory::get('lib.koowa.template.default')->addFilters($rules);
}

$cache = JPATH_ROOT.'/cache/'.$extension_name.'/upgrade';
if(!JFile::exists($cache))
{
	//Run extension specific upgrade procedure if found
	$upgrade = JPATH_COMPONENT_ADMINISTRATOR.'/install/upgrade.php';
	if(JFile::exists($upgrade)) require_once $upgrade;
	//1.6 bugfix
	$buffer = false;
	JFile::write($cache, $buffer);
}