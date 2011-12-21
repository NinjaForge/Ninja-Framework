<?php
// $Id: helper.php 551 2010-10-27 04:41:53Z daniel $

// no direct access
defined('_JEXEC') or die('Restricted access');

class NinjaInstallHelper extends JObject
{

	public $newer = array();
	
	public $exists = false;
	
	public function __construct($options = array())
	{
		$this->setProperties($options);
		
		
		$this->findManifests($this->parent->getPath('source'));
		
		$this->filterManifests($this->parent->getPath('source'));
		
		if($this->exists) $this->compareManifests();
		
		$this->install();
	}
	
	public function install()
	{
		foreach ($this->manifests as $i => $manifest)
		{
			$installer = new JInstaller;
			$installer->setPath('source', $this->parent->getPath('source'));
			//This is essentially what method="upgrade" does
			$installer->_overwrite = true;
			
			$xml	=& JFactory::getXMLParser('Simple');
			$xml->loadFile($manifest);
			$installer->_manifest = $xml;
			$installer->setPath('manifest', $manifest);
			$root = $xml->document;
			$type = $root->attributes('type');
			
			//Don't install if the type isn't defined
			if(!$type) {
				unset($this->manifests[$i]);
				continue;
			}

			// Lazy load the adapter
			if (!isset($installer->_adapters[$type]) || !is_object($installer->_adapters[$type])) {
				$installer->setAdapter($type);
			}
			$installer->_adapters[$type]->install();
			$installer->install($this->parent->getPath('source'));
		}
		
		if(JFolder::exists($this->parent->getPath('source').'/nooku'))
		{
			$this->manifests[] = $manifest = $this->parent->getPath('source').'/nooku/manifest.xml';
			$installer = new JInstaller;
			$installer->setPath('source', $this->parent->getPath('source').'/nooku');
			//This is essentially what method="upgrade" does
			$installer->_overwrite = true;
			
			$xml	=& JFactory::getXMLParser('Simple');
			$xml->loadFile($manifest);
			$installer->_manifest = $xml;
			$installer->setPath('manifest', $manifest);
			$root = $xml->document;
			$type = $root->attributes('type');
			// Lazy load the adapter
			if (!isset($installer->_adapters[$type]) || !is_object($installer->_adapters[$type])) {
				$installer->setAdapter($type);
			}
			$installer->_adapters[$type]->install();
			$installer->install($this->parent->getPath('source'));
		}
		return $this;
	}
	
	public function compareManifests()
	{
		foreach ($this->exists as $name => $manifest)
		{
			if (version_compare(simplexml_load_file($manifest)->version, simplexml_load_file($this->manifests[$name])->version, '>')) unset($this->manifests[$name]);
			//elseif (filemtime($manifest) >= filemtime($this->manifests[$name])) unset($this->manifests[$name]);
		}
		return $this;
	}
	
	public function filterManifests($source = false)
	{
		if(!$source) return false;
		
		$tmp = str_replace(JPATH_ROOT, '', $source);
		
		$exists = false;
		foreach ($this->manifests as $manifest)
		{
			$manifest = str_replace($tmp, '', $manifest);
			if(file_exists($manifest)) $exists[basename(dirname($manifest)) . '/' . basename($manifest)] = $manifest;
		}
		if($exists) $this->set('exists', $exists);
		return $this;
	}
	
	public function findManifests($source = false)
	{
		if(!$source) return false;
		$manifests = array();
		$admin = JFolder::files($source . '/administrator', '.xml$', 2, true, array('language'));
		$site  = JFolder::folders($this->parent->getPath('source'), '.', false, true, array('administrator', 'components', 'language', 'media', 'nooku'));
		foreach ($site as $type)
		{
			foreach (array_merge(JFolder::files($type, '.xml$', 2, true), $admin) as $manifest)
			{
				$manifests[basename(dirname($manifest)) . '/' . basename($manifest)] = $manifest;
			}
		}
		$this->set('manifests', $manifests);
		return $this->manifests;
	}
}