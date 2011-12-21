<?php
/**
 * @version		$Id: install.php 933 2011-03-24 00:09:14Z stian $
 * @category	Napi
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * Napi Template Controller
 *
 * @package Napi
 */
class ComNinjaControllerInstall extends ComNinjaControllerView
{

	/**
	 * Constructor
	 *
	 * @param array An optional associative array of configuration settings.
	 */
	public function __construct(KConfig $options)
	{
		parent::__construct($options);
		//Set template path
		if(empty($options->extension) && empty($options->group))
		{
			$options->extension = $this->_identifier->package;
			$options->group = KInflector::pluralize($this->_identifier->name);
		} 
			$this->_basepath = KInflector::pluralize($options->extension)=='modules' ? JPATH_SITE.DS.'modules'.DS.'mod_'.$options->group.DS.'tmpl' : JPATH_SITE.DS.'components'.DS.'com_'.$options->extension.DS.KInflector::pluralize($options->group);
		
		$this->installer = !empty($options->installer) ? $options->installer : null;
	}
	
	/**
	 * Display a single item
	 *
	 * @param	KCommandContext	A command context object
	 * @return 	KDatabaseRow	A row object containing the selected row
	 */
	protected function _actionRead(KCommandContext $context)
	{
	    return $this->getModel()->getItem();
	}
	
	/**
	 * Display the view
	 *
	 * @return void
	 */
	protected function _actionDisplay(KCommandContext $context)
	{
		//Check if we are reading or browsing
		$action = KInflector::isSingular($this->getView()->getName()) ? 'read' : 'browse';
		
		//Execute the action
		$this->execute($action, $context);
	
		$view = $this->getView();

		if(!$view instanceof ComNinjaViewHtml && $view instanceof KViewTemplate) {
			$view->getTemplate()->addFilters(array(KFactory::get('admin::com.ninja.template.filter.document')));
		}
		
		$view->setLayout($this->_request->layout);

		return $view->display();
	}
	
	/**
	 * Custom install method
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	protected function _actionInstall()
	{
		$manifest =& $this->installer->getManifest();
		$root =& $manifest->document;
		// Get the client application target
		if ($cname = $root->attributes('client')) {
			// Attempt to map the client to a base path
			jimport('joomla.application.helper');
			$client =& JApplicationHelper::getClientInfo($cname, true);
			if ($client === false) {
				$this->extension->abort(KInflector::singularize($this->_identifier->name).' '.JText::_('Install').': '.JText::_('Unknown client type').' ['.$cname.']');
				return false;
			}
			$clientId = $client->id;
		} else {
			// No client attribute was found so we assume the site as the client
			$cname = 'site';
			$clientId = 0;
		}

		// Set the extensions name
		$name =& $root->getElementByPath('name');
		$name = $name->attributes('file') ? $name->attributes('file') : $name->data();
		$name = JFilterInput::clean($name, 'cmd');
		$this->set('tplname', $name);

		// Set the template root path
		$this->installer->setPath('extension_root', $this->_basepath.DS.strtolower(str_replace(" ", "_", $this->get('tplname'))));

		/*
		 * If the template directory already exists, then we will assume that the template is already
		 * installed or another template is using that directory.
		 */
		if (file_exists($this->installer->getPath('extension_root')) && !$this->installer->getOverwrite()) {
			JError::raiseWarning(100, JText::_('Template').' '.JText::_('Install').': '.JText::_('Another template is already using directory').': "'.$this->installer->getPath('extension_root').'"');
			return false;
		}

		// If the template directory does not exist, lets create it
		$created = false;
		if (!file_exists($this->installer->getPath('extension_root'))) {
			if (!$created = JFolder::create($this->installer->getPath('extension_root'))) {
				$this->installer->abort(JText::_('Template').' '.JText::_('Install').': '.JText::_('Failed to create directory').' "'.$this->installer->getPath('extension_root').'"');
				return false;
			}
		}

		// If we created the template directory and will want to remove it if we have to roll back
		// the installation, lets add it to the installation step stack
		if ($created) {
			$this->installer->pushStep(array ('type' => 'folder', 'path' => $this->installer->getPath('extension_root')));
		}

		// Copy all the necessary files
		if ($this->installer->parseFiles($root->getElementByPath('files'), -1) === false) {
			// Install failed, rollback changes
			$this->installer->abort();
			return false;
		}
		if ($this->installer->parseFiles($root->getElementByPath('images'), -1) === false) {
			// Install failed, rollback changes
			$this->installer->abort();
			return false;
		}
		if ($this->installer->parseFiles($root->getElementByPath('css'), -1) === false) {
			// Install failed, rollback changes
			$this->installer->abort();
			return false;
		}

		// Parse optional tags
		$this->installer->parseFiles($root->getElementByPath('media'), $clientId);
		$this->installer->parseLanguages($root->getElementByPath('languages'));
		$this->installer->parseLanguages($root->getElementByPath('administration/languages'), 1);

		// Get the template description
		$description = & $root->getElementByPath('description');
		if (is_a($description, 'JSimpleXMLElement')) {
			$this->installer->set('message', $description->data());
		} else {
			$this->installer->set('message', '' );
		}

		// Lastly, we will copy the manifest file to its appropriate place.
		if (!$this->installer->copyManifest(-1)) {
			// Install failed, rollback changes
			$this->installer->abort(JText::_('Template').' '.JText::_('Install').': '.JText::_('Could not copy setup file'));
			return false;
		}

		// Load template language file
		$lang =& JFactory::getLanguage();
		$lang->load('tpl_'.$this->_identifier->type.'_'.$this->_identifier->package.'_'.$name);
		
		return true;
	}

	/**
	 * Custom uninstall method
	 *
	 * @access	public
	 * @param	int		$path		The template name
	 * @param	int		$clientId	The id of the client
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function _actionUninstall()
	{
		$ids = (array) KRequest::get('post.id', 'string');

		// For a template the id will be the template name which represents the subfolder of the templates folder that the template resides in.
		if (empty($ids)) {
			JError::raiseWarning(100, KInflector::singularize($this->_identifier->name).' '.JText::_('Uninstall').': '.JText::_(KInflector::singularize($this->_identifier->name).' id is empty, cannot uninstall files'));
			return false;
		}


		foreach ($ids as $id)
		{
			$this->set('extension_root', $this->_basepath.DS.$id);
			$this->set('source', $this->get('extension_root'));
	
			$manifest =& $this->_findManifest();
			if (!is_a($manifest, 'JSimpleXML')) {
				// Make sure we delete the folders
				JFolder::delete($this->installer->getPath('extension_root'));
				JError::raiseWarning(100, KInflector::singularize($this->_identifier->name).' '.JTEXT::_('Uninstall').': '.JTEXT::_('Package manifest file invalid or not found'));
				return false;
			}
			$root =& $manifest->document;
	
			// Remove files
			Napi::import('lib.joomla.installer.installer');
			JInstaller::removeFiles($root->getElementByPath('media'), 0);
			JInstaller::removeFiles($root->getElementByPath('languages'));
			JInstaller::removeFiles($root->getElementByPath('administration/languages'), 1);
	
			// Delete the template directory
			if (JFolder::exists($this->get('extension_root'))) {
				JFolder::delete($this->get('extension_root'));
			} else {
				JError::raiseWarning(100, KInflector::singularize($this->_identifier->name).' '.JText::_('Uninstall').': '.JText::_('Directory does not exist, cannot remove files'));
				return false;
			}
		}
		$text = count($ids)>1 ? KInflector::pluralize($this->_identifier->name).' successfully removed' : KInflector::singularize($this->_identifier->name).' successfully removed';
		$this->setRedirect(
			'view='.KInflector::pluralize($this->_identifier->name)
			.'&format='.KRequest::get('get.format', 'cmd', 'html'),
			JText::_($text)
		);
		
		return true;
	}
	
	/**
	 * Tries to find the package manifest file
	 *
	 * @access private
	 * @return boolean True on success, False on error
	 * @since 1.0
	 */
	function _findManifest()
	{
		// Get an array of all the xml files from teh installation directory
		$xmlfiles = JFolder::files($this->get('source'), '.xml$', 1, true);
		// If at least one xml file exists
		if (!empty($xmlfiles)) {
			foreach ($xmlfiles as $file)
			{
				// Is it a valid joomla installation manifest file?
				$manifest = $this->_isManifest($file);
				if (!is_null($manifest)) {

					// If the root method attribute is set to upgrade, allow file overwrite
					$root =& $manifest->document;
					if ($root->attributes('method') == 'upgrade') {
						$this->_overwrite = true;
					}

					// Set the manifest object and path
					$this->set('manifest', $file);

					// Set the installation source path to that of the manifest file
					$this->set('source', dirname($file));
					return $manifest;
				}
			}

			// None of the xml files found were valid install files
			JError::raiseWarning(1, 'JInstaller::install: '.JText::_('ERRORNOTFINDJOOMLAXMLSETUPFILE'));
			return false;
		} else {
			// No xml files were found in the install folder
			JError::raiseWarning(1, 'JInstaller::install: '.JText::_('ERRORXMLSETUP'));
			return false;
		}
	}
	
	/**
	 * Is the xml file a valid Joomla installation manifest file
	 *
	 * @access	private
	 * @param	string	$file	An xmlfile path to check
	 * @return	mixed	A JSimpleXML document, or null if the file failed to parse
	 * @since	1.5
	 */
	function &_isManifest($file)
	{
		// Initialize variables
		$null	= null;
		$xml	=& JFactory::getXMLParser('Simple');

		// If we cannot load the xml file return null
		if (!$xml->loadFile($file)) {
			// Free up xml parser memory and return null
			unset ($xml);
			return $null;
		}

		/*
		 * Check for a valid XML root tag.
		 * @todo: Remove backwards Compatability in a future version
		 * Should be 'install', but for backward Compatability we will accept 'mosinstall'.
		 */
		$root =& $xml->document;
		if (!is_object($root) || ($root->name() != 'install' && $root->name() != 'mosinstall')) {
			// Free up xml parser memory and return null
			unset ($xml);
			return $null;
		}

		// Valid manifest file return the object
		return $xml;
	}
}