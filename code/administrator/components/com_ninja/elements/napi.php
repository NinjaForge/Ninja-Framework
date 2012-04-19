<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category	Napi
 * @package		Napi_Parameter
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

JLoader::register('JElement', JPATH_LIBRARIES.'/joomla/html/parameter/element.php');

/**
 * Used to render ninja elements in JParameter joomla forms
 *
 * We have to use the JElement naming format for this purpose
 *
 * @package 	Napi
 * @subpackage	Napi_Parameter
 */

class JElementNapi extends JElement
{	
	
	/**
	 * The element name
	 *
	 * @var string
	 */
	public $_name = 'Napi';
	
	public function fetchElement($name, $value, &$node, $control_name, $html = null)
	{
		$src = null;
		if($src = $node->attributes('src', false)) $src = ' class="'. $src . '"';

		$form = JPATH_ROOT.'/'.$node->attributes('xml', false);
		if(is_file($form))
		{
			$form = simplexml_load_file($form)->form->asXML();
		}
		else
		{
			$form  = '<form'.$src.'>';
			foreach($this->_parent->_xml as $group => $xml)
			{
				if($group == '_default') $xml->addAttribute('group', 'basic');
				$form .= $xml->toString();
			}
			$form .= '</form>';
		}

		$grouptag  = $node->attributes('grouptag');
		if(!$grouptag) $grouptag = 'params';
		$groupname  = $node->attributes('formname');
		if(!$groupname) $groupname = 'params';
		$data = $this->_parent->_raw;
		if(!$data) $data = $this->_parent->_registry['_default']['data'];

		$parameter = KService::get('ninja:form.parameter', array(
					  		'data' 	   => $data,
					  		'xml'  	   => $form,
					  		'render'   => 'inline',
					  		'groups'   => false,
					  		'grouptag' => $grouptag,
					  		'name'	   => $groupname
					  ));

		$html[] = '</td></tr></tbody></table>';
		$html[] = $parameter->render();
		$html[] = '<table id="'.$name.'"><tbody><tr><td>';
					
		KService::get('ninja:template.helper.document')->load('js', 'window.addEvent(\'domready\', function(){
			$(\''.$name.'\').getParent().getChildren().each(function(el){
				if(el.tagName == "TABLE") {
					(function(){this.setStyle(\'height\', \'\');}.bind(el.getParent())).delay(601);
					el.remove();
				}
			});
		});');
		KService::get('ninja:template.helper.document')->load('/form.css');
		
		return implode($html);
	}
}