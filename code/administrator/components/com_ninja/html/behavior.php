<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
* @version		$Id: behavior.php 635 2010-11-09 10:25:43Z stian $
* @package		Joomla.Framework
* @subpackage	HTML
* @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('JPATH_BASE') or die();
/**
 * Utility class for javascript behaviors
 *
 * @static
 * @package 	Joomla.Framework
 * @subpackage	HTML
 * @version		1.5
 */
class JHTMLBehavior
{
	/**
	 * Method to load the mootools framework and compatibility layer into the document head
	 *
	 * - If debugging mode is on an uncompressed version of mootools is included for easier debugging.
	 *
	 * @static
	 * @param	boolean	$debug	Is debugging mode on? [optional]
	 * @return	void
	 * @since	1.5
	 */
	function mootools($debug = null)
	{
		static $loaded;

		// Only load once
		if ($loaded) {
			return;
		}

		self::framework();

		KFactory::get('admin::com.ninja.helper.default')->js('/moocompat.js');
			
		$loaded = true;
		return;
	}

	/**
	 * Method to load the mootools framework into the document head
	 *
	 * - If debugging mode is on an uncompressed version of mootools is included for easier debugging.
	 *
	 * @static
	 * @param	boolean	$debug	Is debugging mode on? [optional]
	 * @return	void
	 * @since	1.5.16
	 */
	function framework($debug = null)
	{
		static $loaded;

		// Only load once
		if ($loaded) {
			return;
		}

		// If no debugging value is set, use the configuration setting
		if ($debug === null) {
			$config = &JFactory::getConfig();
			$debug = $config->getValue('config.debug');
		}

		// TODO NOTE: Here we are checking for Konqueror - If they fix thier issue with compressed, we will need to update this
		$konkcheck = isset($_SERVER['HTTP_USER_AGENT']) ? strpos(strtolower($_SERVER['HTTP_USER_AGENT']), "konqueror") : null;

		KFactory::get('admin::com.ninja.helper.default')->js('/mootools12.js');

		$loaded = true;
		return;
	}

	function caption() {
		// pass false to script so that we don't load the compatibility layer if we don't need it
		JHTMLBehavior::framework();
		KFactory::get('admin::com.ninja.helper.default')->js('/caption.js');
	}

	function formvalidation() {
		// pass false to script so that we don't load the compatibility layer if we don't need it
		JHTMLBehavior::framework();
		KFactory::get('admin::com.ninja.helper.default')->js('/validate.js');
	}

	function switcher() {
		// pass false to script so that we don't load the compatibility layer if we don't need it
		//JHTMLBehavior::framework();
		//JHTML::script('switcher.js', 'media/com_ninja/js/', false);
		KFactory::get('admin::com.ninja.helper.default')->js('/switcher.js');
	}

	function combobox() {
		// pass false to script so that we don't load the compatibility layer if we don't need it
		//JHTMLBehavior::framework();
		//JHTML::script('combobox.js', 'media/com_ninja/js/', false);
		KFactory::get('admin::com.ninja.helper.default')->js('/combobox.js');
	}

	function tooltip($selector='.hasTip', $params = array())
	{
		static $tips;

		if (!isset($tips)) {
			$tips = array();
		}

		// Include mootools framework
		JHTMLBehavior::framework();
		KFactory::get('admin::com.ninja.helper.default')->js('/tips.js');
		$sig = md5(serialize(array($selector,$params)));
		if (isset($tips[$sig]) && ($tips[$sig])) {
			return;
		}

		// Setup options object
		$opt['maxTitleChars']	= (isset($params['maxTitleChars']) && ($params['maxTitleChars'])) ? (int)$params['maxTitleChars'] : 50 ;
		// offsets needs an array in the format: array('x'=>20, 'y'=>30)
		$opt['offsets']			= (isset($params['offsets']) && (is_array($params['offsets']))) ? $params['offsets'] : null;
		$opt['showDelay']		= (isset($params['showDelay'])) ? (int)$params['showDelay'] : null;
		$opt['hideDelay']		= (isset($params['hideDelay'])) ? (int)$params['hideDelay'] : null;
		$opt['className']		= (isset($params['className'])) ? $params['className'] : null;
		$opt['fixed']			= (isset($params['fixed']) && ($params['fixed'])) ? '\\true' : '\\false';
		$opt['onShow']			= (isset($params['onShow'])) ? '\\'.$params['onShow'] : null;
		$opt['onHide']			= (isset($params['onHide'])) ? '\\'.$params['onHide'] : null;

		$options = JHTMLBehavior::_getJSObject($opt);

		// Attach tooltips to document
		$document = &JFactory::getDocument();
		$document->addScriptDeclaration("
		window.addEvent('domready', function() {
			$$('$selector').each(function(el) {
				var title = el.get('title');
				if (title) {
					var parts = title.split('::', 2);
					el.store('tip:title', parts[0]);
					el.store('tip:text', parts[1]);
				}
			});
			var JTooltips = new Tips($$('$selector'), $options);
		});");

		// Set static array
		$tips[$sig] = true;
		return;
	}

	function modal($selector='a.modal', $params = array())
	{
		static $modals;
		static $included;

		$document =& JFactory::getDocument();

		// Load the necessary files if they haven't yet been loaded
		if (!isset($included)) {

			// Load the javascript and css
			JHTMLBehavior::framework();
			KFactory::get('admin::com.ninja.helper.default')->js('/modal.js');
			KFactory::get('admin::com.ninja.helper.default')->css('/modal.css');

			$included = true;
		}

		if (!isset($modals)) {
			$modals = array();
		}

		$sig = md5(serialize(array($selector,$params)));
		if (isset($modals[$sig]) && ($modals[$sig])) {
			return;
		}

		// Setup options object
		$opt['ajaxOptions']	= (isset($params['ajaxOptions']) && (is_array($params['ajaxOptions']))) ? $params['ajaxOptions'] : null;
		$opt['size']		= (isset($params['size']) && (is_array($params['size']))) ? $params['size'] : null;
		$opt['onOpen']		= (isset($params['onOpen'])) ? $params['onOpen'] : null;
		$opt['onClose']		= (isset($params['onClose'])) ? $params['onClose'] : null;
		$opt['onUpdate']	= (isset($params['onUpdate'])) ? $params['onUpdate'] : null;
		$opt['onResize']	= (isset($params['onResize'])) ? $params['onResize'] : null;
		$opt['onMove']		= (isset($params['onMove'])) ? $params['onMove'] : null;
		$opt['onShow']		= (isset($params['onShow'])) ? $params['onShow'] : null;
		$opt['onHide']		= (isset($params['onHide'])) ? $params['onHide'] : null;

		$options = JHTMLBehavior::_getJSObject($opt);

		// Attach modal behavior to document
		$document->addScriptDeclaration("
		window.addEvent('domready', function() {

			SqueezeBox.initialize(".$options.");

			$$('".$selector."').each(function(el) {
				el.addEvent('click', function(e) {
					new Event(e).stop();
					SqueezeBox.fromElement(el);
				});
			});
		});");

		// Set static array
		$modals[$sig] = true;
		return;
	}

	function uploader($id='file-upload', $params = array())
	{
		KFactory::get('admin::com.ninja.helper.default')->js('/swf.js');
		KFactory::get('admin::com.ninja.helper.default')->js('/uploader.js');

		static $uploaders;

		if (!isset($uploaders)) {
			$uploaders = array();
		}

		if (isset($uploaders[$id]) && ($uploaders[$id])) {
			return;
		}

		// Setup options object
		$opt['url']					= (isset($params['targetURL'])) ? $params['targetURL'] : null ;
		$opt['swf']					= (isset($params['swf'])) ? $params['swf'] : JURI::root(true).'/media/system/swf/uploader.swf';
		$opt['multiple']			= (isset($params['multiple']) && !($params['multiple'])) ? '\\false' : '\\true';
		$opt['queued']				= (isset($params['queued']) && !($params['queued'])) ? '\\false' : '\\true';
		$opt['queueList']			= (isset($params['queueList'])) ? $params['queueList'] : 'upload-queue';
		$opt['instantStart']		= (isset($params['instantStart']) && ($params['instantStart'])) ? '\\true' : '\\false';
		$opt['allowDuplicates']		= (isset($params['allowDuplicates']) && !($params['allowDuplicates'])) ? '\\false' : '\\true';
		$opt['limitSize']			= (isset($params['limitSize']) && ($params['limitSize'])) ? (int)$params['limitSize'] : null;
		$opt['limitFiles']			= (isset($params['limitFiles']) && ($params['limitFiles'])) ? (int)$params['limitFiles'] : null;
		$opt['optionFxDuration']	= (isset($params['optionFxDuration'])) ? (int)$params['optionFxDuration'] : null;
		$opt['container']			= (isset($params['container'])) ? '\\$('.$params['container'].')' : '\\$(\''.$id.'\').getParent()';
		$opt['types']				= (isset($params['types'])) ?'\\'.$params['types'] : '\\{\'All Files (*.*)\': \'*.*\'}';


		// Optional functions
		$opt['createReplacement']	= (isset($params['createReplacement'])) ? '\\'.$params['createReplacement'] : null;
		$opt['onComplete']			= (isset($params['onComplete'])) ? '\\'.$params['onComplete'] : null;
		$opt['onAllComplete']		= (isset($params['onAllComplete'])) ? '\\'.$params['onAllComplete'] : null;

/*  types: Object with (description: extension) pairs, default: Images (*.jpg; *.jpeg; *.gif; *.png)
 */

		$options = JHTMLBehavior::_getJSObject($opt);

		// Attach tooltips to document
		$document =& JFactory::getDocument();
		$uploaderInit = 'sBrowseCaption=\''.JText::_('Browse Files', true).'\';
				sRemoveToolTip=\''.JText::_('Remove from queue', true).'\';
				window.addEvent(\'load\', function(){
				var Uploader = new FancyUpload($(\''.$id.'\'), '.$options.');
				$(\'upload-clear\').adopt(new Element(\'input\', { type: \'button\', events: { click: Uploader.clearList.bind(Uploader, [false])}, value: \''.JText::_('Clear Completed').'\' }));				});';
		$document->addScriptDeclaration($uploaderInit);

		// Set static array
		$uploaders[$id] = true;
		return;
	}

	function tree($id, $params = array(), $root = array())
	{
		static $trees;

		if (!isset($trees)) {
			$trees = array();
		}

		// Include mootools framework
		JHTMLBehavior::framework();
		KFactory::get('admin::com.ninja.helper.default')->js('/mootree.js');
		JHTML::stylesheet('mootree.css');

		if (isset($trees[$id]) && ($trees[$id])) {
			return;
		}

		// Setup options object
		$opt['div']		= (array_key_exists('div', $params)) ? $params['div'] : $id.'_tree';
		$opt['mode']	= (array_key_exists('mode', $params)) ? $params['mode'] : 'folders';
		$opt['grid']	= (array_key_exists('grid', $params)) ? '\\'.$params['grid'] : '\\true';
		$opt['theme']	= (array_key_exists('theme', $params)) ? $params['theme'] : JURI::root(true).'/media/system/images/mootree.gif';

		// Event handlers
		$opt['onExpand']	= (array_key_exists('onExpand', $params)) ? '\\'.$params['onExpand'] : null;
		$opt['onSelect']	= (array_key_exists('onSelect', $params)) ? '\\'.$params['onSelect'] : null;
		$opt['onClick']		= (array_key_exists('onClick', $params)) ? '\\'.$params['onClick'] : '\\function(node){  window.open(node.data.url, $chk(node.data.target) ? node.data.target : \'_self\'); }';
		$options = JHTMLBehavior::_getJSObject($opt);

		// Setup root node
		$rt['text']		= (array_key_exists('text', $root)) ? $root['text'] : 'Root';
		$rt['id']		= (array_key_exists('id', $root)) ? $root['id'] : null;
		$rt['color']	= (array_key_exists('color', $root)) ? $root['color'] : null;
		$rt['open']		= (array_key_exists('open', $root)) ? '\\'.$root['open'] : '\\true';
		$rt['icon']		= (array_key_exists('icon', $root)) ? $root['icon'] : null;
		$rt['openicon']	= (array_key_exists('openicon', $root)) ? $root['openicon'] : null;
		$rt['data']		= (array_key_exists('data', $root)) ? $root['data'] : null;
		$rootNode = JHTMLBehavior::_getJSObject($rt);

		$treeName		= (array_key_exists('treeName', $params)) ? $params['treeName'] : '';

		$js = '		window.addEvent(\'domready\', function(){
			tree'.$treeName.' = new MooTreeControl('.$options.','.$rootNode.');
			tree'.$treeName.'.adopt(\''.$id.'\');})';

		// Attach tooltips to document
		$document =& JFactory::getDocument();
		$document->addScriptDeclaration($js);

		// Set static array
		$trees[$id] = true;
		return;
	}

	function calendar()
	{
		$document =& JFactory::getDocument();
		JHTML::stylesheet('calendar-jos.css', 'media/system/css/', array(' title' => JText::_( 'green' ) ,' media' => 'all' ));
		JHTMLBehavior::framework();
		KFactory::get('admin::com.ninja.helper.default')->js('/calendar.js');
		KFactory::get('admin::com.ninja.helper.default')->js('/calendar-setup.js');

		$translation = JHTMLBehavior::_calendartranslation();
		if($translation) {
			$document->addScriptDeclaration($translation);
		}
	}

	/**
	 * Keep session alive, for example, while editing or creating an article.
	 */
	function keepalive()
	{
		// Include mootools framework
		JHTMLBehavior::framework();

		$config 	 =& JFactory::getConfig();
		$lifetime 	 = ( $config->getValue('lifetime') * 60000 );
		$refreshTime =  ( $lifetime <= 60000 ) ? 30000 : $lifetime - 60000;
		//refresh time is 1 minute less than the liftime assined in the configuration.php file

		$document =& JFactory::getDocument();
		$script  = '';
		$script .= 'function keepAlive( ) {';
		$script .=  '	var myAjax = new Ajax( "index.php", { method: "get" } ).request();';
		$script .=  '}';
		$script .= 	' window.addEvent("domready", function()';
		$script .= 	'{ keepAlive.periodical('.$refreshTime.' ); }';
		$script .=  ');';

		$document->addScriptDeclaration($script);

		return;
	}

	/**
	 * Internal method to get a JavaScript object notation string from an array
	 *
	 * @param	array	$array	The array to convert to JavaScript object notation
	 * @return	string	JavaScript object notation representation of the array
	 * @since	1.5
	 */
	function _getJSObject($array=array())
	{
		// Initialize variables
		$object = '{';

		// Iterate over array to build objects
		foreach ((array)$array as $k => $v)
		{
			if (is_null($v)) {
				continue;
			}
			if (!is_array($v) && !is_object($v)) {
				$object .= ' '.$k.': ';
				$object .= (is_numeric($v) || strpos($v, '\\') === 0) ? (is_numeric($v)) ? $v : substr($v, 1) : "'".$v."'";
				$object .= ',';
			} else {
				$object .= ' '.$k.': '.JHTMLBehavior::_getJSObject($v).',';
			}
		}
		if (substr($object, -1) == ',') {
			$object = substr($object, 0, -1);
		}
		$object .= '}';

		return $object;
	}

	/**
	 * Internal method to translate the JavaScript Calendar
	 *
	 * @return	string	JavaScript that translates the object
	 * @since	1.5
	 */
	function _calendartranslation()
	{
		static $jsscript = 0;

		if($jsscript == 0)
		{
			$return = 'Calendar._DN = new Array ("'.JText::_('Sunday').'", "'.JText::_('Monday').'", "'.JText::_('Tuesday').'", "'.JText::_('Wednesday').'", "'.JText::_('Thursday').'", "'.JText::_('Friday').'", "'.JText::_('Saturday').'", "'.JText::_('Sunday').'");Calendar._SDN = new Array ("'.JText::_('Sun').'", "'.JText::_('Mon').'", "'.JText::_('Tue').'", "'.JText::_('Wed').'", "'.JText::_('Thu').'", "'.JText::_('Fri').'", "'.JText::_('Sat').'", "'.JText::_('Sun').'"); Calendar._FD = 0;	Calendar._MN = new Array ("'.JText::_('January').'", "'.JText::_('February').'", "'.JText::_('March').'", "'.JText::_('April').'", "'.JText::_('May').'", "'.JText::_('June').'", "'.JText::_('July').'", "'.JText::_('August').'", "'.JText::_('September').'", "'.JText::_('October').'", "'.JText::_('November').'", "'.JText::_('December').'");	Calendar._SMN = new Array ("'.JText::_('January_short').'", "'.JText::_('February_short').'", "'.JText::_('March_short').'", "'.JText::_('April_short').'", "'.JText::_('May_short').'", "'.JText::_('June_short').'", "'.JText::_('July_short').'", "'.JText::_('August_short').'", "'.JText::_('September_short').'", "'.JText::_('October_short').'", "'.JText::_('November_short').'", "'.JText::_('December_short').'");Calendar._TT = {};Calendar._TT["INFO"] = "'.JText::_('About the calendar').'";
 		Calendar._TT["ABOUT"] =
 "DHTML Date/Time Selector\n" +
 "(c) dynarch.com 2002-2005 / Author: Mihai Bazon\n" +
"For latest version visit: http://www.dynarch.com/projects/calendar/\n" +
"Distributed under GNU LGPL.  See http://gnu.org/licenses/lgpl.html for details." +
"\n\n" +
"Date selection:\n" +
"- Use the \xab, \xbb buttons to select year\n" +
"- Use the " + String.fromCharCode(0x2039) + ", " + String.fromCharCode(0x203a) + " buttons to select month\n" +
"- Hold mouse button on any of the above buttons for faster selection.";
Calendar._TT["ABOUT_TIME"] = "\n\n" +
"Time selection:\n" +
"- Click on any of the time parts to increase it\n" +
"- or Shift-click to decrease it\n" +
"- or click and drag for faster selection.";

		Calendar._TT["PREV_YEAR"] = "'.JText::_('Prev. year (hold for menu)').'";Calendar._TT["PREV_MONTH"] = "'.JText::_('Prev. month (hold for menu)').'";	Calendar._TT["GO_TODAY"] = "'.JText::_('Go Today').'";Calendar._TT["NEXT_MONTH"] = "'.JText::_('Next month (hold for menu)').'";Calendar._TT["NEXT_YEAR"] = "'.JText::_('Next year (hold for menu)').'";Calendar._TT["SEL_DATE"] = "'.JText::_('Select date').'";Calendar._TT["DRAG_TO_MOVE"] = "'.JText::_('Drag to move').'";Calendar._TT["PART_TODAY"] = "'.JText::_('(Today)').'";Calendar._TT["DAY_FIRST"] = "'.JText::_('Display %s first').'";Calendar._TT["WEEKEND"] = "0,6";Calendar._TT["CLOSE"] = "'.JText::_('Close').'";Calendar._TT["TODAY"] = "'.JText::_('Today').'";Calendar._TT["TIME_PART"] = "'.JText::_('(Shift-)Click or drag to change value').'";Calendar._TT["DEF_DATE_FORMAT"] = "'.JText::_('%Y-%m-%d').'"; Calendar._TT["TT_DATE_FORMAT"] = "'.JText::_('%a, %b %e').'";Calendar._TT["WK"] = "'.JText::_('wk').'";Calendar._TT["TIME"] = "'.JText::_('Time:').'";';
			$jsscript = 1;
			return $return;
		} else {
			return false;
		}
	}
}

