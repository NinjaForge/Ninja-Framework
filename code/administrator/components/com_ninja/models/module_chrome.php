<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: module_chrome.php 1399 2011-11-01 14:22:48Z stian $
 * @package		Koowa
 * @copyright	Copyright (C) 2010 Nooku. All rights reserved.
 * @license 	GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://www.nooku.org
 */

class NinjaModelModule_chrome extends KModelAbstract
{	
	/**
	 * Constructor
	 *
	 * @param	array An optional associative array of configuration settings.
	 */
	public function __construct(KConfig $options)
	{
		parent::__construct($options);
		
		jimport('joomla.filesystem.file');
		
		//$attr = array_diff_key($node->attributes(), array_fill_keys(array('name', 'type', 'default', 'get', 'label', 'description'), null) );

		$this->_state
					->insert('client', 'boolean', 0)
					->insert('optgroup', 'string', true)
					->insert('incpath', 'boolean', 0)
					->insert('limit', 'int', 0);
				
//		$this->_list = array();
//		$this->_total = count($this->_list);
	}
	
	public function getList()
	{
		$root = $this->_state->client ? JPATH_ADMINISTRATOR : JPATH_SITE;
		$path = $root.'/templates/';
		
		foreach(JFolder::folders($path) as $template)
		{
			$chromes = $this->_searchTemplate($template, $path);
			if(!$chromes) continue;
			$this->_list[] = (object) array('id' => !$this->_state->optgroup, 'title' => $template);
			foreach($chromes as $chrome)
			{
				$prefix = $this->_state->incpath ? $template.'/' : null;
				$this->_list[] = (object) array('id' => $prefix.$chrome, 'title' => $chrome);
			}
		}
		
		return $this->_list;
	}
	
	protected function _searchTemplate($template, $path)
	{
		//$fileData  = JFile::read(JPATH_ROOT.DS.'templates'.DS.'system'.DS.'html'.DS.'modules.php', false, 0, filesize(JPATH_ROOT.DS.'templates'.DS.$template->template.DS.'html'.DS.'modules.php'));
		if(!file_exists($path.$template.DS.'html'.DS.'modules.php')) return array();
		$fileData = JFile::read($path.$template.DS.'html'.DS.'modules.php', false, 0, filesize($path.$template.DS.'html'.DS.'modules.php'));
		
		preg_match_all("/function(.)modChrome_(.*?)\(/", $fileData, $matches);
	
		return $matches['2'];
	}
}