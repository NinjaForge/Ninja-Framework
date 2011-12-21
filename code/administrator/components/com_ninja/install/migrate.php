<?php
// $Id: migrate.php 387 2010-07-17 20:25:16Z stian $

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.installer.helper');
jimport('joomla.filesystem.file');

$db = JFactory::getDBO();
$xml = false;

$manifests  = JFolder::files(JPATH_ADMINISTRATOR.'/components/'.$extname, '.xml$', 0, true);
foreach($manifests as $manifest)
{
	$try = simplexml_load_file($manifest);
	if(!isset($try['type'])) continue;
	$xml = $try;
	break;
}

if(!$xml || !isset($xml->migrate)) return;

foreach($xml->migrate->tables->children() as $table)
{
	$query = 'ALTER IGNORE TABLE `#__'.$table.'` RENAME TO `#__'.$table.'_backups`';
	$db->execute($query);	
}

if(!$buffer = file_get_contents(dirname(__FILE__).'/install.sql')) return false;


if(!$queries = JInstallerHelper::splitSql($buffer)) return false;

foreach ($queries as $query)
{
	$query = trim($query);
	if ($query != '' && $query{0} != '#') {
		$db->setQuery($query);
		if (!$db->query()) {
			JError::raiseWarning(1, JText::_('SQL Error')." ".$db->stderr(true));
			return false;
		}
	}
}