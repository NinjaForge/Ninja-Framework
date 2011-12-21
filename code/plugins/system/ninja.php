<?php
/**
* @version		$Id: ninja.php 1161 2011-08-01 19:23:45Z stian $
* @category		Napi
* @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
* @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
* @link     	http://ninjaforge.com
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.plugin.plugin' );

class plgSystemNinja extends JPlugin
{
	/**
	 * The location of the mootools library
	 *
	 * @var string|boolean
	 */
	public $_mootools = false;

	public function __construct($subject, $config)
	{
		parent::__construct($subject, $config);
		
		//If com_ninja don't exist, abort execution to prevent errors
		if(!is_dir(JPATH_ADMINISTRATOR . '/components/com_ninja')) return;

		//If the diagnose failed, FFS don't do anything!
		if(!$this->_diagnose()) return;
		
		//@TODO this is legacy, update your code to use the right identifiers
        KFactory::map('lib.koowa.application', 'lib.joomla.application');
        KFactory::map('lib.koowa.language'   , 'lib.joomla.language');
        KFactory::map('lib.koowa.document'   , 'lib.joomla.document');
        KFactory::map('lib.koowa.user'       , 'lib.joomla.user');
        KFactory::map('lib.koowa.editor'     , 'lib.joomla.editor');
        KFactory::map('lib.koowa.database'	 , 'lib.koowa.database.adapter.mysqli');


		/**
		 * Safety Extender compatability
		 *
		 * @TODO suggest patch for Nooku Framework's plgSystemKoowa after testing
		 *
		 * @author Margus Kaidja <abi@zone.ee>
		 */
		if (extension_loaded("safeex") &&
			strpos("tmpl", ini_get ("safeex.url_include_proto_whitelist")) === false) {
				$s = ini_get("safeex.url_include_proto_whitelist");
				ini_set("safeex.url_include_proto_whitelist", (strlen($s) ? $s . ',' : '') . 'tmpl');
		 }

		
		//Override JModuleHelper if j!1.6.x
		if(JVersion::isCompatible('1.6.0'))
		{
			$override = JPATH_ADMINISTRATOR.'/components/com_ninja/overrides/modulehelper.php';
			if(file_exists($override)) require_once $override;
		}
		
		$napiElement = JPATH_ADMINISTRATOR . '/components/com_ninja/elements/napi.php';
		if(!class_exists('JElementNapi', false) && file_exists($napiElement)) require_once $napiElement;
		
		$app = JFactory::getApplication();
		if($app->isSite()) return;
		
		if(!is_dir(JPATH_ADMINISTRATOR.'/components/com_ninja')) return;
		
		$path = KFactory::get('admin::com.ninja.helper.application')->getPath('com_xml');
		if(!$path) return;
		
		$xml  = simplexml_load_file($path);
		if(!class_exists('KRequest')) return;
		$name = str_replace('com_', '', KRequest::get('get.option', 'cmd'));
		if(!$xml['mootools']) return;
		if(version_compare($xml['mootools'], '1.2', '<')) return;
		
		if(JVersion::isCompatible('1.6.0'))
		{
			KFactory::get('admin::com.ninja.helper.default')->js('/mootools12.js');
			KFactory::get('admin::com.ninja.helper.default')->js('/moocompat.js');
		}
		else
		{
			JHTML::addIncludePath(JPATH_ROOT.'/administrator/components/com_ninja/html');
		
			//Loading Mootools 1.2
			if(class_exists('JHTMLBehavior')) return;
			JHTML::_('behavior.framework');
		}
	}
	
	/**
	 * Performs system checks and see if it's possible to run Ninja software
	 *
	 * @author	Stian Didriksen <stian@ninjaforge.com>
	 * @return	boolean
	 */
	private function _diagnose()
	{
		//Koowa currently defines the KOOWA var even if the site does not run in mysqli
		if(!defined('KOOWA') || JFactory::getApplication()->getCfg('dbtype') != 'mysqli')
		{
			jimport('joomla.filesystem.file');
			$file_exists	= JFile::exists(JPATH_PLUGINS.'/system/koowa.php');
			$mysqli_exists	= class_exists('mysqli');
			$min_php		= version_compare(phpversion(), '5.2', '>=');
			if($file_exists && $mysqli_exists && $min_php)
			{
			    JFactory::getApplication()->set('wrong_koowa_plugin_order', true);
				
				return false;
			}
			
			return false;
		}
		
		return true;
	}
	
	public function onAfterRender()
	{
		//If com_ninja don't exist, abort execution to prevent errors
		if(!is_dir(JPATH_ADMINISTRATOR . '/components/com_ninja')) return;
		
		//If the diagnose failed, FFS don't do anything!
		if(!$this->_diagnose()) return;

		$config		= KFactory::get('lib.joomla.config');
		$debug		= $config->getValue('config.debug');
		$document	= JFactory::getDocument();
		$framework	= KFactory::get('admin::com.ninja.helper.default')->framework();
		
		if($framework == 'mootools12')
		{
			if(JVersion::isCompatible('1.6.0'))
			{
				$core = KRequest::root().'/media/system/js/mootools-core.js';
				$more = KRequest::root().'/media/system/js/mootools-more.js';
				
				//Dramatic workaround code here, should be optimized
				$search[] = '  <script type="text/javascript" src="'.$core.'"></script>'."\n";
				$search[] = '  <script type="text/javascript" src="'.$more.'"></script>'."\n";
				$body	= str_replace($search, false, JResponse::getBody());

				JResponse::setBody($body);
			}
			else
			{
				$script = $debug ? '-uncompressed.js' : '.js';
				unset($document->_scripts[KRequest::root().'/media/system/js/mootools'.$script]);
			}
			
			return;
		}
	}
}