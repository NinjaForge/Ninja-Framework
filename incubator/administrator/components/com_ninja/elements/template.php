<?php
/**
 * @version		$Id: template.php 365 2010-06-23 11:43:38Z stian $
 * @category	Napi
 * @package		Napi_Parameter
 * @copyright	Copyright (C) 2007 - 2010 NinjaForge. All rights reserved.
 * @license		GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link     	http://ninjaforge.com
 */

class ComNinjaElementTemplate extends ComNinjaElementAbstract
{	
	protected $_nopath = false;

	function fetchElement($name, $value, &$node, $control_name)
	{	
		nimport('napi.plugin');
		
		$values = $this->_parent->_raw;
		
		$doc = JFactory::getDocument();
		$db			= & JFactory::getDBO();
		$db->setQuery('SELECT element, name FROM #__napi_plugins WHERE published = 1 AND folder = '.$db->Quote($node['plugin']?$node['plugin']:'content').' ORDER BY ordering, name, id');
		$params = $control_name.$name.'loadHere';
		$plugin = $control_name.$name.'update';
		$n      = $control_name.$name;
		if($node['option'])
		{
			$option = $node['option'];
		} else {
			switch(JRequest::getCmd('option'))
			{
				case 'com_modules':
				case 'com_plugins':
				case 'com_templates':
					$option = 'com_napi';
					break;
				default:
					$option = JRequest::getInt('com_napi')?JRequest::getCmd('option'):'com_napi';
					break; 
			}
		}
		$seeJson = $node['debug'] ? "$('$n"."seeJson').setProperty('href', $n"."url);" : "";
		$getxmlpath = JText::sprintf($node['getxmlpath']?$node['getxmlpath']:'%s.tmpl.', 'mod_ninjashowcase');
		$varvalue = '\'+'.$n.'value';
		$urlformat = $node['urlformat']?$node['urlformat']:'index.php?option=com_modules&format=cli&cli[update]='.$n.'desc&cli[getxmlpath]=%s';
		$urlparams = $node['urlparams']?explode($node['urlparamseparator']?$node['urlparamseparator']:',', $node['urlparams']):array('option', 'getxmlpath', 'varvalue');
		$url  = str_replace(array(), array(), 'index.php?option=com_napi&format=cli&view=theme&layout=params&cli[update]='.$n.'desc&cli[getxmlpath]=%s');
		JHTML::_('cli.render', 'params', array('trigger' => $n, 'url' => '\\\''.$url.'\'+$(\''.$n.'\').value', 'trigger_url' => '\\\''.$url.'\'+this.value', 'control_name' => $control_name, 'bind' => $values, 'loadOnInit' => true));
		$doc->addScriptDeclaration("
window.addEvent('domready', function() {

//	var $n"."parent = $('$n').getParent().getParent();
//	var scriptObj;
//	var $n"."prev;
//	var $n = $('$n');
//	var $n"."value = $n.value;
//	var $n"."url = '$url;
//	var $n"."data = '".json_encode(explode("\n", $values))."';
//	
//	function $plugin(plugin) {
//		if($('$n"."desc')) $('$n"."desc').remove();
//		var el = new Element('div', {'id': '$n"."desc', 'class': 'paramlist_json'});
//		el.setHTML(plugin.html);
//		$('$n').fireEvent('update');
//		el.injectAfter($n"."parent);
//		i = 0;
//		if($n"."prev)
//		{
//			$n"."prev.each(function(old){
//				old.remove();
//			});
//		}
//		if(plugin.scripts)
//		{
//			plugin.scripts.each(function(script){
//				scriptObj = Json.evaluate(script);
//				new Asset.javascript(scriptObj.src, {'type': scriptObj.type, 'class': '$n"."temp'});
//			});
//		}
//		if(plugin.styles)
//		{
//			plugin.styles.each(function(style){
//				styleObj = Json.evaluate(style);
//				new Asset.css(styleObj.href, {'class': '$n"."temp'});
//			});
//		}
//		$n"."prev = $$('.$n"."temp');
		//update($n"."parent);
//	};
//	
//	$n.addEvent('change', function(e) {
//		e = new Event(e).stop();
//		$n"."value = this.value;
//		$n"."url = '$url;
//		var $n"."request = new Json.Remote($n"."url, {
//			onComplete: function(jsonObj) {
//				$plugin(jsonObj);
//				".$seeJson."
//			}
//		}).send({'bind': $n"."data});
//	});
//	
//	var $n"."startrequest = new Json.Remote($n"."url, {
//			onComplete: function(jsonObj) {
//				$plugin(jsonObj);
//			}
//	}).send({'bind': $n"."data});
});");
		$arr = $db->loadObjectList();
		
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');
		$path = $this->_parent->_path ? dirname($this->_parent->_path) : false;
		$path = $node['path'] ? JPATH_ROOT.DIRECTORY_SEPARATOR.$node['path'] : $path;
		if(!$path) 
		{
			$this->_nopath = true;
		}
		$path = $path.DS.($node['folder'] ? $node['folder'] : 'tmpl');
		$files = JFolder::files($path, '.xml');
		foreach($files as $file)
		{
			$template			= new stdClass;
			$xml = & JFactory::getXMLParser('Simple');
			$xml->loadFile($path.DS.$file);
			
			$template->value	= JFile::stripExt($file);
			$template->text		= $xml->document->name[0]->data();
			
			$templates[] = $template;
		}
		if($node['trigger']=='error')
		{
			$obj = new stdClass;
			$obj->value = 'triggererrorevent';
			$obj->text 		= 'Trigger Error';
			$templates[] = $obj;
		}
		$label = $node['label'] ? $node['label'] : $name;
		$description = $node['description'];
		$output = '<label id="'.$control_name.$name.'-lbl" for="'.$control_name.$name.'"';
		if ($description) {
			$output .= ' class="hasTip" title="'.JText::_($label).'::'.JText::_($description).'">';
		} else {
			$output .= '>';
		}
		$output .= JText::_( $label ).'</label>';
		
		$html = $this->_nopath ? JText::_('Path isn\'t set. Either use NParameter instead of JParameter, or set the path to the template folder with the "path" attribute.') : JHTML::_('select.genericlist',  $templates, ''.$control_name.'['.$name.']', 'class="inputbox"', 'value', 'text', $value, $control_name.$name);
		
		return  '<p class="paramlist_item"><span class="editlinktip">'.$output.'</span><span class="paramlist_input">'.$html.'</span>'.'<div id="'.$n.'desc" class="paramlist_json"></div>';
	}
	
	function fetchTooltip($label, $description, &$xmlElement, $control_name='', $name='')
	{
		return;
	}
	
	function render(&$xmlElement, $value, $control_name = 'params')
	{
		$name	= $xmlElement['name'];
		$label	= $xmlElement['label'];
		$descr	= $xmlElement['description'];
		//make sure we have a valid label
		$label = $label ? $label : $name;
		$result[1] = $this->fetchElement($name, $value, $xmlElement, $control_name);
		$result[0] = null;
		$result[2] = $descr;
		$result[3] = $label;
		$result[4] = $value;
		$result[5] = $name;

		return $result;
	}
}
