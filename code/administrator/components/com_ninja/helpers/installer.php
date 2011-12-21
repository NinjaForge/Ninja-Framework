<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: installer.php 1399 2011-11-01 14:22:48Z stian $
 * @package		Ninja
 * @copyright	Copyright (C) 2011 NinjaForge. All rights reserved.
 * @license 	GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */
 
 /**
 * Installer cleanup script
 *
 * @author		Stian Didriksen <stian@ninjaforge.com>
 */
class NinjaHelperInstaller extends KObject
{	
	public function __construct(KConfig $options)
	{
		parent::__construct($options);
		
		$identifier = $options->identifier;
		$type		= $identifier->type;
		$package	= $identifier->package;
		
		$admin = JPATH_ADMINISTRATOR.'/components/'.$type.'_'.$package;
		$site  = JPATH_ROOT.'/components/'.$type.'_'.$package;
		$media = JPATH_ROOT.'/media/'.$type.'_'.$package;
		$xmls  = JFolder::files(JPATH_ADMINISTRATOR.'/components/'.$type.'_'.$package, '.xml$', 0, true);

		foreach($xmls as $manifest)
		{
			$xml = simplexml_load_file($manifest);
			if(isset($xml['type'])) break;
		}
		if(empty($xml)) return;
		
		if(!$xml->deleted) return;
		
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');
		
		if($xml->deleted->admin)
		{
			foreach($xml->deleted->admin->children() as $name => $item)
			{
				if($name == 'folder' && JFolder::exists($admin.'/'.$item)) JFolder::delete($admin.'/'.$item);
				if($name == 'file' && JFile::exists($admin.'/'.$item)) JFile::delete($admin.'/'.$item);
			}
		}
		
		if($xml->deleted->site)
		{
			if($xml->deleted->site['removed'] && JFolder::exists($site)) JFolder::delete($site);
	
			foreach($xml->deleted->site->children() as $name => $item)
			{
				if($name == 'folder' && JFolder::exists($site.'/'.$item)) JFolder::delete($site.'/'.$item);
				if($name == 'file' && JFile::exists($site.'/'.$item)) JFile::delete($site.'/'.$item);
			}
		}
		
		if($xml->deleted->media)
		{
			foreach($xml->deleted->media->children() as $name => $item)
			{
				if($name == 'folder' && JFolder::exists($media.'/'.$item)) JFolder::delete($media.'/'.$item);
				if($name == 'file' && JFile::exists($media.'/'.$item)) JFile::delete($media.'/'.$item);
			}
		}
	}
}