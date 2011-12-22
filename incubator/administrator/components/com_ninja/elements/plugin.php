<?php
/**
 * @category	Napi
 * @package		Napi_Parameter
 * @copyright	Copyright (C) 2007 - 2010 NinjaForge. All rights reserved.
 * @license		GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link     	http://ninjaforge.com
 */

class ComNinjaElementPlugin extends ComNinjaElementAbstract
{
	function fetchElement($name, $value=null, &$node, $control_name)
	{	
		nimport('napi.plugin');
		//Legacy code
		if($node['plugin']=='showcase')
		{
			return NJPluginHelper::renderParams($node['plugin'], $name, $value, $node, $control_name);
		}
		$id 	= JRequest::getInt('id');
		if (!$id) { 
			$id = reset(JRequest::getVar( 'cid', array())); 
		}
		$dbs 	=& JFactory::getDBO();
		$querys = 'SELECT params'
		. ' FROM #__modules'
		. ' WHERE id ='. $id;
		$dbs->setQuery( $querys );
		$values = $dbs->loadResult();
		
		$values = $this->_parent->_raw;
		
		$doc = JFactory::getDocument();
		$db			= & JFactory::getDBO();
		$db->setQuery('SELECT DISTINCT plugin, id, name, NOT enabled AS disable FROM #__napi_plugins WHERE type = '.$db->Quote($node['plugin']).' ORDER BY name, id');
		$key = ($node['key_field'] ? $node['key_field'] : 'id');
		$val = ($node['value_field'] ? $node['value_field'] : 'name');
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
		$getxmlpath = JText::sprintf($node['getxmlpath']?$node['getxmlpath']:'%s.', 'napi_'.$node['plugin']);
		$urlformat = $node['urlformat']?$node['urlformat']:'index.php?option=%s&view=plugin&layout=params&format=cli&cli[update]='.$n.'desc&id=';
		$urlparams = $node['urlparams']?explode(',', $node['urlparams']):array('option', 'getxmlpath');
		$url  = JText::sprintf($urlformat, $$urlparams[0], $$urlparams[1]);
		$seeJson = $node['debug'] ? "$('$n"."seeJson').setProperty('href', $n"."url);" : "";
		JHTML::_('cli.render', 'params', array('trigger' => $n, 'url' => '\\\''.$url.'\'+$(\''.$n.'\').value', 'trigger_url' => '\\\''.$url.'\'+this.value', 'control_name' => $control_name, 'bind' => $values, 'loadOnInit' => true));
		/*$doc->addScriptDeclaration("
window.addEvent('domready', function() {
	var $n"."parent = $('$n').getParent().getParent();
	var scriptObj;
	var $n"."prev;
	var $n = $('$n');
	var $n"."value = $n.value;
	var $n"."url = 'index.php?option=$option&task=cli&format=json&cli[getxmlpath]=napi_".$node['plugin'].".'+$n"."value;
	var $n"."data = '".json_encode(explode("\n", $values))."';
	
	function $plugin(plugin) {
		if($('$n"."desc')) $('$n"."desc').empty();
		var el = $('$n"."desc');
		el.setHTML(plugin.html);
		$('$n').fireEvent('update');
		el.injectAfter($n"."parent);
		i = 0;
		if($n"."prev)
		{
			$n"."prev.each(function(old){
				old.remove();
			});
		}
		if(plugin.scripts)
		{
			plugin.scripts.each(function(script){
				scriptObj = Json.evaluate(script);
				new Asset.javascript(scriptObj.src, {'type': scriptObj.type, 'class': '$n"."temp'});
			});
		}
		if(plugin.styles)
		{
			plugin.styles.each(function(style){
				styleObj = Json.evaluate(style);
				new Asset.css(styleObj.href, {'class': '$n"."temp'});
			});
		}
		$n"."prev = $$('.$n"."temp');
		//update($n"."parent);
	};
	
	$n.addEvent('change', function(e) {
		e = new Event(e).stop();
		$n"."value = this.value;
		$n"."url = 'index.php?option=$option&task=cli&format=cli&cli[getxmlpath]=napi_".$node['plugin'].".'+$n"."value;
		var $n"."request = new Json.Remote($n"."url, {
			onComplete: function(jsonObj) {
				$plugin(jsonObj);
				".$seeJson."
			}
		}).send({'bind': $n"."data});
	});
	
	var $n"."startrequest = new Json.Remote($n"."url, {
			onComplete: function(jsonObj) {
				$plugin(jsonObj);
			}
	}).send({'bind': $n"."data});
});");*/
		$arr = $db->loadObjectList();
		if(empty($arr))
		{
			$arr[] = JHTML::_('select.option', '', 'Trigger Error', $key, $val, true);
		}
		//$arr = array_unshift($db->loadObjectList(), $obj);
		$debug = $node['debug']?' <a href="index.php?option=com_napi&format=cli&cli[getxmlpath]=napi_'.$node['plugin'].'.'.$value.'" id="'.$control_name.$name.'seeJson" title="Json: data.js">see <strong>data.js</strong></a>':'';
		$label = $node['label'] ? $node['label'] : $name;
		$description = $node['description'];
		$output = '<label id="'.$control_name.$name.'-lbl" for="'.$control_name.$name.'"';
		if ($description) {
			$output .= ' class="hasTip" title="'.JText::_($label).'::'.JText::_($description).'">';
		} else {
			$output .= '>';
		}
		$output .= JText::_( $label ).'</label>';
		return '<p class="paramlist_item"><span class="editlinktip">'.$output.'</span><span class="paramlist_input">'.JHTML::_('select.genericlist',  $arr, ''.$control_name.'['.$name.']', 'class="inputbox"', $key, $val, $value, $control_name.$name).$debug.'</span>'.'<div id="'.$n.'desc" class="paramlist_json"></div>';
	}
	
	function fetchTooltip()
	{
		return false;
	}
}
