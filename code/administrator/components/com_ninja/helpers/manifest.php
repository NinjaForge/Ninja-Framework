<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: manifest.php 897 2011-02-23 14:09:23Z betweenbrain $
 * @package		Ninja
 * @copyright	Copyright (C) 2011 NinjaForge. All rights reserved.
 * @license 	GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */
 jimport('joomla.filesystem.file');
 /**
 * Helper for rendering changelogs, instructions, links, footer, etc.
 *
 * @author		Stian Didriksen <stian@ninjaforge.com>
 * @category	Koowa
 * @package		Koowa_Template
 * @subpackage	Helper
 * @uses		KArrayHelper
 */
class ComNinjaHelperManifest extends KTemplateHelperAbstract
{

	/**
	 * If the extension has a changelog.xml, simplexml_import_file it here
	 *
	 * @var bool|SimpleXML
	 */
	protected $_changelog = false;
	
	/**
	 * Constructor
	 *
	 * @param array Associative array of values
	 */
	public function __construct(KConfig $options)
	{
		//Create an object holding our default settings
		$defaults = array(
			'path'		=> false,
			'changelog'	=> false
		);
		
		//Override default settings
		$options->append($defaults);
				
		if(!$options->path)
		{
			$manifests  = JFolder::files(JPATH_COMPONENT_ADMINISTRATOR, '.xml$', 0, true);
			foreach($manifests as $manifest)
			{
				$xml = simplexml_load_file($manifest);
				if(isset($xml['type'])) break;
			}
			$options->path = $manifest;
		}
				
		//load the file, and save it to our object
		$this->_xml = simplexml_load_file($options->path);
		
		
		if(!$options->changelog)
		{
			$changelog = false;
			$manifests  = JFolder::files(JPATH_COMPONENT_ADMINISTRATOR, '.xml$', 0, true);
			foreach($manifests as $manifest)
			{
				$xml = simplexml_load_file($manifest);
				if($xml->getName() == 'changelogs') {
					$changelog = $xml;
					break;
				}
			}
			if($changelog) $this->_changelog = $changelog;
		}

	}
	
	/**
	 * Get the changelog from our manifest
	 *
	 */
	public function changelogs()
	{
		//Get the changelogs
		$changelogs = ($xml = @$this->_xml->changelogs) ? $xml : array();
		if($this->_changelog) $changelogs = $this->_changelog;
		
		KFactory::get('admin::com.ninja.helper.default')->css('/manifest.css');
		
		
		//Render the changelogs
		foreach ( $changelogs->children() as $name => $changelog)
		{
			if ($name == 'version')	$html[] = '<div class="changelog"><dl>';
			$html[] = '<dt><strong>' . $name . ':</strong></dt><dd>';
			if ($name == 'date')
			{
				$date = KFactory::tmp('lib.joomla.utilities.date', (array) $changelog);
				$html[] = (string)$changelog == 'TBA' ? JText::_('To Be Announced') : $date->toFormat(JText::_('DATE_FORMAT_LC'));
			}
			else 					$html[] = $changelog;
			$html[] = '</dd>';
			
			if ($states = $name == 'state') $html[] = '</dl><ul>';
			foreach ($changelog->children() as $type => $state)
			{
				//This function is working, but not currently needed.
				//It lets you add more classes to your list item based on your type="" attribute.
				//$html[] = '<li class="' . implode(' ', array_merge((array) $type, explode('.', $state['type']))) . '">';
				$title 	= $state['title'] ? ' title="' . $state['title'] . '"' : null;
				$html[] = '<li class="' . $type . '" rel="' . $type . '"' . $title . '>';
				$html[] = $state;
				$html[] = '</li>';
			}
			if ($states) 			$html[] = '</ul>';
			
			if($name == 'state')	$html[] = '</div>';			
		}
		
		return implode(PHP_EOL, $html);
	}
	
	/**
	 * Get the footer from our manifest
	 *
	 */
	public function footer($open = false)
	{
		if(KRequest::get('get.tmpl', 'cmd')=='component') return;

		$footer = ($xml = @$this->_xml->footer) ? $xml : array();
		
		//Add the stylesheet to the header
		JHTML::stylesheet('manifest.css', JURI::root() . 'media/com_ninja/css/');
		
		$html[] = '<hr class="nf-footer-ruler" />';
		if(($view = JRequest::getCmd('view'))=='dashboard') $view = 'dashboards';
		if($open || KInflector::isPlural($view)) $open = 'open';
		$html[] = '<div class="nf-footer ' . $open . '">';

		$html[] = '<div class="inner">';
		
		//Render the changelogs
		foreach ( $footer->children() as $name => $section)
		{
			$html[] = '<ul class="' . KInflector::underscore(trim($section)) . '">';
			$html[] = '<li><h4>'.JText::_(trim($section)).'</h4></li>';
			
			foreach ( $section->children() as $name => $child )
			{
				$html[] = '<li>';
				$child['title'] = JText::_($child['title']);
				$html[] = '<' . $name . ' ' . KHelperArray::toString(current((array)$child->attributes())) . '">';
				$html[] = JText::_($child);
				$html[] = '</' . $name . '>';
				$html[] = '</li>';
			}
			$html[] = '</ul>';
		}
		
		$html[] = '</div>';
		$html[] = '<a id="nf-logo" href="http://ninjaforge.com"></a>';
		$html[] = '</div>';
		
		return implode(PHP_EOL, $html);
	}
	
	/**
	* Function to output dashboard button html
	*
	* TODO: needs to be refactored to do associative arrays.
	*
	* @buttons Comma separated list of which buttons to use.
	* @return returns a string with html.
	*/
	function buttons() {     
         
         //Get the changelogs
		$buttons = ($xml = @$this->_xml->administration->submenu) ? $xml : false;
		if(!$buttons) return false;
		
		$url = new KObject;
		$url->set('option', JRequest::getCmd('option'));
		
		$html = array();
		
		//process the array of button info into buttons
		foreach( $buttons->children() as $name => $button )
		{			
			
			if($submenus = @$this->_xml->administration->submenu->{(string) $button['view']})
			{
				foreach ($submenus->children() as $name => $submenu)
				{
					$html[] = $this->_createButton($url, $name, $submenu);
				}
			} elseif($name == 'menu') $html[] = $this->_createButton($url, $name, $button);
		}
		return implode($html);
	}
	
	protected function _createButton($url, $name, $button)
	{
		$html = array();
		$var = KInflector::underscore($button);
		$href = new KObject;
		$href->set(array_merge($url->get(), array('view' => $var)));
		$attr = current( (array) $button->attributes());
		if($attr['view'] == KRequest::get('get.view', 'cmd', 'dashboard')) return false;
		$img = isset($attr['img']) ? $attr['img'] : null;
		$attr['img'] = null;
		if( !empty($attr )) $href->set($attr);
		
		$html[] = '<div><div class="dashboard-button">';
		$html[] = '<a href="' . JRoute::_('index.php?' . http_build_query($href->get())) . '"><img src="' . KFactory::get('admin::com.ninja.helper.default')->img('/48/'. JFile::stripExt(basename($img)) .'.png') . '" alt="'.$button.'"/><span>'. JText::_($button) .'</span></a>';
		$html[] = '</div></div>';
		return implode($html);
	}
}